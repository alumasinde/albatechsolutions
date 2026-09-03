<?php

declare(strict_types=1);

namespace App\Modules\Assistant\Service;

use App\Core\Auth;
use App\Core\BaseService;
use App\Core\Helpers\Validator;
use App\Modules\Assistant\Repository\AssistantRepository;
use App\Modules\Assistance\Service\AssistanceRequestService;
use App\Modules\Cms\Repository\ServiceRepository;
use App\Modules\Growth\Service\GrowthAnalyticsService;

final class AssistantService extends BaseService
{
    public function __construct(private readonly AssistantRepository $repo, private readonly ServiceRepository $services, private readonly AssistanceRequestService $requests, private readonly GrowthAnalyticsService $growth) {}

    public function start(): array
    {
        $token=bin2hex(random_bytes(32));
        $state=['step'=>'intent','matches'=>[],'category'=>null,'service_id'=>null,'name'=>null,'phone'=>null,'email'=>null,'preferred_contact'=>null,'message'=>null];
        $id=$this->repo->createSession(hash('sha256',$token), Auth::check()?Auth::id():null,$state);
        $this->repo->addMessage($id,'assistant','Hi 👋 I\'m AlbaTech\'s digital assistant. Tell me what you are trying to get done online — you can explain it in your own words.');
        return ['token'=>$token,'session_id'=>$id,'message'=>'Hi 👋 I\'m AlbaTech\'s digital assistant. Tell me what you are trying to get done online — you can explain it in your own words.'];
    }

    public function message(string $token,string $text,string $ip): array
    {
        $text=trim($text); if($text==='') return ['success'=>false,'message'=>'Please type what you need help with.'];
        if(mb_strlen($text)>2000) return ['success'=>false,'message'=>'Please keep your message under 2,000 characters.'];
        $session=$this->repo->findByTokenHash(hash('sha256',$token)); if(!$session) return ['success'=>false,'message'=>'This assistant session has expired. Please start a new one.','restart'=>true];
        if(!empty($session['completed_at'])) return ['success'=>false,'message'=>'This conversation has already been handed to AlbaTech. Start a new conversation if you need help with something else.'];
        $state=json_decode((string)($session['state']??'{}'),true); if(!is_array($state)) $state=[];
        $this->repo->addMessage((int)$session['id'],'user',$text);
        $step=(string)($state['step']??'intent');
        if($step==='intent') return $this->handleIntent($session,$state,$text);
        if($step==='confirm_service') return $this->handleConfirm($session,$state,$text);
        if($step==='name') return $this->handleName($session,$state,$text);
        if($step==='phone') return $this->handlePhone($session,$state,$text);
        if($step==='contact') return $this->handleContact($session,$state,$text);
        if($step==='details') return $this->handleDetails($session,$state,$text,$ip);
        return ['success'=>true,'message'=>'I can hand this over to AlbaTech. Please start a new conversation if you need another request.'];
    }

    private function handleIntent(array $session,array $state,string $text): array
    {
        $matches=$this->matchServices($text);
        if(!$matches) {
            $state['step']='intent'; $this->repo->updateState((int)$session['id'],$state);
            return $this->reply((int)$session['id'],'I can help with government and online services, business setup, documents and CVs, websites, software and other digital tasks. Tell me a little more about what you need.', ['needs_clarification'=>true]);
        }
        $best=$matches[0]; $state['matches']=array_map(static fn($m)=>$m['id'],$matches); $state['service_id']=$best['id']; $state['category']=$best['category']; $state['step']='confirm_service'; $this->repo->updateState((int)$session['id'],$state);
        foreach($matches as $m) $this->repo->saveMatch((int)$session['id'],$m['id'],$m['score'],$m['reason']);
        $price=$this->priceText($best);
        $reply='It sounds like you may need help with '.$best['name'].'.'.$price.'\n\nIs that what you need, or should I look for something else? Reply yes or tell me what you actually need.';
        return $this->reply((int)$session['id'],$reply,['service_id'=>$best['id'],'matches'=>$matches]);
    }

    private function handleConfirm(array $session,array $state,string $text): array
    {
        $lower=mb_strtolower($text);
        if(preg_match('/\b(no|different|something else|not that|wrong)\b/u',$lower)) { $state['step']='intent'; $state['service_id']=null; $this->repo->updateState((int)$session['id'],$state); return $this->reply((int)$session['id'],'No problem. Tell me again, in your own words, what you are trying to do.'); }
        if(!preg_match('/\b(yes|yeah|yep|correct|that|exactly|okay|ok)\b/u',$lower)) return $this->reply((int)$session['id'],'Just reply yes if that is the service you need, or tell me what you are trying to do instead.');
        $state['step']='name'; $this->repo->updateState((int)$session['id'],$state); return $this->reply((int)$session['id'],'Great. I can prepare an assistance request for you. What is your name?');
    }

    private function handleName(array $session,array $state,string $text): array
    { if(mb_strlen($text)<2||mb_strlen($text)>120) return $this->reply((int)$session['id'],'Please enter your name (2–120 characters).'); $state['name']=$text;$state['step']='phone';$this->repo->updateState((int)$session['id'],$state);return $this->reply((int)$session['id'],'Thanks, '.htmlspecialchars($text,ENT_QUOTES,'UTF-8').'. What phone number or WhatsApp number should AlbaTech use to contact you?'); }

    private function handlePhone(array $session,array $state,string $text): array
    { $digits=preg_replace('/\D+/','',$text); if(strlen($digits)<9||strlen($digits)>15)return $this->reply((int)$session['id'],'Please enter a valid Kenyan phone/WhatsApp number.'); $state['phone']=$text;$state['step']='contact';$this->repo->updateState((int)$session['id'],$state);return $this->reply((int)$session['id'],'How would you prefer AlbaTech to contact you? Reply WhatsApp, phone, or email.'); }

    private function handleContact(array $session,array $state,string $text): array
    { $v=mb_strtolower(trim($text)); $contact=str_contains($v,'whatsapp')?'whatsapp':(str_contains($v,'phone')||str_contains($v,'call')?'phone':(str_contains($v,'email')?'email':null)); if(!$contact)return $this->reply((int)$session['id'],'Please choose **WhatsApp**, **phone**, or **email**.');$state['preferred_contact']=$contact;$state['step']='details';$this->repo->updateState((int)$session['id'],$state);return $this->reply((int)$session['id'],'Last step: briefly tell me anything important about the task — for example what you have already tried, where you are stuck, or what outcome you want. Do not send passwords, PINs or OTPs.'); }

    private function handleDetails(array $session,array $state,string $text,string $ip): array
    { if(mb_strlen($text)<10)return $this->reply((int)$session['id'],'Please give me a little more detail so AlbaTech knows what you need.'); $state['message']=$text; $category=$state['category']??'other'; $serviceId=(int)($state['service_id']??0); $result=$this->requests->submit(['name'=>$state['name'],'phone'=>$state['phone'],'email'=>$state['email']??'','category'=>$category,'service_id'=>$serviceId?:'','message'=>$text,'preferred_contact'=>$state['preferred_contact'],'consent'=>'1'], $ip, 'AlbaTech Digital Assistant'); if(!$result['success']) return $this->reply((int)$session['id'],'I could not create the request yet. '.implode(' ',array_map(static fn($x)=>implode(' ',(array)$x),(array)$result['errors']))); $this->repo->complete((int)$session['id']); return $this->reply((int)$session['id'],'Done. Your assistance request is '.$result['reference'].'. AlbaTech can now review it and contact you using your preferred method.\n\nYou can also continue directly on WhatsApp if that is easier.', ['reference'=>$result['reference'],'handoff'=>true]); }

    private function matchServices(string $text): array
    {
        $services=$this->services->allPublished(); $q=$this->tokens($text); $results=[];
        $categoryKeywords=['government'=>['kra','ecitizen','sha','nhif','nssf','ntsa','passport','government','returns','pin','certificate','license','licence'], 'business'=>['business','company','registration','register','shop','enterprise','google business'], 'documents'=>['document','cv','resume','application','form','letter','cover letter'], 'jobs'=>['job','jobs','cv','resume','interview','career','application'], 'website'=>['website','web','domain','hosting','google profile','email'], 'software'=>['software','system','app','automation','api','ecommerce','e-commerce']];
        foreach($services as $s){$textBlob=mb_strtolower((string)$s['name'].' '.strip_tags((string)($s['short_description']??'')).' '.strip_tags((string)($s['description']??'')).' '.(string)($s['category_name']??''));$stokens=$this->tokens($textBlob);$score=0;$reasons=[];foreach($q as $word){if(strlen($word)<3)continue;if(in_array($word,$stokens,true)){ $score+=2;$reasons[]=$word; }} foreach($categoryKeywords as $cat=>$words){if(in_array((string)($s['category_slug']??''),[$cat],true)){foreach($words as $kw){if(str_contains(mb_strtolower($text),$kw)){ $score+=3;$reasons[]=$kw;}}}} if($score>0)$results[]=['id'=>(int)$s['id'],'name'=>$s['name'],'slug'=>$s['slug'],'category'=>$this->mapCategory((string)($s['category_slug']??''),(string)($s['category_name']??'')),'score'=>$score,'reason'=>'Matched: '.implode(', ',array_slice(array_unique($reasons),0,5)),'service'=>$s];}
        usort($results,static fn($a,$b)=>$b['score']<=>$a['score']); return array_slice($results,0,3);
    }

    private function mapCategory(string $slug,string $name): string
    { $s=mb_strtolower($slug.' '.$name); if(str_contains($s,'job'))return 'jobs'; if(str_contains($s,'document')||str_contains($s,'cv'))return 'documents'; if(str_contains($s,'government')||preg_match('/kra|sha|ecitizen|ntsa|nssf/u',$s))return 'government'; if(str_contains($s,'business'))return 'business'; if(str_contains($s,'software')||str_contains($s,'system'))return 'software'; if(str_contains($s,'web')||str_contains($s,'hosting'))return 'website'; return 'other'; }

    private function tokens(string $text): array { $text=mb_strtolower(strip_tags($text)); $parts=preg_split('/[^\p{L}\p{N}]+/u',$text)?:[]; return array_values(array_unique(array_filter($parts,static fn($v)=>mb_strlen($v)>=2))); }

    private function priceText(array $match): string { $s=$match['service']; $mode=$s['commerce_pricing_mode']??null; $fee=$s['customer_fee']??null; if($mode==='fixed'&&$fee!==null)return ' The listed AlbaTech fee is KES '.number_format((float)$fee,2).'.'; if($mode==='starting_from'&&$fee!==null)return ' Pricing starts from KES '.number_format((float)$fee,2).'.'; if($mode==='free')return ' This service is currently free.'; return ''; }

    private function reply(int $sessionId,string $message,array $meta=[]): array { $this->repo->addMessage($sessionId,'assistant',$message,$meta); return ['success'=>true,'message'=>$message]+$meta; }
}

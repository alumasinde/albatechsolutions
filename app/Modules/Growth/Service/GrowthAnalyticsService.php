<?php

declare(strict_types=1);
namespace App\Modules\Growth\Service;
use App\Modules\Growth\Repository\GrowthAnalyticsRepository;

final class GrowthAnalyticsService
{
    public function __construct(private readonly GrowthAnalyticsRepository $repo) {}
    public function visitorHash(): string { return hash('sha256','alba-analytics|'.session_id()); }
    public function pageView(string $path,?string $title,array $query,?string $referrer): void {
        $path='/'.ltrim(parse_url($path,PHP_URL_PATH)?:'/','/'); if($this->isExcluded($path))return;
        $host=$referrer?(parse_url($referrer,PHP_URL_HOST)?:null):null;
        $this->repo->recordPageView(['visitor_hash'=>$this->visitorHash(),'path'=>$path,'page_type'=>$this->clean($query['page_type']??null,40) ?: $this->pageType($path),'entity_id'=>$this->entityIdFromQuery($query),'title'=>mb_substr((string)$title,0,255),'referrer_host'=>$host?mb_substr($host,0,255):null,'utm_source'=>$this->clean($query['utm_source']??null,120),'utm_medium'=>$this->clean($query['utm_medium']??null,120),'utm_campaign'=>$this->clean($query['utm_campaign']??null,180)]);
    }
    public function event(string $name,?string $path=null,?int $serviceId=null,?int $entityId=null,array $metadata=[]): void {
        $allowed=['cta_get_help','cta_assistant','cta_whatsapp','assistance_request_created','assistant_handoff','quote_created','quote_accepted','payment_verified','assistance_completed','review_submitted']; if(!in_array($name,$allowed,true))return;
        $safe=[];foreach($metadata as $k=>$v){if(!is_string($k))continue;if(is_scalar($v)||$v===null)$safe[mb_substr($k,0,60)]=is_string($v)?mb_substr($v,0,500):$v;}
        $this->repo->recordEvent(['visitor_hash'=>$this->visitorHash(),'event_name'=>$name,'path'=>$path,'service_id'=>$serviceId,'entity_id'=>$entityId,'metadata'=>$safe]);
    }
    private function clean(mixed $v,int $max):?string{$v=trim((string)$v);return $v===''?null:mb_substr($v,0,$max);}
    private function isExcluded(string $p):bool{return preg_match('#^/(admin|dashboard|account|login|register|forgot-password|reset-password|verify-email|auth/)#',$p)===1||str_starts_with($p,'/request/')||str_starts_with($p,'/quote/')||str_starts_with($p,'/receipt/')||str_starts_with($p,'/review/');}
    private function pageType(string $p):string{if($p==='/')return'home';if(str_starts_with($p,'/services/'))return'service';if($p==='/services')return'services_index';if(str_starts_with($p,'/blog/'))return'blog';if($p==='/blog')return'blog_index';if(str_starts_with($p,'/projects/'))return'project';if($p==='/get-help')return'assistance';if($p==='/assistant')return'assistant';return'page';}
    private function entityIdFromQuery(array $q):?int{
        $value=$q['entity_id']??($q['service_id']??null);
        return $value!==null&&ctype_digit((string)$value)?(int)$value:null;
    }
}

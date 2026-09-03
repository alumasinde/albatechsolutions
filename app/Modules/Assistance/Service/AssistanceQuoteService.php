<?php

declare(strict_types=1);

namespace App\Modules\Assistance\Service;

use App\Core\AuditLog;
use App\Core\BaseService;
use App\Core\Config;
use App\Core\Logger;
use App\Core\Settings;
use App\Modules\Growth\Service\GrowthAnalyticsService;
use App\Modules\Assistance\Repository\AssistanceQuoteRepository;
use App\Modules\Assistance\Repository\AssistanceRequestRepository;
use PHPMailer\PHPMailer\PHPMailer;

final class AssistanceQuoteService extends BaseService
{
    public function __construct(
        private readonly AssistanceQuoteRepository $quotes,
        private readonly AssistanceRequestRepository $requests,
        private readonly AssistanceNotificationService $notifications,
        private readonly GrowthAnalyticsService $growth
    ) {}

    public function create(int $requestId,int $adminId,array $items,string $note,?string $expiresAt): array {
        $clean=[];$subtotal=0.0;
        foreach($items as $item){
            $description=trim((string)($item['description']??'')); $qty=(float)($item['quantity']??1); $unit=(float)($item['unit_price']??0);
            if($description===''||$qty<=0||$unit<0) continue;
            $line=round($qty*$unit,2); $subtotal+=$line; $clean[]=['description'=>$description,'quantity'=>$qty,'unit_price'=>$unit,'line_total'=>$line];
        }
        if(!$clean||$subtotal<=0)return ['success'=>false,'message'=>'Add at least one billable item with a positive amount.'];
        $token=bin2hex(random_bytes(24)); $number='AT-QTE-'.date('Y').'-'.strtoupper(bin2hex(random_bytes(3)));
        $id=$this->quotes->createQuote(['assistance_request_id'=>$requestId,'quote_number'=>$number,'public_token_hash'=>hash('sha256',$token),'public_token_encrypted'=>$this->encryptToken($token),'subtotal'=>$subtotal,'total'=>$subtotal,'status'=>'sent','note'=>$note?:null,'expires_at'=>$expiresAt?:null,'sent_at'=>date('Y-m-d H:i:s'),'created_by'=>$adminId]);
        foreach($clean as $i=>$row)$this->quotes->addItem(['quote_id'=>$id,...$row,'sort_order'=>$i]);
        $this->quotes->addEvent($id,'sent','admin',$adminId,'Quote created and sent.'); AuditLog::record('assistance_quote.sent','assistance_quote',$id,['quote_number'=>$number]);
        $quote=$this->quotes->findAdmin($id)??[]; $quote['public_token']=$token; $this->notifications->quoteSent($quote);
        $this->growth->event('quote_created',null,!empty($quote['service_id'])?(int)$quote['service_id']:null,$requestId,['quote_id'=>$id,'amount'=>(float)$subtotal]);
        return ['success'=>true,'id'=>$id,'number'=>$number,'token'=>$token];
    }

    public function accept(string $token,array $data=[]): array {
        $quote=$this->quotes->findPublicByToken($token); if(!$quote)return ['success'=>false,'message'=>'Quote not found.'];
        if($this->expireIfNeeded($quote))return ['success'=>false,'message'=>'This quote has expired.'];
        if($quote['status']!=='sent')return ['success'=>false,'message'=>'This quote is no longer awaiting approval.'];
        if(!$this->phoneCheckPasses($quote,$data))return ['success'=>false,'message'=>'Please enter the last 4 digits of the phone number used for this request.'];
        $this->quotes->updateQuote((int)$quote['id'],['status'=>'accepted','accepted_at'=>date('Y-m-d H:i:s')]);
        $this->growth->event('quote_accepted',null,!empty($quote['service_id'])?(int)$quote['service_id']:null,(int)$quote['assistance_request_id'],['quote_id'=>(int)$quote['id'],'amount'=>(float)$quote['total']]);
        $this->quotes->addEvent((int)$quote['id'],'accepted','customer',null,'Customer accepted the quote.');
        AuditLog::record('assistance_quote.accepted','assistance_quote',(int)$quote['id']); $this->notifications->quoteAccepted($quote);
        return ['success'=>true];
    }

    public function submitPayment(string $token,array $data): array {
        $quote=$this->quotes->findPublicByToken($token); if(!$quote)return ['success'=>false,'message'=>'Quote not found.'];
        if($this->expireIfNeeded($quote))return ['success'=>false,'message'=>'This quote has expired.'];
        if($quote['status']!=='accepted')return ['success'=>false,'message'=>'Accept the quote before submitting payment.'];
        if(!$this->phoneCheckPasses($quote,$data))return ['success'=>false,'message'=>'Please enter the last 4 digits of the phone number used for this request.'];
        $receipt=trim((string)($data['mpesa_receipt']??'')); $phone=trim((string)($data['payer_phone']??''));
        if($receipt===''||strlen($receipt)>80)return ['success'=>false,'message'=>'Enter the M-Pesa transaction code.'];
        $ref='AT-PAY-'.date('Y').'-'.strtoupper(bin2hex(random_bytes(3)));
        $id=$this->quotes->createPayment(['quote_id'=>(int)$quote['id'],'payment_reference'=>$ref,'method'=>'mpesa','amount'=>$quote['total'],'currency'=>$quote['currency'],'mpesa_receipt'=>$receipt,'payer_phone'=>$phone?:null,'status'=>'submitted','customer_note'=>trim((string)($data['customer_note']??''))?:null]);
        $this->quotes->addEvent((int)$quote['id'],'payment_submitted','customer',null,'Payment submitted for verification.'); AuditLog::record('assistance_payment.submitted','assistance_payment',$id,['quote_id'=>$quote['id'],'reference'=>$ref]);
        $this->notifyAdmin($quote,$ref);$this->notifications->paymentSubmitted($quote,$id,$ref);return ['success'=>true,'reference'=>$ref];
    }

    public function verifyPayment(int $paymentId,int $adminId): array {
        $p=$this->quotes->findPayment($paymentId);if(!$p)return ['success'=>false,'message'=>'Payment not found.'];
        if($p['status']==='verified')return ['success'=>true]; if($p['status']!=='submitted')return ['success'=>false,'message'=>'Payment is not awaiting verification.'];
        if(abs((float)$p['amount']-(float)$p['total'])>0.001)return ['success'=>false,'message'=>'Payment amount does not match the quote.'];
        $token=bin2hex(random_bytes(24));$this->quotes->updatePayment($paymentId,['status'=>'verified','verified_by'=>$adminId,'verified_at'=>date('Y-m-d H:i:s'),'receipt_token_hash'=>hash('sha256',$token),'receipt_token_encrypted'=>$this->encryptToken($token),'receipt_issued_at'=>date('Y-m-d H:i:s')]);
        $this->quotes->updateQuote((int)$p['quote_id'],['status'=>'paid','paid_at'=>date('Y-m-d H:i:s')]);$this->growth->event('payment_verified',null,!empty($p['service_id'])?(int)$p['service_id']:null,(int)$p['assistance_request_id'],['payment_id'=>$paymentId,'amount'=>(float)$p['amount']]);
        $this->quotes->addEvent((int)$p['quote_id'],'payment_verified','admin',$adminId,'Payment verified and quote marked paid.');AuditLog::record('assistance_payment.verified','assistance_payment',$paymentId,['quote_id'=>$p['quote_id']]);
        $verified=$this->quotes->findPayment($paymentId)??$p;$this->notifications->paymentVerified($verified,rtrim(Config::get('app.url',''),'/').'/receipt/'.rawurlencode($token));return ['success'=>true,'receipt_token'=>$token];
    }

    public function rejectPayment(int $paymentId,int $adminId,?string $reason): array {
        $p=$this->quotes->findPayment($paymentId);if(!$p)return ['success'=>false,'message'=>'Payment not found.'];if($p['status']!=='submitted')return ['success'=>false,'message'=>'Payment is not awaiting verification.'];
        $reason=$reason?:'Payment rejected.';$this->quotes->updatePayment($paymentId,['status'=>'rejected','verified_by'=>$adminId,'verified_at'=>date('Y-m-d H:i:s'),'admin_note'=>$reason]);$this->quotes->addEvent((int)$p['quote_id'],'payment_rejected','admin',$adminId,$reason);AuditLog::record('assistance_payment.rejected','assistance_payment',$paymentId,['quote_id'=>$p['quote_id']]);$rejected=$this->quotes->findPayment($paymentId)??$p;$this->notifications->paymentRejected($rejected,$reason);return ['success'=>true];
    }

    public function decryptToken(string $encrypted): ?string
    {
        $key=hash('sha256',(string)Config::get('app.key',''),true);$raw=base64_decode($encrypted,true);if($raw===false||strlen($raw)<28)return null;$iv=substr($raw,0,12);$tag=substr($raw,12,16);$cipher=substr($raw,28);$plain=openssl_decrypt($cipher,'aes-256-gcm',$key,OPENSSL_RAW_DATA,$iv,$tag);return $plain===false?null:$plain;
    }

    private function encryptToken(string $token): string
    {
        $key=hash('sha256',(string)Config::get('app.key',''),true);$iv=random_bytes(12);$tag='';$cipher=openssl_encrypt($token,'aes-256-gcm',$key,OPENSSL_RAW_DATA,$iv,$tag);if($cipher===false)throw new \RuntimeException('Unable to protect quote token.');return base64_encode($iv.$tag.$cipher);
    }

    private function expireIfNeeded(array $quote): bool
    {
        if(empty($quote['expires_at'])||strtotime((string)$quote['expires_at'])>=time()||in_array($quote['status'],['expired','paid','cancelled'],true))return false;
        $this->quotes->updateQuote((int)$quote['id'],['status'=>'expired']);
        $this->quotes->addEvent((int)$quote['id'],'expired','system',null,'Quote expired after its expiry time.');
        AuditLog::record('assistance_quote.expired','assistance_quote',(int)$quote['id']);
        return true;
    }

    private function phoneCheckPasses(array $quote,array $data): bool
    {
        if(!filter_var($_ENV['QUOTE_REQUIRE_PHONE_LAST4']??false,FILTER_VALIDATE_BOOLEAN))return true;
        $expected=preg_replace('/\D+/','',(string)($quote['phone']??''));$provided=preg_replace('/\D+/','',(string)($data['phone_last4']??''));
        return strlen($expected)>=4&&strlen($provided)===4&&hash_equals(substr($expected,-4),$provided);
    }

    private function notifyAdmin(array $quote,string $paymentRef):void { $recipient=Settings::get('contact_email');if(!$recipient||empty($_ENV['MAIL_HOST']??null))return;try{$mail=new PHPMailer(true);$mail->isSMTP();$mail->Host=$_ENV['MAIL_HOST'];$mail->Port=(int)($_ENV['MAIL_PORT']??587);$mail->SMTPAuth=true;$mail->Username=$_ENV['MAIL_USER']??'';$mail->Password=$_ENV['MAIL_PASS']??'';$mail->SMTPSecure=$_ENV['MAIL_ENCRYPTION']??'tls';$mail->setFrom($_ENV['MAIL_FROM_ADDRESS']??'noreply@'.parse_url(Config::get('app.url'),PHP_URL_HOST),$_ENV['MAIL_FROM_NAME']??Settings::get('site_name','AlbaTech Solutions'));$mail->addAddress($recipient);$mail->Subject='Payment submitted — '.$paymentRef;$mail->Body="A customer submitted an M-Pesa payment for quote {$quote['quote_number']}.\n\nPayment reference: {$paymentRef}\nAmount: KES {$quote['total']}\nCustomer: {$quote['name']}\n\nVerify the transaction before starting work.";$mail->send();}catch(\Throwable $e){Logger::warning('Assistance payment email notification failed: '.$e->getMessage());}}
}

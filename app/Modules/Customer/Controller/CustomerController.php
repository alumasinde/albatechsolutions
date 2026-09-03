<?php

declare(strict_types=1);

namespace App\Modules\Customer\Controller;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Helpers\Validator;
use App\Modules\Assistance\Repository\AssistanceQuoteRepository;
use App\Modules\Assistance\Repository\AssistanceNotificationRepository;
use App\Modules\Assistance\Repository\AssistanceWorkRepository;
use App\Modules\Assistance\Service\AssistanceWorkService;
use App\Modules\Assistance\Service\AssistanceQuoteService;
use App\Modules\Customer\Repository\CustomerRepository;

final class CustomerController extends BaseController
{
    public function __construct(
        private readonly CustomerRepository $customers,
        private readonly AssistanceWorkRepository $work,
        private readonly AssistanceQuoteRepository $quotes,
        private readonly AssistanceWorkService $workService,
        private readonly AssistanceQuoteService $quoteService,
        private readonly AssistanceNotificationRepository $notifications,
    ) {}

    public function dashboard(Request $request): Response
    {
        $id=(int)Auth::id();
        return $this->view('customer.dashboard', [
            'user'=>Auth::user(),
            'stats'=>$this->customers->dashboard($id),
            'activity'=>$this->customers->recentActivity($id),
        ]);
    }

    public function requests(Request $request): Response
    {
        return $this->view('customer.requests', ['requests'=>$this->customers->requests((int)Auth::id())]);
    }

    public function requestShow(Request $request): Response
    {
        $id=(int)Auth::id(); $requestId=(int)$request->param('id');
        $item=$this->customers->request($id,$requestId);
        if(!$item)return Response::text('Not found',404);
        $receipt=$this->quotes->latestVerifiedPaymentForRequest($requestId);
        $receiptUrl=null;
        if($receipt && !empty($receipt['receipt_token_encrypted'])){
            $token=$this->quoteService->decryptToken((string)$receipt['receipt_token_encrypted']);
            if($token)$receiptUrl=rtrim(Config::get('app.url',''),'/').'/receipt/'.rawurlencode($token);
        }
        $review=$this->work->reviewByRequest($requestId);
        $reviewUrl=$review?$this->workService->reviewPublicUrl($requestId,$item):null;
        return $this->view('customer.request-show',[
            'requestItem'=>$item,
            'tasks'=>$this->work->tasks($requestId),
            'updates'=>$this->work->updates($requestId,true),
            'quote'=>$this->quotes->latestForRequest($requestId),
            'payment'=>$receipt,
            'receiptUrl'=>$receiptUrl,
            'review'=>$review,
            'reviewUrl'=>$reviewUrl,
        ]);
    }

    public function quotes(Request $request): Response
    {
        return $this->view('customer.quotes',['quotes'=>$this->customers->quotes((int)Auth::id())]);
    }

    public function quoteShow(Request $request): Response
    {
        $quote=$this->customers->quote((int)Auth::id(),(int)$request->param('id'));
        if(!$quote)return Response::text('Not found',404);
        return $this->view('customer.quote-show',[
            'quote'=>$quote,
            'items'=>$this->quotes->items((int)$quote['id']),
            'payments'=>$this->quotes->payments((int)$quote['id']),
            'quoteServiceToken'=>$this->quoteToken($quote),
        ]);
    }

    public function payments(Request $request): Response
    {
        return $this->view('customer.payments',['payments'=>$this->customers->payments((int)Auth::id())]);
    }

    public function profile(Request $request): Response
    {
        return $this->view('customer.profile',['user'=>Auth::user(),'errors'=>Session::getFlash('_errors')??[],'success'=>Session::getFlash('_success'),'notificationPreferences'=>$this->notifications->userPreference((int)Auth::id())]);
    }

    public function updateProfile(Request $request): Response
    {
        $validator=new Validator($request->all(),['name'=>'required|min:2|max:150','phone'=>'max:20']);
        if($validator->fails()){Session::flash('_errors',$validator->errors());return $this->back();}
        $userId=(int)Auth::id();
        $this->customers->updateProfile($userId,trim((string)$request->input('name')),trim((string)$request->input('phone'))?:null);
        $this->notifications->saveUserPreference($userId, [
            'email_enabled'=>$request->input('email_notifications') === '1',
            'sms_enabled'=>$request->input('sms_notifications') === '1',
            'whatsapp_enabled'=>$request->input('whatsapp_notifications') === '1',
        ]);
        Session::flash('_success','Your profile has been updated.');
        return $this->redirect('/account/profile');
    }

    public function claimRequest(Request $request): Response
    {
        $token=trim((string)$request->param('token'));
        $item=$this->findRequestByPortalToken($token);
        if(!$item)return Response::text('Request not found',404);
        if(!empty($item['customer_user_id']) && (int)$item['customer_user_id'] !== (int)Auth::id()) return Response::text('This request is already linked to an account.',403);
        if(empty($item['customer_user_id'])) $this->customers->linkRequest((int)$item['id'],(int)Auth::id());
        Session::flash('_success','This request is now linked to your AlbaTech account.');
        return $this->redirect('/account/requests/'.(int)$item['id']);
    }

    private function quoteToken(array $quote): ?string
    {
        if (empty($quote['public_token_encrypted'])) return null;
        return $this->quoteService->decryptToken((string)$quote['public_token_encrypted']);
    }

    private function findRequestByPortalToken(string $token): ?array
    {
        if($token===''||!preg_match('/^[a-f0-9]{48}$/',$token))return null;
        $stmt=\App\Core\Database::connection()->prepare('SELECT * FROM assistance_requests WHERE customer_token_hash=:hash LIMIT 1');
        $stmt->execute(['hash'=>hash('sha256',$token)]);
        return $stmt->fetch()?:null;
    }
}

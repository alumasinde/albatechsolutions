<?php

declare(strict_types=1);
namespace App\Modules\Growth\Controller;
use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Growth\Repository\GrowthAnalyticsRepository;
use App\Modules\Growth\Service\GrowthAnalyticsService;

final class IntelligenceController extends BaseController
{
    public function __construct(private readonly GrowthAnalyticsRepository $analytics,private readonly GrowthAnalyticsService $service){}
    public function dashboard(Request $request):Response{
        $days=max(7,min(365,(int)$request->input('days',30)));$summary=$this->analytics->summary($days);$views=max(1,$summary['page_views']);
        $summary['request_rate']=round($summary['assistance_requests']/$views*100,2);$summary['payment_rate']=round($summary['payments_verified']/$views*100,2);$summary['completion_rate']=round($summary['completed']/max(1,$summary['assistance_requests'])*100,2);
        return $this->view('admin.growth.intelligence',['days'=>$days,'summary'=>$summary,'topPages'=>$this->analytics->topPages($days),'sources'=>$this->analytics->sources($days),'events'=>$this->analytics->eventCounts($days),'services'=>$this->analytics->servicePerformance($days),'assistantIntents'=>$this->analytics->assistantIntentInsights($days),'gaps'=>$this->analytics->contentGaps($days),'notes'=>$this->analytics->openNotes()]);
    }
    public function collect(Request $request):Response{
        $path=trim((string)$request->input('path','/'));if($path===''||strlen($path)>500)return $this->json(['success'=>false],422);
        $this->service->pageView($path,(string)$request->input('title',''),$request->all(),(string)($request->input('referrer','')?:($_SERVER['HTTP_REFERER']??'')));return Response::empty(204,['Cache-Control'=>'no-store']);
    }
    public function event(Request $request):Response{
        $name=trim((string)$request->input('event_name',''));if($name==='')return $this->json(['success'=>false],422);$sid=$request->input('service_id');$eid=$request->input('entity_id');$meta=$request->input('metadata',[]);
        $this->service->event($name,(string)$request->input('path',''),is_numeric($sid)?(int)$sid:null,is_numeric($eid)?(int)$eid:null,is_array($meta)?$meta:[]);return Response::empty(204,['Cache-Control'=>'no-store']);
    }
    public function dismiss(Request $request):Response{$this->analytics->dismissNote((int)$request->param('id'));Session::flash('_success','Growth note dismissed.');return $this->redirect(Config::get('admin.path','/admin').'/growth/intelligence');}
}

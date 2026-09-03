<?php

declare(strict_types=1);

namespace App\Modules\Assistant\Controller;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Helpers\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Assistant\Repository\AssistantRepository;
use App\Modules\Assistant\Service\AssistantService;

final class AssistantController extends BaseController
{
    public function __construct(private readonly AssistantService $assistant, private readonly AssistantRepository $repo) {}

    public function index(Request $request): Response
    {
        return $this->view('public.assistant.index', ['csrf'=>Csrf::token()]);
    }

    public function start(Request $request): Response
    {
        return $this->json($this->assistant->start());
    }

    public function message(Request $request): Response
    {
        $token=trim((string)$request->input('token','')); $message=trim((string)$request->input('message',''));
        if($token===''||$message==='') return $this->json(['success'=>false,'message'=>'Please provide a message.'],422);
        return $this->json($this->assistant->message($token,$message,$request->ip()));
    }

    public function sessions(Request $request): Response
    {
        return $this->view('admin.assistant.sessions',['sessions'=>$this->repo->recentForAdmin()]);
    }

    public function session(Request $request): Response
    {
        $id=(int)$request->param('id');
        $sessions=$this->repo->recentForAdmin(500); $session=null; foreach($sessions as $row) if((int)$row['id']===$id){$session=$row;break;}
        if(!$session)return Response::text('Not found',404);
        return $this->view('admin.assistant.session',['session'=>$session,'messages'=>$this->repo->messages($id),'matches'=>$this->repo->matches($id)]);
    }
}

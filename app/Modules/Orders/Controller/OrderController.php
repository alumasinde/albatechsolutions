<?php

declare(strict_types=1);

namespace App\Modules\Orders\Controller;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Cms\Repository\ServiceRepository;
use App\Modules\Orders\Repository\OrderDocumentRepository;
use App\Modules\Orders\Repository\OrderRepository;
use App\Modules\Orders\Service\OrderDocumentService;
use App\Modules\Orders\Service\OrderService;

final class OrderController extends BaseController
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly OrderRepository $orders,
        private readonly ServiceRepository $services,
        private readonly OrderDocumentService $documentService,
        private readonly OrderDocumentRepository $documents
    ) {
    }

    public function index(Request $request): Response
    {
        return $this->view('orders.index', [
            'orders' => $this->orders->forCustomer((int) Auth::id()),
        ]);
    }

    public function create(Request $request): Response
    {
        $service = $this->services->findBySlug((string) $request->input('service', ''));

        if (!$service) {
            Session::flash('_errors', ['service' => ['Please choose a service to request first.']]);

            return $this->redirect('/services');
        }

        return $this->view('orders.create', ['service' => $service]);
    }

    public function store(Request $request): Response
    {
        $service = $this->services->findBySlug((string) $request->input('service_slug', ''));

        if (!$service) {
            Session::flash('_errors', ['service' => ['That service could not be found.']]);

            return $this->back();
        }

        $notes = trim((string) $request->input('customer_notes', ''));

        if ($notes === '') {
            Session::flash('_errors', ['customer_notes' => ['Please describe what you need.']]);

            return $this->back();
        }

        $result = $this->orderService->create((int) Auth::id(), (int) $service['id'], $notes);

        $orderId = $result['id'];

        $file = $request->file('document');
        if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $this->documentService->upload($orderId, $file, (int) Auth::id());
        }

        Session::flash('_success', "Order {$result['order_number']} submitted — we'll review it shortly.");

        return $this->redirect('/orders/' . $orderId);
    }

    public function show(Request $request): Response
    {
        $orderId = (int) $request->param('id');
        $order = $this->orders->findWithDetails($orderId);

        if (!$order || !$this->canView($order)) {
            return Response::text('Not found', 404);
        }

        return $this->view('orders.show', [
            'order' => $order,
            'history' => $this->orders->statusHistory($orderId),
            'documents' => $this->documents->forOrder($orderId),
        ]);
    }

    public function uploadDocument(Request $request): Response
    {
        $orderId = (int) $request->param('id');
        $order = $this->orders->find($orderId);

        if (!$order || !$this->canView($order)) {
            return Response::text('Not found', 404);
        }

        $file = $request->file('document');

        if ($file) {
            $result = $this->documentService->upload($orderId, $file, (int) Auth::id());

            if (!$result['success']) {
                Session::flash('_errors', ['document' => [$result['message']]]);
            }
        }

        return $this->redirect('/orders/' . $orderId);
    }

    public function accept(Request $request): Response
    {
        return $this->customerTransition($request, 'accepted', 'Customer accepted the quote.');
    }

    public function decline(Request $request): Response
    {
        return $this->customerTransition($request, 'declined', 'Customer declined the quote.');
    }

    private function customerTransition(Request $request, string $status, string $note): Response
    {
        $orderId = (int) $request->param('id');
        $order = $this->orders->find($orderId);

        if (!$order || !$this->canView($order)) {
            return Response::text('Not found', 404);
        }

        $result = $this->orderService->transition($orderId, $status, (int) Auth::id(), $note);

        if (!$result['success']) {
            Session::flash('_errors', ['status' => [$result['message']]]);
        }

        return $this->redirect('/orders/' . $orderId);
    }

    public function downloadDocument(Request $request): Response
    {
        $orderId = (int) $request->param('id');
        $docId = (int) $request->param('docId');

        $order = $this->orders->find($orderId);

        if (!$order || !$this->canView($order)) {
            return Response::text('Not found', 404);
        }

        $document = $this->documents->find($docId);

        if (!$document || (int) $document['order_id'] !== $orderId || !file_exists($document['disk_path'])) {
            return Response::text('Not found', 404);
        }

        header('Content-Type: ' . $document['mime_type']);
        header('Content-Disposition: attachment; filename="' . basename($document['original_name']) . '"');
        header('Content-Length: ' . filesize($document['disk_path']));
        readfile($document['disk_path']);
        exit;
    }

    private function canView(array $order): bool
    {
        return (int) $order['user_id'] === (int) Auth::id() || Auth::can('orders.view');
    }
}

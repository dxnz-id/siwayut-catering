<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\{Request, Response, Session, Validator, Database, Turnstile, Logger};
use App\Exceptions\NotFoundException;
use App\Services\OrderService;
use App\Services\MenuService;
use App\Services\EventService;
use App\Models\Customer;

class OrderController extends BaseController
{
    public function __construct(
        private OrderService $orderService,
        private MenuService $menuService,
        private EventService $eventService,
        private Customer $customer
    ) {
        parent::__construct();
    }

    public function myOrders(Request $request): void
    {
        $user = Session::get('user');
        if (!$user) {
            $this->redirect('/auth');
            return;
        }

        $customer = $this->customer->findByUserId((int) $user['id']);
        $orders = $customer ? $this->orderService->getOrdersByCustomerId((int) $customer['id']) : [];

        $events = $this->eventService->getActive();
        $eventMap = [];
        foreach ($events as $ev) {
            $eventMap[$ev['id']] = $ev['name'];
        }

        $this->render('order/my-orders', [
            'title' => __('my_orders') . ' — Siwayut Catering',
            'orders' => $orders,
            'eventMap' => $eventMap,
        ], '');
    }

    public function publicForm(Request $request): void
    {
        $menus = $this->menuService->all();
        $activeMenus = array_filter($menus, fn($m) => ($m['status'] ?? 'active') === 'active');
        $events = $this->eventService->getActive();

        $this->render('order/public-form', [
            'title' => __('order_catering') . ' — Siwayut Catering',
            'menus' => $activeMenus,
            'events' => $events,
        ], '');
    }

    public function publicSubmit(Request $request): void
    {
        $data = $request->only(['name', 'event_date', 'address', 'notes']);
        $items = $request->input('items', []);



        $validator = new Validator();
        $validator->validate($data, [
            'name' => 'required|min:3|max:255',
            'event_date' => 'required',
            'address' => 'required|min:10',
        ]);

        if ($validator->fails()) {
            $this->withOldInput($data);
            $errors = $validator->errors();
            $firstError = reset($errors);
            Session::flash('error', $firstError);
            $this->redirect('/order-form');
        }

        if (empty($items) || !is_array($items)) {
            $this->withOldInput($data);
            Session::flash('error', __('select_menu_item'));
            $this->redirect('/order-form');
        }

        // Build WhatsApp message
        $message = __('whatsapp_intro') . "\n\n"
            . __('whatsapp_name') . ": {$data['name']}\n"
            . __('whatsapp_event_date') . ": {$data['event_date']}\n"
            . __('whatsapp_address') . ": {$data['address']}\n"
            . __('whatsapp_menu_items') . ":\n";

        foreach ($items as $item) {
            $menuId = (int) ($item['menu_id'] ?? 0);
            $menu = $this->menuService->find($menuId);
            $menuName = $menu['name'] ?? __('unknown');
            $qty = (int) ($item['quantity'] ?? 1);
            $message .= "- {$menuName}: {$qty} " . __('whatsapp_portions') . "\n";
        }

        if (!empty($data['notes'])) {
            $message .= "\n" . __('whatsapp_notes') . ": {$data['notes']}\n";
        }

        $message .= "\n" . __('whatsapp_thank_you');

        $this->redirect('https://wa.me/6287865252313?text=' . urlencode($message));
    }

    public function trackForm(Request $request): void
    {
        $this->render('order/track', [
            'title' => __('track_order_title') . ' — Siwayut Catering',
        ], '');
    }

    public function track(Request $request): void
    {
        $orderNumber = $request->input('order_number');
        $phone = $request->input('phone');

        if (!Turnstile::verify($request->input('cf-turnstile-response', ''))) {
            $this->withOldInput(['order_number' => $orderNumber, 'phone' => $phone]);
            Session::flash('error', __('captcha_failed'));
            $this->redirect('/track-order');
        }

        $validator = new Validator();
        $validator->validate(['order_number' => $orderNumber, 'phone' => $phone], [
            'order_number' => 'required',
            'phone' => 'required|min:10|max:20',
        ]);

        if ($validator->fails()) {
            $this->withOldInput(['order_number' => $orderNumber, 'phone' => $phone]);
            $errors = $validator->errors();
            $firstError = reset($errors);
            Session::flash('error', $firstError);
            $this->redirect('/track-order');
        }

        $order = $this->orderService->findByOrderNumber($orderNumber);
        if (!$order) {
            $this->withOldInput(['order_number' => $orderNumber, 'phone' => $phone]);
            Session::flash('error', __('order_not_found_check'));
            $this->redirect('/track-order');
        }

        $customer = $this->customer->find((int) $order['customer_id']);
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $cleanCustomerPhone = preg_replace('/[^0-9]/', '', $customer['phone'] ?? '');

        if ($cleanCustomerPhone !== $cleanPhone) {
            $this->withOldInput(['order_number' => $orderNumber, 'phone' => $phone]);
            Session::flash('error', __('phone_mismatch'));
            $this->redirect('/track-order');
        }

        $verified = Session::get('track_verified', []);
        $verified[] = $orderNumber;
        Session::set('track_verified', $verified);

        $this->redirect('/track-order/' . urlencode($orderNumber));
    }

    public function trackResult(Request $request): void
    {
        $orderNumber = $request->param('id');

        $order = $this->orderService->findByOrderNumber($orderNumber);
        if (!$order) {
            $this->redirect('/track-order');
        }

        $customer = $this->customer->find((int) $order['customer_id']);

        $user = Session::get('user');
        $isOwner = $user && $customer && !empty($customer['user_id']) && (int) $customer['user_id'] === (int) $user['id'];
        $verifiedOrders = Session::get('track_verified', []);
        $isVerified = in_array($orderNumber, $verifiedOrders, true);

        if (!$isOwner && !$isVerified) {
            $this->redirect('/track-order');
        }

        $items = $this->orderService->getItems((int) $order['id']);
        $event = $this->eventService->find((int) $order['event_id']);

        $this->render('order/track-result', [
            'title' => __('order_details') . ' ' . e($orderNumber) . ' — Siwayut Catering',
            'order' => $order,
            'customer' => $customer,
            'items' => $items,
            'event' => $event,
        ], '');
    }

    public function index(Request $request): void
    {
        $page = (int) $request->input('page', 1);
        $search = $request->input('search', '');
        $orderBy = $request->input('sort_by', 'created_at');
        $direction = $request->input('dir', 'DESC');
        $filters = [
            'status' => $request->input('status', ''),
            'payment_status' => $request->input('payment_status', ''),
        ];
        $result = $this->orderService->paginate($page, 10, $search, $filters, $orderBy, $direction);

        $menus = $this->menuService->paginate(1, 1000)['data'];
        $events = $this->eventService->getActive();

        $this->render('order/index', [
            'title' => __('orders'),
            'orders' => $result['data'],
            'pagination' => $result,
            'menus' => $menus,
            'events' => $events,
            'search' => $search,
            'filters' => $filters,
            'sort_by' => $orderBy,
            'dir' => $direction,
        ]);
    }

    public function create(Request $request): void
    {
        $menus = $this->menuService->paginate(1, 100)['data'];
        $events = $this->eventService->getActive();

        $this->render('order/create', [
            'title' => __('create_order'),
            'menus' => $menus,
            'events' => $events,
        ]);
    }

    public function store(Request $request): void
    {
        $data = $request->only(['phone', 'customer_name', 'delivery_address', 'event_id', 'event_date', 'notes']);
        $items = $request->input('items', []);

        $validator = new Validator(Database::getInstance());
        $validator->validate($data, [
            'phone' => 'required|min:10|max:20',
            'customer_name' => 'required|min:3|max:255',
            'delivery_address' => 'required|min:10',
            'event_id' => 'required|numeric',
            'event_date' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->isAjax()) {
                Response::jsonError(__('validation_failed'), $validator->errors());
            }
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect('/orders/create');
        }

        if (empty($items) || !is_array($items)) {
            if ($request->isAjax()) {
                Response::jsonError(__('select_menu_item'));
            }
            $this->withOldInput($data);
            Session::flash('error', __('select_menu_item'));
            $this->redirect('/orders/create');
        }

        try {
            $this->orderService->createOrder($data, $items);
            if ($request->isAjax()) {
                Response::jsonSuccess(null, __('order_created'));
            }
            $this->redirectWithFlash('/orders', 'success', __('order_created'));
        } catch (\Exception $e) {
            Logger::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $errorMsg = APP_DEBUG ? $e->getMessage() : __('operation_failed');
            if ($request->isAjax()) {
                Response::jsonError($errorMsg);
            }
            $this->withOldInput($data);
            Session::flash('error', $errorMsg);
            $this->redirect('/orders/create');
        }
    }

    private function resolveOrder(string $id): ?array
    {
        if (is_numeric($id)) {
            $order = $this->orderService->find((int) $id);
        } else {
            $order = $this->orderService->findByOrderNumber($id);
        }
        return $order;
    }

    public function show(Request $request): void
    {
        $id = $request->param('id');
        $order = $this->resolveOrder($id);

        if (!$order) {
            throw new NotFoundException(__('order_not_found'));
        }

        $customer = $this->customer->find((int) $order['customer_id']);
        $items = $this->orderService->getItems((int) $order['id']);

        $this->render('order/show', [
            'title' => __('order') . ' ' . $order['order_number'],
            'order' => $order,
            'customer' => $customer,
            'items' => $items,
        ]);
    }

    public function update(Request $request): void
    {
        $id = $request->param('id');
        $data = $request->only(['status', 'payment_status']);

        $order = $this->resolveOrder($id);
        if (!$order) {
            if ($request->isAjax())
                Response::jsonError(__('order_not_found_short'));
            Session::flash('error', __('order_not_found_short'));
            $this->redirect('/orders');
        }

        $validator = new Validator();
        $validator->validate($data, [
            'status' => 'required|in:pending,processing,delivering,completed,cancelled',
            'payment_status' => 'required|in:unpaid,paid,refunded',
        ]);

        if ($validator->fails()) {
            if ($request->isAjax())
                Response::jsonError(__('validation_failed'), $validator->errors());
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect('/orders/' . $order['order_number']);
        }

        try {
            $this->orderService->updateStatus((int) $order['id'], $data['status'], $data['payment_status']);
            if ($request->isAjax())
                Response::jsonSuccess(null, __('order_updated'));
            $this->redirectWithFlash('/orders', 'success', __('order_update_success'));
        } catch (\Exception $e) {
            Logger::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $errorMsg = APP_DEBUG ? $e->getMessage() : __('operation_failed');
            if ($request->isAjax())
                Response::jsonError(__('failed_update_order', ['error' => $errorMsg]));
            Session::flash('error', __('failed_update_order', ['error' => $errorMsg]));
            $this->redirect("/orders/{$order['order_number']}");
        }
    }
}

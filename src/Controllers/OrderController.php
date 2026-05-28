<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\{Request, Session, Validator, Database, Turnstile};
use App\Exceptions\NotFoundException;
use App\Services\OrderService;
use App\Services\MenuService;
use App\Services\EventService;
use App\Models\Customer;

class OrderController extends BaseController {
    public function __construct(
        private OrderService $orderService,
        private MenuService $menuService,
        private EventService $eventService,
        private Customer $customer
    ) {
        parent::__construct();
    }

    public function publicForm(Request $request): void {
        $menus = $this->menuService->all();
        $activeMenus = array_filter($menus, fn($m) => ($m['status'] ?? 'active') === 'active');
        $events = $this->eventService->getActive();

        $this->render('order/public-form', [
            'title' => 'Order Catering — Siwayut Catering',
            'menus' => $activeMenus,
            'events' => $events,
        ], '');
    }

    public function publicSubmit(Request $request): void {
        $data = $request->only(['name', 'menu', 'event_date', 'quantity', 'address', 'notes']);

        if (!Turnstile::verify($request->input('cf-turnstile-response', ''))) {
            $this->withOldInput($data);
            Session::flash('error', 'Captcha verification failed.');
            $this->redirect('/order-form');
        }

        $validator = new Validator();
        $validator->validate($data, [
            'name' => 'required|min:3|max:255',
            'menu' => 'required',
            'event_date' => 'required',
            'quantity' => 'required|numeric|min:1',
            'address' => 'required|min:10',
        ]);

        if ($validator->fails()) {
            $this->withOldInput($data);
            $firstError = reset($validator->errors());
            Session::flash('error', $firstError);
            $this->redirect('/order-form');
        }

        $message = "Hello Siwayut Catering, I would like to order:\n\n"
                 . "Name: {$data['name']}\n"
                 . "Menu: {$data['menu']}\n"
                 . "Event Date: {$data['event_date']}\n"
                 . "Portions: {$data['quantity']}\n"
                 . "Delivery Address: {$data['address']}\n";

        if (!empty($data['notes'])) {
            $message .= "Notes: {$data['notes']}\n";
        }

        $message .= "\nThank you.";

        $this->redirect('https://wa.me/6287865252313?text=' . urlencode($message));
    }

    public function trackForm(Request $request): void {
        $this->render('order/track', [
            'title' => 'Track Order — Siwayut Catering'
        ], '');
    }

    public function track(Request $request): void {
        $orderId = $request->input('order_id');
        $phone = $request->input('phone');

        if (!Turnstile::verify($request->input('cf-turnstile-response', ''))) {
            $this->withOldInput(['order_id' => $orderId, 'phone' => $phone]);
            Session::flash('error', 'Captcha verification failed.');
            $this->redirect('/track-order');
        }

        $validator = new Validator();
        $validator->validate(['order_id' => $orderId, 'phone' => $phone], [
            'order_id' => 'required|numeric',
            'phone' => 'required|min:10|max:20',
        ]);

        if ($validator->fails()) {
            $this->withOldInput(['order_id' => $orderId, 'phone' => $phone]);
            $firstError = reset($validator->errors());
            Session::flash('error', $firstError);
            $this->redirect('/track-order');
        }

        $order = $this->orderService->find((int)$orderId);
        if (!$order) {
            $this->withOldInput(['order_id' => $orderId, 'phone' => $phone]);
            Session::flash('error', 'Order not found. Please check your order number.');
            $this->redirect('/track-order');
        }

        $customer = $this->customer->find((int)$order['customer_id']);
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $cleanCustomerPhone = preg_replace('/[^0-9]/', '', $customer['phone'] ?? '');

        if ($cleanCustomerPhone !== $cleanPhone) {
            $this->withOldInput(['order_id' => $orderId, 'phone' => $phone]);
            Session::flash('error', 'Phone number does not match the order.');
            $this->redirect('/track-order');
        }

        $this->redirect('/track-order/' . $orderId . '?phone=' . urlencode($phone));
    }

    public function trackResult(Request $request): void {
        $orderId = (int) $request->param('id');
        $phone = $request->input('phone');

        $order = $this->orderService->find($orderId);
        if (!$order) {
            $this->redirect('/track-order');
        }

        $customer = $this->customer->find((int)$order['customer_id']);
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $cleanCustomerPhone = preg_replace('/[^0-9]/', '', $customer['phone'] ?? '');
        if ($cleanCustomerPhone !== $cleanPhone) {
            $this->redirect('/track-order');
        }

        $menu = $this->menuService->find((int)$order['menu_id']);
        $event = $this->eventService->find((int)$order['event_id']);

        $this->render('order/track-result', [
            'title' => 'Order Detail #' . $orderId . ' — Siwayut Catering',
            'order' => $order,
            'customer' => $customer,
            'menu' => $menu,
            'event' => $event,
        ], '');
    }

    public function index(Request $request): void {
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
        $menuMap = [];
        foreach ($menus as $m) $menuMap[$m['id']] = $m['name'];

        $customers = $this->customer->all();
        $customerMap = [];
        foreach ($customers as $c) $customerMap[$c['id']] = [
            'name' => $c['name'],
            'phone' => $c['phone']
        ];

        $this->render('order/index', [
            'title' => 'Order List',
            'orders' => $result['data'],
            'pagination' => $result,
            'menuMap' => $menuMap,
            'customerMap' => $customerMap,
            'search' => $search,
            'filters' => $filters,
            'sort_by' => $orderBy,
            'dir' => $direction,
        ]);
    }

    public function create(Request $request): void {
        $menus = $this->menuService->paginate(1, 100)['data'];
        $events = $this->eventService->getActive();

        $this->render('order/create', [
            'title' => 'Create New Order',
            'menus' => $menus,
            'events' => $events,
        ]);
    }

    public function store(Request $request): void {
        $data = $request->only(['phone', 'customer_name', 'delivery_address', 'event_id', 'menu_id', 'quantity', 'event_date', 'notes']);

        $validator = new Validator(Database::getInstance());
        $validator->validate($data, [
            'phone' => 'required|min:10|max:20',
            'customer_name' => 'required|min:3|max:255',
            'delivery_address' => 'required|min:10',
            'event_id' => 'required|numeric',
            'menu_id' => 'required|numeric',
            'quantity' => 'required|numeric',
            'event_date' => 'required',
        ]);

        if ($validator->fails()) {
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect('/orders/create');
        }

        try {
            $this->orderService->createOrder($data);
            $this->redirectWithFlash('/orders', 'success', 'Order successfully created.');
        } catch (\Exception $e) {
            $this->withOldInput($data);
            Session::flash('error', $e->getMessage());
            $this->redirect('/orders/create');
        }
    }

    public function edit(Request $request): void {
        $id = (int) $request->param('id');
        $order = $this->orderService->find($id);

        if (!$order) {
            throw new NotFoundException('Order not found.');
        }

        $customerRow = $this->customer->find((int)$order['customer_id']);
        $menuRow = $this->menuService->find((int)$order['menu_id']);

        $this->render('order/edit', [
            'title' => 'Update Order Status',
            'order' => $order,
            'customer' => $customerRow,
            'menu' => $menuRow,
        ]);
    }

    public function update(Request $request): void {
        $id = (int) $request->param('id');
        $data = $request->only(['status', 'payment_status']);

        $validator = new Validator();
        $validator->validate($data, [
            'status' => 'required|in:pending,processing,delivering,completed,cancelled',
            'payment_status' => 'required|in:unpaid,paid,refunded',
        ]);

        if ($validator->fails()) {
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect("/orders/{$id}/edit");
        }

        $this->orderService->updateStatus($id, $data['status'], $data['payment_status']);
        $this->redirectWithFlash('/orders', 'success', 'Order status successfully updated.');
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\{Request, Response, Session, Validator, Database, Turnstile, Logger};
use App\Exceptions\NotFoundException;
use App\Services\OrderService;
use App\Services\MenuService;
use App\Models\Customer;

class OrderController extends BaseController
{
    public function __construct(
        private OrderService $orderService,
        private MenuService $menuService,
        private Customer $customer
    ) {
        parent::__construct();
    }

    // ============================================================
    // PUBLIC: Customer-facing routes
    // ============================================================

    public function myOrders(Request $request): void
    {
        $user = Session::get('user');
        if (!$user) {
            $this->redirect('/auth');
            return;
        }

        $customer = $this->customer->findByUserId((int) $user['id']);
        $orders = $customer ? $this->orderService->getOrdersByCustomerId((int) $customer['id']) : [];

        $this->render('order/my-orders', [
            'title' => __('my_orders') . ' — Siwayut Catering',
            'orders' => $orders,
            'navMode' => 'back_logout',
        ], 'public');
    }

    public function publicForm(Request $request): void
    {
        $menus = $this->menuService->all();
        $activeMenus = array_filter($menus, fn($m) => ($m['status'] ?? 'active') === 'active');

        $this->render('order/public-form', [
            'title' => __('order_catering') . ' — Siwayut Catering',
            'menus' => $activeMenus,
            'navMode' => 'back_home',
        ], 'public');
    }

    public function publicSubmit(Request $request): void
    {
        $data = $request->only(['name', 'event_date', 'event_time', 'occasion', 'occasion_custom', 'address', 'notes']);
        $items = $request->input('items', []);

        // Keep raw date/time for form redisplay
        $rawDate = $data['event_date'];
        $rawTime = $data['event_time'] ?? '';

        $data['occasion'] = ($data['occasion'] ?? '') === '__other__' ? trim($data['occasion_custom'] ?? '') : ($data['occasion'] ?? '');
        $data['event_date'] .= ' ' . ($data['event_time'] ?: '12:00') . ':00';

        $validator = new Validator();
        $validator->validate($data, [
            'name' => 'required|min:3|max:255',
            'event_date' => 'required|after_or_equal:today',
            'address' => 'required|min:10',
            'occasion' => 'required',
        ]);

        if ($validator->fails()) {
            $this->withOldInput(array_merge($data, ['event_date' => $rawDate, 'event_time' => $rawTime]));
            $errors = $validator->errors();
            $firstError = reset($errors);
            Session::flash('error', $firstError);
            $this->redirect('/order-form');
        }

        if (empty($items) || !is_array($items)) {
            $this->withOldInput(array_merge($data, ['event_date' => $rawDate, 'event_time' => $rawTime]));
            Session::flash('error', __('select_menu_item'));
            $this->redirect('/order-form');
        }

        $occasion = $data['occasion'];
        $displayDate = date('d/m/Y', strtotime($data['event_date']));
        if (!empty($data['event_time'])) {
            $displayDate = date('d/m/Y H:i', strtotime($data['event_date']));
        }

        // Build WhatsApp message
        $message = __('whatsapp_intro') . "\n\n"
            . __('whatsapp_name') . ": {$data['name']}\n"
            . __('whatsapp_event_date') . ": {$displayDate}\n"
            . __('whatsapp_occasion') . ": {$occasion}\n"
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
            'navMode' => 'back',
        ], 'public');
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

        $this->render('order/track-result', [
            'title' => __('order_details') . ' ' . e($orderNumber) . ' — Siwayut Catering',
            'order' => $order,
            'customer' => $customer,
            'items' => $items,
            'navMode' => 'track_another',
        ], 'public');
    }

    // ============================================================
    // ADMIN: Dashboard routes (requires auth + role:admin)
    // ============================================================

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

        $this->render('order/index', [
            'title' => __('orders'),
            'orders' => $result['data'],
            'pagination' => $result,
            'menus' => $menus,
            'search' => $search,
            'filters' => $filters,
            'sort_by' => $orderBy,
            'dir' => $direction,
        ]);
    }

    public function exportCsv(Request $request): void
    {
        $search = $request->input('search', '');
        $orderBy = $request->input('sort_by', 'created_at');
        $direction = $request->input('dir', 'DESC');
        $filters = [
            'status' => $request->input('status', ''),
            'payment_status' => $request->input('payment_status', ''),
        ];

        $orders = $this->orderService->getAllForExport($search, $filters, $orderBy, $direction);

        ob_clean();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="orders-export.csv"');

        $out = fopen('php://output', 'w');

        fputcsv($out, [__('orders')], escape: "\\");
        fputcsv($out, [], escape: "\\");

        fputcsv($out, [
            __('order_no'), __('customer'), __('phone'), __('items'), __('occasion'),
            __('total_price'), __('status'), __('payment'), __('payment_method'),
            __('event_date'), __('delivery_address'), __('created_at'),
        ], escape: "\\");

        foreach ($orders as $row) {
            fputcsv($out, [
                $row['order_number'] ?? '',
                $row['customer_name'] ?? '',
                $row['customer_phone'] ?? '',
                (int) ($row['item_cnt'] ?? 0),
                $row['occasion'] ?? '',
                number_format((float) ($row['total_price'] ?? 0), 0, ',', '.'),
                $row['status'] ?? '',
                $row['payment_status'] ?? '',
                $row['payment_method'] ?? '',
                $row['event_date'] ?? '',
                $row['delivery_address'] ?? '',
                $row['created_at'] ?? '',
            ], escape: "\\");
        }

        fclose($out);
        exit;
    }

    public function store(Request $request): void
    {
        $data = $request->only(['phone', 'customer_name', 'delivery_address', 'event_date', 'event_time', 'occasion', 'occasion_custom', 'notes']);
        $items = $request->input('items', []);

        $data['occasion'] = $data['occasion'] === '__other__' ? trim($data['occasion_custom'] ?? '') : ($data['occasion'] ?? '');
        $data['event_date'] .= ' ' . ($data['event_time'] ?: '12:00') . ':00';

        $validator = new Validator(Database::getInstance());
        $validator->validate($data, [
            'phone' => 'required|min:10|max:20',
            'customer_name' => 'required|min:3|max:255',
            'delivery_address' => 'required|min:10',
            'event_date' => 'required|after_or_equal:today',
            'occasion' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->isAjax()) {
                Response::jsonError(__('validation_failed'), $validator->errors());
            }
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect('/orders');
        }

        if (empty($items) || !is_array($items)) {
            if ($request->isAjax()) {
                Response::jsonError(__('select_menu_item'));
            }
            $this->withOldInput($data);
            Session::flash('error', __('select_menu_item'));
            $this->redirect('/orders');
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
            $this->redirect('/orders');
        }
    }

    // ============================================================
    // PRIVATE HELPERS
    // ============================================================

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
        $orderNumber = $request->param('order_number');
        $order = $this->resolveOrder($orderNumber);

        if (!$order) {
            throw new NotFoundException(__('order_not_found'));
        }

        $customer = $this->customer->find((int) $order['customer_id']);
        $items = $this->orderService->getItems((int) $order['id']);
        $menus = $this->menuService->all();

        $this->render('order/show', [
            'title' => __('order') . ' ' . $order['order_number'],
            'order' => $order,
            'customer' => $customer,
            'items' => $items,
            'menus' => $menus,
            'canEditCustomerName' => empty($customer['user_id']),
        ]);
    }

    public function receipt(Request $request): void
    {
        $orderNumber = $request->param('order_number');
        $order = $this->resolveOrder($orderNumber);

        if (!$order) {
            throw new NotFoundException(__('order_not_found'));
        }

        $customer = $this->customer->find((int) $order['customer_id']);
        $items = $this->orderService->getItems((int) $order['id']);

        $this->render('order/receipt', [
            'title' => __('receipt') . ' — ' . $order['order_number'],
            'order' => $order,
            'customer' => $customer,
            'items' => $items,
        ], '');
    }

    public function update(Request $request): void
    {
        $orderNumber = $request->param('order_number');
        $data = $request->only(['customer_name', 'delivery_address', 'event_date', 'event_time', 'occasion', 'occasion_custom', 'notes', 'status', 'payment_status', 'tax_rate', 'discount_type', 'discount_value', 'payment_method', 'down_payment', 'down_payment_due']);

        $order = $this->resolveOrder($orderNumber);
        if (!$order) {
            if ($request->isAjax())
                Response::jsonError(__('order_not_found_short'));
            Session::flash('error', __('order_not_found_short'));
            $this->redirect('/orders');
        }

        // Don't allow changing customer name if they have an account
        $customer = $this->customer->find((int) $order['customer_id']);
        if ($customer && !empty($customer['user_id'])) {
            $data['customer_name'] = $customer['name'];
        }

        $data['occasion'] = ($data['occasion'] ?? '') === '__other__' ? trim($data['occasion_custom'] ?? '') : ($data['occasion'] ?? '');
        $data['event_date'] .= ' ' . ($data['event_time'] ?: '12:00') . ':00';

        $validator = new Validator();
        $validator->validate($data, [
            'customer_name' => 'required|min:3|max:255',
            'delivery_address' => 'required|min:10',
            'event_date' => 'required|after_or_equal:today',
            'occasion' => 'required',
            'status' => 'required|in:pending,processing,delivering,completed,cancelled',
            'payment_status' => 'required|in:unpaid,paid,refunded',
            'tax_rate' => 'numeric|min:0|max:100',
            'discount_type' => 'in:none,percentage,fixed',
            'discount_value' => 'numeric|min:0',
            'payment_method' => 'in:cash,transfer,qris,other',
            'down_payment' => 'numeric|min:0',
            'down_payment_due' => 'date',
        ]);

        if ($validator->fails()) {
            if ($request->isAjax())
                Response::jsonError(__('validation_failed'), $validator->errors());
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect('/orders/' . $order['order_number']);
        }

        // Handle items update from edit modal
        $items = $request->input('items', []);
        if (!empty($items) && is_array($items)) {
            $filteredItems = [];
            foreach ($items as $item) {
                $menuId = (int)($item['menu_id'] ?? 0);
                $qty = (int)($item['quantity'] ?? 0);
                if ($menuId > 0 && $qty > 0) {
                    $filteredItems[] = ['menu_id' => $menuId, 'quantity' => $qty];
                }
            }
            if (!empty($filteredItems)) {
                $data['items'] = $filteredItems;
            }
        }

        try {
            $this->orderService->updateOrder((int) $order['id'], $data);
            if ($request->isAjax())
                Response::jsonSuccess(null, __('order_updated'));
            $this->redirectWithFlash('/orders/' . $order['order_number'], 'success', __('order_update_success'));
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

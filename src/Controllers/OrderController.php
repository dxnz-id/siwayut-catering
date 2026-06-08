<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\{Request, Response, Session, Validator, Database, Turnstile, Logger};
use App\Exceptions\NotFoundException;
use App\Services\OrderService;
use App\Services\MenuService;
use App\Services\CategoryService;
use App\Services\EventService;
use App\Services\CartService;
use App\Models\Customer;

class OrderController extends BaseController
{
    public function __construct(
        private OrderService $orderService,
        private MenuService $menuService,
        private Customer $customer,
        private CategoryService $categoryService,
        private EventService $eventService,
        private CartService $cartService
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

    public function publicMenu(Request $request): void
    {
        $categories = $this->categoryService->all();
        $initial = $this->menuService->paginate(1, 9, ['status' => 'active']);

        $events = $this->eventService->getActive();
        $eventMap = [];
        foreach ($events as $ev) {
            $eventMap[$ev['id']] = $ev['name'];
        }
        $categoryMap = [];
        foreach ($categories as $cat) {
            $categoryMap[$cat['id']] = $cat['name'];
        }

        foreach ($initial['data'] as &$m) {
            $m['event_name'] = $eventMap[$m['event_id']] ?? null;
            $m['category_name'] = $categoryMap[$m['category_id']] ?? null;
        }
        unset($m);

        $cartCount = $this->cartService->count();

        $this->render('order/menu', [
            'title' => __('choose_menu') . ' — Siwayut Catering',
            'categories' => $categories,
            'initialMenus' => $initial['data'],
            'totalMenus' => $initial['total'],
            'perPage' => $initial['per_page'],
            'currentPage' => $initial['current_page'],
            'lastPage' => $initial['last_page'],
            'cartCount' => $cartCount,
            'navMode' => 'back',
        ], 'public');
    }

    public function cartAdd(Request $request): void
    {
        $menuId = (int) $request->input('menu_id');
        $qty = max(1, (int) $request->input('quantity', 1));

        $menu = $this->menuService->find($menuId);
        if (!$menu) {
            if ($request->isAjax()) {
                Response::jsonError('Menu not found');
            }
            Session::flash('error', __('menu_not_found'));
            $this->redirect('/menu');
            return;
        }

        if ($qty < (int) $menu['minimum_portions']) {
            if ($request->isAjax()) {
                Response::jsonError('Minimum ' . (int) $menu['minimum_portions'] . ' portions');
            }
            Session::flash('error', __('min_portion') . ': ' . (int) $menu['minimum_portions']);
            $this->redirect('/menu');
            return;
        }

        $this->cartService->add($menuId, $qty);

        $count = $this->cartService->count();
        $total = $this->cartService->getTotal();

        if ($request->isAjax()) {
            Response::jsonSuccess([
                'count' => $count,
                'total' => $total,
                'message' => __('added_to_cart'),
            ]);
            return;
        }

        Session::flash('success', __('added_to_cart'));
        $this->redirect('/menu');
    }

    public function cartShow(Request $request): void
    {
        $items = $this->cartService->getItems();
        $cartCount = $this->cartService->count();
        $total = $this->cartService->getTotal();

        $this->render('order/cart', [
            'title' => __('cart') . ' — Siwayut Catering',
            'items' => $items,
            'cartCount' => $cartCount,
            'total' => $total,
            'navMode' => 'back',
            'hideFooter' => true,
        ], 'public');
    }

    public function cartUpdate(Request $request): void
    {
        if (!$request->isAjax()) {
            $this->redirect('/cart');
            return;
        }

        $menuId = (int) $request->input('menu_id');
        $qty = max(0, (int) $request->input('quantity', 0));
        $menu = $this->menuService->find($menuId);

        if (!$menu) {
            Response::jsonError('Menu not found');
            return;
        }

        if ($qty > 0 && $qty < (int) $menu['minimum_portions']) {
            Response::jsonError('Minimum ' . (int) $menu['minimum_portions'] . ' portions');
            return;
        }

        $this->cartService->set($menuId, $qty);

        Response::jsonSuccess([
            'count' => $this->cartService->count(),
            'total' => $this->cartService->getTotal(),
            'subtotal' => $qty > 0 ? (float) $menu['price'] * $qty : 0,
        ]);
    }

    public function cartRemove(Request $request): void
    {
        if (!$request->isAjax()) {
            $this->redirect('/cart');
            return;
        }

        $menuId = (int) $request->input('menu_id');
        $this->cartService->remove($menuId);

        Response::jsonSuccess([
            'count' => $this->cartService->count(),
            'total' => $this->cartService->getTotal(),
        ]);
    }

    public function cartRemoveSelected(Request $request): void
    {
        if (!$request->isAjax()) {
            $this->redirect('/cart');
            return;
        }

        $menuIds = $request->input('menu_ids', []);
        if (empty($menuIds) || !is_array($menuIds)) {
            Response::jsonError('No items selected');
            return;
        }

        foreach ($menuIds as $id) {
            $this->cartService->remove((int) $id);
        }

        Response::jsonSuccess([
            'count' => $this->cartService->count(),
            'total' => $this->cartService->getTotal(),
        ]);
    }

    public function checkoutShow(Request $request): void
    {
        $items = $this->cartService->getItems();
        if (empty($items)) {
            $this->redirect('/cart');
            return;
        }

        $total = $this->cartService->getTotal();
        $cartCount = $this->cartService->count();

        $this->render('order/checkout', [
            'title' => __('checkout') . ' — Siwayut Catering',
            'items' => $items,
            'total' => $total,
            'cartCount' => $cartCount,
            'navMode' => 'back',
            'hideFooter' => true,
        ], 'public');
    }

    public function checkoutSubmit(Request $request): void
    {
        $data = $request->only(['name', 'phone', 'event_date', 'event_time', 'occasion', 'occasion_custom', 'address', 'notes']);

        $rawDate = $data['event_date'];
        $rawTime = $data['event_time'] ?? '';

        $data['occasion'] = ($data['occasion'] ?? '') === '__other__' ? trim($data['occasion_custom'] ?? '') : ($data['occasion'] ?? '');

        $validator = new Validator();
        $validator->validate($data, [
            'name' => 'required|min:3|max:255',
            'phone' => 'required|min:10|max:20',
            'event_date' => 'required|after_or_equal:today',
            'address' => 'required|min:10',
            'occasion' => 'required',
        ]);

        if ($validator->fails()) {
            $this->withOldInput(array_merge($data, ['event_date' => $rawDate, 'event_time' => $rawTime]));
            $errors = $validator->errors();
            $firstError = reset($errors);
            Session::flash('error', $firstError);
            $this->redirect('/checkout');
        }

        $items = $this->cartService->getItems();
        if (empty($items)) {
            $this->withOldInput(array_merge($data, ['event_date' => $rawDate, 'event_time' => $rawTime]));
            Session::flash('error', __('select_menu_item'));
            $this->redirect('/checkout');
        }

        $occasion = $data['occasion'];
        $displayDate = date('d/m/Y', strtotime($data['event_date']));
        if (!empty($data['event_time'])) {
            $displayDate = date('d/m/Y', strtotime($data['event_date'])) . ' ' . date('H:i', strtotime($data['event_time']));
        }

        $message = __('whatsapp_intro') . "\n\n"
            . __('whatsapp_name') . ": {$data['name']}\n"
            . __('whatsapp_phone') . ": {$data['phone']}\n"
            . __('whatsapp_event_date') . ": {$displayDate}\n"
            . __('whatsapp_occasion') . ": {$occasion}\n"
            . __('whatsapp_address') . ": {$data['address']}\n"
            . __('whatsapp_menu_items') . ":\n";

        foreach ($items as $item) {
            $menuName = $item['name'] ?? __('unknown');
            $qty = (int) ($item['quantity'] ?? 1);
            $message .= "- {$menuName}: {$qty} " . __('whatsapp_portions') . "\n";
        }

        if (!empty($data['notes'])) {
            $message .= "\n" . __('whatsapp_notes') . ": {$data['notes']}\n";
        }

        $message .= "\n" . __('whatsapp_thank_you');

        $waNumber = $_ENV['WHATSAPP_NUMBER'] ?? '6287865252313';

        $this->cartService->clear();

        $this->redirect('https://wa.me/' . $waNumber . '?text=' . urlencode($message));
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
            __('event_date'), __('event_time'), __('delivery_address'), __('created_at'),
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
                $row['event_time'] ?? '',
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

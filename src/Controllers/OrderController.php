<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Database;
use App\Exceptions\NotFoundException;
use App\Services\OrderService;
use App\Services\MenuService;
use App\Models\Customer;

class OrderController extends BaseController {
    public function __construct(
        private OrderService $orderService,
        private MenuService $menuService,
        private Customer $customer
    ) {
        parent::__construct();
    }

    public function index(Request $request): void {
        $page = (int) $request->input('page', 1);
        $result = $this->orderService->paginate($page);

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
        ]);
    }

    public function create(Request $request): void {
        $menus = $this->menuService->paginate(1, 100)['data'];

        $this->render('order/create', [
            'title' => 'Create New Order',
            'menus' => $menus,
        ]);
    }

    public function store(Request $request): void {
        $data = $request->only(['phone', 'customer_name', 'delivery_address', 'menu_id', 'quantity', 'event_date', 'notes']);

        $validator = new Validator(Database::getInstance());
        $validator->validate($data, [
            'phone' => 'required|min:10|max:20',
            'customer_name' => 'required|min:3|max:255',
            'delivery_address' => 'required|min:10',
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
        $data = $request->only(['status']);

        $validator = new Validator();
        $validator->validate($data, [
            'status' => 'required|in:pending,processing,delivering,completed,cancelled',
        ]);

        if ($validator->fails()) {
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect("/orders/{$id}/edit");
        }

        $this->orderService->updateStatus($id, $data['status']);
        $this->redirectWithFlash('/orders', 'success', 'Order status successfully updated.');
    }
}

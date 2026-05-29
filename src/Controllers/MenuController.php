<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Exceptions\NotFoundException;
use App\Services\MenuService;
use App\Services\CategoryService;
use App\Services\EventService;
use App\Services\AiService;
use App\Models\Order;

class MenuController extends BaseController {
    public function __construct(
        private MenuService $menuService,
        private CategoryService $categoryService,
        private EventService $eventService,
        private AiService $aiService
    ) {
        parent::__construct();
    }

    public function index(Request $request): void {
        $page = (int) $request->input('page', 1);
        $search = $request->input('search', '');
        $orderBy = $request->input('sort_by', 'created_at');
        $direction = $request->input('dir', 'DESC');
        $conditions = [];
        $catId = $request->input('category_id', '');
        $status = $request->input('status', '');
        $eventId = $request->input('event_id', '');
        if ($catId !== '') $conditions['category_id'] = $catId;
        if ($status !== '') $conditions['status'] = $status;
        if ($eventId !== '') $conditions['event_id'] = $eventId;
        $result = $this->menuService->paginate($page, 15, $conditions, $search, ['name'], $orderBy, $direction);
        $menus = $result['data'];

        $categories = $this->categoryService->all();
        $katMap = [];
        foreach ($categories as $k) {
            $katMap[$k['id']] = $k['name'];
        }

        $events = $this->eventService->getActive();
        $eventMap = [];
        foreach ($events as $ev) {
            $eventMap[$ev['id']] = $ev['name'];
        }

        $this->render('menu/index', [
            'title' => 'Catering Menu List',
            'menus' => $menus,
            'pagination' => $result,
            'katMap' => $katMap,
            'eventMap' => $eventMap,
            'search' => $search,
            'filterCategory' => $catId,
            'filterStatus' => $status,
            'filterEvent' => $eventId,
            'sort_by' => $orderBy,
            'dir' => $direction,
        ]);
    }

    public function show(Request $request): void {
        $id = (int) $request->param('id');
        $menu = $this->menuService->find($id);
        if (!$menu) throw new NotFoundException('Menu not found');

        $category = $this->categoryService->find((int)$menu['category_id']);
        $event = $this->eventService->find((int)$menu['event_id']);

        $orderModel = new Order();
        $recentOrders = $orderModel->getOrdersByMenuId($id, 10);

        $this->render('menu/show', [
            'title' => $menu['name'] . ' — Menu Detail',
            'menu' => $menu,
            'category' => $category,
            'event' => $event,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function create(Request $request): void {
        $this->render('menu/create', [
            'title' => 'Add Menu',
            'categories' => $this->categoryService->all(),
            'events' => $this->eventService->getActive(),
        ]);
    }

    public function store(Request $request): void {
        $data = $request->only(['name', 'description', 'price', 'category_id', 'event_id', 'minimum_portions', 'status']);
        $gambar = $request->file('image');

        $validator = new Validator();
        $validator->validate($data, [
            'name' => 'required|min:3|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
            'event_id' => 'required|numeric',
            'minimum_portions' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            if ($request->isAjax()) {
                Response::jsonError('Validation failed.', $validator->errors());
            }
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect('/menus/create');
        }

        if ($gambar) {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            $maxSize = 5242880;
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($gambar['tmp_name']);
            if (!in_array($mime, $allowedMimes, true)) {
                if ($request->isAjax()) {
                    Response::jsonError('Invalid file type. Only JPG, PNG, WEBP files are allowed.');
                }
                $this->withOldInput($data);
                Session::flash('error', 'Invalid file type. Only JPG, PNG, WEBP files are allowed.');
                $this->redirect('/menus/create');
            }
            if ($gambar['size'] > $maxSize) {
                if ($request->isAjax()) {
                    Response::jsonError('File too large. Maximum size is 5 MB.');
                }
                $this->withOldInput($data);
                Session::flash('error', 'File too large. Maximum size is 5 MB.');
                $this->redirect('/menus/create');
            }
        }

        try {
            $this->menuService->create($data, $gambar);
            if ($request->isAjax()) {
                Response::jsonSuccess(null, 'Menu successfully added.');
            }
            $this->redirectWithFlash('/menus', 'success', 'Menu successfully added.');
        } catch (\Exception $e) {
            if ($request->isAjax()) {
                Response::jsonError('Failed to add menu: ' . $e->getMessage());
            }
            $this->withOldInput($data);
            Session::flash('error', 'Failed to add menu: ' . $e->getMessage());
            $this->redirect('/menus/create');
        }
    }

    public function apiShow(Request $request): void {
        $id = (int) $request->param('id');
        $menu = $this->menuService->find($id);
        if (!$menu) Response::jsonError('Not found', [], 404);
        $menu['image_url'] = $menu['image'] ? '/uploads/menus/' . $menu['image'] : null;
        Response::jsonSuccess($menu);
    }

    public function update(Request $request): void {
        $id = (int) $request->param('id');
        $data = $request->only(['name', 'description', 'price', 'category_id', 'event_id', 'minimum_portions', 'status']);
        $gambar = $request->file('image');

        $validator = new Validator();
        $validator->validate($data, [
            'name' => 'required|min:3|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
            'event_id' => 'required|numeric',
            'minimum_portions' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            if ($request->isAjax()) Response::jsonError('Validation failed.', $validator->errors());
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect("/menus/{$id}/edit");
        }

        if ($gambar) {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            $maxSize = 5242880;
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($gambar['tmp_name']);
            if (!in_array($mime, $allowedMimes, true)) {
                if ($request->isAjax()) Response::jsonError('Invalid file type. Only JPG, PNG, WEBP files are allowed.');
                $this->withOldInput($data);
                Session::flash('error', 'Invalid file type. Only JPG, PNG, WEBP files are allowed.');
                $this->redirect("/menus/{$id}/edit");
            }
            if ($gambar['size'] > $maxSize) {
                if ($request->isAjax()) Response::jsonError('File too large. Maximum size is 5 MB.');
                $this->withOldInput($data);
                Session::flash('error', 'File too large. Maximum size is 5 MB.');
                $this->redirect("/menus/{$id}/edit");
            }
        }

        try {
            $this->menuService->update($id, $data, $gambar);
            if ($request->isAjax()) Response::jsonSuccess(null, 'Menu successfully updated.');
            $this->redirectWithFlash('/menus', 'success', 'Menu successfully updated.');
        } catch (\Exception $e) {
            if ($request->isAjax()) Response::jsonError('Failed to update menu: ' . $e->getMessage());
            $this->withOldInput($data);
            Session::flash('error', 'Failed to update menu: ' . $e->getMessage());
            $this->redirect("/menus/{$id}/edit");
        }
    }

    public function generateDescription(Request $request): void {
        try {
            $context = [
                'name' => $request->input('name', ''),
                'category' => $request->input('category', ''),
                'event' => $request->input('event', ''),
                'price' => $request->input('price', ''),
                'minimum_portions' => $request->input('minimum_portions', ''),
            ];

            $description = $this->aiService->generateDescription($context);
            Response::json(['description' => $description]);
        } catch (\Throwable $e) {
            Response::jsonError($e->getMessage(), [], 500);
        }
    }

    public function destroy(Request $request): void {
        $id = (int) $request->param('id');
        
        try {
            if ($this->menuService->delete($id)) {
                $this->redirectWithFlash('/menus', 'success', 'Menu successfully deleted.');
            } else {
                $this->redirectWithFlash('/menus', 'error', 'Failed to delete menu.');
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $this->redirectWithFlash('/menus', 'error', 'This menu has existing orders and cannot be deleted.');
            } else {
                $this->redirectWithFlash('/menus', 'error', 'Database error: ' . $e->getMessage());
            }
        }
    }
}

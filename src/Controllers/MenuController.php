<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Logger;
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
        $categoryMap = [];
        foreach ($categories as $k) {
            $categoryMap[$k['id']] = $k['name'];
        }

        $events = $this->eventService->getActive();
        $eventMap = [];
        foreach ($events as $ev) {
            $eventMap[$ev['id']] = $ev['name'];
        }

        $this->render('menu/index', [
            'title' => __('menus'),
            'menus' => $menus,
            'pagination' => $result,
            'categoryMap' => $categoryMap,
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
        $code = $request->param('code');
        $menu = $this->menuService->findByCode($code);
        if (!$menu) throw new NotFoundException(__('menu_not_found'));

        $category = $this->categoryService->find((int)$menu['category_id']);
        $event = $this->eventService->find((int)$menu['event_id']);

        $orderModel = new Order();
        $recentOrders = $orderModel->getOrdersByMenuId((int)$menu['id'], 10);

        $this->render('menu/show', [
            'title' => __('menu_details'),
            'menu' => $menu,
            'category' => $category,
            'event' => $event,
            'recentOrders' => $recentOrders,
            'categories' => $this->categoryService->all(),
            'events' => $this->eventService->getActive(),
        ]);
    }

    public function create(Request $request): void {
        $this->render('menu/create', [
            'title' => __('add_menu'),
            'categories' => $this->categoryService->all(),
            'events' => $this->eventService->getActive(),
        ]);
    }

    public function store(Request $request): void {
        $data = $request->only(['name', 'description', 'price', 'category_id', 'event_id', 'minimum_portions', 'status']);
        $imageFile = $request->file('image');

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
                Response::jsonError(__('validation_failed'), $validator->errors());
            }
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect('/menus/create');
        }

        if ($imageFile) {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            $maxSize = 5242880;
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($imageFile['tmp_name']);
            if (!in_array($mime, $allowedMimes, true)) {
                if ($request->isAjax()) {
                    Response::jsonError(__('invalid_file_type'));
                }
                $this->withOldInput($data);
                Session::flash('error', __('invalid_file_type'));
                $this->redirect('/menus/create');
            }
            if ($imageFile['size'] > $maxSize) {
                if ($request->isAjax()) {
                    Response::jsonError(__('file_too_large'));
                }
                $this->withOldInput($data);
                Session::flash('error', __('file_too_large'));
                $this->redirect('/menus/create');
            }
        }

        try {
            $this->menuService->create($data, $imageFile);
            if ($request->isAjax()) {
                Response::jsonSuccess(null, __('menu_added'));
            }
            $this->redirectWithFlash('/menus', 'success', __('menu_added'));
        } catch (\Exception $e) {
            Logger::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $errorMsg = APP_DEBUG ? $e->getMessage() : __('operation_failed');
            if ($request->isAjax()) {
                Response::jsonError(__('failed_create_menu', ['error' => $errorMsg]));
            }
            $this->withOldInput($data);
            Session::flash('error', __('failed_create_menu', ['error' => $errorMsg]));
            $this->redirect('/menus/create');
        }
    }

    public function apiShow(Request $request): void {
        $code = $request->param('code');
        $menu = $this->menuService->findByCode($code);
        if (!$menu) Response::jsonError(__('not_found_api'), [], 404);
        $menu['image_url'] = $menu['image'] ? '/uploads/' . $menu['image'] : null;
        Response::jsonSuccess($menu);
    }

    public function update(Request $request): void {
        $code = $request->param('code');
        $menu = $this->menuService->findByCode($code);
        if (!$menu) throw new NotFoundException(__('menu_not_found'));
        $id = (int)$menu['id'];

        $data = $request->only(['name', 'description', 'price', 'category_id', 'event_id', 'minimum_portions', 'status']);
        $imageFile = $request->file('image');

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
            if ($request->isAjax()) Response::jsonError(__('validation_failed'), $validator->errors());
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect("/menus/{$code}");
        }

        if ($imageFile) {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            $maxSize = 5242880;
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($imageFile['tmp_name']);
            if (!in_array($mime, $allowedMimes, true)) {
                if ($request->isAjax()) Response::jsonError(__('invalid_file_type'));
                $this->withOldInput($data);
                Session::flash('error', __('invalid_file_type'));
                $this->redirect("/menus/{$code}");
            }
            if ($imageFile['size'] > $maxSize) {
                if ($request->isAjax()) Response::jsonError(__('file_too_large'));
                $this->withOldInput($data);
                Session::flash('error', __('file_too_large'));
                $this->redirect("/menus/{$code}");
            }
        }

        try {
            $this->menuService->update($id, $data, $imageFile);
            if ($request->isAjax()) Response::jsonSuccess(null, __('menu_updated'));
            $this->redirectWithFlash('/menus', 'success', __('menu_updated'));
        } catch (\Exception $e) {
            Logger::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $errorMsg = APP_DEBUG ? $e->getMessage() : __('operation_failed');
            if ($request->isAjax()) Response::jsonError(__('failed_update_menu', ['error' => $errorMsg]));
            $this->withOldInput($data);
            Session::flash('error', __('failed_update_menu', ['error' => $errorMsg]));
            $this->redirect("/menus/{$code}");
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
            Logger::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $errorMsg = APP_DEBUG ? $e->getMessage() : __('operation_failed');
            Response::jsonError($errorMsg, [], 500);
        }
    }

    public function destroy(Request $request): void {
        $code = $request->param('code');
        $menu = $this->menuService->findByCode($code);
        if (!$menu) throw new NotFoundException(__('menu_not_found'));
        $id = (int)$menu['id'];
        
        try {
            if ($this->menuService->delete($id)) {
                $this->redirectWithFlash('/menus', 'success', __('menu_deleted'));
            } else {
                $this->redirectWithFlash('/menus', 'error', __('failed_delete_menu'));
            }
        } catch (\PDOException $e) {
            Logger::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            if ($e->getCode() == 23000) {
                $this->redirectWithFlash('/menus', 'error', __('menu_has_orders'));
            } else {
                $errorMsg = APP_DEBUG ? $e->getMessage() : __('operation_failed');
                $this->redirectWithFlash('/menus', 'error', __('db_error', ['error' => $errorMsg]));
            }
        }
    }
}

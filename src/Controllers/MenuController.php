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
        if ($catId !== '') $conditions['category_id'] = $catId;
        if ($status !== '') $conditions['status'] = $status;
        $result = $this->menuService->paginate($page, 15, $conditions, $search, ['name'], $orderBy, $direction);

        $categories = $this->categoryService->all();
        $katMap = [];
        foreach ($categories as $k) {
            $katMap[$k['id']] = $k['name'];
        }

        $this->render('menu/index', [
            'title' => 'Catering Menu List',
            'menus' => $result['data'],
            'pagination' => $result,
            'katMap' => $katMap,
            'search' => $search,
            'filterCategory' => $catId,
            'filterStatus' => $status,
            'sort_by' => $orderBy,
            'dir' => $direction,
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
                $this->withOldInput($data);
                Session::flash('error', 'Invalid file type. Only JPG, PNG, WEBP files are allowed.');
                $this->redirect('/menus/create');
            }
            if ($gambar['size'] > $maxSize) {
                $this->withOldInput($data);
                Session::flash('error', 'File too large. Maximum size is 5 MB.');
                $this->redirect('/menus/create');
            }
        }

        try {
            $this->menuService->create($data, $gambar);
            $this->redirectWithFlash('/menus', 'success', 'Menu successfully added.');
        } catch (\Exception $e) {
            $this->withOldInput($data);
            Session::flash('error', 'Failed to add menu: ' . $e->getMessage());
            $this->redirect('/menus/create');
        }
    }

    public function edit(Request $request): void {
        $id = (int) $request->param('id');
        $menu = $this->menuService->find($id);

        if (!$menu) {
            throw new NotFoundException('Menu not found.');
        }

        $this->render('menu/edit', [
            'title' => 'Edit Menu',
            'menu' => $menu,
            'categories' => $this->categoryService->all(),
            'events' => $this->eventService->getActive(),
        ]);
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
                $this->withOldInput($data);
                Session::flash('error', 'Invalid file type. Only JPG, PNG, WEBP files are allowed.');
                $this->redirect("/menus/{$id}/edit");
            }
            if ($gambar['size'] > $maxSize) {
                $this->withOldInput($data);
                Session::flash('error', 'File too large. Maximum size is 5 MB.');
                $this->redirect("/menus/{$id}/edit");
            }
        }

        try {
            $this->menuService->update($id, $data, $gambar);
            $this->redirectWithFlash('/menus', 'success', 'Menu successfully updated.');
        } catch (\Exception $e) {
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
        
        if ($this->menuService->delete($id)) {
            $this->redirectWithFlash('/menus', 'success', 'Menu successfully deleted.');
        }
        
        $this->redirectWithFlash('/menus', 'error', 'Failed to delete menu.');
    }
}

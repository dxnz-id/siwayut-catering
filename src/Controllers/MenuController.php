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

class MenuController extends BaseController {
    public function __construct(
        private MenuService $menuService,
        private CategoryService $categoryService
    ) {
        parent::__construct();
    }

    public function index(Request $request): void {
        $page = (int) $request->input('page', 1);
        $result = $this->menuService->paginate($page);

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
        ]);
    }

    public function create(Request $request): void {
        $this->render('menu/create', [
            'title' => 'Add Menu',
            'categories' => $this->categoryService->all(),
        ]);
    }

    public function store(Request $request): void {
        $data = $request->only(['name', 'description', 'price', 'category_id', 'minimum_portions', 'status']);
        $gambar = $request->file('image');

        $validator = new Validator();
        $validator->validate($data, [
            'name' => 'required|min:3|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
            'minimum_portions' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect('/menus/create');
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
        ]);
    }

    public function update(Request $request): void {
        $id = (int) $request->param('id');
        $data = $request->only(['name', 'description', 'price', 'category_id', 'minimum_portions', 'status']);
        $gambar = $request->file('image');

        $validator = new Validator();
        $validator->validate($data, [
            'name' => 'required|min:3|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
            'minimum_portions' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect("/menus/{$id}/edit");
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

    public function destroy(Request $request): void {
        $id = (int) $request->param('id');
        
        if ($this->menuService->delete($id)) {
            $this->redirectWithFlash('/menus', 'success', 'Menu successfully deleted.');
        }
        
        $this->redirectWithFlash('/menus', 'error', 'Failed to delete menu.');
    }
}

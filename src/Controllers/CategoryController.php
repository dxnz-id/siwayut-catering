<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Exceptions\NotFoundException;
use App\Services\CategoryService;

class CategoryController extends BaseController {
    public function __construct(private CategoryService $categoryService) {
        parent::__construct();
    }

    public function index(Request $request): void {
        $page = (int) $request->input('page', 1);
        $search = $request->input('search', '');
        $orderBy = $request->input('sort_by', 'created_at');
        $direction = $request->input('dir', 'DESC');
        $result = $this->categoryService->paginate($page, 15, $search, $orderBy, $direction);

        $this->render('category/index', [
            'title' => 'Menu Categories',
            'categories' => $result['data'],
            'pagination' => $result,
            'search' => $search,
            'sort_by' => $orderBy,
            'dir' => $direction,
        ]);
    }

    public function create(Request $request): void {
        $this->render('category/create', [
            'title' => 'Add Category',
        ]);
    }

    public function store(Request $request): void {
        $data = $request->only(['name']);

        $validator = new Validator();
        $validator->validate($data, [
            'name' => 'required|min:3|max:100',
        ]);

        if ($validator->fails()) {
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect('/categories/create');
        }

        $this->categoryService->create($data);
        $this->redirectWithFlash('/categories', 'success', 'Category successfully added.');
    }

    public function edit(Request $request): void {
        $id = (int) $request->param('id');
        $category = $this->categoryService->find($id);

        if (!$category) {
            throw new NotFoundException('Category not found.');
        }

        $this->render('category/edit', [
            'title' => 'Edit Category',
            'category' => $category,
        ]);
    }

    public function update(Request $request): void {
        $id = (int) $request->param('id');
        $data = $request->only(['name']);

        $validator = new Validator();
        $validator->validate($data, [
            'name' => 'required|min:3|max:100',
        ]);

        if ($validator->fails()) {
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect("/categories/{$id}/edit");
        }

        $this->categoryService->update($id, $data);
        $this->redirectWithFlash('/categories', 'success', 'Category successfully updated.');
    }

    public function destroy(Request $request): void {
        $id = (int) $request->param('id');
        
        try {
            if ($this->categoryService->delete($id)) {
                $this->redirectWithFlash('/categories', 'success', 'Category successfully deleted.');
            } else {
                $this->redirectWithFlash('/categories', 'error', 'Failed to delete category.');
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $this->redirectWithFlash('/categories', 'error', 'Cannot delete category. It is still used by a menu.');
            } else {
                $this->redirectWithFlash('/categories', 'error', 'Database error: ' . $e->getMessage());
            }
        }
    }
}

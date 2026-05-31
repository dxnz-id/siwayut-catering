<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Logger;
use App\Exceptions\NotFoundException;
use App\Services\CategoryService;
use App\Services\MenuService;

class CategoryController extends BaseController {
    public function __construct(
        private CategoryService $categoryService,
        private MenuService $menuService
    ) {
        parent::__construct();
    }

    public function index(Request $request): void {
        $page = (int) $request->input('page', 1);
        $search = $request->input('search', '');
        $orderBy = $request->input('sort_by', 'created_at');
        $direction = $request->input('dir', 'DESC');
        $result = $this->categoryService->paginate($page, 15, $search, $orderBy, $direction);

        $catIds = array_column($result['data'], 'id');
        $menuCounts = $this->menuService->countByCategoryIds($catIds);
        $categories = array_map(fn($c) => [...$c, 'menu_count' => $menuCounts[$c['id']] ?? 0], $result['data']);

        $this->render('category/index', [
            'title' => __('menu_categories'),
            'categories' => $categories,
            'pagination' => $result,
            'search' => $search,
            'sort_by' => $orderBy,
            'dir' => $direction,
        ]);
    }

    public function create(Request $request): void {
        $this->render('category/create', [
            'title' => __('add_category'),
        ]);
    }

    public function store(Request $request): void {
        $data = $request->only(['name']);

        $validator = new Validator();
        $validator->validate($data, [
            'name' => 'required|min:3|max:100',
        ]);

        if ($validator->fails()) {
            if ($request->isAjax()) {
                Response::jsonError(__('validation_failed'), $validator->errors());
            }
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect('/categories/create');
        }

        try {
            $this->categoryService->create($data);
            if ($request->isAjax()) {
                Response::jsonSuccess(null, __('category_added'));
            }
            $this->redirectWithFlash('/categories', 'success', __('category_added'));
        } catch (\Exception $e) {
            Logger::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $errorMsg = APP_DEBUG ? $e->getMessage() : __('operation_failed');
            if ($request->isAjax()) {
                Response::jsonError(__('failed_create_category', ['error' => $errorMsg]));
            }
            $this->withOldInput($data);
            Session::flash('error', __('failed_create_category', ['error' => $errorMsg]));
            $this->redirect('/categories/create');
        }
    }

    public function apiShow(Request $request): void {
        $id = (int) $request->param('id');
        $category = $this->categoryService->find($id);
        if (!$category) Response::jsonError(__('not_found_api'), [], 404);
        Response::jsonSuccess($category);
    }

    public function update(Request $request): void {
        $id = (int) $request->param('id');
        $data = $request->only(['name']);

        $validator = new Validator();
        $validator->validate($data, [
            'name' => 'required|min:3|max:100',
        ]);

        if ($validator->fails()) {
            if ($request->isAjax()) Response::jsonError(__('validation_failed'), $validator->errors());
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect("/categories/{$id}/edit");
        }

        try {
            $this->categoryService->update($id, $data);
            if ($request->isAjax()) Response::jsonSuccess(null, __('category_updated'));
            $this->redirectWithFlash('/categories', 'success', __('category_updated'));
        } catch (\Exception $e) {
            Logger::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $errorMsg = APP_DEBUG ? $e->getMessage() : __('operation_failed');
            if ($request->isAjax()) Response::jsonError(__('failed_update_category', ['error' => $errorMsg]));
            $this->withOldInput($data);
            Session::flash('error', __('failed_update_category', ['error' => $errorMsg]));
            $this->redirect("/categories/{$id}/edit");
        }
    }

    public function destroy(Request $request): void {
        $id = (int) $request->param('id');
        
        try {
            if ($this->categoryService->delete($id)) {
                $this->redirectWithFlash('/categories', 'success', __('category_deleted'));
            } else {
                $this->redirectWithFlash('/categories', 'error', __('failed_delete_category'));
            }
        } catch (\PDOException $e) {
            Logger::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            if ($e->getCode() == 23000) {
                $this->redirectWithFlash('/categories', 'error', __('category_in_use'));
            } else {
                $errorMsg = APP_DEBUG ? $e->getMessage() : __('operation_failed');
                $this->redirectWithFlash('/categories', 'error', __('db_error', ['error' => $errorMsg]));
            }
        }
    }
}

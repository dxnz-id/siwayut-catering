<?php
declare(strict_types=1);
// File: src/Controllers/UserController.php

namespace App\Controllers;
use App\Core\{Request, Response, Session, Validator, Database, Logger};
use App\Services\UserService;

class UserController extends BaseController {
    public function __construct(private UserService $userService) {
        parent::__construct();
    }

    public function index(Request $request): void {
        $page = (int) ($request->input('page', 1));
        $search = $request->input('search', '');
        $orderBy = $request->input('sort_by', 'created_at');
        $direction = $request->input('dir', 'DESC');
        $filters = [
            'role' => $request->input('role', ''),
        ];
        $result = $this->userService->getAll($page, 15, $search, $filters, $orderBy, $direction);
        $this->render('user/index', [
            'title' => __('users'),
            'users' => $result['data'],
            'pagination' => $result,
            'success' => Session::getFlash('success'),
            'error' => Session::getFlash('error'),
            'currentUser' => $this->currentUser(),
            'search' => $search,
            'filters' => $filters,
            'sort_by' => $orderBy,
            'dir' => $direction,
        ]);
    }

    public function create(Request $request): void {
        $this->render('user/create', [
            'title' => __('add_user'),
            'errors' => Session::getFlash('errors') ? json_decode(Session::getFlash('errors'), true) : [],
            'currentUser' => $this->currentUser(),
        ]);
    }

    public function store(Request $request): void {
        $data = $request->only(['name', 'email', 'password', 'role']);

        $validator = new Validator(Database::getInstance());
        $validator->validate($data, [
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,user',
        ]);

        if ($validator->fails()) {
            if ($request->isAjax()) {
                Response::jsonError(__('validation_failed'), $validator->errors());
            }
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect('/users/create');
        }

        try {
            $this->userService->create($data);
            if ($request->isAjax()) {
                Response::jsonSuccess(null, __('user_created'));
            }
            $this->redirectWithFlash('/users', 'success', __('user_created'));
        } catch (\Exception $e) {
            Logger::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $errorMsg = APP_DEBUG ? $e->getMessage() : __('operation_failed');
            if ($request->isAjax()) {
                Response::jsonError(__('failed_create_user', ['error' => $errorMsg]));
            }
            $this->withOldInput($data);
            Session::flash('error', __('failed_create_user', ['error' => $errorMsg]));
            $this->redirect('/users/create');
        }
    }

    public function apiShow(Request $request): void {
        $id = (int) $request->param('id');
        $user = $this->userService->getById($id);
        if (!$user) Response::jsonError(__('not_found_api'), [], 404);
        unset($user['password']);
        Response::jsonSuccess($user);
    }

    public function update(Request $request): void {
        $id = (int) $request->param('id');
        $data = $request->only(['name', 'email', 'password', 'role', 'password_confirmation']);

        $validator = new Validator(Database::getInstance());
        $rules = [
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,user',
        ];

        $password = $data['password'] ?? '';
        if ($password !== '') {
            $rules['password'] = 'min:6|confirmed';
        }

        $validator->validate($data, $rules);

        if ($validator->fails()) {
            if ($request->isAjax()) Response::jsonError(__('validation_failed'), $validator->errors());
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect("/users/{$id}/edit");
        }

        try {
            $this->userService->update($id, $data);
            if ($request->isAjax()) Response::jsonSuccess(null, __('user_updated'));
            $this->redirectWithFlash('/users', 'success', __('user_updated'));
        } catch (\Exception $e) {
            Logger::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $errorMsg = APP_DEBUG ? $e->getMessage() : __('operation_failed');
            if ($request->isAjax()) Response::jsonError(__('failed_update_user', ['error' => $errorMsg]));
            $this->withOldInput($data);
            Session::flash('error', __('failed_update_user', ['error' => $errorMsg]));
            $this->redirect("/users/{$id}/edit");
        }
    }

    public function destroy(Request $request): void {
        $id = (int) $request->param('id');
        $currentUser = $this->currentUser();

        if ($currentUser && (int) $currentUser['id'] === $id) {
            $this->redirectWithFlash('/users', 'error', __('cannot_delete_self'));
        }

        $this->userService->delete($id);
        $this->redirectWithFlash('/users', 'success', __('user_deleted'));
    }
}

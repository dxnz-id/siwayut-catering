<?php
declare(strict_types=1);
// File: src/Controllers/UserController.php

namespace App\Controllers;
use App\Core\{Request, Response, Session, Validator, Database};
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
            'title' => 'Users',
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
            'title' => 'Create User',
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
                Response::jsonError('Validation failed.', $validator->errors());
            }
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect('/users/create');
        }

        try {
            $this->userService->create($data);
            if ($request->isAjax()) {
                Response::jsonSuccess(null, 'User created successfully.');
            }
            $this->redirectWithFlash('/users', 'success', 'User created successfully.');
        } catch (\Exception $e) {
            if ($request->isAjax()) {
                Response::jsonError('Failed to create user: ' . $e->getMessage());
            }
            $this->withOldInput($data);
            Session::flash('error', 'Failed to create user: ' . $e->getMessage());
            $this->redirect('/users/create');
        }
    }

    public function apiShow(Request $request): void {
        $id = (int) $request->param('id');
        $user = $this->userService->getById($id);
        if (!$user) Response::jsonError('Not found', [], 404);
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
            if ($request->isAjax()) Response::jsonError('Validation failed.', $validator->errors());
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect("/users/{$id}/edit");
        }

        try {
            $this->userService->update($id, $data);
            if ($request->isAjax()) Response::jsonSuccess(null, 'User updated successfully.');
            $this->redirectWithFlash('/users', 'success', 'User updated successfully.');
        } catch (\Exception $e) {
            if ($request->isAjax()) Response::jsonError('Update failed: ' . $e->getMessage());
            $this->withOldInput($data);
            Session::flash('error', 'Failed to update user: ' . $e->getMessage());
            $this->redirect("/users/{$id}/edit");
        }
    }

    public function destroy(Request $request): void {
        $id = (int) $request->param('id');
        $currentUser = $this->currentUser();

        if ($currentUser && (int) $currentUser['id'] === $id) {
            $this->redirectWithFlash('/users', 'error', 'You cannot delete your own account.');
        }

        $this->userService->delete($id);
        $this->redirectWithFlash('/users', 'success', 'User deleted successfully.');
    }
}

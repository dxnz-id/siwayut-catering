<?php
declare(strict_types=1);
// File: src/Controllers/UserController.php

namespace App\Controllers;
use App\Core\{Request, Session, Validator, Database};
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
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect('/users/create');
        }

        $this->userService->create($data);
        $this->redirectWithFlash('/users', 'success', 'User created successfully.');
    }

    public function edit(Request $request): void {
        $id = (int) $request->param('id');
        $user = $this->userService->getById($id);
        $this->render('user/edit', [
            'title' => 'Edit User',
            'user' => $user,
            'errors' => Session::getFlash('errors') ? json_decode(Session::getFlash('errors'), true) : [],
            'currentUser' => $this->currentUser(),
        ]);
    }

    public function update(Request $request): void {
        $id = (int) $request->param('id');
        $data = $request->only(['name', 'email', 'password', 'role']);

        $validator = new Validator(Database::getInstance());
        $validator->validate($data, [
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,user',
        ]);

        if ($validator->fails()) {
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect("/users/{$id}/edit");
        }

        $this->userService->update($id, $data);
        $this->redirectWithFlash('/users', 'success', 'User updated successfully.');
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

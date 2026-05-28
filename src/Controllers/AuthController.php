<?php
declare(strict_types=1);
// File: src/Controllers/AuthController.php

namespace App\Controllers;
use App\Core\{Request, Session, Validator, Database, Turnstile};
use App\Services\AuthService;

class AuthController extends BaseController {
    public function __construct(private AuthService $authService) {
        parent::__construct();
    }

    public function index(Request $request): void {
        if ($this->currentUser()) {
            $user = $this->currentUser();
            $this->redirect(($user['role'] ?? '') === 'admin' ? '/users' : '/');
        }
        $this->render('auth/auth', [
            'title' => 'Auth',
            'error' => Session::getFlash('error'),
            'success' => Session::getFlash('success'),
            'activeTab' => Session::getFlash('auth_tab') ?? 'login',
        ], 'auth');
    }

    public function loginPageRedirect(Request $request): void {
        $this->redirect('/auth');
    }

    public function login(Request $request): void {
        $email = (string) $request->input('email', '');
        $password = (string) $request->input('password', '');

        if (!Turnstile::verify($request->input('cf-turnstile-response', ''))) {
            $this->withOldInput(['email' => $email]);
            Session::flash('auth_tab', 'login');
            $this->redirectWithFlash('/auth', 'error', 'Captcha verification failed.');
        }

        $validator = new Validator();
        $validator->validate(
            ['email' => $email, 'password' => $password],
            ['email' => 'required|email', 'password' => 'required']
        );

        if ($validator->fails()) {
            $this->withOldInput(['email' => $email]);
            Session::flash('auth_tab', 'login');
            $this->redirectWithFlash('/auth', 'error', $validator->error('email') ?? $validator->error('password') ?? 'Validation failed.');
        }

        if ($this->authService->login($email, $password)) {
            $user = $this->currentUser();
            $this->redirect(($user['role'] ?? '') === 'admin' ? '/users' : '/');
        }

        $this->withOldInput(['email' => $email]);
        Session::flash('auth_tab', 'login');
        $this->redirectWithFlash('/auth', 'error', 'Invalid email or password.');
    }

    public function register(Request $request): void {
        $data = $request->only(['name', 'email', 'phone', 'password', 'password_confirmation']);

        if (!Turnstile::verify($request->input('cf-turnstile-response', ''))) {
            $this->withOldInput([
                'name' => (string) ($data['name'] ?? ''),
                'email' => (string) ($data['email'] ?? ''),
                'phone' => (string) ($data['phone'] ?? ''),
            ]);
            Session::flash('auth_tab', 'register');
            $this->redirectWithFlash('/auth', 'error', 'Captcha verification failed.');
        }

        $validator = new Validator(Database::getInstance());
        $validator->validate($data, [
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|min:10|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            $this->withOldInput([
                'name' => (string) ($data['name'] ?? ''),
                'email' => (string) ($data['email'] ?? ''),
                'phone' => (string) ($data['phone'] ?? ''),
            ]);
            Session::flash('auth_tab', 'register');
            $firstError = reset($validator->errors());
            $this->redirectWithFlash('/auth', 'error', (string) $firstError);
        }

        $this->authService->register(
            (string) $data['name'],
            (string) $data['email'],
            (string) $data['phone'],
            (string) $data['password']
        );

        Session::flash('auth_tab', 'login');
        $this->redirectWithFlash('/auth', 'success', 'Registration successful. Please sign in.');
    }

    public function logout(Request $request): void {
        $this->authService->logout();
        $this->redirect('/auth');
    }
}

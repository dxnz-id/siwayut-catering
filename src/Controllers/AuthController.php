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
            $this->redirect('/users');
        }
        $this->render('auth/login', [
            'title' => 'Login',
            'error' => Session::getFlash('error'),
        ], 'auth');
    }

    public function login(Request $request): void {
        $email = (string) $request->input('email', '');
        $password = (string) $request->input('password', '');

        if (!Turnstile::verify($request->input('cf-turnstile-response', ''))) {
            $this->withOldInput(['email' => $email]);
            $this->redirectWithFlash('/login', 'error', 'Captcha verification failed.');
        }

        $validator = new Validator();
        $validator->validate(
            ['email' => $email, 'password' => $password],
            ['email' => 'required|email', 'password' => 'required']
        );

        if ($validator->fails()) {
            $this->withOldInput(['email' => $email]);
            $this->redirectWithFlash('/login', 'error', $validator->error('email') ?? $validator->error('password') ?? 'Validation failed.');
        }

        if ($this->authService->login($email, $password)) {
            $this->redirect('/users');
        }

        $this->withOldInput(['email' => $email]);
        $this->redirectWithFlash('/login', 'error', 'Invalid email or password.');
    }

    public function logout(Request $request): void {
        $this->authService->logout();
        $this->redirect('/login');
    }
}

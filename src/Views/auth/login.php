<!-- File: src/Views/auth/login.php -->
<div class="auth-card">
    <div class="auth-brand">
        <h1><?= \App\Core\View::e(APP_NAME) ?></h1>
        <p>Sign in to your account</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="alert alert-error">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
        <?= \App\Core\View::e($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="/login">
        <?= \App\Core\Csrf::field() ?>

        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" id="email" name="email" class="form-input" value="<?= \App\Core\View::e(old('email')) ?>" placeholder="admin@example.com" required autofocus>
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
        </div>

        <div class="form-actions" style="margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                Sign In
            </button>
        </div>
    </form>
</div>

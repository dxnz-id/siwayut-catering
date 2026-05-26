<!-- File: src/Views/user/edit.php -->
<div class="content-header">
    <h1 class="content-title">Edit User</h1>
    <a href="/users" class="btn btn-secondary">
        &larr; Back to Users
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/users/<?= (int)$user['id'] ?>">
            <?= \App\Core\Csrf::field() ?>

            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" id="name" name="name" class="form-input<?= isset($errors['name']) ? ' is-invalid' : '' ?>" value="<?= \App\Core\View::e(old('name', $user['name'] ?? '')) ?>" placeholder="John Doe" required>
                <?php if (isset($errors['name'])): ?>
                <div class="form-error"><?= \App\Core\View::e($errors['name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-input<?= isset($errors['email']) ? ' is-invalid' : '' ?>" value="<?= \App\Core\View::e(old('email', $user['email'] ?? '')) ?>" placeholder="user@example.com" required>
                <?php if (isset($errors['email'])): ?>
                <div class="form-error"><?= \App\Core\View::e($errors['email']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password <span style="font-weight: 400; color: var(--color-text-muted);">(leave blank to keep current)</span></label>
                <input type="password" id="password" name="password" class="form-input<?= isset($errors['password']) ? ' is-invalid' : '' ?>" placeholder="Min. 6 characters">
                <?php if (isset($errors['password'])): ?>
                <div class="form-error"><?= \App\Core\View::e($errors['password']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="role" class="form-label">Role</label>
                <select id="role" name="role" class="form-select<?= isset($errors['role']) ? ' is-invalid' : '' ?>">
                    <?php $currentRole = old('role', $user['role'] ?? 'user'); ?>
                    <option value="user" <?= $currentRole === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $currentRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
                <?php if (isset($errors['role'])): ?>
                <div class="form-error"><?= \App\Core\View::e($errors['role']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="/users" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

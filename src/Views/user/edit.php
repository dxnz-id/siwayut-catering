<div class="content-header">
    <h1 class="content-title">Edit User</h1>
    <a href="/users" class="btn btn-secondary">
        &larr; Back to Users
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/users/<?= e($user['id']) ?>">
            <?= \App\Core\Csrf::field() ?>

            <?php component('form/input', [
                'name' => 'name',
                'label' => 'Full Name',
                'value' => old('name', $user['name']),
                'required' => true
            ]); ?>

            <?php component('form/input', [
                'name' => 'email',
                'label' => 'Email Address',
                'type' => 'email',
                'value' => old('email', $user['email']),
                'required' => true
            ]); ?>

            <?php component('form/input', [
                'name' => 'password',
                'label' => 'Password',
                'type' => 'password',
                'placeholder' => 'Leave blank to keep current password',
                'help_text' => 'Fill this only if you want to change the password.'
            ]); ?>

            <?php component('form/select', [
                'name' => 'role',
                'label' => 'Role',
                'options' => [
                    'user' => 'User',
                    'admin' => 'Admin'
                ],
                'selected' => old('role', $user['role'])
            ]); ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="/users" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

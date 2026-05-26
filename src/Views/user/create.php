<div class="content-header">
    <h1 class="content-title">Create User</h1>
    <a href="/users" class="btn btn-secondary">
        &larr; Back to Users
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/users">
            <?= \App\Core\Csrf::field() ?>

            <?php component('form/input', [
                'name' => 'name',
                'label' => 'Full Name',
                'placeholder' => 'John Doe',
                'required' => true
            ]); ?>

            <?php component('form/input', [
                'name' => 'email',
                'label' => 'Email Address',
                'type' => 'email',
                'placeholder' => 'user@example.com',
                'required' => true
            ]); ?>

            <?php component('form/input', [
                'name' => 'password',
                'label' => 'Password',
                'type' => 'password',
                'placeholder' => 'Min. 6 characters',
                'required' => true
            ]); ?>

            <?php component('form/select', [
                'name' => 'role',
                'label' => 'Role',
                'options' => [
                    'user' => 'User',
                    'admin' => 'Admin'
                ],
                'selected' => old('role', 'user')
            ]); ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create User</button>
                <a href="/users" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

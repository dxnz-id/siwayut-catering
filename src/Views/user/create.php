<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold font-display text-text">Create User</h1>
    <a href="/users" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">
        &larr; Back to Users
    </a>
</div>

<div class="bg-[#18181b] border border-border rounded-xl overflow-hidden">
    <div class="p-6">
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

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">Create User</button>
                <a href="/users" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">Cancel</a>
            </div>
        </form>
    </div>
</div>

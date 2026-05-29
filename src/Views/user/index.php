<?php $headerTitle = 'Users'; $createModal = 'createUserModal'; ?>
<?php require __DIR__ . '/../partials/table-header.php' ?>
<?php
$searchPlaceholder = 'Search name, email, or role...';
$search = $search ?? '';
$filters = [
    [
        'label' => 'Role',
        'name' => 'role',
        'options' => ['' => 'All Roles', 'admin' => 'Admin', 'user' => 'User'],
        'selected' => $filters['role'] ?? '',
    ],
];
?>
<?php require __DIR__ . '/../partials/table-search.php' ?>
<div id="table-container" class="bg-[#18181b] border border-border rounded-xl overflow-hidden">
    <?php if (empty($users)): ?>
    <div class="col-span-full bg-card-bg border border-dashed border-border rounded-[20px] px-6 py-12 text-center text-muted">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="mx-auto opacity-40"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
        <p>No users found. Create your first user to get started.</p>
    </div>
    <?php else: ?>
    <?php require __DIR__ . '/../partials/table-sort.php' ?>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('id') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">ID<?= $sortIcon('id') ?></a></th>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('name') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">Name<?= $sortIcon('name') ?></a></th>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('email') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">Email<?= $sortIcon('email') ?></a></th>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('role') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">Role<?= $sortIcon('role') ?></a></th>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('created_at') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">Created<?= $sortIcon('created_at') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text"><?= \App\Core\View::e($user['id']) ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text font-medium"><?= \App\Core\View::e($user['name']) ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text"><?= \App\Core\View::e($user['email']) ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $user['role'] === 'admin' ? 'bg-primary-light text-gold border border-[rgba(229,142,38,0.2)]' : 'bg-[rgba(16,185,129,0.1)] text-[#6ee7b7] border border-[rgba(16,185,129,0.2)]' ?>">
                            <?= \App\Core\View::e(ucfirst($user['role'])) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text text-muted text-[0.8125rem]">
                        <?= \App\Core\View::e($user['created_at']) ?>
                    </td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text">
                        <div class="flex items-center gap-2">
                            <a href="#" data-edit="users" data-id="<?= (int)$user['id'] ?>" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-[0.8125rem] rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">Edit</a>
                            <?php if (($currentUser['id'] ?? 0) !== (int)$user['id']): ?>
                            <form method="POST" action="/users/<?= (int)$user['id'] ?>/delete" class="inline">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" data-modal-confirm="Are you sure you want to delete this user?" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-[0.8125rem] rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-danger text-white border-danger hover:bg-danger-hover hover:border-danger-hover hover:text-white">Delete</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php $totalLabel = 'users'; require __DIR__ . '/../partials/table-pagination.php' ?>
    <?php endif; ?>
</div>
<?php
$createModalId = 'createUserModal';
$createTitle = 'Create User';
$createAction = '/users';
$createSubmitText = 'Create User';
ob_start();
component('form/input', ['name' => 'name', 'label' => 'Full Name', 'placeholder' => 'John Doe', 'required' => true]);
component('form/input', ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'placeholder' => 'user@example.com', 'required' => true]);
component('form/input', ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'placeholder' => 'Leave blank to keep current']);
component('form/input', ['name' => 'password_confirmation', 'label' => 'Confirm Password', 'type' => 'password', 'placeholder' => 'Confirm new password']);
component('form/select', ['name' => 'role', 'label' => 'Role', 'options' => ['user' => 'User', 'admin' => 'Admin']]);
$createFormContent = ob_get_clean();
require __DIR__ . '/../partials/create-modal.php';
?>

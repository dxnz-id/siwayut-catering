<!-- File: src/Views/user/index.php -->
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold font-display text-text">Users</h1>
    <a href="/users/create" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Add User
    </a>
</div>

<!-- Search & Filter -->
<div class="mb-4">
    <form method="GET" class="flex items-center gap-3 relative">
        <input type="hidden" name="page" value="1">
        <div class="relative flex-1">
            <input type="text" name="search" value="<?= e($search ?? '') ?>" placeholder="Search name, email, or role..." class="w-full px-4 py-2.5 pl-10 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
        </div>
        <button type="button" onclick="this.nextElementSibling.classList.toggle('hidden')" class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-border bg-black/40 text-muted hover:text-text hover:border-primary transition-all duration-150 shrink-0" title="Filters">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/></svg>
        </button>
        <div class="hidden p-4 bg-[#18181b] border border-border rounded-xl absolute mt-2 right-0 top-full z-10 min-w-[280px] shadow-lg">
            <div class="flex items-end gap-3 flex-wrap">
                <div>
                    <label class="block text-xs font-medium text-muted mb-1">Role</label>
                    <select name="role" class="px-3 py-2 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light min-w-[140px]">
                        <option value="">All Roles</option>
                        <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="user" <?= ($filters['role'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">Apply</button>
            </div>
        </div>
    </form>
</div>

<div id="table-container" class="bg-[#18181b] border border-border rounded-xl overflow-hidden">
    <?php if (empty($users)): ?>
    <div class="col-span-full bg-card-bg border border-dashed border-border rounded-[20px] px-6 py-12 text-center text-muted">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="mx-auto opacity-40"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
        <p>No users found. Create your first user to get started.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr>
<?php
$s = $sort_by ?? 'created_at';
$d = $dir ?? 'DESC';
$sortUrl = function($col) use ($s, $d) {
    $next = ($s === $col && $d === 'asc') ? 'desc' : 'asc';
    return '?' . http_build_query(array_merge($_GET, ['sort_by' => $col, 'dir' => $next]));
};
$sortIcon = function($col) use ($s, $d) {
    if ($s !== $col) return '';
    return '<span class="ml-1 text-gold">' . ($d === 'asc' ? '↑' : '↓') . '</span>';
};
?>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('id') ?>" class="text-muted hover:text-gold transition-colors no-underline">ID<?= $sortIcon('id') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('name') ?>" class="text-muted hover:text-gold transition-colors no-underline">Name<?= $sortIcon('name') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('email') ?>" class="text-muted hover:text-gold transition-colors no-underline">Email<?= $sortIcon('email') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('role') ?>" class="text-muted hover:text-gold transition-colors no-underline">Role<?= $sortIcon('role') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('created_at') ?>" class="text-muted hover:text-gold transition-colors no-underline">Created<?= $sortIcon('created_at') ?></a></th>
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
                            <a href="/users/<?= (int)$user['id'] ?>/edit" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-[0.8125rem] rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">Edit</a>
                            <?php if (($currentUser['id'] ?? 0) !== (int)$user['id']): ?>
                            <form method="POST" action="/users/<?= (int)$user['id'] ?>/delete" class="inline">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-[0.8125rem] rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-danger text-white border-danger hover:bg-danger-hover hover:border-danger-hover hover:text-white" data-confirm="Are you sure you want to delete this user?">Delete</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (isset($pagination) && $pagination['last_page'] > 1): ?>
    <div class="flex items-center justify-center gap-2 px-6 py-4 border-t border-border">
        <div class="text-[0.8125rem] text-muted">
            Showing page <?= (int)$pagination['current_page'] ?> of <?= (int)$pagination['last_page'] ?> (<?= (int)$pagination['total'] ?> total)
        </div>
        <div>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => max(1, $pagination['current_page'] - 1)])) ?>" class="inline-flex items-center justify-center min-w-[2rem] h-8 px-2 rounded-lg text-[0.8125rem] font-medium text-muted border border-border hover:bg-white/5 hover:text-text<?= $pagination['current_page'] <= 1 ? ' opacity-50 pointer-events-none' : '' ?>">&laquo; Prev</a>
            <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="inline-flex items-center justify-center min-w-[2rem] h-8 px-2 rounded-lg text-[0.8125rem] font-medium text-muted border border-border hover:bg-white/5 hover:text-text<?= $i === $pagination['current_page'] ? ' bg-primary text-white border-primary' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => min($pagination['last_page'], $pagination['current_page'] + 1)])) ?>" class="inline-flex items-center justify-center min-w-[2rem] h-8 px-2 rounded-lg text-[0.8125rem] font-medium text-muted border border-border hover:bg-white/5 hover:text-text<?= $pagination['current_page'] >= $pagination['last_page'] ? ' opacity-50 pointer-events-none' : '' ?>">Next &raquo;</a>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</div>

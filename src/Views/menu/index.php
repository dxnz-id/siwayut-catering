<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold font-display text-text">Catering Menu Management</h1>
    <a href="/menus/create" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Add Menu</a>
</div>

<!-- Search & Filter -->
<div class="mb-4">
    <form method="GET" class="flex items-center gap-3 relative">
        <input type="hidden" name="page" value="1">
        <div class="relative flex-1">
            <input type="text" name="search" value="<?= e($search ?? '') ?>" placeholder="Search menu name or description..." class="w-full px-4 py-2.5 pl-10 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
        </div>
        <button type="button" onclick="this.nextElementSibling.classList.toggle('hidden')" class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-border bg-black/40 text-muted hover:text-text hover:border-primary transition-all duration-150 shrink-0" title="Filters">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/></svg>
        </button>
        <div class="hidden p-4 bg-[#18181b] border border-border rounded-xl absolute mt-2 right-0 top-full z-10 min-w-[320px] shadow-lg">
            <div class="flex items-end gap-3 flex-wrap">
                <div>
                    <label class="block text-xs font-medium text-muted mb-1">Category</label>
                    <select name="category_id" class="px-3 py-2 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light min-w-[140px]">
                        <option value="">All Categories</option>
                        <?php foreach ($katMap as $id => $name): ?>
                        <option value="<?= $id ?>" <?= ($filterCategory ?? '') == $id ? 'selected' : '' ?>><?= e($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-muted mb-1">Status</label>
                    <select name="status" class="px-3 py-2 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light min-w-[140px]">
                        <option value="">All Statuses</option>
                        <option value="active" <?= ($filterStatus ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($filterStatus ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">Apply</button>
            </div>
        </div>
    </form>
</div>

<div id="table-container" class="bg-[#18181b] border border-border rounded-xl overflow-hidden">
    <?php if (empty($menus)): ?>
    <div class="col-span-full bg-card-bg border border-dashed border-border rounded-[20px] px-6 py-12 text-center text-muted">
        <p>No menus added yet.</p>
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
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">Image</th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('name') ?>" class="text-muted hover:text-gold transition-colors no-underline">Menu Name<?= $sortIcon('name') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">Category</th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('price') ?>" class="text-muted hover:text-gold transition-colors no-underline">Price<?= $sortIcon('price') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('minimum_portions') ?>" class="text-muted hover:text-gold transition-colors no-underline">Min Portions<?= $sortIcon('minimum_portions') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('status') ?>" class="text-muted hover:text-gold transition-colors no-underline">Status<?= $sortIcon('status') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menus as $menu): ?>
                <tr>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text"><?= e($menu['id']) ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text">
                        <?php if ($menu['image']): ?>
                            <?php component('progressive-image', ['src' => $menu['image'], 'alt' => $menu['name'], 'style' => 'width:40px;height:40px;object-fit:cover;border-radius:4px']); ?>
                        <?php else: ?>
                            <div class="w-10 h-10 bg-border rounded flex items-center justify-center text-muted text-xs">No Img</div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text font-medium"><?= e($menu['name']) ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text"><?= e($katMap[$menu['category_id']] ?? 'Unknown') ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-success font-medium">Rp <?= number_format((float)$menu['price'], 0, ',', '.') ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text"><?= e($menu['minimum_portions']) ?> portions</td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text">
                        <?php if ($menu['status'] === 'active'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success text-white">Active</span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger text-white">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text">
                        <div class="flex items-center gap-2">
                            <a href="/menus/<?= e($menu['id']) ?>/edit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 px-3 py-1.5 text-[0.8125rem] bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">Edit</a>
                            <form action="/menus/<?= e($menu['id']) ?>/delete" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete this menu?');">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 px-3 py-1.5 text-[0.8125rem] bg-danger text-white border-danger hover:bg-danger-hover hover:border-danger-hover hover:text-white">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pagination['last_page'] > 1): ?>
    <div class="flex items-center justify-between px-6 py-4 border-t border-border">
        <div class="text-[0.8125rem] text-muted">
            Showing page <?= (int)$pagination['current_page'] ?> of <?= (int)$pagination['last_page'] ?> (Total: <?= (int)$pagination['total'] ?> menus)
        </div>
        <div class="flex gap-1">
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

<?php $headerTitle = 'Catering Menu Management'; $createUrl = '/menus/create'; ?>
<?php require __DIR__ . '/../partials/table-header.php' ?>
<?php
$searchPlaceholder = 'Search menu name or description...';
$search = $search ?? '';
$filterMinWidth = '320px';
$filters = [
    [
        'label' => 'Category',
        'name' => 'category_id',
        'options' => ['' => 'All Categories'] + ($katMap ?? []),
        'selected' => $filterCategory ?? '',
    ],
    [
        'label' => 'Status',
        'name' => 'status',
        'options' => ['' => 'All Statuses', 'active' => 'Active', 'inactive' => 'Inactive'],
        'selected' => $filterStatus ?? '',
    ],
];
?>
<?php require __DIR__ . '/../partials/table-search.php' ?>
<div id="table-container" class="bg-[#18181b] border border-border rounded-xl overflow-hidden">
    <?php if (empty($menus)): ?>
    <div class="col-span-full bg-card-bg border border-dashed border-border rounded-[20px] px-6 py-12 text-center text-muted">
        <p>No menus added yet.</p>
    </div>
    <?php else: ?>
    <?php require __DIR__ . '/../partials/table-sort.php' ?>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('id') ?>" class="text-muted hover:text-gold transition-colors no-underline">ID<?= $sortIcon('id') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">Image</th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('name') ?>" class="text-muted hover:text-gold transition-colors no-underline">Menu Name<?= $sortIcon('name') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">Category</th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('price') ?>" class="text-muted hover:text-gold transition-colors no-underline">Price<?= $sortIcon('price') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('minimum_portions') ?>" class="text-muted hover:text-gold transition-colors no-underline">Min Portions<?= $sortIcon('minimum_portions') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('status') ?>" class="text-muted hover:text-gold transition-colors no-underline">Status<?= $sortIcon('status') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">Orders</th>
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
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text text-center"><?= (int)$menu['order_count'] ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text">
                        <div class="flex items-center gap-2">
                            <a href="/menus/<?= e($menu['id']) ?>/edit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 px-3 py-1.5 text-[0.8125rem] bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">Edit</a>
                            <form action="/menus/<?= e($menu['id']) ?>/delete" method="POST" class="inline">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" data-modal-confirm="Yakin ingin menghapus menu ini?" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 px-3 py-1.5 text-[0.8125rem] bg-danger text-white border-danger hover:bg-danger-hover hover:border-danger-hover hover:text-white">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php $totalLabel = 'menus'; require __DIR__ . '/../partials/table-pagination.php' ?>
    <?php endif; ?>
</div>

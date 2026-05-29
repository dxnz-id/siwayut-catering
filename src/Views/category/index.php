<?php $headerTitle = 'Menu Categories'; $createUrl = '/categories/create'; ?>
<?php require __DIR__ . '/../partials/table-header.php' ?>
<?php $searchPlaceholder = 'Search category name or slug...'; $showFilter = false; $search = $search ?? ''; ?>
<?php require __DIR__ . '/../partials/table-search.php' ?>
<div id="table-container" class="bg-[#18181b] border border-border rounded-xl overflow-hidden">
    <?php if (empty($categories)): ?>
    <div class="col-span-full bg-card-bg border border-dashed border-border rounded-[20px] px-6 py-12 text-center text-muted">
        <p>No categories found.</p>
    </div>
    <?php else: ?>
    <?php require __DIR__ . '/../partials/table-sort.php' ?>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('id') ?>" class="text-muted hover:text-gold transition-colors no-underline">ID<?= $sortIcon('id') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('name') ?>" class="text-muted hover:text-gold transition-colors no-underline">Name<?= $sortIcon('name') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('slug') ?>" class="text-muted hover:text-gold transition-colors no-underline">Slug<?= $sortIcon('slug') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">Menus</th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr class="cursor-pointer hover:bg-white/[0.03]" onclick="location.href='/menus?category_id=<?= (int)$cat['id'] ?>'">
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text"><?= $cat['id'] ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text"><?= htmlspecialchars($cat['name']) ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text"><?= htmlspecialchars($cat['slug']) ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text"><?= (int)$cat['menu_count'] ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text" onclick="event.stopPropagation()">
                        <div class="flex items-center gap-2">
                            <a href="/categories/<?= $cat['id'] ?>/edit" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-[0.8125rem] rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">Edit</a>
                            <form action="/categories/<?= $cat['id'] ?>/delete" method="POST" class="inline">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" data-modal-confirm="Are you sure you want to delete this category?" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-[0.8125rem] rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-danger text-white border-danger hover:bg-danger-hover hover:border-danger-hover hover:text-white">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php $totalLabel = 'categories'; require __DIR__ . '/../partials/table-pagination.php' ?>
    <?php endif; ?>
</div>

<?php $headerTitle = 'Catering Menu Management'; $createModal = 'createMenuModal'; ?>
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
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('menu_code') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">Menu Code<?= $sortIcon('menu_code') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">Image</th>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('name') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">Menu Name<?= $sortIcon('name') ?></a></th>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('category_id') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">Category<?= $sortIcon('category_id') ?></a></th>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('price') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">Price<?= $sortIcon('price') ?></a></th>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('minimum_portions') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">Min Portions<?= $sortIcon('minimum_portions') ?></a></th>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('status') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">Status<?= $sortIcon('status') ?></a></th>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('order_count') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">Orders<?= $sortIcon('order_count') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menus as $menu): ?>
                <tr class="cursor-pointer hover:bg-white/[0.03]" onclick="if(!event.target.closest('a,button,form')){location.href='/menus/<?= (int)$menu['id'] ?>'}">
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text font-medium"><?= e($menu['menu_code']) ?></td>
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
                            <a href="#" data-edit="menus" data-id="<?= e($menu['id']) ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 px-3 py-1.5 text-[0.8125rem] bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">Edit</a>
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
<?php
$createModalId = 'createMenuModal';
$createTitle = 'Add Catering Menu';
$createAction = '/menus';
$createSubmitText = 'Save Menu';
$createEnctype = 'multipart/form-data';
ob_start();
component('form/input', ['name' => 'name', 'label' => 'Menu Name', 'required' => true]);
component('form/textarea', ['name' => 'description', 'label' => 'Description', 'rows' => 4]);
echo '<button type="button" onclick="generateDescription(this)" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-[0.8125rem] rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text -mt-3 mb-5">Generate with AI</button>';
echo '<div class="grid grid-cols-2 gap-4">';
component('form/select', ['name' => 'category_id', 'label' => 'Category', 'options' => $katMap ?? [], 'placeholder' => '-- Select Category --', 'required' => true]);
component('form/select', ['name' => 'event_id', 'label' => 'Event', 'options' => $eventMap ?? [], 'placeholder' => '-- Select Event --', 'required' => true]);
echo '</div>';
echo '<div class="grid grid-cols-2 gap-4">';
component('form/select', ['name' => 'status', 'label' => 'Status', 'options' => ['active' => 'Active', 'inactive' => 'Inactive'], 'required' => true]);
echo '</div>';
echo '<div class="grid grid-cols-2 gap-4">';
component('form/input', ['name' => 'price', 'label' => 'Price (Rp)', 'type' => 'number', 'required' => true]);
component('form/input', ['name' => 'minimum_portions', 'label' => 'Minimum Portions', 'type' => 'number', 'value' => '1', 'min' => '1', 'required' => true]);
echo '</div>';
echo '<div data-image-preview="image" data-image-dir="menus" class="hidden mb-2"></div>';
component('form/input', ['name' => 'image', 'label' => 'Menu Image (Optional)', 'type' => 'file', 'accept' => 'image/jpeg,image/png,image/webp']);
$createFormContent = ob_get_clean();
require __DIR__ . '/../partials/create-modal.php';
?>

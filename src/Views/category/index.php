<?php $headerTitle = $title;
$createModal = 'createCategoryModal'; ?>
<?php require __DIR__ . '/../partials/table-header.php' ?>
<?php $searchPlaceholder = __('search_category');
$showFilter = false;
$search = $search ?? ''; ?>
<?php require __DIR__ . '/../partials/table-search.php' ?>
<div id="table-container" class="bg-[#18181b] border border-border rounded-xl overflow-hidden">
    <?php if (empty($categories)): ?>
        <div
            class="col-span-full bg-card-bg border border-dashed border-border rounded-[20px] px-6 py-12 text-center text-muted">
            <p><?= __('no_categories') ?></p>
        </div>
    <?php else: ?>
        <?php require __DIR__ . '/../partials/table-sort.php' ?>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th
                            class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">
                            <a href="<?= $sortUrl('id') ?>"
                                class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline"><?= __('id') ?><?= $sortIcon('id') ?></a>
                        </th>
                        <th
                            class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">
                            <a href="<?= $sortUrl('name') ?>"
                                class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline"><?= __('name') ?><?= $sortIcon('name') ?></a>
                        </th>
                        <th
                            class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">
                            <a href="<?= $sortUrl('slug') ?>"
                                class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline"><?= __('slug') ?><?= $sortIcon('slug') ?></a>
                        </th>
                        <th
                            class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">
                            <?= __('menus') ?></th>
                        <th
                            class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">
                            <?= __('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr class="cursor-pointer hover:bg-white/[0.03]"
                            onclick="if(!event.target.closest('a,button,form')) location.href='/menus?category_id=<?= (int) $cat['id'] ?>'">
                            <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text">
                                <?= $cat['id'] ?></td>
                            <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text">
                                <?= e($cat['name']) ?></td>
                            <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text">
                                <?= e($cat['slug']) ?></td>
                            <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text">
                                <?= (int) $cat['menu_count'] ?></td>
                            <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text">
                                <div class="flex items-center gap-2">
                                    <a href="#" data-edit="categories" data-id="<?= $cat['id'] ?>"
                                        class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-[0.8125rem] rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text"><?= __('edit') ?></a>
                                    <form action="/categories/<?= $cat['id'] ?>/delete" method="POST" class="inline">
                                        <?= \App\Core\Csrf::field() ?>
                                        <button type="submit"
                                            data-modal-confirm="<?= __('confirm_delete_category') ?>"
                                            class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-[0.8125rem] rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-danger text-white border-danger hover:bg-danger-hover hover:border-danger-hover hover:text-white"><?= __('delete') ?></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php $totalLabel = __('categories');
        require __DIR__ . '/../partials/table-pagination.php' ?>
    <?php endif; ?>
</div>
<?php
$createModalId = 'createCategoryModal';
$createTitle = __('add_category');
$createAction = '/categories';
$createSubmitText = __('save_category');
ob_start();
component('form/input', ['name' => 'name', 'label' => __('category_name'), 'required' => true]);
$createFormContent = ob_get_clean();
require __DIR__ . '/../partials/create-modal.php';
?>
<?php $headerTitle = 'Events'; $createUrl = '/events/create'; ?>
<?php require __DIR__ . '/../partials/table-header.php' ?>
<?php
$searchPlaceholder = 'Search event name or date...';
$search = $search ?? '';
$filters = [
    [
        'label' => 'Status',
        'name' => 'status',
        'options' => ['' => 'All Statuses', 'active' => 'Active', 'inactive' => 'Inactive'],
        'selected' => $filters['status'] ?? '',
    ],
];
?>
<?php require __DIR__ . '/../partials/table-search.php' ?>
<div id="table-container" class="bg-[#18181b] border border-border rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr>
<?php require __DIR__ . '/../partials/table-sort.php' ?>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('id') ?>" class="text-muted hover:text-gold transition-colors no-underline">ID<?= $sortIcon('id') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('name') ?>" class="text-muted hover:text-gold transition-colors no-underline">Name<?= $sortIcon('name') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">Duration</th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('status') ?>" class="text-muted hover:text-gold transition-colors no-underline">Status<?= $sortIcon('status') ?></a></th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">Menus</th>
                    <th class="bg-black/30 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text text-center p-8">No events found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($events as $event): ?>
                        <tr class="cursor-pointer hover:bg-white/[0.03]" onclick="location.href='/menus?event_id=<?= e($event['id']) ?>'">
                            <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text"><?= e($event['id']) ?></td>
                            <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text"><?= e($event['name']) ?></td>
                            <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text">
                                <?= e(date('d M Y', strtotime($event['start_date']))) ?> - 
                                <?= e(date('d M Y', strtotime($event['end_date']))) ?>
                            </td>
                            <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text">
                                <?php if ($event['status'] === 'active'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[var(--color-success)] text-white">Active</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[var(--text-muted)] text-white">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text"><?= (int)$event['menu_count'] ?></td>
                            <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text" onclick="event.stopPropagation()">
                                <div class="flex gap-2">
                                    <a href="/events/<?= e($event['id']) ?>/edit" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-[0.8125rem] rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">Edit</a>
                                    <form method="POST" action="/events/<?= e($event['id']) ?>/delete" class="inline">
                                        <?= \App\Core\Csrf::field() ?>
                                        <button type="submit" data-modal-confirm="Are you sure you want to delete this event?" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-[0.8125rem] rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-danger text-white border-danger hover:bg-danger-hover hover:border-danger-hover hover:text-white">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <?php $paginationCompact = true; require __DIR__ . '/../partials/table-pagination.php' ?>
    </div>
</div>

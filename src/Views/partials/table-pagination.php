<?php if (($pagination['last_page'] ?? 1) > 1): ?>
<div class="flex items-center px-6 py-4 border-t border-border <?= ($paginationCompact ?? false) ? 'justify-center gap-2' : 'justify-between' ?>">
    <?php if (!($paginationCompact ?? false)): ?>
    <div class="text-[0.8125rem] text-muted">
        Showing page <?= (int)$pagination['current_page'] ?> of <?= (int)$pagination['last_page'] ?> (Total: <?= (int)$pagination['total'] ?> <?= e($totalLabel ?? 'items') ?>)
    </div>
    <?php endif; ?>
    <div class="flex gap-1">
        <?php if (!($paginationCompact ?? false)): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => max(1, $pagination['current_page'] - 1)])) ?>" class="pagination-link inline-flex items-center justify-center min-w-[2rem] h-8 px-2 rounded-lg text-[0.8125rem] font-medium text-muted border border-border hover:bg-white/5 hover:text-text<?= $pagination['current_page'] <= 1 ? ' opacity-50 pointer-events-none' : '' ?>">&laquo; Prev</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="pagination-link inline-flex items-center justify-center min-w-[2rem] h-8 px-2 rounded-lg text-[0.8125rem] font-medium text-muted border border-border hover:bg-white/5 hover:text-text<?= $i === $pagination['current_page'] ? ' bg-primary text-white border-primary' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if (!($paginationCompact ?? false)): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => min($pagination['last_page'], $pagination['current_page'] + 1)])) ?>" class="pagination-link inline-flex items-center justify-center min-w-[2rem] h-8 px-2 rounded-lg text-[0.8125rem] font-medium text-muted border border-border hover:bg-white/5 hover:text-text<?= $pagination['current_page'] >= $pagination['last_page'] ? ' opacity-50 pointer-events-none' : '' ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

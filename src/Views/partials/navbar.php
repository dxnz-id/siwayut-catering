<?php $navUser = \App\Core\Session::get('user'); ?>
<header class="h-[64px] bg-[#111113e6] border-b border-border flex items-center justify-between px-8 sticky top-0 z-40 backdrop-blur-[12px]">
    <div class="text-lg font-semibold text-text"><?= \App\Core\View::e($title ?? '') ?></div>
    <?php if ($navUser): ?>
    <div class="flex items-center gap-3">
        <span class="text-sm font-medium text-text"><?= \App\Core\View::e($navUser['name']) ?></span>
        <span class="text-xs text-gold bg-primary-light border border-[rgba(229,142,38,0.2)] px-2 py-0.5 rounded-full font-medium"><?= \App\Core\View::e(ucfirst($navUser['role'])) ?></span>
    </div>
    <?php endif; ?>
</header>

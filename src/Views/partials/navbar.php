<?php $navUser = \App\Core\Session::get('user'); ?>
<header
    class="h-[64px] bg-[#111113e6] border-b border-border flex items-center justify-between px-8 sticky top-0 z-40 backdrop-blur-[12px]">
    <div class="text-lg font-semibold text-text"><?= \App\Core\View::e($title ?? '') ?></div>
    <div class="flex items-center gap-4">
        <?php component('lang-switcher') ?>
        <?php if ($navUser): ?>
            <div class="relative group">
                <?php
                $initials = '';
                foreach (explode(' ', trim($navUser['name'])) as $word) {
                    $initials .= mb_strtoupper(mb_substr($word, 0, 1));
                    if (strlen($initials) >= 2)
                        break;
                }
                ?>
                <?php if ($navUser['avatar'] ?? null): ?>
                    <img src="/uploads/<?= \App\Core\View::e($navUser['avatar']) ?>" class="navbar-avatar-img w-9 h-9 rounded-full object-cover border border-[rgba(229,142,38,0.4)] cursor-pointer select-none
                        group-hover:border-[rgba(229,142,38,0.7)] group-hover:shadow-[0_0_12px_rgba(229,142,38,0.25)]
                        transition-all duration-200" alt="Avatar">
                <?php else: ?>
                    <div class="w-9 h-9 rounded-full bg-[rgba(229,142,38,0.15)] border border-white/10 flex items-center justify-center cursor-pointer select-none
                        group-hover:border-[rgba(229,142,38,0.7)] group-hover:bg-[rgba(229,142,38,0.22)] group-hover:shadow-[0_0_12px_rgba(229,142,38,0.25)]
                        transition-all duration-200">
                        <span class="text-xs font-semibold text-gold leading-none"><?= \App\Core\View::e($initials) ?></span>
                    </div>
                <?php endif; ?>
                <!-- Dropdown -->
                <div class="absolute right-0 top-full mt-2 w-48 bg-[#18181b] border border-white/10 rounded-xl shadow-xl
                        opacity-0 invisible translate-y-[-4px]
                        group-hover:opacity-100 group-hover:visible group-hover:translate-y-0
                        transition-all duration-150 ease-out z-50 overflow-hidden">
                    <a href="/profile"
                        class="flex items-center gap-3 px-4 py-3 text-sm text-white/80 hover:text-white hover:bg-white/[0.06] no-underline transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <?= __('profile') ?>
                    </a>
                    <div class="border-t border-white/5"></div>
                    <form method="POST" action="/logout" class="block">
                        <?= csrf_field() ?>
                        <button type="submit"
                            class="flex items-center gap-3 w-full px-4 py-3 text-sm text-white/80 hover:text-red-400 hover:bg-white/[0.06] transition-colors cursor-pointer bg-transparent border-0 font-body text-left">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                            <?= __('logout') ?>
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</header>
<aside class="w-[260px] bg-sidebar-bg border-r border-border text-sidebar-active fixed top-0 left-0 bottom-0 overflow-y-auto z-50 flex flex-col transition-transform duration-150">
    <div class="px-6 py-5 text-xl font-bold text-white border-b border-border tracking-tight flex items-center gap-2">
        <span class="drop-shadow-[0_0_8px_var(--accent-gold-glow)]">🍲</span>
        <span class="font-display bg-gradient-to-r from-white to-gold bg-clip-text text-transparent text-xl tracking-tight"><?= htmlspecialchars((string)APP_NAME, ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <nav class="py-4 flex-1">
        <a href="/dashboard" class="flex items-center gap-3 px-6 py-2.5 text-sm font-medium transition-all duration-150 border-l-[3px] border-transparent hover:bg-white/[0.06] text-sidebar-active hover:text-gold hover:border-l-gold<?= (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/dashboard')) ? ' bg-white/[0.06] text-gold border-l-gold' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0 opacity-70">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6Zm0 9.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6Zm0 9.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
            </svg>
            <?= __('dashboard') ?>
        </a>
        <a href="/categories" class="flex items-center gap-3 px-6 py-2.5 text-sm font-medium transition-all duration-150 border-l-[3px] border-transparent hover:bg-white/[0.06] text-sidebar-active hover:text-gold hover:border-l-gold<?= (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/categories')) ? ' bg-white/[0.06] text-gold border-l-gold' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0 opacity-70 group-hover:opacity-100">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
            </svg>
            <?= __('categories') ?>
        </a>
        <a href="/events" class="flex items-center gap-3 px-6 py-2.5 text-sm font-medium transition-all duration-150 border-l-[3px] border-transparent hover:bg-white/[0.06] text-sidebar-active hover:text-gold hover:border-l-gold<?= (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/events')) ? ' bg-white/[0.06] text-gold border-l-gold' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0 opacity-70">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            <?= __('events') ?>
        </a>
        <a href="/menus" class="flex items-center gap-3 px-6 py-2.5 text-sm font-medium transition-all duration-150 border-l-[3px] border-transparent hover:bg-white/[0.06] text-sidebar-active hover:text-gold hover:border-l-gold<?= (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/menus')) ? ' bg-white/[0.06] text-gold border-l-gold' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0 opacity-70">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
            </svg>
            <?= __('menus') ?>
        </a>
        <a href="/orders" class="flex items-center gap-3 px-6 py-2.5 text-sm font-medium transition-all duration-150 border-l-[3px] border-transparent hover:bg-white/[0.06] text-sidebar-active hover:text-gold hover:border-l-gold<?= (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/orders')) ? ' bg-white/[0.06] text-gold border-l-gold' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0 opacity-70">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            </svg>
            <?= __('orders') ?>
        </a>
        <a href="/users" class="flex items-center gap-3 px-6 py-2.5 text-sm font-medium transition-all duration-150 border-l-[3px] border-transparent hover:bg-white/[0.06] text-sidebar-active hover:text-gold hover:border-l-gold<?= (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/users')) ? ' bg-white/[0.06] text-gold border-l-gold' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0 opacity-70">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>
            <?= __('users') ?>
        </a>
    </nav>
    <div class="px-6 pt-2 pb-1">
        <div class="text-[10px] text-muted uppercase tracking-widest font-medium"><?= __('reports') ?></div>
    </div>
    <nav class="pb-2">
        <a href="/reports/revenue" class="flex items-center gap-3 px-6 py-2 text-sm font-medium transition-all duration-150 border-l-[3px] border-transparent hover:bg-white/[0.06] text-sidebar-active hover:text-gold hover:border-l-gold<?= (in_array(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), ['/reports/revenue', '/reports/revenue/export'])) ? ' bg-white/[0.06] text-gold border-l-gold' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0 opacity-70">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
            </svg>
            <?= __('revenue_report') ?>
        </a>
        <a href="/reports/menu-revenue" class="flex items-center gap-3 px-6 py-2 text-sm font-medium transition-all duration-150 border-l-[3px] border-transparent hover:bg-white/[0.06] text-sidebar-active hover:text-gold hover:border-l-gold<?= (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/reports/menu-revenue')) ? ' bg-white/[0.06] text-gold border-l-gold' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0 opacity-70">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 3H12v7h7V8.25A5.25 5.25 0 0 0 13.5 3Z" />
            </svg>
            <?= __('menu_revenue') ?>
        </a>
    </nav>
</aside>

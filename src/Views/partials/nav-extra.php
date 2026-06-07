<?php
/**
 * Nav-extra partial for public layout.
 *
 * Accepts ONE of these data modes:
 *   $navMode = 'back'          → history.back() button
 *   $navMode = 'back_home'     → link to /
 *   $navMode = 'back_logout'   → back button + logout form
 *   $navMode = 'track_another' → link to /track-order
 *   $navMode = 'session'       → session-aware (dashboard / my-orders + logout / login)
 *   $navMode = 'custom'        → use $navHref + $navLabel
 */

$btnClass = 'inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-medium no-underline bg-white/5 border border-border text-text backdrop-blur-[8px] hover:bg-gold hover:border-gold hover:shadow-[0_0_15px_var(--color-gold-glow)] transition-all duration-300';
$logoutBtnClass = 'inline-flex items-center gap-2 px-3 py-2 rounded-full text-sm font-medium no-underline bg-transparent border border-transparent text-muted hover:text-danger hover:border-danger/30 hover:bg-danger/10 transition-all duration-300 cursor-pointer';

$navMode = $navMode ?? 'session';

switch ($navMode) {
    case 'back':
        echo '<a href="javascript:void(0)" onclick="history.back();return false" class="' . $btnClass . '">' . __('back') . '</a>';
        break;

    case 'back_home':
        echo '<a href="/" class="' . $btnClass . '">' . __('back_home') . '</a>';
        break;

    case 'back_logout':
        echo '<a href="javascript:void(0)" onclick="history.back();return false" class="' . $btnClass . '">' . __('back') . '</a>';
        echo '<form method="POST" action="/logout" class="m-0 p-0 inline">';
        echo \App\Core\Csrf::field();
        echo '<button type="submit" class="' . $logoutBtnClass . '">' . __('logout') . '</button>';
        echo '</form>';
        break;

    case 'track_another':
        echo '<a href="/track-order" class="' . $btnClass . '">' . __('track_another') . '</a>';
        break;

    case 'custom':
        echo '<a href="' . e($navHref ?? '/') . '" class="' . $btnClass . '">' . e($navLabel ?? __('back')) . '</a>';
        break;

    case 'session':
    default:
        $navUser = $navUser ?? \App\Core\Session::get('user');
        if ($navUser) {
            if (($navUser['role'] ?? '') === 'admin') {
                echo '<a href="/orders" class="' . $btnClass . '">' . __('dashboard') . '</a>';
            } else {
                echo '<a href="/my-orders" class="' . $btnClass . '">' . __('my_orders') . '</a>';
                echo '<form method="POST" action="/logout" class="m-0 p-0 inline">';
                echo \App\Core\Csrf::field();
                echo '<button type="submit" class="' . $logoutBtnClass . '">' . __('logout') . '</button>';
                echo '</form>';
            }
        } else {
            echo '<a href="/auth" class="' . $btnClass . '">' . __('login') . '</a>';
        }
        break;
}

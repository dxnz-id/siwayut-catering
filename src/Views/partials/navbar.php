<!-- File: src/Views/partials/navbar.php -->
<?php $navUser = \App\Core\Session::get('user'); ?>
<header class="navbar">
    <div class="navbar-title"><?= \App\Core\View::e($title ?? '') ?></div>
    <?php if ($navUser): ?>
    <div class="navbar-user">
        <span class="navbar-user-name"><?= \App\Core\View::e($navUser['name']) ?></span>
        <span class="navbar-user-role"><?= \App\Core\View::e(ucfirst($navUser['role'])) ?></span>
    </div>
    <?php endif; ?>
</header>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $titleSuffix = APP_NAME;
    if (\App\Core\Turnstile::enabled()) {
        $headExtra = '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
    }
    require __DIR__ . '/../partials/head.php';
    ?>
</head>

<body>
    <div class="parallax-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>
    <div class="min-h-screen flex items-center justify-center p-4 relative z-1">
        <?= $content ?? '' ?>
    </div>
    <script src="/assets/js/modules/turnstile.js"></script>
    <script src="/assets/js/modules/toast.js"></script>
    <script src="/assets/js/modules/file-upload.js"></script>
    <script src="/assets/js/modules/progressive-image.js"></script>
    <script src="/assets/js/modules/load-more-menu.js"></script>
    <script src="/assets/js/modules/modal.js"></script>
    <script src="/assets/js/modules/ai-description.js"></script>
    <?php component('modal') ?>
    <?php component('toast') ?>
    <script src="/assets/js/app.js"></script>
</body>

</html>
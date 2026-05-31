<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars((string) ($title ?? 'Login'), ENT_QUOTES, 'UTF-8') ?> —
        <?= htmlspecialchars((string) APP_NAME, ENT_QUOTES, 'UTF-8') ?>
    </title>
    <link rel="stylesheet" href="/assets/css/fonts.css">
    <link rel="stylesheet" href="/assets/css/app.css?v=2">
    <?php if (\App\Core\Turnstile::enabled()): ?>
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <?php endif; ?>
    <link rel="icon" type="image/svg+xml" href="/assets/icon/favicon.svg">
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
    <sc ript src="/assets/js/app.js">
        </script>
</body>

</html>
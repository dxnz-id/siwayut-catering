<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars((string)($title ?? ''), ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars((string)APP_NAME, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css?v=2">
</head>
<body>
    <div class="parallax-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>
    <div class="flex min-h-screen relative z-1">
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>
        <div class="flex-1 ml-[260px] flex flex-col">
            <?php require __DIR__ . '/../partials/navbar.php'; ?>
            <main class="flex-1 p-8">
                <div id="main-content">
                    <?php require __DIR__ . '/../partials/flash.php'; ?>
                    <?= $content ?? '' ?>
                </div>
            </main>
        </div>
    </div>
    <script src="/assets/js/modules/turnstile.js"></script>
    <script src="/assets/js/modules/file-upload.js"></script>
    <script src="/assets/js/modules/progressive-image.js"></script>
    <script src="/assets/js/modules/load-more-menu.js"></script>
    <script src="/assets/js/modules/ai-description.js"></script>
    <script src="/assets/js/app.js"></script>
    <script>
    (function() {
        var container = document.getElementById('table-container');
        if (!container) return;

        function load(url) {
            history.replaceState({ url: url }, '', url);
            container.innerHTML = '<div class="flex items-center justify-center py-12"><div class="w-8 h-8 border-2 border-gold border-t-transparent rounded-full animate-spin"></div></div>';
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.text(); })
                .then(function(html) {
                    var tmp = document.createElement('div');
                    tmp.innerHTML = html;
                    var newContent = tmp.querySelector('#table-container');
                    if (newContent) {
                        container.innerHTML = newContent.innerHTML;
                        attach();
                    }
                });
        }

        function attach() {
            // Search debounce
            document.querySelectorAll('input[name="search"]').forEach(function(input) {
                if (input.dataset.liveSearch) return;
                input.dataset.liveSearch = '1';
                var timer = null;
                input.addEventListener('input', function() {
                    clearTimeout(timer);
                    timer = setTimeout(function() {
                        var form = input.closest('form');
                        if (!form) return;
                        var pageInput = form.querySelector('input[name="page"]');
                        if (pageInput) pageInput.value = '1';
                        var data = new FormData(form);
                        load(form.action + '?' + new URLSearchParams(data).toString());
                    }, 1000);
                });
            });

            // Intercept form submissions (filter Apply, categories search)
            document.querySelectorAll('form').forEach(function(form) {
                if (form.dataset.liveForm) return;
                var hasSearch = form.querySelector('input[name="search"]');
                var hasFilter = form.querySelector('select[name="status"], select[name="payment_status"], select[name="category_id"], select[name="role"]');
                if (!hasSearch && !hasFilter) return;
                form.dataset.liveForm = '1';
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var data = new FormData(form);
                    load(form.action + '?' + new URLSearchParams(data).toString());
                });
            });

            // Intercept pagination links
            document.querySelectorAll('.pagination-link, a[href*="page="]').forEach(function(a) {
                if (a.dataset.livePage) return;
                if (a.classList.contains('disabled')) return;
                a.dataset.livePage = '1';
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    load(a.getAttribute('href'));
                });
            });
        }

        window.addEventListener('popstate', function(e) {
            if (e.state && e.state.url) load(e.state.url);
        });

        attach();
    })();
    </script>
</body>
</html>

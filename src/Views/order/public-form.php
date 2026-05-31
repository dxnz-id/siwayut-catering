<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::e($title ?? 'Order Catering — Siwayut Catering') ?></title>
    <link rel="stylesheet" href="/assets/css/fonts.css">
    <link rel="stylesheet" href="/assets/css/app.css?v=3">

    <link rel="icon" type="image/svg+xml" href="/assets/icon/favicon.svg">
</head>

<body class="bg-bg text-text min-h-screen leading-relaxed font-body overflow-x-hidden bg-fixed bg-[radial-gradient(circle_at_15%_25%,rgba(229,142,38,0.12)_0%,transparent_45%),radial-gradient(circle_at_85%_75%,rgba(234,32,39,0.08)_0%,transparent_45%)]">

    <header class="sticky top-0 z-[100] bg-bg/60 backdrop-blur-[12px] border-b border-border py-4">
        <div class="max-w-[1200px] mx-auto px-6 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 no-underline text-text">
                <span class="text-[1.8rem] drop-shadow-[0_0_8px_var(--accent-gold-glow)]">🍲</span>
                <span class="font-display text-2xl font-bold tracking-tight bg-gradient-to-r from-white to-gold bg-clip-text text-transparent">Siwayut Catering</span>
            </a>
            <a href="/" class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-medium no-underline bg-white/5 border border-border text-text backdrop-blur-[8px] hover:bg-gold hover:border-gold hover:shadow-[0_0_15px_var(--color-gold-glow)] transition-all duration-300"><?= __('back_home') ?></a>
        </div>
    </header>

    <main class="max-w-[600px] mx-auto px-6">
        <div class="bg-card-bg border border-border backdrop-blur-[16px] rounded-xl p-10 px-8 max-md:p-6 max-md:px-5 mt-10">
            <h1 class="text-center text-[1.75rem] max-md:text-[1.4rem] font-bold mb-2 font-display"><?= __('order_catering') ?></h1>
            <p class="text-center text-muted text-sm mb-8"><?= __('order_form_desc') ?></p>

            <form action="/order-form" method="POST">
                <?= \App\Core\Csrf::field() ?>

                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium mb-1 text-muted"><?= __('full_name') ?></label>
                    <input type="text" id="name" name="name"
                        class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20"
                        placeholder="<?= __('enter_name') ?>"
                        value="<?= \App\Core\View::e(old('name')) ?>" required>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium mb-1 text-muted"><?= __('menu_items') ?></label>
                    <div id="menu-items-container">
                        <div class="menu-item-row flex items-start gap-2" data-index="0">
                            <div class="flex-1">
                                <select name="items[0][menu_id]" required
                                    class="w-full px-4 py-3 bg-white/5 text-text border border-border rounded-xl font-body leading-relaxed text-[0.95rem] outline-none transition-all duration-300 focus:border-gold focus:ring-[3px] focus:ring-gold/20">
                                    <option value=""><?= __('select_menu') ?></option>
                                    <?php foreach ($menus as $m): ?>
                                        <option value="<?= (int) $m['id'] ?>">
                                            <?= \App\Core\View::e($m['name']) ?> &mdash; Rp
                                            <?= number_format((float) $m['price'], 0, ',', '.') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="w-28 shrink-0">
                                <input type="number" name="items[0][quantity]" value="1" min="1" required
                                    class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20"
                                    placeholder="<?= e(__('qty')) ?>">
                            </div>
                            <button type="button" class="remove-menu-item mt-1 w-9 h-9 flex items-center justify-center rounded-lg text-muted hover:text-danger hover:bg-danger/10 transition-all duration-150 cursor-pointer border-0 bg-transparent shrink-0 hidden" data-index="0" title="<?= e(__('remove')) ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                    <button type="button" id="add-menu-item"
                        class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-[0.8125rem] rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text mt-2"><?= __('add_another_menu') ?></button>
                </div>

                <div class="mb-5">
                    <label for="event_date" class="block text-sm font-medium mb-1 text-muted"><?= __('event_date') ?></label>
                    <input type="datetime-local" id="event_date" name="event_date"
                        class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20"
                        value="<?= \App\Core\View::e(old('event_date')) ?>" required>
                </div>

                <div class="mb-5">
                    <label for="address" class="block text-sm font-medium mb-1 text-muted"><?= __('delivery_address') ?></label>
                    <textarea id="address" name="address"
                        class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20 min-h-[100px] resize-vertical"
                        placeholder="<?= __('enter_address') ?>"
                        required><?= \App\Core\View::e(old('address')) ?></textarea>
                </div>

                <div class="mb-5">
                    <label for="notes" class="block text-sm font-medium mb-1 text-muted"><?= __('notes') ?> <span class="text-muted">(<?= __('optional') ?>)</span></label>
                    <textarea id="notes" name="notes"
                        class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20 min-h-[100px] resize-vertical"
                        placeholder="<?= __('notes_placeholder') ?>"><?= \App\Core\View::e(old('notes')) ?></textarea>
                </div>



                <button type="submit" id="submit-btn"
                    class="w-full py-[0.85rem] bg-gold border border-gold rounded-xl text-white text-base font-semibold cursor-pointer transition-all duration-300 flex items-center justify-center gap-2 shadow-[0_0_15px_var(--color-gold-glow)] hover:bg-primary-hover hover:border-primary-hover hover:-translate-y-0.5 hover:shadow-[0_0_25px_var(--color-gold-glow)] font-body">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                        fill="currentColor">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                    </svg>
                    <?= __('send_via_whatsapp') ?>
                </button>
            </form>
        </div>
    </main>
    <?php component('footer') ?>
    <?php
    $flashError = \App\Core\Session::getFlash('error');
    $pageFlashes = [];
    if ($flashError) $pageFlashes[] = ['type' => 'error', 'message' => $flashError];
    ?>
    <script src="/assets/js/modules/toast.js"></script>
    <script src="/assets/js/modules/file-upload.js"></script>
    <script src="/assets/js/modules/progressive-image.js"></script>
    <script src="/assets/js/modules/load-more-menu.js"></script>
    <script src="/assets/js/modules/ai-description.js"></script>
    <?php component('toast', ['flashes' => $pageFlashes]) ?>
    <script src="/assets/js/app.js"></script>
    <script>
<?php $menuJson = json_encode(array_map(function($m) {
    return ['id' => $m['id'], 'name' => $m['name'], 'price' => $m['price']];
}, $menus), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP); ?>
    (function() {
        'use strict';
        var container = document.getElementById('menu-items-container');
        var addBtn = document.getElementById('add-menu-item');
        if (!container || !addBtn) return;

        var menuList = <?= $menuJson ?>;

        function buildSelect(index) {
            var select = document.createElement('select');
            select.name = 'items[' + index + '][menu_id]';
            select.required = true;
            select.className = 'w-full px-4 py-3 bg-white/5 text-text border border-border rounded-xl font-body leading-relaxed text-[0.95rem] outline-none transition-all duration-300 focus:border-gold focus:ring-[3px] focus:ring-gold/20';
            var emptyOpt = document.createElement('option');
            emptyOpt.value = '';
            emptyOpt.textContent = '<?= e(__('select_menu')) ?>';
            select.appendChild(emptyOpt);
            for (var i = 0; i < menuList.length; i++) {
                var m = menuList[i];
                var opt = document.createElement('option');
                opt.value = m.id;
                opt.textContent = m.name + ' \u2014 ' + 'Rp ' + Number(m.price).toLocaleString('id-ID');
                select.appendChild(opt);
            }
            return select.outerHTML;
        }

        function updateIndices() {
            var rows = container.querySelectorAll('.menu-item-row');
            rows.forEach(function(row, idx) {
                row.dataset.index = idx;
                var sel = row.querySelector('select');
                if (sel) sel.name = 'items[' + idx + '][menu_id]';
                var qty = row.querySelector('input[type="number"]');
                if (qty) qty.name = 'items[' + idx + '][quantity]';
                var removeBtn = row.querySelector('.remove-menu-item');
                if (removeBtn) {
                    removeBtn.dataset.index = idx;
                    removeBtn.classList.toggle('hidden', idx === 0);
                }
            });
        }

        addBtn.addEventListener('click', function() {
            var rows = container.querySelectorAll('.menu-item-row');
            var newIndex = rows.length;
            var div = document.createElement('div');
            div.className = 'menu-item-row flex items-start gap-2';
            div.dataset.index = newIndex;
            div.innerHTML =
                '<div class="flex-1">' +
                buildSelect(newIndex) +
                '</div>' +
                '<div class="w-28 shrink-0">' +
                '<input type="number" name="items[' + newIndex + '][quantity]" value="1" min="1" required class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl font-body leading-relaxed text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20" placeholder="<?= e(__('qty')) ?>">' +
                '</div>' +
                '<button type="button" class="remove-menu-item mt-1 w-9 h-9 flex items-center justify-center rounded-lg text-muted hover:text-danger hover:bg-danger/10 transition-all duration-150 cursor-pointer border-0 bg-transparent shrink-0" data-index="' + newIndex + '" title="<?= e(__('remove')) ?>">' +
                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></button>';
            container.appendChild(div);
            updateIndices();
        });

        container.addEventListener('click', function(e) {
            var btn = e.target.closest('.remove-menu-item');
            if (!btn) return;
            var row = btn.closest('.menu-item-row');
            if (row) {
                row.remove();
                updateIndices();
            }
        });

        updateIndices();
    })();
    </script>
</body>

</html>

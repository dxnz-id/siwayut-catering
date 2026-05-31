<?php $activeTab = ($activeTab ?? 'login') === 'register' ? 'register' : 'login'; ?>
<section class="w-full px-4 py-8 md:py-10">
    <div class="bg-[#18181b] border border-white/10 rounded-[24px] shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] w-full max-w-[420px] mx-auto p-10">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-extrabold text-white mb-1 font-display bg-gradient-to-r from-white to-gold bg-clip-text text-transparent">
                <?= \App\Core\View::e(APP_NAME) ?>
            </h1>
            <p id="auth-subtitle" class="text-sm text-muted transition-all duration-200">
                <?= __('sign_in_subtitle') ?>
            </p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="flex items-center gap-2 px-4 py-3 rounded-lg text-sm mb-4 bg-error-bg text-[#fca5a5] border border-error-border">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                <?= \App\Core\View::e($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="flex items-center gap-2 px-4 py-3 rounded-lg text-sm mb-4 bg-success-bg text-[#86efac] border border-success-border">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <?= \App\Core\View::e($success) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-2 gap-2 mb-6 p-1 rounded-xl bg-white/5 border border-border">
            <button type="button" data-auth-tab-btn="login" class="auth-tab-btn px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150">
                <?= __('login') ?>
            </button>
            <button type="button" data-auth-tab-btn="register" class="auth-tab-btn px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150">
                <?= __('register') ?>
            </button>
        </div>

        <div id="auth-panel-wrap" class="relative overflow-hidden transition-[height] duration-300 ease-out">
            <div data-auth-tab-panel="login">
                <form method="POST" action="/auth/login">
                    <?= \App\Core\Csrf::field() ?>

                    <div class="mb-5">
                        <label for="login-email" class="block text-sm font-medium text-text mb-1.5"><?= __('email_address') ?></label>
                        <input type="email" id="login-email" name="email"
                            class="w-full px-3 py-3 border border-border rounded-lg text-sm leading-relaxed text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light"
                            value="<?= \App\Core\View::e(old('email')) ?>" placeholder="admin@example.com" required autofocus>
                    </div>

                    <div class="mb-5">
                        <label for="login-password" class="block text-sm font-medium text-text mb-1.5"><?= __('password') ?></label>
                        <input type="password" id="login-password" name="password"
                            class="w-full px-3 py-3 border border-border rounded-lg text-sm leading-relaxed text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light"
                            placeholder="••••••••" required>
                    </div>

                    <?php if (\App\Core\Turnstile::enabled()): ?>
                        <div class="mt-6 flex justify-center">
                            <?= \App\Core\Turnstile::widget() ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" data-turnstile-submit="1" <?= \App\Core\Turnstile::enabled() ? 'disabled' : '' ?>
                        class="mt-6 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white w-full">
                        <?= __('sign_in') ?>
                    </button>
                </form>
            </div>

            <div data-auth-tab-panel="register" class="hidden">
                <form method="POST" action="/auth/register">
                    <?= \App\Core\Csrf::field() ?>

                    <div class="mb-5">
                        <label for="register-name" class="block text-sm font-medium text-text mb-1.5"><?= __('full_name') ?></label>
                        <input type="text" id="register-name" name="name"
                            class="w-full px-3 py-3 border border-border rounded-lg text-sm leading-relaxed text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light"
                            value="<?= \App\Core\View::e(old('name')) ?>" placeholder="Full name" required>
                    </div>

                    <div class="mb-5">
                        <label for="register-email" class="block text-sm font-medium text-text mb-1.5"><?= __('email_address') ?></label>
                        <input type="email" id="register-email" name="email"
                            class="w-full px-3 py-3 border border-border rounded-lg text-sm leading-relaxed text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light"
                            value="<?= \App\Core\View::e(old('email')) ?>" placeholder="user@example.com" required>
                    </div>

                    <div class="mb-5">
                        <label for="register-phone" class="block text-sm font-medium text-text mb-1.5"><?= __('phone_number') ?></label>
                        <input type="tel" id="register-phone" name="phone"
                            class="w-full px-3 py-3 border border-border rounded-lg text-sm leading-relaxed text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light"
                            value="<?= \App\Core\View::e(old('phone')) ?>" placeholder="08xxxxxxxxxx" required>
                    </div>

                    <div class="mb-5">
                        <label for="register-password" class="block text-sm font-medium text-text mb-1.5"><?= __('password') ?></label>
                        <input type="password" id="register-password" name="password"
                            class="w-full px-3 py-3 border border-border rounded-lg text-sm leading-relaxed text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light"
                            placeholder="<?= __('min_chars', ['min' => 8]) ?>" required>
                    </div>

                    <div class="mb-5">
                        <label for="register-password-confirmation" class="block text-sm font-medium text-text mb-1.5"><?= __('confirm_password') ?></label>
                        <input type="password" id="register-password-confirmation" name="password_confirmation"
                            class="w-full px-3 py-3 border border-border rounded-lg text-sm leading-relaxed text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light"
                            placeholder="<?= __('repeat_password') ?>" required>
                    </div>

                    <?php if (\App\Core\Turnstile::enabled()): ?>
                        <div class="mt-6 flex justify-center">
                            <?= \App\Core\Turnstile::widget() ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" data-turnstile-submit="1" <?= \App\Core\Turnstile::enabled() ? 'disabled' : '' ?>
                        class="mt-6 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white w-full">
                        <?= __('create_account') ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    (function() {
        var activeTab = <?= json_encode($activeTab) ?>;
        var buttons = document.querySelectorAll('[data-auth-tab-btn]');
        var panels = document.querySelectorAll('[data-auth-tab-panel]');
        var panelWrap = document.getElementById('auth-panel-wrap');
        var subtitleEl = document.getElementById('auth-subtitle');
        var currentTab = null;
        var subtitles = {
            login: <?= json_encode(__('sign_in_subtitle')) ?>,
            register: <?= json_encode(__('create_account')) ?>
        };

        function updateTabButton(tab) {
            buttons.forEach(function(btn) {
                var isActive = btn.getAttribute('data-auth-tab-btn') === tab;
                btn.classList.toggle('bg-primary', isActive);
                btn.classList.toggle('text-white', isActive);
                btn.classList.toggle('text-muted', !isActive);
            });
        }

        function updateSubtitle(tab) {
            if (!subtitleEl) return;
            subtitleEl.style.opacity = '0';
            subtitleEl.style.transform = 'translateY(4px)';
            setTimeout(function() {
                subtitleEl.textContent = subtitles[tab] || subtitles.login;
                subtitleEl.style.opacity = '1';
                subtitleEl.style.transform = 'translateY(0)';
            }, 120);
        }

        function animateSwitch(nextPanel, currentPanel) {
            if (!panelWrap) return;

            var currentHeight = currentPanel ? currentPanel.offsetHeight : panelWrap.offsetHeight;
            panelWrap.style.height = currentHeight + 'px';

            if (currentPanel) {
                currentPanel.style.transition = 'opacity 180ms ease, transform 180ms ease';
                currentPanel.style.opacity = '0';
                currentPanel.style.transform = 'translateY(6px)';
            }

            setTimeout(function() {
                if (currentPanel) {
                    currentPanel.classList.add('hidden');
                    currentPanel.style.transition = '';
                    currentPanel.style.opacity = '';
                    currentPanel.style.transform = '';
                }

                nextPanel.classList.remove('hidden');
                nextPanel.style.opacity = '0';
                nextPanel.style.transform = 'translateY(10px)';
                nextPanel.style.transition = 'none';

                var nextHeight = nextPanel.offsetHeight;
                panelWrap.style.height = nextHeight + 'px';

                requestAnimationFrame(function() {
                    nextPanel.style.transition = 'opacity 260ms ease, transform 260ms ease';
                    nextPanel.style.opacity = '1';
                    nextPanel.style.transform = 'translateY(0)';
                });

                setTimeout(function() {
                    nextPanel.style.transition = '';
                    nextPanel.style.opacity = '';
                    nextPanel.style.transform = '';
                    panelWrap.style.height = '';
                }, 300);
            }, currentPanel ? 190 : 0);
        }

        function setTab(tab) {
            if (currentTab === tab) return;

            var nextPanel = null;
            var currentPanel = null;
            panels.forEach(function(panel) {
                var panelTab = panel.getAttribute('data-auth-tab-panel');
                if (panelTab === tab) nextPanel = panel;
                if (panelTab === currentTab) currentPanel = panel;
            });

            updateTabButton(tab);
            updateSubtitle(tab);
            if (nextPanel) {
                animateSwitch(nextPanel, currentPanel);
            }
            currentTab = tab;
        }

        buttons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                setTab(btn.getAttribute('data-auth-tab-btn'));
            });
        });

        setTab(activeTab);
    })();
</script>
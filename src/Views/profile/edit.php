<div class="max-w-[1040px] mx-auto">
    <?php $__old = \App\Core\Session::old(); ?>
    <?php $__user = \App\Core\Session::get('user'); ?>
    <?php $__role = $profile['role'] ?: ($__user['role'] ?? ''); ?>
    <?php $__isAdmin = $__role === 'admin'; ?>

    <!-- Back link -->
    <div class="flex items-center justify-between mb-10">
        <a href="/dashboard"
            class="inline-flex items-center gap-2 px-0 text-sm text-muted no-underline hover:text-gold transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <?= __('back') ?>
        </a>
    </div>

    <!-- Hero Profile Card (matches menu detail hero style) -->
    <div class="relative mb-10">
        <!-- Hero banner area with avatar -->
        <div
            class="h-[200px] max-md:h-[160px] rounded-2xl bg-gradient-to-br from-gold/15 via-gold/5 to-accent-red/5 flex items-center justify-center overflow-hidden relative">
            <div class="absolute inset-0 opacity-[0.06]">
                <svg class="w-full h-full" viewBox="0 0 1040 200" preserveAspectRatio="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 120 Q 260 40, 520 100 T 1040 80 L 1040 200 L 0 200 Z" fill="currentColor" />
                </svg>
            </div>
        </div>

        <!-- Floating info panel (same style as menu detail) -->
        <div class="absolute -bottom-8 left-6 right-6 max-md:static max-md:mt-6 max-md:px-0">
            <div
                class="bg-[#18181b]/90 backdrop-blur-[20px] border border-white/10 rounded-xl px-6 py-5 flex items-center justify-between flex-wrap gap-4 shadow-xl">
                <!-- Left side: role badge + name + email -->
                <div>
                    <div class="inline-flex items-center gap-2.5 mb-2">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.7rem] font-semibold uppercase tracking-widest"
                            style="background:<?= $__isAdmin ? 'rgba(229,142,38,0.15)' : 'rgba(161,161,170,0.15)' ?>;color:<?= $__isAdmin ? '#e58e26' : '#a1a1aa' ?>">
                            <?= $__isAdmin ? __('admin') : __('user_role') ?>
                        </span>
                        <span class="text-[0.7rem] text-muted uppercase tracking-widest"><?= __('profile') ?></span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold font-display text-text leading-tight">
                        <?= e($profile['name']) ?>
                    </h1>
                    <div class="text-xs text-muted mt-1 font-mono tracking-wide"><?= e($profile['email']) ?></div>
                </div>
                <!-- Right side: account meta -->
                <div class="text-right shrink-0">
                    <div class="text-xs text-muted uppercase tracking-wider font-medium"><?= __('member_since') ?></div>
                    <div class="font-display text-2xl md:text-3xl font-bold text-gold">
                        <?= date('M j, Y', strtotime($profile['created_at'])) ?>
                    </div>
                    <div class="text-xs text-muted mt-1.5 font-mono tracking-wide"><?= e($profile['user_code']) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Spacer for floating panel (desktop) -->
    <div class="h-8 max-md:hidden"></div>

    <!-- Two-column section -->
    <div class="grid grid-cols-1 md:grid-cols-[1.6fr_1fr] gap-8 mb-10">
        <!-- LEFT: Profile Information -->
        <div class="bg-white/[0.03] border border-white/5 rounded-xl p-6">
            <h2 class="text-sm font-semibold text-muted uppercase tracking-widest mb-6 flex items-center gap-3">
                <span class="w-6 h-px bg-gold/50"></span>
                <?= __('profile_information') ?>
            </h2>

            <form method="POST" action="/profile">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-[11px] font-medium text-muted mb-1"><?= __('full_name') ?></label>
                        <input type="text" name="name" value="<?= e($__old['name'] ?? $profile['name']) ?>"
                            class="w-full px-3 py-1.5 text-sm bg-black/40 border border-white/10 rounded-lg text-white placeholder:text-muted focus:outline-none focus:border-gold/50 transition-colors"
                            required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-muted mb-1"><?= __('email') ?></label>
                        <input type="email" name="email" value="<?= e($__old['email'] ?? $profile['email']) ?>"
                            class="w-full px-3 py-1.5 text-sm bg-black/40 border border-white/10 rounded-lg text-white placeholder:text-muted focus:outline-none focus:border-gold/50 transition-colors"
                            required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-muted mb-1"><?= __('phone') ?></label>
                        <input type="text" name="phone" value="<?= e($__old['phone'] ?? $profile['phone']) ?>"
                            class="w-full px-3 py-1.5 text-sm bg-black/40 border border-white/10 rounded-lg text-white placeholder:text-muted focus:outline-none focus:border-gold/50 transition-colors">
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-muted mb-1"><?= __('address') ?></label>
                        <input type="text" name="address" value="<?= e($__old['address'] ?? $profile['address']) ?>"
                            class="w-full px-3 py-1.5 text-sm bg-black/40 border border-white/10 rounded-lg text-white placeholder:text-muted focus:outline-none focus:border-gold/50 transition-colors">
                    </div>
                </div>

                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold leading-tight cursor-pointer border transition-all duration-300 no-underline whitespace-nowrap font-body bg-gold border-gold text-white shadow-[0_0_12px_var(--color-gold-glow)] hover:-translate-y-0.5 hover:shadow-[0_0_20px_var(--color-gold-glow)]">
                    <?= __('save_changes') ?>
                </button>
            </form>
        </div>

        <!-- RIGHT: Change Password -->
        <div class="bg-white/[0.03] border border-white/5 rounded-xl p-6">
            <h2 class="text-sm font-semibold text-muted uppercase tracking-widest mb-1 flex items-center gap-3">
                <span class="w-6 h-px bg-gold/50"></span>
                <?= __('change_password') ?>
            </h2>
            <p class="text-[11px] text-muted mb-6 ml-9"><?= __('leave_blank_password') ?></p>

            <form method="POST" action="/profile">
                <?= csrf_field() ?>

                <div class="space-y-4 mb-6">
                    <div>
                        <label
                            class="block text-[11px] font-medium text-muted mb-1"><?= __('current_password') ?></label>
                        <input type="password" name="current_password"
                            class="w-full px-3 py-1.5 text-sm bg-black/40 border border-white/10 rounded-lg text-white placeholder:text-muted focus:outline-none focus:border-gold/50 transition-colors"
                            placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-muted mb-1"><?= __('new_password') ?></label>
                        <input type="password" name="new_password"
                            class="w-full px-3 py-1.5 text-sm bg-black/40 border border-white/10 rounded-lg text-white placeholder:text-muted focus:outline-none focus:border-gold/50 transition-colors"
                            placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium text-muted mb-1"><?= __('confirm_new_password') ?></label>
                        <input type="password" name="new_password_confirmation"
                            class="w-full px-3 py-1.5 text-sm bg-black/40 border border-white/10 rounded-lg text-white placeholder:text-muted focus:outline-none focus:border-gold/50 transition-colors"
                            placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                    </div>
                </div>

                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold leading-tight cursor-pointer border transition-all duration-300 no-underline whitespace-nowrap font-body bg-white/5 border-border text-text backdrop-blur-[8px] hover:bg-gold hover:border-gold hover:text-white hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:-translate-y-0.5">
                    <?= __('change_password') ?>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var dropZone = document.getElementById('avatar-drop');
            var fileInput = document.getElementById('avatar-input');
            var preview = document.getElementById('avatar-preview');
            var placeholder = document.getElementById('avatar-placeholder');
            var spinner = document.getElementById('avatar-spinner');
            var errorEl = document.getElementById('avatar-error');
            var successEl = document.getElementById('avatar-success');
            var deleteBtn = document.getElementById('avatar-delete-btn');
            var csrfToken = document.querySelector('[name="_csrf_token"]')?.value;
            var navImg = document.querySelector('.navbar-avatar-img');
            var MB = 1048576;

            function upload(file) {
                if (!file.type.startsWith('image/')) return;
                if (file.size > 5 * MB) {
                    errorEl.textContent = '<?= __('file_too_large') ?>';
                    errorEl.classList.remove('hidden');
                    return;
                }
                errorEl.classList.add('hidden');
                successEl.classList.add('hidden');
                spinner.classList.remove('hidden');

                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');

                var fd = new FormData();
                fd.append('avatar', file);
                if (csrfToken) fd.append('_csrf_token', csrfToken);

                fetch('/profile/avatar', { method: 'POST', body: fd })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        spinner.classList.add('hidden');
                        if (res.success) {
                            preview.src = '/uploads/' + res.data.avatar;
                            if (navImg) navImg.src = '/uploads/' + res.data.avatar;
                            deleteBtn.classList.remove('hidden');
                            successEl.textContent = '<?= __('upload_success') ?>';
                            successEl.classList.remove('hidden');
                        } else {
                            preview.src = '';
                            preview.classList.add('hidden');
                            placeholder.classList.remove('hidden');
                            errorEl.textContent = res.message || '<?= __('upload_failed') ?>';
                            errorEl.classList.remove('hidden');
                        }
                    })
                    .catch(function () {
                        spinner.classList.add('hidden');
                        preview.src = '';
                        preview.classList.add('hidden');
                        placeholder.classList.remove('hidden');
                        errorEl.textContent = '<?= __('network_error') ?>';
                        errorEl.classList.remove('hidden');
                    });
            }

            function deleteAvatar() {
                var fd = new FormData();
                if (csrfToken) fd.append('_csrf_token', csrfToken);
                fetch('/profile/avatar/delete', { method: 'POST', body: fd })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        if (res.success) {
                            window.location.reload();
                        } else {
                            errorEl.textContent = res.message || '<?= __('upload_failed') ?>';
                            errorEl.classList.remove('hidden');
                        }
                    })
                    .catch(function () {
                        errorEl.textContent = '<?= __('network_error') ?>';
                        errorEl.classList.remove('hidden');
                    });
            }

            dropZone.addEventListener('click', function () { fileInput.click(); });

            fileInput.addEventListener('change', function () {
                if (fileInput.files[0]) upload(fileInput.files[0]);
            });

            dropZone.addEventListener('dragover', function (e) {
                e.preventDefault();
                dropZone.classList.add('drag-over');
            });

            dropZone.addEventListener('dragleave', function () {
                dropZone.classList.remove('drag-over');
            });

            dropZone.addEventListener('drop', function (e) {
                e.preventDefault();
                dropZone.classList.remove('drag-over');
                var f = e.dataTransfer.files[0];
                if (f) {
                    fileInput.files = e.dataTransfer.files;
                    upload(f);
                }
            });

            if (deleteBtn) {
                deleteBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    deleteAvatar();
                });
            }
        });
    </script>
</div>
// File: public/assets/js/modules/modal.js
(function () {
    'use strict';

    var root, card, titleEl, msgEl, iconEl, actionsEl, closeBtn;
    var activeCallback = null;

    var icons = {
        danger: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>',
        warning: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#f59e0b"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>',
        info: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#3b82f6"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>',
        success: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#10b981"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>',
    };

    var btnStyles = {
        danger: 'bg-danger text-white border-danger hover:bg-danger-hover hover:border-danger-hover',
        warning: 'bg-warning text-white border-warning hover:brightness-110',
        primary: 'bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover',
    };

    function cacheElements() {
        root = document.getElementById('modal-root');
        if (!root) return;
        card = document.getElementById('modal-card');
        titleEl = document.getElementById('modal-title');
        msgEl = document.getElementById('modal-message');
        iconEl = document.getElementById('modal-icon');
        actionsEl = document.getElementById('modal-actions');
        closeBtn = document.getElementById('modal-close');
    }

    function close() {
        if (!root || !card) return;
        card.style.transform = 'scale(0.95) translateY(10px)';
        card.style.opacity = '0';
        setTimeout(function () {
            root.classList.add('hidden');
            activeCallback = null;
        }, 150);
    }

    function open() {
        if (!root || !card) return;
        root.classList.remove('hidden');
        // Force reflow so transition plays
        void card.offsetHeight;
        card.style.transform = 'scale(1) translateY(0)';
        card.style.opacity = '1';
    }

    function createButton(label, className, isPrimary) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 ' + className;
        btn.textContent = label;
        return btn;
    }

    var Modal = {
        init: function () {
            cacheElements();
            if (!root) return;

            // Close on overlay click
            root.addEventListener('click', function (e) {
                if (e.target === root) close();
            });

            // Close on ESC
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !root.classList.contains('hidden')) {
                    close();
                }
            });

            // Close button
            if (closeBtn) {
                closeBtn.addEventListener('click', close);
            }

            // Event delegation for data-modal-confirm (works after AJAX content replace)
            document.addEventListener('click', function (e) {
                var el = e.target.closest('[data-modal-confirm]');
                if (!el) return;
                e.preventDefault();
                var msg = el.dataset.modalConfirm || 'Are you sure?';
                var form = el.closest('form');
                Modal.confirm({
                    title: 'Confirm',
                    message: msg,
                    onConfirm: function () {
                        if (form) form.submit();
                    }
                });
            });
        },

        confirm: function (opts) {
            cacheElements();
            if (!root) return;

            opts = opts || {};
            var title = opts.title || 'Confirm';
            var message = opts.message || 'Are you sure?';
            var type = opts.type || 'danger';
            var confirmText = opts.confirmText || 'Confirm';
            var cancelText = opts.cancelText || 'Cancel';
            var onConfirm = opts.onConfirm || function () {};
            var onCancel = opts.onCancel || function () {};

            iconEl.innerHTML = icons[type] || icons.danger;
            titleEl.textContent = title;
            msgEl.textContent = message;
            actionsEl.innerHTML = '';

            var btnCancel = createButton(cancelText, 'bg-white/6 text-text border-border hover:bg-white/10 hover:text-text');
            btnCancel.addEventListener('click', function () {
                close();
                if (onCancel) onCancel();
            });

            var btnClass = btnStyles[type] || btnStyles.danger;
            var btnConfirm = createButton(confirmText, btnClass);
            btnConfirm.addEventListener('click', function () {
                close();
                if (onConfirm) onConfirm();
                activeCallback = null;
            });

            actionsEl.appendChild(btnCancel);
            actionsEl.appendChild(btnConfirm);

            activeCallback = { onConfirm: onConfirm, onCancel: onCancel };
            open();
        },

        alert: function (opts) {
            cacheElements();
            if (!root) return;

            opts = opts || {};
            var title = opts.title || 'Notice';
            var message = opts.message || '';
            var type = opts.type || 'info';
            var buttonText = opts.buttonText || 'OK';

            iconEl.innerHTML = icons[type] || icons.info;
            titleEl.textContent = title;
            msgEl.textContent = message;
            actionsEl.innerHTML = '';

            var btnOk = createButton(buttonText, 'bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover');
            btnOk.addEventListener('click', function () {
                close();
            });

            actionsEl.appendChild(btnOk);
            open();
        }
    };

    window.AppModules = window.AppModules || {};
    window.AppModules.modal = Modal;
})();

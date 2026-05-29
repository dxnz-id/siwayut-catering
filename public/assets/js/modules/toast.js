// File: public/assets/js/modules/toast.js
(function () {
    'use strict';

    var typeClasses = {
        success: ' bg-success text-white',
        error: ' bg-danger text-white',
        info: ' bg-blue-600 text-white',
        warning: ' bg-warning text-white',
    };

    var Toast = {
        init: function () {
            var container = document.getElementById('toast-container');
            if (!container) return;

            // Read flash data from PHP-rendered data attribute
            var flashData = container.getAttribute('data-flash');
            if (flashData) {
                try {
                    var items = JSON.parse(flashData);
                    if (Array.isArray(items)) {
                        items.forEach(function (item) {
                            Toast.show(item.message, item.type);
                        });
                    }
                } catch (e) {
                    // ignore malformed data
                }
            }

            // Convert any legacy .alert elements on the page to toasts
            document.querySelectorAll('.alert').forEach(function (el) {
                var msg = el.textContent || el.innerText || '';
                if (msg.trim()) {
                    var type = 'info';
                    if (el.classList.contains('alert-error') || el.classList.contains('alert-danger')) type = 'error';
                    else if (el.classList.contains('alert-success')) type = 'success';
                    else if (el.classList.contains('alert-warning')) type = 'warning';
                    Toast.show(msg.trim(), type);
                }
                el.remove();
            });
        },

        show: function (message, type) {
            type = type || 'error';
            var container = document.getElementById('toast-container');
            if (!container) return;

            // Check for existing toast with same message + type
            var existing = null;
            for (var i = 0; i < container.children.length; i++) {
                var child = container.children[i];
                if (child.dataset.toastMessage === message && child.dataset.toastType === type) {
                    existing = child;
                    break;
                }
            }

            if (existing) {
                var count = parseInt(existing.dataset.toastCount || '1', 10) + 1;
                existing.dataset.toastCount = count;

                var badge = existing.querySelector('.toast-badge');
                if (badge) {
                    badge.textContent = count;
                } else {
                    badge = document.createElement('span');
                    badge.className = 'toast-badge absolute -top-2 -right-2 min-w-5 h-5 rounded-full bg-red-600 text-white text-xs font-bold flex items-center justify-center leading-none shadow-lg border-2 border-zinc-900';
                    badge.textContent = count;
                    existing.appendChild(badge);
                }

                // Reset dismiss timer
                clearTimeout(existing._dismissTimer);
                existing._dismissTimer = setTimeout(function () {
                    existing.style.transition = 'transform 250ms ease-in, opacity 250ms ease-in';
                    existing.style.transform = 'translateX(calc(100% + 1rem))';
                    existing.style.opacity = '0';
                    setTimeout(function () { existing.remove(); }, 250);
                }, 3500);

                return;
            }

            // Create new toast
            var toast = document.createElement('div');
            toast.className = 'toast pointer-events-auto px-4 py-3 rounded-lg text-sm shadow-lg relative';
            toast.className += typeClasses[type] || typeClasses.error;
            toast.textContent = message;
            toast.dataset.toastMessage = message;
            toast.dataset.toastType = type;
            toast.dataset.toastCount = '1';

            // Set initial state: off-screen right + invisible
            toast.style.transform = 'translateX(calc(100% + 1rem))';
            toast.style.opacity = '0';
            toast.style.transition = 'transform 350ms cubic-bezier(0.16, 1, 0.3, 1), opacity 350ms ease-out';

            // Prepend to top — newest always on top like iOS
            container.insertBefore(toast, container.firstChild);

            // Trigger enter animation in next frame
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    toast.style.transform = 'translateX(0)';
                    toast.style.opacity = '1';
                });
            });

            // Auto dismiss after 3.5s
            toast._dismissTimer = setTimeout(function () {
                toast.style.transition = 'transform 250ms ease-in, opacity 250ms ease-in';
                toast.style.transform = 'translateX(calc(100% + 1rem))';
                toast.style.opacity = '0';
                setTimeout(function () { toast.remove(); }, 250);
            }, 3500);
        }
    };

    window.AppModules = window.AppModules || {};
    window.AppModules.toast = Toast;

    window.handleDeleteMenu = function (event, orderCount) {
        if (orderCount > 0) {
            Toast.show('Menu has ' + orderCount + ' order(s), cannot delete');
            event.preventDefault();
            return;
        }
        if (confirm('Are you sure you want to delete this menu?')) {
            event.target.closest('form').submit();
        }
    };
})();

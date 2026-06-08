(function () {
    'use strict';

    window.AppModules = window.AppModules || {};

    var root, card, modalClose, modalCancel, modalAdd;
    var modalName, modalPrice, modalMin, modalImageWrap;
    var modalQtyMinus, modalQtyPlus, modalQtyInput;
    var modalTotal, modalUnitPrice;
    var modalCsrf, modalMenuId, modalMinQty;

    var cartBadge, cartBadgeLink;
    var closeTimer = null;

    function esc(str) {
        var d = document.createElement('div');
        d.textContent = str || '';
        return d.innerHTML;
    }

    function formatPrice(price) {
        return Number(price).toLocaleString('id-ID', { maximumFractionDigits: 0 });
    }

    function updateTotalDisplay() {
        var qty = parseInt(modalQtyInput.value, 10) || 0;
        var price = parseInt(modalUnitPrice.value, 10) || 0;
        modalTotal.innerHTML = 'Rp&nbsp;' + formatPrice(qty * price);
    }

    function getThumbPath(src) {
        if (!src || src.indexOf('http') === 0) return { thumb: src || '', full: src || '' };
        var i = src.lastIndexOf('/');
        if (i === -1) return { thumb: '/uploads/thumbs/' + src, full: '/uploads/' + src };
        var base = src.substring(i + 1);
        var dir = src.substring(0, i);
        return { thumb: '/uploads/' + dir + '/thumbs/' + base, full: '/uploads/' + src };
    }

    function updateCartUI(count, total) {
        if (cartBadge) {
            if (count > 0) {
                cartBadge.textContent = count;
                cartBadge.classList.remove('hidden');
            } else {
                cartBadge.classList.add('hidden');
            }
        }
        if (cartBadgeLink) {
            if (count > 0) {
                cartBadgeLink.classList.remove('pointer-events-none');
            } else {
                cartBadgeLink.classList.add('pointer-events-none');
            }
        }
    }

    function open(data) {
        if (!root) return;
        if (closeTimer) {
            clearTimeout(closeTimer);
            closeTimer = null;
        }

        modalMenuId.value = data.id || '';
        var min = parseInt(data.min, 10) || 1;
        modalMinQty.value = min;
        modalQtyInput.value = min;
        modalQtyInput.min = min;
        modalName.textContent = data.name || '';
        modalPrice.textContent = 'Rp ' + formatPrice(data.price) + '/porsi';
        modalMin.textContent = 'Min. ' + min + ' porsi';
        modalUnitPrice.value = data.price || '0';

        if (data.image) {
            var paths = getThumbPath(data.image);
            modalImageWrap.innerHTML = '<img src="' + esc(paths.thumb) + '" alt="' + esc(data.name) + '" class="w-full h-full object-cover">';
        } else {
            modalImageWrap.innerHTML = '<span class="text-3xl">\uD83C\uDF71</span>';
        }

        updateTotalDisplay();

        root.style.display = '';
        void root.offsetHeight;
        root.style.opacity = '1';
        void card.offsetHeight;
        card.style.transform = 'scale(1) translateY(0)';
        card.style.opacity = '1';
    }

    function close() {
        if (!root || !card) return;
        if (closeTimer) clearTimeout(closeTimer);
        root.style.opacity = '0';
        card.style.transform = 'scale(0.95) translateY(10px)';
        card.style.opacity = '0';
        closeTimer = setTimeout(function () {
            root.style.display = 'none';
            closeTimer = null;
        }, 200);
    }

    function init() {
        root = document.getElementById('cart-modal');
        if (!root) return;

        card = document.getElementById('cart-modal-card');
        modalClose = document.getElementById('cart-modal-close');
        modalCancel = document.getElementById('cart-modal-cancel');
        modalAdd = document.getElementById('cart-modal-add');
        modalName = document.getElementById('cart-modal-name');
        modalPrice = document.getElementById('cart-modal-price');
        modalMin = document.getElementById('cart-modal-min');
        modalImageWrap = document.getElementById('cart-modal-image-wrap');
        modalQtyMinus = document.getElementById('modal-qty-minus');
        modalQtyPlus = document.getElementById('modal-qty-plus');
        modalQtyInput = document.getElementById('modal-qty-input');
        modalTotal = document.getElementById('cart-modal-total');
        modalUnitPrice = document.getElementById('cart-modal-unit-price');
        modalCsrf = document.getElementById('cart-modal-csrf');
        modalMenuId = document.getElementById('cart-modal-menu-id');
        modalMinQty = document.getElementById('cart-modal-min-qty');

        cartBadge = document.getElementById('cart-badge');
        cartBadgeLink = document.getElementById('cart-badge-link');

        if (modalQtyMinus) {
            modalQtyMinus.addEventListener('click', function () {
                var val = parseInt(modalQtyInput.value, 10);
                var min = parseInt(modalQtyInput.min, 10);
                if (val > min) {
                    modalQtyInput.value = val - 1;
                    updateTotalDisplay();
                }
            });
        }
        if (modalQtyPlus) {
            modalQtyPlus.addEventListener('click', function () {
                var val = parseInt(modalQtyInput.value, 10);
                modalQtyInput.value = val + 1;
                updateTotalDisplay();
            });
        }
        if (modalQtyInput) {
            modalQtyInput.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
                updateTotalDisplay();
            });
            modalQtyInput.addEventListener('blur', function () {
                var val = parseInt(this.value, 10);
                var min = parseInt(modalMinQty.value, 10) || 1;
                if (isNaN(val) || val < min) {
                    this.value = min;
                    updateTotalDisplay();
                }
            });
        }

        function closeModal() { close(); }
        if (modalClose) modalClose.addEventListener('click', closeModal);
        if (modalCancel) modalCancel.addEventListener('click', closeModal);
        if (root) {
            root.addEventListener('click', function (e) {
                if (e.target === root) close();
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && root && root.style.display !== 'none') {
                close();
            }
        });

        if (modalAdd) {
            modalAdd.addEventListener('click', function () {
                var menuId = modalMenuId.value;
                var qty = parseInt(modalQtyInput.value, 10);
                var min = parseInt(modalMinQty.value, 10);

                if (!menuId || qty < min) return;

                var csrfToken = modalCsrf ? modalCsrf.value : '';

                var fd = new FormData();
                fd.append('_csrf_token', csrfToken);
                fd.append('menu_id', menuId);
                fd.append('quantity', qty);

                fetch('/cart/add', {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res.success) {
                        updateCartUI(res.data.count, res.data.total);
                        if (typeof window.showToast === 'function') {
                            window.showToast(res.data.message || 'Added', 'success');
                        }
                        close();
                    } else {
                        if (typeof window.showToast === 'function') {
                            window.showToast(res.message || 'Error', 'error');
                        }
                    }
                })
                .catch(function () {
                    close();
                });
            });
        }

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.add-to-cart-btn');
            if (!btn) return;
            e.preventDefault();
            open({
                id: btn.getAttribute('data-id'),
                name: btn.getAttribute('data-name'),
                price: btn.getAttribute('data-price'),
                min: btn.getAttribute('data-min'),
                image: btn.getAttribute('data-image'),
            });
        });
    }

    window.AppModules.cartModal = {
        init: init,
        open: open,
        close: close,
        updateCartUI: updateCartUI
    };
})();

(function () {
    'use strict';

    window.AppModules = window.AppModules || {};

    var csrfToken = '';
    var summarySubtotal, summaryCount, summaryTotal, barTotal;

    function formatPrice(price) {
        return Number(price).toLocaleString('id-ID', { maximumFractionDigits: 0 });
    }

    function updateSummary(data) {
        if (summarySubtotal) summarySubtotal.textContent = 'Rp ' + formatPrice(data.total);
        if (summaryTotal) summaryTotal.textContent = 'Rp ' + formatPrice(data.total);
        if (summaryCount) summaryCount.textContent = data.count;
        if (barTotal) barTotal.textContent = formatPrice(data.total);
    }

    function updateItemSubtotal(item, subtotal) {
        var el = item.querySelector('.cart-subtotal');
        if (el) el.textContent = 'Rp ' + formatPrice(subtotal);
    }

    function removeItem(item) {
        item.style.transition = 'opacity 200ms, transform 200ms';
        item.style.opacity = '0';
        item.style.transform = 'scale(0.95)';
        setTimeout(function () {
            item.remove();
            var remaining = document.querySelectorAll('.cart-item').length;
            if (remaining === 0) {
                window.location.reload();
            }
        }, 200);
    }

    function init() {
        var dataEl = document.getElementById('cart-data');
        if (!dataEl) return;
        try {
            var pageData = JSON.parse(dataEl.textContent);
            csrfToken = pageData.csrfToken || '';
        } catch (e) { return; }

        summarySubtotal = document.getElementById('cart-summary-subtotal');
        summaryCount = document.getElementById('cart-summary-count');
        summaryTotal = document.getElementById('cart-summary-total');
        barTotal = document.getElementById('cart-bar-total');

        // Quantity stepper + remove — delegation on items container
        var list = document.querySelector('#cart-items');
        if (!list) return;

        list.addEventListener('click', function (e) {
            var btn = e.target.closest('.cart-qty-minus, .cart-qty-plus');
            var removeBtn = e.target.closest('.cart-remove-btn');
            var item, input, menuId;

            if (btn) {
                item = btn.closest('.cart-item');
                input = item.querySelector('.cart-qty-input');
                menuId = item.getAttribute('data-menu-id');
                var min = parseInt(item.getAttribute('data-min'), 10) || 1;
                var val = parseInt(input.value, 10) || min;

                if (btn.classList.contains('cart-qty-minus')) {
                    if (val > min) val--;
                    else return;
                } else {
                    val++;
                }

                input.value = val;
                updateCart(menuId, val, item);
                return;
            }

            if (removeBtn) {
                e.preventDefault();
                item = removeBtn.closest('.cart-item');
                menuId = item.getAttribute('data-menu-id');

                var fd = new FormData();
                fd.append('_csrf_token', csrfToken);
                fd.append('menu_id', menuId);

                fetch('/cart/remove', {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res.success) {
                        removeItem(item);
                        updateSummary(res.data);
                        if (typeof window.showToast === 'function') {
                            window.showToast('Item removed', 'success');
                        }
                    }
                })
                .catch(function () {});
            }
        });

        // Direct input typing
        list.addEventListener('input', function (e) {
            var input = e.target.closest('.cart-qty-input');
            if (!input) return;
            input.value = input.value.replace(/[^0-9]/g, '');
        });

        // Blur — enforce minimum, then update
        list.addEventListener('blur', function (e) {
            var input = e.target.closest('.cart-qty-input');
            if (!input) return;
            var item = input.closest('.cart-item');
            var min = item ? parseInt(item.getAttribute('data-min'), 10) || 1 : 1;
            var val = parseInt(input.value, 10);
            if (isNaN(val) || val < min) {
                val = min;
                input.value = val;
            }
            var menuId = item.getAttribute('data-menu-id');
            updateCart(menuId, val, item);
        }, true);
    }

    function updateCart(menuId, qty, item) {
        var fd = new FormData();
        fd.append('_csrf_token', csrfToken);
        fd.append('menu_id', menuId);
        fd.append('quantity', qty);

        fetch('/cart/update', {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.success) {
                updateSummary(res.data);
                if (item) updateItemSubtotal(item, res.data.subtotal);
            } else {
                if (typeof window.showToast === 'function') {
                    window.showToast(res.message || 'Error', 'error');
                }
            }
        })
        .catch(function () {});
    }

    window.AppModules.cartPage = { init: init };
})();

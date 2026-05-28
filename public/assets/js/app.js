// File: public/assets/js/app.js

(function () {
    function initAlerts() {
        document.querySelectorAll('.alert').forEach(function (alert) {
            setTimeout(function () {
                alert.style.transition = 'opacity 300ms ease';
                alert.style.opacity = '0';
                setTimeout(function () { alert.remove(); }, 300);
            }, 5000);
        });
    }

    function initConfirmActions() {
        document.querySelectorAll('[data-confirm]').forEach(function (el) {
            el.addEventListener('click', function (e) {
                if (!confirm(el.dataset.confirm || 'Are you sure?')) {
                    e.preventDefault();
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initAlerts();
        initConfirmActions();

        try { window.AppModules?.turnstile?.init?.(); } catch (e) { console.error(e); }
        try { window.AppModules?.fileUpload?.init?.(); } catch (e) { console.error(e); }
        try { window.AppModules?.progressiveImage?.init?.(); } catch (e) { console.error(e); }
        try { window.AppModules?.loadMoreMenu?.init?.(); } catch (e) { console.error(e); }
        try { window.AppModules?.aiDescription?.init?.(); } catch (e) { console.error(e); }
    });
})();

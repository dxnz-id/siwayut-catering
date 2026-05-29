// File: public/assets/js/app.js

(function () {
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
        initConfirmActions();

        try { window.AppModules?.toast?.init?.(); } catch (e) { console.error(e); }
        try { window.AppModules?.turnstile?.init?.(); } catch (e) { console.error(e); }
        try { window.AppModules?.fileUpload?.init?.(); } catch (e) { console.error(e); }
        try { window.AppModules?.progressiveImage?.init?.(); } catch (e) { console.error(e); }
        try { window.AppModules?.loadMoreMenu?.init?.(); } catch (e) { console.error(e); }
        try { window.AppModules?.aiDescription?.init?.(); } catch (e) { console.error(e); }
    });
})();

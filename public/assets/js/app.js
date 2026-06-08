// File: public/assets/js/app.js

(function () {
    document.addEventListener('DOMContentLoaded', function () {
        try { window.AppModules?.modal?.init?.(); } catch (e) { console.error(e); }
        try { window.AppModules?.toast?.init?.(); } catch (e) { console.error(e); }
        try { window.AppModules?.turnstile?.init?.(); } catch (e) { console.error(e); }
        try { window.AppModules?.fileUpload?.init?.(); } catch (e) { console.error(e); }
        try { window.AppModules?.progressiveImage?.init?.(); } catch (e) { console.error(e); }
        try { window.AppModules?.loadMoreMenu?.init?.(); } catch (e) { console.error(e); }
        try { window.AppModules?.cartModal?.init?.(); } catch (e) { console.error(e); }
        try { window.AppModules?.cartPage?.init?.(); } catch (e) { console.error(e); }
        try { window.AppModules?.aiDescription?.init?.(); } catch (e) { console.error(e); }
    });
})();

(function () {
    window.AppModules = window.AppModules || {};

    function setSubmitState(enabled) {
        document.querySelectorAll('[data-turnstile-submit]').forEach(function (btn) {
            btn.disabled = !enabled;
        });
    }

    function onTurnstileSuccess() {
        setSubmitState(true);
    }

    function onTurnstileError() {
        setSubmitState(false);
    }

    function onTurnstileExpired() {
        setSubmitState(false);
    }

    // Register callbacks immediately so Turnstile can call them
    // even if app bootstrap is delayed or fails.
    window.onTurnstileSuccess = onTurnstileSuccess;
    window.onTurnstileError = onTurnstileError;
    window.onTurnstileExpired = onTurnstileExpired;

    function init() {}

    window.AppModules.turnstile = { init: init };
})();

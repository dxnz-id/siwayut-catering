(function () {
    window.AppModules = window.AppModules || {};

    function loadProgressiveImages(container) {
        container = container || document;
        container.querySelectorAll('.progressive-img[data-full]').forEach(function (img) {
            if (img.getAttribute('data-loaded')) return;
            img.setAttribute('data-loaded', '1');

            var full = new Image();
            full.onload = function () {
                img.src = full.src;
                img.classList.remove('blur-up');
                img.classList.add('loaded');
            };
            full.onerror = function () {
                img.classList.remove('blur-up');
                img.classList.add('loaded');
            };
            full.src = img.getAttribute('data-full');
        });
    }

    function init() {
        loadProgressiveImages();
    }

    window.loadProgressiveImages = loadProgressiveImages;
    window.AppModules.progressiveImage = { init: init, loadProgressiveImages: loadProgressiveImages };
})();

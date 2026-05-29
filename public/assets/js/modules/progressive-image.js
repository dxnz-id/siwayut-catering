(function () {
    window.AppModules = window.AppModules || {};

    function loadProgressiveImages(container) {
        container = container || document;
        container.querySelectorAll('.progressive-img[data-thumb]').forEach(function (img) {
            if (img.getAttribute('data-loaded')) return;
            img.setAttribute('data-loaded', '1');

            var thumbSrc = img.getAttribute('data-thumb');
            var fullSrc = img.getAttribute('data-full');

            // Step 2: load thumbnail
            var thumbImg = new Image();
            thumbImg.onload = function () {
                img.src = thumbSrc;
                img.style.opacity = '1';
                img.classList.add('blur-up');

                // Step 3: load full image
                var fullImg = new Image();
                fullImg.onload = function () {
                    img.src = fullSrc;
                    img.classList.remove('blur-up');
                    img.classList.add('loaded');
                };
                fullImg.onerror = function () {
                    img.classList.remove('blur-up');
                    img.classList.add('loaded');
                    if (img.naturalWidth === 0) img.style.display = 'none';
                };
                if (fullSrc) fullImg.src = fullSrc;
            };
            thumbImg.onerror = function () {
                // thumb failed, try full directly
                var fullImg = new Image();
                fullImg.onload = function () {
                    img.src = fullSrc;
                    img.style.opacity = '1';
                    img.classList.add('loaded');
                };
                fullImg.onerror = function () {
                    img.classList.add('loaded');
                    if (img.naturalWidth === 0) img.style.display = 'none';
                };
                if (fullSrc) fullImg.src = fullSrc;
            };
            if (thumbSrc) thumbImg.src = thumbSrc;
        });
    }

    function init() {
        loadProgressiveImages();
    }

    window.loadProgressiveImages = loadProgressiveImages;
    window.AppModules.progressiveImage = { init: init, loadProgressiveImages: loadProgressiveImages };
})();

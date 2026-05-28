(function () {
    window.AppModules = window.AppModules || {};

    function esc(str) {
        var d = document.createElement('div');
        d.textContent = str || '';
        return d.innerHTML;
    }

    function formatPrice(price) {
        return Number(price).toLocaleString('id-ID', { maximumFractionDigits: 0 });
    }

    function getThumbPath(src) {
        if (!src || src.indexOf('http') === 0) return { thumb: src || '', full: src || '' };
        var i = src.lastIndexOf('/');
        if (i === -1) return { thumb: '/uploads/thumbs/' + src, full: '/uploads/' + src };
        var base = src.substring(i + 1);
        var dir = src.substring(0, i);
        return { thumb: '/uploads/' + dir + '/thumbs/' + base, full: '/uploads/' + src };
    }

    function renderMenuCard(menu) {
        var paths = getThumbPath(menu.image);
        var eventName = menu.event_name || null;

        var html = '<div class="menu-card">';
        html += '<div class="menu-img-container">';

        if (menu.image) {
            html += '<span class="progressive-wrap menu-img" style="display:inline-block;overflow:hidden;line-height:0;vertical-align:top;">';
            html += '<img src="' + esc(paths.thumb) + '" data-full="' + esc(paths.full) + '" alt="' + esc(menu.name) + '"';
            html += ' class="progressive-img blur-up" style="display:block;width:100%;height:100%;object-fit:cover"';
            html += ' onerror="this.onerror=null;this.src=\'' + paths.full + '\';this.classList.remove(\'blur-up\');this.classList.add(\'loaded\')">';
            html += '</span>';
        } else {
            html += '<span style="font-size:3.5rem;">🍱</span>';
        }

        if (eventName) {
            html += '<span class="menu-tag">' + esc(eventName) + '</span>';
        }

        html += '</div>';
        html += '<div class="menu-body">';
        html += '<h3 class="menu-title">' + esc(menu.name) + '</h3>';
        html += '<p class="menu-desc">' + esc(menu.description || '') + '</p>';
        html += '<div class="menu-meta">';
        html += '<span class="menu-price">Rp ' + formatPrice(menu.price) + '</span>';
        html += '<span class="menu-portions">Min. ' + esc(menu.minimum_portions) + ' Portions</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        return html;
    }

    function init() {
        var dataEl = document.getElementById('menu-data');
        if (!dataEl) return;

        var data;
        try { data = JSON.parse(dataEl.textContent); } catch (e) { return; }

        var btn = document.getElementById('see-more-btn');
        if (!btn) return;

        var grid = document.getElementById('menu-grid');
        if (!grid) return;

        var currentPage = data.currentPage || 1;
        var lastPage = data.lastPage || 1;
        var loading = false;

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            if (loading) return;
            if (currentPage >= lastPage) {
                btn.style.display = 'none';
                return;
            }

            loading = true;
            btn.textContent = 'Loading...';
            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.5';

            var nextPage = currentPage + 1;
            fetch('/api/menus?page=' + nextPage)
                .then(function (res) { return res.json(); })
                .then(function (result) {
                    if (!result.success || !result.data || !result.data.data) {
                        throw new Error(result.message || 'Failed to load menus');
                    }

                    var items = result.data.data;
                    var frag = document.createDocumentFragment();

                    items.forEach(function (menu) {
                        var div = document.createElement('div');
                        div.innerHTML = renderMenuCard(menu);
                        frag.appendChild(div.firstElementChild);
                    });

                    grid.appendChild(frag);
                    if (typeof window.loadProgressiveImages === 'function') {
                        window.loadProgressiveImages(grid);
                    }

                    currentPage = nextPage;
                    btn.textContent = 'See More ↓';
                    btn.style.pointerEvents = '';
                    btn.style.opacity = '';
                    loading = false;

                    if (currentPage >= lastPage) {
                        btn.style.display = 'none';
                    }
                })
                .catch(function (err) {
                    console.error('Load more error:', err);
                    btn.textContent = 'See More ↓';
                    btn.style.pointerEvents = '';
                    btn.style.opacity = '';
                    loading = false;
                });
        });
    }

    window.AppModules.loadMoreMenu = { init: init };
})();

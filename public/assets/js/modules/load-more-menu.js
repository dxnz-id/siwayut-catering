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

        var html = '<a href="/menu/' + esc(menu.menu_code) + '" class="menu-card-link">';
        html += '<div class="menu-card">';
        html += '<div class="menu-img-container">';

        if (menu.image) {
            html += '<span class="progressive-wrap" style="display:inline-block;overflow:hidden;line-height:0;vertical-align:top;width:100%;height:100%;">';
            html += '<img src="' + esc(paths.thumb) + '" data-full="' + esc(paths.full) + '" alt="' + esc(menu.name) + '"';
            html += ' class="progressive-img blur-up" style="display:block;width:100%;height:100%;object-fit:cover"';
            var safeFull = esc(paths.full).replace(/'/g, '&#39;');
            html += ' onerror="this.onerror=null;this.src=\'' + safeFull + '\';this.classList.remove(\'blur-up\');this.classList.add(\'loaded\')">';
            html += '</span>';
        } else {
            html += '<span style="font-size:3.5rem;">🍱</span>';
        }

        if (eventName) {
            html += '<span class="menu-card-tag">' + esc(eventName) + '</span>';
        }

        html += '</div>';
        html += '<div class="menu-card-body">';
        html += '<h3 class="menu-card-title">' + esc(menu.name) + '</h3>';
        html += '<p class="menu-card-desc">' + esc(menu.description || '') + '</p>';
        html += '<div class="menu-card-meta">';
        html += '<span class="menu-card-price">Rp ' + formatPrice(menu.price) + '</span>';
        html += '<span class="menu-card-portions">Min. ' + esc(menu.minimum_portions) + ' Portions</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</a>';

        return html;
    }

    function setActiveTab(categoryId) {
        document.querySelectorAll('.filter-tab').forEach(function (btn) {
            var val = btn.getAttribute('data-category');
            btn.classList.toggle('active', val === categoryId);
        });
    }

    function replaceGrid(items) {
        var grid = document.getElementById('menu-grid');
        if (!grid) return;
        grid.innerHTML = '';

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
    }

    function init() {
        var dataEl = document.getElementById('menu-data');
        if (!dataEl) return;

        var data;
        try { data = JSON.parse(dataEl.textContent); } catch (e) { return; }

        var btn = document.getElementById('see-more-btn');
        var grid = document.getElementById('menu-grid');
        if (!grid) return;

        var currentPage = data.currentPage || 1;
        var lastPage = data.lastPage || 1;
        var activeCategory = '';
        var loading = false;

        // --- Category filter tabs ---
        var tabContainer = document.getElementById('category-filters');
        if (tabContainer) {
            var seeMoreBtn = btn;
            tabContainer.addEventListener('click', function (e) {
                var tabBtn = e.target.closest('.filter-tab');
                if (!tabBtn) return;

                if (loading) return;

                var catId = tabBtn.getAttribute('data-category') || '';
                if (catId === activeCategory) return;

                activeCategory = catId;
                setActiveTab(activeCategory);
                currentPage = 0;
                lastPage = 1;
                loading = true;

                if (seeMoreBtn) {
                    seeMoreBtn.style.display = 'none';
                }

                var params = 'page=1' + (activeCategory ? '&category_id=' + activeCategory : '');
                fetch('/api/menus?' + params)
                    .then(function (res) { return res.json(); })
                    .then(function (result) {
                        if (!result.success || !result.data) {
                            throw new Error(result.message || 'Failed to load menus');
                        }

                        var items = result.data.data || [];
                        replaceGrid(items);

                        currentPage = result.data.current_page || 1;
                        lastPage = result.data.last_page || 1;

                        if (currentPage < lastPage) {
                            if (seeMoreBtn) {
                                seeMoreBtn.style.display = '';
                                seeMoreBtn.textContent = 'See More \u2193';
                                seeMoreBtn.style.pointerEvents = '';
                                seeMoreBtn.style.opacity = '';
                            }
                        }

                        loading = false;
                    })
                    .catch(function (err) {
                        console.error('Filter error:', err);
                        loading = false;
                        if (seeMoreBtn) {
                            seeMoreBtn.textContent = 'See More \u2193';
                            seeMoreBtn.style.pointerEvents = '';
                            seeMoreBtn.style.opacity = '';
                        }
                    });
            });
        }

        // --- See More / Load More ---
        if (!btn) return;

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
            var params = 'page=' + nextPage;
            if (activeCategory) params += '&category_id=' + activeCategory;

            fetch('/api/menus?' + params)
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
                    btn.textContent = 'See More \u2193';
                    btn.style.pointerEvents = '';
                    btn.style.opacity = '';
                    loading = false;

                    if (currentPage >= lastPage) {
                        btn.style.display = 'none';
                    }
                })
                .catch(function (err) {
                    console.error('Load more error:', err);
                    btn.textContent = 'See More \u2193';
                    btn.style.pointerEvents = '';
                    btn.style.opacity = '';
                    loading = false;
                });
        });
    }

    window.AppModules.loadMoreMenu = { init: init };
})();

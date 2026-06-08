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
        var paths = menu.image ? getThumbPath(menu.image) : null;
        var eventName = menu.event_name || null;

        var html = '<a href="/menu/' + esc(menu.menu_code) + '"';
        html += ' class="bg-card-bg border border-border backdrop-blur-[16px] rounded-xl overflow-hidden flex flex-col no-underline text-inherit transition-all duration-300 hover:-translate-y-[5px] hover:border-gold/25 hover:shadow-xl group">';

        // Image area
        html += '<div class="h-[180px] bg-gradient-to-br from-gold/20 to-accent-red/10 relative flex items-center justify-center text-white/15">';

        if (paths) {
            html += '<span class="progressive-wrap w-full h-full"';
            html += ' style="display:inline-block;overflow:hidden;line-height:0;vertical-align:top;background:rgba(255,255,255,0.04) url(&quot;data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'24\' height=\'24\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'rgba(255,255,255,0.2)\' stroke-width=\'1.5\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z\'/%3E%3C/svg%3E&quot;) center/20px no-repeat">';
            html += '<img data-thumb="' + esc(paths.thumb) + '" data-full="' + esc(paths.full) + '" alt="' + esc(menu.name) + '" class="progressive-img" style="display:block;width:100%;height:100%;object-fit:cover;opacity:0">';
            html += '</span>';
        } else {
            html += '<span class="text-6xl">\uD83C\uDF71</span>';
        }

        if (eventName) {
            html += '<span class="absolute bottom-3 left-3 bg-bg/80 border border-border backdrop-blur-[6px] text-gold text-xs font-semibold px-3 py-1 rounded-[6px]">' + esc(eventName) + '</span>';
        }

        html += '</div>';

        // Content area
        html += '<div class="p-5 flex flex-col flex-1">';
        html += '<h3 class="text-lg font-bold mb-2 text-white font-display group-hover:text-gold transition-colors duration-200">' + esc(menu.name) + '</h3>';
        html += '<p class="text-sm text-muted mb-5 flex-1 line-clamp-2">' + esc(menu.description || '') + '</p>';
        html += '<div class="flex items-center justify-between border-t border-border pt-3 mt-auto">';
        html += '<span class="font-display text-xl font-bold text-gold">Rp ' + formatPrice(menu.price) + '</span>';
        html += '<span class="text-xs text-muted bg-white/5 px-2 py-0.5 rounded border border-border">Min. ' + esc(menu.minimum_portions) + ' Portions</span>';
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

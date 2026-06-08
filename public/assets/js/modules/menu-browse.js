(function () {
    'use strict';

    window.AppModules = window.AppModules || {};

    var dataEl = document.getElementById('menu-data');
    if (!dataEl) return;
    var pageData;
    try { pageData = JSON.parse(dataEl.textContent); } catch (e) { return; }

    var grid = document.getElementById('menu-grid');
    var searchInput = document.getElementById('menu-search');
    var tabContainer = document.getElementById('category-filters');
    var seeMoreBtn = document.getElementById('see-more-btn');
    var seeMoreWrap = document.getElementById('see-more-wrap');
    var cartBadge = document.getElementById('cart-badge');
    var cartBadgeLink = document.getElementById('cart-badge-link');

    var addToCartText = pageData.addToCartText || 'Add';
    var currentPage = pageData.currentPage || 1;
    var lastPage = pageData.lastPage || 1;
    var activeCategory = '';
    var currentSearch = '';
    var loading = false;

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

        var html = '<div class="bg-card-bg border border-border backdrop-blur-[16px] rounded-xl overflow-hidden flex flex-col">';

        html += '<div class="h-[180px] bg-gradient-to-br from-gold/20 to-accent-red/10 relative flex items-center justify-center text-white/15">';

        if (paths) {
            html += '<span class="progressive-wrap w-full h-full"';
            html += ' style="display:inline-block;overflow:hidden;line-height:0;vertical-align:top;background:rgba(255,255,255,0.04) url(&quot;data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'24\' height=\'24\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'rgba(255,255,255,0.2)\' stroke-width=\'1.5\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z\'/%3E%3C/svg%3E&quot;) center/20px no-repeat">';
            html += '<img data-thumb="' + esc(paths.thumb) + '" data-full="' + esc(paths.full) + '" alt="' + esc(menu.name) + '" class="progressive-img" onerror="this.parentElement.innerHTML=\'<span class=text-6xl>\\uD83C\\uDF71</span>\'" style="display:block;width:100%;height:100%;object-fit:cover;opacity:0">';
            html += '</span>';
        } else {
            html += '<span class="text-6xl">\uD83C\uDF71</span>';
        }

        if (eventName) {
            html += '<span class="absolute bottom-3 left-3 bg-bg/80 border border-border backdrop-blur-[6px] text-gold text-xs font-semibold px-3 py-1 rounded-[6px]">' + esc(eventName) + '</span>';
        }

        html += '</div>';

        html += '<div class="p-5 flex flex-col flex-1">';
        html += '<h3 class="text-lg font-bold mb-2 text-white font-display">' + esc(menu.name) + '</h3>';
        html += '<p class="text-sm text-muted mb-4 flex-1 line-clamp-2">' + esc(menu.description || '') + '</p>';
        html += '<div class="flex items-center justify-between mb-4">';
        html += '<span class="font-display text-xl font-bold text-gold">Rp ' + formatPrice(menu.price) + '</span>';
        html += '<span class="text-xs text-muted bg-white/5 px-2 py-0.5 rounded border border-border">Min. ' + esc(menu.minimum_portions) + '</span>';
        html += '</div>';
        html += '<button type="button" class="w-full px-4 py-2 bg-gold border border-gold rounded-lg text-white text-sm font-semibold cursor-pointer transition-all duration-200 hover:bg-primary-hover hover:border-primary-hover active:scale-[0.97] add-to-cart-btn"';
        html += ' data-id="' + esc(menu.id) + '"';
        html += ' data-name="' + esc(menu.name) + '"';
        html += ' data-price="' + esc(menu.price) + '"';
        html += ' data-min="' + esc(menu.minimum_portions || 1) + '"';
        html += ' data-image="' + esc(menu.image || '') + '">';
        html += '+ ' + esc(addToCartText) + '</button>';
        html += '</div>';

        html += '</div>';

        return html;
    }

    function fetchMenus(page, append) {
        if (loading) return;
        loading = true;

        var params = 'page=' + page;
        if (activeCategory) params += '&category_id=' + activeCategory;
        if (currentSearch) params += '&search=' + encodeURIComponent(currentSearch);

        if (seeMoreBtn) {
            seeMoreBtn.textContent = 'Loading...';
            seeMoreBtn.style.pointerEvents = 'none';
            seeMoreBtn.style.opacity = '0.5';
        }

        fetch('/api/menus?' + params)
            .then(function (res) { return res.json(); })
            .then(function (result) {
                if (!result.success || !result.data) {
                    throw new Error(result.message || 'Failed to load menus');
                }

                var items = result.data.data || [];
                currentPage = result.data.current_page || 1;
                lastPage = result.data.last_page || 1;

                if (append) {
                    appendGrid(items);
                } else {
                    replaceGrid(items);
                }

                if (seeMoreBtn) {
                    if (currentPage < lastPage) {
                        seeMoreBtn.textContent = 'See More \u2193';
                        seeMoreBtn.style.pointerEvents = '';
                        seeMoreBtn.style.opacity = '';
                        if (seeMoreWrap) seeMoreWrap.style.display = '';
                    } else {
                        if (seeMoreWrap) seeMoreWrap.style.display = 'none';
                    }
                }

                loading = false;
            })
            .catch(function (err) {
                console.error('Menu fetch error:', err);
                if (seeMoreBtn) {
                    seeMoreBtn.textContent = 'See More \u2193';
                    seeMoreBtn.style.pointerEvents = '';
                    seeMoreBtn.style.opacity = '';
                }
                loading = false;
            });
    }

    function replaceGrid(items) {
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

    function appendGrid(items) {
        if (!grid) return;
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

    function debounce(fn, delay) {
        var timer;
        return function () {
            var ctx = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () { fn.apply(ctx, args); }, delay);
        };
    }

    function init() {
        if (!grid) return;

        // Remove menu-data so loadMoreMenu.init() (from landing page) doesn't hijack this page
        var dataEl = document.getElementById('menu-data');
        if (dataEl) dataEl.remove();

        // Search
        if (searchInput) {
            searchInput.addEventListener('input', debounce(function () {
                var val = this.value.trim();
                if (val === currentSearch) return;
                currentSearch = val;
                activeCategory = '';
                // Reset active tab
                if (tabContainer) {
                    var tabs = tabContainer.querySelectorAll('.filter-tab');
                    tabs.forEach(function (t) { t.classList.remove('active'); });
                    var first = tabContainer.querySelector('.filter-tab[data-category=""]');
                    if (first) first.classList.add('active');
                }
                fetchMenus(1, false);
            }, 300));
        }

        // Category tabs
        if (tabContainer) {
            tabContainer.addEventListener('click', function (e) {
                var btn = e.target.closest('.filter-tab');
                if (!btn || loading) return;
                var catId = btn.getAttribute('data-category') || '';
                if (catId === activeCategory) return;

                activeCategory = catId;
                currentSearch = '';
                if (searchInput) searchInput.value = '';

                // Update active state
                var tabs = tabContainer.querySelectorAll('.filter-tab');
                tabs.forEach(function (t) { t.classList.remove('active'); });
                btn.classList.add('active');

                fetchMenus(1, false);
            });
        }

        // Load more
        if (seeMoreBtn) {
            seeMoreBtn.addEventListener('click', function () {
                if (loading || currentPage >= lastPage) return;
                fetchMenus(currentPage + 1, true);
            });
        }

        // Init progressive images
        if (typeof window.loadProgressiveImages === 'function') {
            window.loadProgressiveImages(grid);
        }

    }

    window.AppModules.menuBrowse = { init: init };
})();

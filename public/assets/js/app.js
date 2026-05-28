// File: public/assets/js/app.js

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

function getFileTypeLabel(mime) {
    var map = {
        'image/jpeg': 'JPEG',
        'image/png': 'PNG',
        'image/webp': 'WEBP',
        'image/gif': 'GIF',
        'image/svg+xml': 'SVG',
        'application/pdf': 'PDF',
    };
    return map[mime] || mime;
}

function initFileUploadZone(zone) {
    var input = zone.querySelector('.file-upload-input');
    var placeholder = zone.querySelector('.file-upload-placeholder');
    var preview = zone.querySelector('.file-upload-preview');
    var thumb = zone.querySelector('.file-upload-thumb');
    var fileName = zone.querySelector('.file-upload-name');
    var fileMeta = zone.querySelector('.file-upload-meta');
    var errorEl = zone.querySelector('.file-upload-error');
    var removeBtn = zone.querySelector('.file-upload-remove');

    var acceptTypes = (zone.dataset.accept || '').split(',');
    var maxSize = parseInt(zone.dataset.maxSize, 10) || 5242880;

    function resetZone() {
        zone.classList.remove('has-file', 'has-error');
        if (input) input.value = '';
        errorEl.style.display = 'none';
        errorEl.textContent = '';
    }

    function showError(msg) {
        zone.classList.add('has-error');
        zone.classList.remove('has-file');
        errorEl.textContent = msg;
        errorEl.style.display = 'block';
    }

    function validateAndShow(file) {
        zone.classList.remove('has-error');
        errorEl.style.display = 'none';

        var ext = '.' + file.name.split('.').pop().toLowerCase();
        var mimeValid = acceptTypes.length === 0 || acceptTypes.some(function (t) {
            t = t.trim();
            if (t.startsWith('.')) return ext === t.toLowerCase();
            return file.type === t;
        });

        if (!mimeValid) {
            showError('Invalid file type. Accepted: ' + acceptTypes.join(', ').replace(/image\//g, '').toUpperCase());
            return false;
        }

        if (file.size > maxSize) {
            showError('File too large (' + formatFileSize(file.size) + '). Maximum ' + formatFileSize(maxSize) + '.');
            return false;
        }

        zone.classList.add('has-file');
        zone.classList.remove('has-error');

        fileName.textContent = file.name;

        var typeLabel = getFileTypeLabel(file.type) || file.type || 'Unknown';
        fileMeta.innerHTML = '<span>' + typeLabel + '</span><span>' + formatFileSize(file.size) + '</span>';

        if (file.type.startsWith('image/')) {
            thumb.style.display = 'block';
            var reader = new FileReader();
            reader.onload = function (e) {
                thumb.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            thumb.style.display = 'none';
            thumb.src = '';
        }

        return true;
    }

    function handleFiles(files) {
        if (!files || files.length === 0) return;
        var file = files[0];

        if (validateAndShow(file)) {
            if (input) {
                var dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
            }
        }
    }

    // Drag events
    zone.addEventListener('dragover', function (e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'copy';
        zone.classList.add('drag-over');
    });

    zone.addEventListener('dragleave', function (e) {
        e.preventDefault();
        zone.classList.remove('drag-over');
    });

    zone.addEventListener('drop', function (e) {
        e.preventDefault();
        zone.classList.remove('drag-over');
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            handleFiles(e.dataTransfer.files);
        }
    });

    // Click to browse
    zone.addEventListener('click', function () {
        if (input) input.click();
    });

    if (input) {
        input.addEventListener('change', function () {
            if (input.files && input.files.length > 0) {
                handleFiles(input.files);
            }
        });
    }

    // Remove button
    if (removeBtn) {
        removeBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            resetZone();
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Auto-dismiss alerts after 5 seconds
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 300ms ease';
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 300);
        }, 5000);
    });

    // Confirm delete actions
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(el.dataset.confirm || 'Are you sure?')) {
                e.preventDefault();
            }
        });
    });

    // Init all file upload zones
    document.querySelectorAll('.file-upload-zone').forEach(initFileUploadZone);
});

// Progressive image loading (blur-up)
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

try { loadProgressiveImages(); } catch (e) { console.error(e); }
try { initLoadMore(); } catch (e) { console.error(e); }

function generateDescription(btn) {
    var form = btn.closest('form');
    if (!form) return;

    var textarea = form.querySelector('#description');
    var name = form.querySelector('#name');
    var category = form.querySelector('#category_id');
    var event = form.querySelector('#event_id');
    var price = form.querySelector('#price');
    var minPortions = form.querySelector('#minimum_portions');
    var csrf = form.querySelector('input[name="_csrf_token"]');

    if (!textarea || !name) return;

    var data = new URLSearchParams();
    data.append('_csrf_token', csrf ? csrf.value : '');
    data.append('name', name.value || '');
    data.append('category', category ? category.options[category.selectedIndex]?.text || '' : '');
    data.append('event', event ? event.options[event.selectedIndex]?.text || '' : '');
    data.append('price', price ? price.value || '' : '');
    data.append('minimum_portions', minPortions ? minPortions.value || '' : '');

    btn.disabled = true;
    btn.textContent = 'Generating...';

    fetch('/menus/generate-description', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: data
    })
    .then(function (res) { return res.json(); })
    .then(function (result) {
        if (result.description) {
            textarea.value = result.description;
        } else {
            alert(result.message || result.error || 'Failed to generate description.');
        }
    })
    .catch(function (err) {
        alert('Error: ' + err.message);
    })
    .finally(function () {
        btn.disabled = false;
        btn.textContent = 'Generate with AI';
    });
}

// ===== Load More — Landing Page Featured Menu =====

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
        html += '<span style="font-size:3.5rem;">\uD83C\uDF71</span>';
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

function initLoadMore() {
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
        if (currentPage >= lastPage) { btn.style.display = 'none'; return; }

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
                loadProgressiveImages(grid);

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



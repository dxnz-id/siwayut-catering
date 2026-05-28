(function () {
    window.AppModules = window.AppModules || {};

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

            if (validateAndShow(file) && input) {
                var dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
            }
        }

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

        if (removeBtn) {
            removeBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                resetZone();
            });
        }
    }

    function init() {
        document.querySelectorAll('.file-upload-zone').forEach(initFileUploadZone);
    }

    window.AppModules.fileUpload = { init: init };
})();

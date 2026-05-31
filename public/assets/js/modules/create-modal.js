(function () {
    var openModals = [];

    function getForm(modalId) {
        return document.getElementById(modalId + '-form');
    }

    function getTitleEl(modalId) {
        return document.querySelector('#' + modalId + ' h3');
    }

    function getErrorsEl(modalId) {
        return document.getElementById(modalId + '-errors');
    }

    function singularize(word) {
        return word.replace(/ies$/, 'y').replace(/s$/, '');
    }

    function saveOriginalState(modalId) {
        var form = getForm(modalId);
        if (form && !form.dataset.origAction) {
            form.dataset.origAction = form.action;
        }
        var titleEl = getTitleEl(modalId);
        if (titleEl && !titleEl.dataset.origTitle) {
            titleEl.dataset.origTitle = titleEl.textContent;
        }
    }

    function resetModal(modalId) {
        var form = getForm(modalId);
        if (!form) return;
        if (form.dataset.origAction) {
            form.action = form.dataset.origAction;
        }
        form.reset();
        var titleEl = getTitleEl(modalId);
        if (titleEl && titleEl.dataset.origTitle) {
            titleEl.textContent = titleEl.dataset.origTitle;
        }
        var errorsEl = getErrorsEl(modalId);
        if (errorsEl) { errorsEl.classList.add('hidden'); errorsEl.innerHTML = ''; }
        var preview = document.querySelector('#' + modalId + ' [data-image-preview]');
        if (preview) {
            preview.classList.add('hidden');
            preview.innerHTML = '';
        }
    }

    function showModal(modalId) {
        var el = document.getElementById(modalId);
        if (!el) return;
        el.classList.remove('hidden');
        openModals.push(modalId);
        var card = el.firstElementChild.firstElementChild;
        if (card) {
            void card.offsetHeight;
            card.style.transform = 'scale(1) translateY(0)';
            card.style.opacity = '1';
        }
        document.body.style.overflow = 'hidden';
    }

    function hideModal(modalId) {
        var el = document.getElementById(modalId);
        if (!el) return;
        var card = el.firstElementChild.firstElementChild;
        if (card) {
            card.style.transform = 'scale(0.95) translateY(10px)';
            card.style.opacity = '0';
        }
        setTimeout(function () {
            el.classList.add('hidden');
            var errors = getErrorsEl(modalId);
            if (errors) { errors.classList.add('hidden'); errors.innerHTML = ''; }
        }, 150);
        openModals = openModals.filter(function (i) { return i !== modalId; });
        if (openModals.length === 0) document.body.style.overflow = '';
    }

    // Public: open a fresh create modal (resets first)
    window.openCreateModal = function (modalId) {
        if (!document.getElementById(modalId)) return;
        saveOriginalState(modalId);
        resetModal(modalId);
        showModal(modalId);
    };

    // Public: close
    window.closeCreateModal = function (modalId) {
        hideModal(modalId);
    };

    // Overlay click to close
    document.addEventListener('click', function (e) {
        if (openModals.length === 0) return;
        var topId = openModals[openModals.length - 1];
        var el = document.getElementById(topId);
        if (el && e.target === el) closeCreateModal(topId);
    });

    // ESC to close
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && openModals.length > 0) {
            closeCreateModal(openModals[openModals.length - 1]);
        }
    });

    // Edit link delegation: <a href="#" data-edit="categories" data-id="5">
    document.addEventListener('click', function (e) {
        var link = e.target.closest('[data-edit]');
        if (!link) return;
        e.preventDefault();
        var entity = link.dataset.edit;
        var id = link.dataset.id;
        if (!entity || !id) return;

        var singular = singularize(entity);
        var modalId = 'create' + singular.charAt(0).toUpperCase() + singular.slice(1) + 'Modal';

        openEditModal(modalId, entity, id);
    });

    // Public: edit modal — fetch data, populate, then show (no reset after populate)
    window.openEditModal = function (modalId, entity, id) {
        var form = getForm(modalId);
        if (!form) return;

        saveOriginalState(modalId);
        resetModal(modalId);

        fetch('/api/' + entity + '/' + id)
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (!res.success) {
                    if (window.AppModules && window.AppModules.toast) {
                        AppModules.toast.show(res.message || 'Failed to load data', 'error');
                    }
                    return;
                }
                var data = res.data;

                var titleEl = getTitleEl(modalId);
                if (titleEl) {
                    var label = singularize(entity);
                    titleEl.textContent = 'Edit ' + label.charAt(0).toUpperCase() + label.slice(1);
                }

                form.action = '/' + entity + '/' + id;

                Array.from(form.elements).forEach(function (el) {
                    if (!el.name || el.type === 'file' || el.type === 'submit' || el.type === 'button' || el.type === 'hidden') return;
                    if (data[el.name] !== undefined && data[el.name] !== null) el.value = data[el.name];
                });

                var preview = document.querySelector('#' + modalId + ' [data-image-preview]');
                if (preview) {
                    var field = preview.dataset.imagePreview || 'image';
                    var filename = data[field];
                    if (filename) {
                        var img = document.createElement('img');
                        img.src = '/uploads/' + filename.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                        img.className = 'w-24 h-24 object-cover rounded-lg border border-border';
                        img.onerror = function () { this.style.display = 'none'; };
                        preview.innerHTML = '';
                        preview.appendChild(img);
                        preview.classList.remove('hidden');
                    }
                }

                showModal(modalId);
            })
            .catch(function (err) {
                if (window.AppModules && window.AppModules.toast) {
                    AppModules.toast.show('Error loading data: ' + err.message, 'error');
                }
            });
    };

    // AJAX form submission delegation
    document.addEventListener('submit', function (e) {
        var form = e.target;
        var container = form.closest('[id$="Modal"]');
        if (!container) return;
        var modalId = container.id;

        e.preventDefault();
        if (form.dataset.submitting) return;
        form.dataset.submitting = '1';

        var submitBtn = form.querySelector('button[type="submit"]');
        var origText = submitBtn ? submitBtn.textContent : '';
        if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Saving...'; }

        var errorsEl = getErrorsEl(modalId);

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    closeCreateModal(modalId);
                    if (data.message && window.AppModules && window.AppModules.toast) {
                        AppModules.toast.show(data.message, 'success');
                    }
                    if (window.reloadTable) {
                        window.reloadTable();
                    } else {
                        window.location.reload();
                    }
                } else {
                    if (errorsEl) {
                        errorsEl.innerHTML = '';
                        if (data.message) {
                            var p = document.createElement('p');
                            p.className = 'mb-2 font-medium';
                            p.textContent = data.message;
                            errorsEl.appendChild(p);
                        }
                        if (data.errors) {
                            var ul = document.createElement('ul');
                            ul.className = 'list-disc pl-4 space-y-0.5';
                            for (var key in data.errors) {
                                if (data.errors.hasOwnProperty(key)) {
                                    var li = document.createElement('li');
                                    li.textContent = data.errors[key];
                                    ul.appendChild(li);
                                }
                            }
                            errorsEl.appendChild(ul);
                        }
                        errorsEl.classList.remove('hidden');
                    }
                }
            })
            .catch(function (err) {
                if (errorsEl) {
                    errorsEl.innerHTML = '';
                    var p = document.createElement('p');
                    p.textContent = 'Something went wrong: ' + err.message;
                    errorsEl.appendChild(p);
                    errorsEl.classList.remove('hidden');
                }
            })
            .finally(function () {
                delete form.dataset.submitting;
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = origText; }
            });
    });
})();

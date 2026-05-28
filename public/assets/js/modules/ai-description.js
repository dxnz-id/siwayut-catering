(function () {
    window.AppModules = window.AppModules || {};

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

    function init() {
        window.generateDescription = generateDescription;
    }

    window.AppModules.aiDescription = { init: init };
})();

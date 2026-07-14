(function () {
    var key = 'goodfriend-admin-sidebar';
    var collapsed = localStorage.getItem(key) === 'collapsed';

    function applyState(value) {
        document.body.classList.toggle('admin-sidebar-collapsed', value);
        var toggle = document.querySelector('[data-admin-sidebar-toggle]');

        if (toggle) {
            toggle.setAttribute('aria-expanded', value ? 'false' : 'true');
            toggle.setAttribute('aria-label', value ? 'ขยายเมนู' : 'ย่อเมนู');
        }
    }

    applyState(collapsed);

    window.addEventListener('DOMContentLoaded', function () {
        applyState(collapsed);

        var toggle = document.querySelector('[data-admin-sidebar-toggle]');

        if (! toggle) {
            return;
        }

        toggle.addEventListener('click', function () {
            collapsed = ! document.body.classList.contains('admin-sidebar-collapsed');
            localStorage.setItem(key, collapsed ? 'collapsed' : 'expanded');
            applyState(collapsed);
        });
    });
})();

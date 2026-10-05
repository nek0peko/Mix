(function () {
    function initializeActions() {
        var form = document.getElementById('mix-theme-settings');
        if (!form) return;
        var mouseToggle = form.querySelector('#mix-imouse-toggle');
        var mouseInput = form.querySelector('input[name="IMouseEnabled[]"][value="enabled"]');
        if (mouseToggle && mouseInput) {
            var mouseSettings = mouseToggle.parentElement;
            function updateMouseSettings() {
                Array.prototype.forEach.call(mouseSettings.children, function (child) {
                    if (child !== mouseToggle) child.hidden = !mouseInput.checked;
                });
            }
            mouseInput.addEventListener('change', updateMouseSettings);
            updateMouseSettings();
        }
        var actions = form.querySelector('.mix-backup-actions');
        var save = form.querySelector('.typecho-option-submit');
        if (!actions || !save) return;

        var toolbar = document.createElement('div');
        toolbar.className = 'mix-settings-actions';
        form.appendChild(toolbar);
        // The native save button stays first in the DOM, so Enter saves settings.
        toolbar.appendChild(save);
        toolbar.appendChild(actions);

        actions.addEventListener('click', function (event) {
            var button = event.target.closest('[data-mix-backup-action]');
            if (!button || !window.confirm(button.dataset.confirmMessage)) return;
            var action = document.createElement('input');
            action.type = 'hidden';
            action.name = 'type';
            action.value = button.value;
            form.appendChild(action);
            form.action = button.dataset.mixBackupAction;
            HTMLFormElement.prototype.submit.call(form);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeActions);
    } else {
        initializeActions();
    }
})();

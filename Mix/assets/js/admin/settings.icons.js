(function () {
    function initializeIcons() {
        var form = document.getElementById('mix-theme-settings');
        if (!form) return;
        var editor = form.querySelector('#mix-icon-editor');
        var data = form.querySelector('input[name="SocialIcons"]');
        if (!editor || !data || editor.dataset.initialized) return;
        editor.dataset.initialized = 'true';
        var list = editor.querySelector('.mix-icon-list');

        function addIcon(item) {
            var row = document.createElement('div');
            row.className = 'mix-hyperlink-item mix-icon-row';
            var heading = document.createElement('div');
            heading.className = 'mix-hyperlink-item-heading';
            var title = document.createElement('span');
            title.className = 'mix-icon-number';
            heading.appendChild(title);
            var remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'mdui-btn mix-delete-link';
            remove.textContent = '删除图标';
            heading.appendChild(remove);
            row.appendChild(heading);
            var fields = document.createElement('div');
            fields.className = 'mix-hyperlink-fields mix-icon-fields';
            [['图标地址', 'icon', '图片 URL，例如 SVG 或 PNG'], ['跳转链接', 'url', '留空不显示图标']].forEach(function (entry) {
                fields.appendChild(window.MixSettingControls.field(entry[0], entry[1], item[entry[1]], entry[2]));
            });
            row.appendChild(fields);
            list.appendChild(row);
            return row;
        }

        var pendingSync = null;
        function scheduleSync() {
            if (pendingSync !== null) window.clearTimeout(pendingSync);
            pendingSync = window.setTimeout(synchronize, 120);
        }

        function synchronize() {
            if (pendingSync !== null) { window.clearTimeout(pendingSync); pendingSync = null; }
            var items = [];
            Array.prototype.forEach.call(list.children, function (row, index) {
                row.querySelector('.mix-icon-number').textContent = '图标 ' + (index + 1);
                items.push({icon: row.querySelector('[data-field="icon"]').value, url: row.querySelector('[data-field="url"]').value});
            });
            data.value = JSON.stringify(items);
        }

        editor.addEventListener('input', scheduleSync);
        editor.addEventListener('change', synchronize);
        editor.addEventListener('click', function (event) {
            var button = event.target.closest('button');
            if (!button) return;
            if (button.classList.contains('mix-add-icon')) {
                addIcon({icon: '', url: ''}).querySelector('input').focus();
            } else if (button.classList.contains('mix-delete-link')) {
                button.closest('.mix-icon-row').remove();
                editor.querySelector('.mix-add-icon').focus();
            }
            synchronize();
        });
        form.addEventListener('submit', synchronize);
        form.addEventListener('formdata', function (event) {
            synchronize();
            event.formData.set(data.name, data.value);
        });
        var items;
        try { items = JSON.parse(data.value); } catch (error) { items = []; }
        if (Array.isArray(items)) items.forEach(addIcon);
        synchronize();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeIcons);
    } else {
        initializeIcons();
    }
})();

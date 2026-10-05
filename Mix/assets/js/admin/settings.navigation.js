(function () {
    function initializeNavigation() {
        var form = document.getElementById('mix-theme-settings');
        if (!form) return;
        var editor = form.querySelector('#mix-navigation-editor');
        var data = form.querySelector('input[name="headnavItems"]');
        if (!editor || !data || editor.dataset.initialized) return;
        editor.dataset.initialized = 'true';
        var list = editor.querySelector('.mix-navigation-list');
        function button(text, action) {
            var el = document.createElement('button');
            el.type = 'button'; el.className = 'mdui-btn ' + action; el.textContent = text;
            return el;
        }
        function addItem(parent, item) {
            var row = document.createElement('div');
            row.className = 'mix-hyperlink-item mix-navigation-item';
            row._navigation = item;
            var heading = document.createElement('div');
            heading.className = 'mix-hyperlink-item-heading';
            var number = document.createElement('span'); number.className = 'mix-navigation-number';
            heading.appendChild(number);
            heading.appendChild(button('删除模块', 'mix-delete-navigation'));
            row.appendChild(heading);
            var fields = document.createElement('div'); fields.className = 'mix-hyperlink-fields';
            [['标题', 'name', '留空不展示'], ['跳转链接', 'link', 'https:// 或站内地址，留空不展示'], ['图标类名', 'class', '例如 fab fa-github，可留空']].forEach(function (entry) {
                var label = document.createElement('label'); label.className = 'mix-hyperlink-field';
                var caption = document.createElement('span'); caption.textContent = entry[0];
                var input = document.createElement('input'); input.type = 'text'; input.dataset.field = entry[1];
                input.value = item[entry[1]] || ''; input.placeholder = entry[2];
                label.appendChild(caption); label.appendChild(input); fields.appendChild(label);
            });
            row.appendChild(fields);
            var label = document.createElement('label'); label.className = 'mdui-checkbox mix-navigation-target';
            var target = document.createElement('input'); target.type = 'checkbox'; target.dataset.field = 'target'; target.checked = item.target === '_blank';
            var icon = document.createElement('i'); icon.className = 'mdui-checkbox-icon';
            label.appendChild(target); label.appendChild(icon); label.appendChild(document.createTextNode('在新标签页打开')); row.appendChild(label);
            parent.appendChild(row); return row;
        }
        function serialize(parent) {
            return Array.prototype.map.call(parent.children, function (row, index) {
                row.querySelector('.mix-navigation-number').textContent = '模块 ' + (index + 1);
                var item = Object.assign({}, row._navigation);
                var fields = row.querySelector('.mix-hyperlink-fields');
                ['name', 'link', 'class'].forEach(function (key) { item[key] = fields.querySelector('[data-field="' + key + '"]').value; });
                item.target = row.querySelector('[data-field="target"]').checked ? '_blank' : (item.target && item.target !== '_blank' ? item.target : '_self');
                return item;
            });
        }
        var pendingSync = null;
        function scheduleSync() {
            if (pendingSync !== null) window.clearTimeout(pendingSync);
            pendingSync = window.setTimeout(synchronize, 120);
        }

        function synchronize() {
            if (pendingSync !== null) { window.clearTimeout(pendingSync); pendingSync = null; }
            data.value = serialize(list).map(function (item) { return JSON.stringify(item); }).join(','); }
        editor.addEventListener('input', scheduleSync); editor.addEventListener('change', synchronize);
        editor.addEventListener('click', function (event) {
            var action = event.target.closest('button'); if (!action) return;
            var row = action.closest('.mix-navigation-item');
            if (action.classList.contains('mix-add-navigation')) addItem(list, {}).querySelector('input').focus();
            if (action.classList.contains('mix-delete-navigation')) row.remove();
            synchronize();
        });
        form.addEventListener('submit', synchronize);
        form.addEventListener('formdata', function (event) {
            synchronize();
            event.formData.set(data.name, data.value);
        });
        var items;
        try { var raw = data.value.trim().replace(/,\s*$/, ''); items = JSON.parse(raw.charAt(0) === '[' ? raw : '[' + raw + ']'); } catch (error) { return; }
        if (Array.isArray(items)) items.forEach(function (item) { addItem(list, item); });
        synchronize();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeNavigation);
    else initializeNavigation();
})();

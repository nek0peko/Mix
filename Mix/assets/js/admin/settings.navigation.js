(function () {
    function initializeNavigation() {
        var form = document.getElementById('mix-theme-settings');
        if (!form) return;
        var editor = form.querySelector('#mix-navigation-editor');
        var data = form.querySelector('input[name="headnavItems"]');
        if (!editor || !data || editor.dataset.initialized) return;
        editor.dataset.initialized = 'true';
        var list = editor.querySelector('.mix-navigation-list');
        var button = window.MixSettingControls.button;
        var field = window.MixSettingControls.field;
        var moveRow = window.MixSettingControls.moveRow;
        var systems = {home: '主页', articles: '文章', search: '搜索', stats: '统计', friends: '友链'};
        function addItem(parent, item) {
            var row = document.createElement('div');
            row.className = 'mix-hyperlink-item mix-navigation-item';
            row._navigation = item;
            var builtin = systems[item.builtin];
            if (builtin) row.classList.add('mix-navigation-builtin');
            if (item.builtin === 'friends' && editor.dataset.friendsAvailable !== '1') row.hidden = true;
            var heading = document.createElement('div');
            heading.className = 'mix-hyperlink-item-heading';
            var options = document.createElement('div'); options.className = 'mix-navigation-options';
            heading.appendChild(options);
            var actions = document.createElement('div'); actions.className = 'mix-link-actions';
            if (!builtin) actions.appendChild(button('删除模块', 'mix-delete-navigation'));
            actions.appendChild(button('上移模块', 'mix-move-navigation-up'));
            actions.appendChild(button('下移模块', 'mix-move-navigation-down'));
            heading.appendChild(actions);
            row.appendChild(heading);
            var enabledLabel = document.createElement('label'); enabledLabel.className = 'mdui-checkbox mix-navigation-enabled';
            var enabled = document.createElement('input'); enabled.type = 'checkbox'; enabled.dataset.field = 'enabled'; enabled.checked = item.enabled !== false;
            var enabledIcon = document.createElement('i'); enabledIcon.className = 'mdui-checkbox-icon';
            enabledLabel.appendChild(enabled); enabledLabel.appendChild(enabledIcon); enabledLabel.appendChild(document.createTextNode(builtin || '启用模块'));
            options.appendChild(enabledLabel);
            if (builtin) {
                var badge = document.createElement('span'); badge.className = 'mix-builtin-badge'; badge.textContent = '内置'; heading.insertBefore(badge, actions);
                parent.appendChild(row); return row;
            }
            var fields = document.createElement('div'); fields.className = 'mix-hyperlink-fields';
            [['标题', 'name', '留空不展示'], ['跳转链接', 'link', 'https:// 或站内地址，留空不展示'], ['图标类名', 'class', '例如 fab fa-github，可留空']].forEach(function (entry) {
                fields.appendChild(field(entry[0], entry[1], item[entry[1]], entry[2]));
            });
            row.appendChild(fields);
            var label = document.createElement('label'); label.className = 'mdui-checkbox mix-navigation-target';
            var target = document.createElement('input'); target.type = 'checkbox'; target.dataset.field = 'target'; target.checked = item.target === '_blank';
            var icon = document.createElement('i'); icon.className = 'mdui-checkbox-icon';
            label.appendChild(target); label.appendChild(icon); label.appendChild(document.createTextNode('在新标签页打开')); options.appendChild(label);
            parent.appendChild(row); return row;
        }
        function serialize(parent) {
            return Array.prototype.map.call(parent.children, function (row, index) {
                var visible = Array.prototype.filter.call(parent.children, function (item) { return !item.hidden; });
                var position = visible.indexOf(row);
                row.querySelector('.mix-move-navigation-up').disabled = position <= 0;
                row.querySelector('.mix-move-navigation-down').disabled = position === visible.length - 1;
                var item = Object.assign({}, row._navigation);
                item.enabled = row.querySelector('[data-field="enabled"]').checked;
                if (systems[item.builtin]) return {builtin: item.builtin, enabled: item.enabled};
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
            if (action.classList.contains('mix-add-navigation')) addItem(list, {}).querySelector('[data-field="name"]').focus();
            if (action.classList.contains('mix-delete-navigation') && !systems[row._navigation.builtin]) row.remove();
            if (action.classList.contains('mix-move-navigation-up') || action.classList.contains('mix-move-navigation-down')) {
                var up = action.classList.contains('mix-move-navigation-up');
                var adjacent = up ? row.previousElementSibling : row.nextElementSibling;
                while (adjacent && adjacent.hidden) adjacent = up ? adjacent.previousElementSibling : adjacent.nextElementSibling;
                if (adjacent) {
                    // Put the nearest visible row beside this one before the shared animation.
                    if (up && row.previousElementSibling !== adjacent) list.insertBefore(row, adjacent.nextElementSibling);
                    if (!up && row.nextElementSibling !== adjacent) list.insertBefore(row, adjacent);
                    moveRow(row, up);
                }
                action.focus({preventScroll: true});
            }
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

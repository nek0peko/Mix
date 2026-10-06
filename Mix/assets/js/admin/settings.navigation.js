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
            if (action === 'mix-move-navigation-up' || action === 'mix-move-navigation-down') {
                el.classList.add('mix-link-step-button');
                el.setAttribute('aria-label', text);
                el.title = text;
                var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('viewBox', '0 0 24 24');
                svg.setAttribute('aria-hidden', 'true');
                svg.setAttribute('focusable', 'false');
                var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('d', action === 'mix-move-navigation-up' ? 'm6 14 6-6 6 6' : 'm6 10 6 6 6-6');
                svg.appendChild(path);
                el.textContent = '';
                el.appendChild(svg);
            }
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
            var actions = document.createElement('div'); actions.className = 'mix-link-actions';
            actions.appendChild(button('删除模块', 'mix-delete-navigation'));
            actions.appendChild(button('上移模块', 'mix-move-navigation-up'));
            actions.appendChild(button('下移模块', 'mix-move-navigation-down'));
            heading.appendChild(actions);
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
                row.querySelector('.mix-move-navigation-up').disabled = index === 0;
                row.querySelector('.mix-move-navigation-down').disabled = index === parent.children.length - 1;
                var item = Object.assign({}, row._navigation);
                var fields = row.querySelector('.mix-hyperlink-fields');
                ['name', 'link', 'class'].forEach(function (key) { item[key] = fields.querySelector('[data-field="' + key + '"]').value; });
                item.target = row.querySelector('[data-field="target"]').checked ? '_blank' : (item.target && item.target !== '_blank' ? item.target : '_self');
                return item;
            });
        }
        var pendingSync = null;
        function moveRow(row, upward) {
            var neighbor = upward ? row.previousElementSibling : row.nextElementSibling;
            if (!neighbor) return;
            var elements = Array.prototype.slice.call(list.children);
            elements.forEach(function (element) {
                if (element._mixMoveAnimation) element._mixMoveAnimation.cancel();
            });
            var positions = elements.map(function (element) { return element.getBoundingClientRect().top; });
            if (upward) list.insertBefore(row, neighbor);
            else list.insertBefore(neighbor, row);
            if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            elements.forEach(function (element, index) {
                var distance = positions[index] - element.getBoundingClientRect().top;
                if (!distance || !element.animate) return;
                element._mixMoveAnimation = element.animate([
                    {transform: 'translateY(' + distance + 'px)'},
                    {transform: 'translateY(0)'}
                ], {duration: 240, easing: 'cubic-bezier(0.2, 0, 0, 1)'});
            });
        }
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
            if (action.classList.contains('mix-move-navigation-up') || action.classList.contains('mix-move-navigation-down')) {
                moveRow(row, action.classList.contains('mix-move-navigation-up'));
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

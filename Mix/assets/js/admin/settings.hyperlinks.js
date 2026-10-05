(function () {
    function initializeHyperlinks() {
        var form = document.getElementById('mix-theme-settings');
        if (!form) return;
        var editor = form.querySelector('#mix-hyperlink-editor');
        var data = form.querySelector('input[name="HyperlinkModules"]');
        if (!editor || !data) return;
        if (editor.dataset.initialized) return;
        editor.dataset.initialized = 'true';

        var list = editor.querySelector('.mix-hyperlink-modules');
        var nextId = 0;

        function button(text, className) {
            var element = document.createElement('button');
            element.type = 'button';
            element.className = 'mdui-btn ' + className;
            var symbols = {'mix-add-link': '+', 'mix-delete-link': '-', 'mix-move-module-up': '↑', 'mix-move-module-down': '↓', 'mix-move-link-up': '↑', 'mix-move-link-down': '↓'};
            element.textContent = symbols[className] || text;
            if (symbols[className]) {
                var paths = {'+': 'M12 5v14M5 12h14', '-': 'M5 12h14', '↑': 'm6 14 6-6 6 6', '↓': 'm6 10 6 6 6-6'};
                var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('viewBox', '0 0 24 24');
                svg.setAttribute('aria-hidden', 'true');
                svg.setAttribute('focusable', 'false');
                var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('d', paths[symbols[className]]);
                svg.appendChild(path);
                element.textContent = '';
                element.appendChild(svg);
            }
            if (symbols[className]) {
                element.classList.add('mix-link-step-button');
                element.setAttribute('aria-label', text);
                element.title = text;
            }
            return element;
        }

        function field(title, key, value, placeholder) {
            var label = document.createElement('label');
            label.className = 'mix-hyperlink-field';
            var caption = document.createElement('span');
            caption.textContent = title;
            var input = document.createElement('input');
            input.type = 'text';
            input.id = 'mix-hyperlink-field-' + (++nextId);
            input.dataset.field = key;
            input.value = value || '';
            input.placeholder = placeholder || '';
            label.htmlFor = input.id;
            label.appendChild(caption);
            label.appendChild(input);
            return label;
        }

        var friendsCard = form.querySelector('#mix-friends-module');
        var friendsPosition = form.querySelector('input[name="FriendsModulePosition"]');
        var friendsTitle = form.querySelector('input[name="FriendsModuleTitle"]');
        if (friendsCard && friendsTitle) {
            var titleField = field('友链模块标题', 'friends-title', friendsTitle.value, '留空不展示');
            friendsCard.querySelector('.mix-friends-title-slot').replaceWith(titleField);
            var friendsToggle = friendsCard.querySelector('.mdui-checkbox');
            friendsToggle.classList.add('mix-module-toggle');
            friendsCard.querySelector('.mix-checkbox-control').replaceWith(friendsToggle);
            var friendsActions = document.createElement('div');
            friendsActions.className = 'mix-module-actions';
            friendsActions.appendChild(button('上移模块', 'mix-move-module-up'));
            friendsActions.appendChild(button('下移模块', 'mix-move-module-down'));
            friendsCard.appendChild(friendsActions);
            titleField.querySelector('input').addEventListener('input', function (event) {
                friendsTitle.value = event.target.value;
            });
        }

        function addItem(module, item) {
            var row = document.createElement('div');
            row.className = 'mix-hyperlink-item';
            var heading = document.createElement('div');
            heading.className = 'mix-hyperlink-item-heading';
            var number = document.createElement('span');
            number.className = 'mix-hyperlink-item-number';
            heading.appendChild(number);
            var actions = document.createElement('div');
            actions.className = 'mix-link-actions';
            actions.appendChild(button('删除链接', 'mix-delete-link'));
            actions.appendChild(button('上移链接', 'mix-move-link-up'));
            actions.appendChild(button('下移链接', 'mix-move-link-down'));
            heading.appendChild(actions);
            row.appendChild(heading);
            var fields = document.createElement('div');
            fields.className = 'mix-hyperlink-fields';
            fields.appendChild(field('标题', 'title', item.title, '留空不展示'));
            fields.appendChild(field('链接', 'url', item.url, 'https:// 或站内地址，留空不展示'));
            fields.appendChild(field('介绍', 'description', item.description, '可留空'));
            row.appendChild(fields);
            module.querySelector('.mix-hyperlink-items').appendChild(row);
            return row;
        }

        function addModule(module) {
            var section = document.createElement('section');
            section.className = 'mix-hyperlink-module';
            var heading = document.createElement('div');
            heading.className = 'mix-hyperlink-module-heading';
            heading.appendChild(field('模块标题', 'module-title', module.title, '留空不展示'));
            var toggle = document.createElement('label');
            toggle.className = 'mdui-checkbox mix-module-toggle';
            var checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.dataset.field = 'module-enabled';
            checkbox.checked = module.enabled !== false;
            toggle.appendChild(checkbox);
            var icon = document.createElement('i');
            icon.className = 'mdui-checkbox-icon';
            toggle.appendChild(icon);
            toggle.appendChild(document.createTextNode('启用模块'));
            heading.appendChild(toggle);
            heading.appendChild(button('删除模块', 'mix-delete-module'));
            section.appendChild(heading);
            var items = document.createElement('div');
            items.className = 'mix-hyperlink-items';
            var content = document.createElement('div');
            content.className = 'mix-module-content';
            content.appendChild(items);
            var actions = document.createElement('div');
            actions.className = 'mix-module-actions';
            actions.appendChild(button('添加链接', 'mix-add-link'));
            actions.appendChild(button('上移模块', 'mix-move-module-up'));
            actions.appendChild(button('下移模块', 'mix-move-module-down'));
            content.appendChild(actions);
            section.appendChild(content);
            if (friendsCard && list.lastElementChild === friendsCard) list.insertBefore(section, friendsCard);
            else list.appendChild(section);
            (module.items || []).forEach(function (item) { addItem(section, item); });
            return section;
        }

        function moveRow(row, upward) {
            var parent = row.parentElement;
            var neighbor = upward ? row.previousElementSibling : row.nextElementSibling;
            if (!neighbor) return;
            var elements = Array.prototype.slice.call(parent.children);
            elements.forEach(function (element) {
                if (element._mixMoveAnimation) element._mixMoveAnimation.cancel();
            });
            var positions = elements.map(function (element) { return element.getBoundingClientRect().top; });
            if (upward) parent.insertBefore(row, neighbor);
            else parent.insertBefore(neighbor, row);
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

        var pendingSync = null;
        function scheduleSync() {
            if (pendingSync !== null) window.clearTimeout(pendingSync);
            pendingSync = window.setTimeout(synchronize, 120);
        }

        function synchronize() {
            if (pendingSync !== null) { window.clearTimeout(pendingSync); pendingSync = null; }
            var modules = [];
            Array.prototype.forEach.call(list.children, function (module, moduleIndex) {
                module.querySelector('.mix-move-module-up').disabled = moduleIndex === 0;
                module.querySelector('.mix-move-module-down').disabled = moduleIndex === list.children.length - 1;
                if (module === friendsCard) {
                    if (friendsPosition) friendsPosition.value = String(modules.length);
                    return;
                }
                var items = [];
                Array.prototype.forEach.call(module.querySelector('.mix-hyperlink-items').children, function (row, index) {
                    row.querySelector('.mix-hyperlink-item-number').textContent = '链接 ' + (index + 1);
                    row.querySelector('.mix-move-link-up').disabled = index === 0;
                    row.querySelector('.mix-move-link-down').disabled = index === module.querySelector('.mix-hyperlink-items').children.length - 1;
                    var item = {};
                    Array.prototype.forEach.call(row.querySelectorAll('input'), function (input) {
                        item[input.dataset.field] = input.value;
                    });
                    items.push(item);
                });
                modules.push({title: module.querySelector('[data-field="module-title"]').value, enabled: module.querySelector('[data-field="module-enabled"]').checked, items: items});
            });
            data.value = JSON.stringify(modules);
        }

        editor.addEventListener('input', scheduleSync);
        editor.addEventListener('click', function (event) {
            var action = event.target.closest('button');
            if (!action) return;
            var module = action.closest('.mix-hyperlink-module');
            var focusTarget;
            if (action.classList.contains('mix-add-module')) {
                focusTarget = addModule({title: '', items: [{title: '', url: '', description: ''}]}).querySelector('input');
            } else if (action.classList.contains('mix-add-link')) {
                focusTarget = addItem(module, {title: '', url: '', description: ''}).querySelector('input');
            } else if (action.classList.contains('mix-delete-link')) {
                action.closest('.mix-hyperlink-item').remove();
                focusTarget = module.querySelector('.mix-add-link');
            } else if (action.classList.contains('mix-move-link-up')) {
                var row = action.closest('.mix-hyperlink-item');
                moveRow(row, true);
                focusTarget = row.querySelector('[data-field="title"]');
            } else if (action.classList.contains('mix-move-link-down')) {
                var row = action.closest('.mix-hyperlink-item');
                moveRow(row, false);
                focusTarget = row.querySelector('[data-field="title"]');
            } else if (action.classList.contains('mix-move-module-up')) {
                moveRow(module, true);
                focusTarget = module.querySelector('[data-field="module-title"], [data-field="friends-title"]');
            } else if (action.classList.contains('mix-move-module-down')) {
                moveRow(module, false);
                focusTarget = module.querySelector('[data-field="module-title"], [data-field="friends-title"]');
            } else if (action.classList.contains('mix-delete-module')) {
                if (!window.confirm('确定删除这个模块及其中的全部链接吗？保存设置后生效。')) return;
                module.remove();
                focusTarget = editor.querySelector('.mix-add-module');
            }
            synchronize();
            if (focusTarget) focusTarget.focus({preventScroll: action.classList.contains('mix-move-link-up') || action.classList.contains('mix-move-link-down') || action.classList.contains('mix-move-module-up') || action.classList.contains('mix-move-module-down')});
        });

        editor.addEventListener('change', function (event) {
            synchronize();
        });
        form.addEventListener('submit', synchronize);
        form.addEventListener('formdata', function (event) {
            synchronize();
            event.formData.set(data.name, data.value);
        });
        var modules;
        try { modules = JSON.parse(data.value); } catch (error) { modules = []; }
        if (Array.isArray(modules)) modules.forEach(addModule);
        if (friendsCard && friendsPosition) {
            var position = parseInt(friendsPosition.value, 10);
            if (!Number.isFinite(position) || position < 0) position = list.children.length;
            var wrapper = friendsCard.parentElement;
            list.insertBefore(friendsCard, list.children[Math.min(position, list.children.length)] || null);
            wrapper.remove();
        }
        synchronize();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeHyperlinks);
    } else {
        initializeHyperlinks();
    }
})();

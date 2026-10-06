(function () {
    if (window.MixSettingControls) return;
    var nextId = 0;
    function button(text, action) {
        var element = document.createElement('button');
        element.type = 'button';
        element.className = 'mdui-btn ' + action;
        element.textContent = text;
        var paths = {
            'mix-add-link': 'M12 5v14M5 12h14', 'mix-delete-link': 'M5 12h14',
            'mix-move-module-up': 'm6 14 6-6 6 6', 'mix-move-module-down': 'm6 10 6 6 6-6',
            'mix-move-link-up': 'm6 14 6-6 6 6', 'mix-move-link-down': 'm6 10 6 6 6-6',
            'mix-move-navigation-up': 'm6 14 6-6 6 6', 'mix-move-navigation-down': 'm6 10 6 6 6-6'
        };
        if (paths[action]) {
            var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('viewBox', '0 0 24 24');
            svg.setAttribute('aria-hidden', 'true');
            svg.setAttribute('focusable', 'false');
            var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', paths[action]);
            svg.appendChild(path);
            element.textContent = '';
            element.appendChild(svg);
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
        input.id = 'mix-setting-field-' + (++nextId);
        input.dataset.field = key;
        input.value = value || '';
        input.placeholder = placeholder || '';
        label.htmlFor = input.id;
        label.appendChild(caption);
        label.appendChild(input);
        return label;
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
                { transform: 'translateY(' + distance + 'px)' }, { transform: 'translateY(0)' }
            ], { duration: 240, easing: 'cubic-bezier(0.2, 0, 0, 1)' });
        });
    }
    window.MixSettingControls = { button: button, field: field, moveRow: moveRow };
})();

(function () {
    function initializeValidation() {
        var form = document.getElementById('mix-theme-settings');
        if (!form || form.dataset.mixValidation) return;
        form.dataset.mixValidation = 'true';
        var selector = '.mix-hyperlink-item [data-field="url"], .mix-icon-row [data-field="icon"], .mix-navigation-item [data-field="link"], [name="CDNURL"], [name="FriendURL"]:not([type="hidden"]), [name="HeaderPhoto"], [name="BackGroundImage"], [name="BackGroundImageDark"]';
        function validUrl(value) {
            value = value.trim();
            if (!value) return true;
            if (/[\x00-\x20]/.test(value)) return false;
            if (value.charAt(0) === '#' || (value.charAt(0) === '/' && value.charAt(1) !== '/')) return true;
            if (!/^https?:\/\//i.test(value) || /[^\x21-\x7e]/.test(value)) return false;
            try {
                var parsed = new URL(value);
                if (!parsed.hostname) return false;
                if (parsed.hostname.charAt(0) === '[') return true;
                return parsed.hostname.replace(/\.$/, '').split('.').every(function (label) {
                    return label.length <= 63 && /^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/i.test(label);
                });
            } catch (error) { return false; }
        }
        form.addEventListener('input', function (event) {
            if (event.target.matches(selector)) event.target.setCustomValidity('');
        });
        form.addEventListener('submit', function (event) {
            var invalid = Array.prototype.find.call(form.querySelectorAll(selector), function (input) {
                if (input.name === 'CDNURL' && input.value.trim() && !/^https?:\/\//i.test(input.value.trim())) return true;
                return !validUrl(input.value);
            });
            if (!invalid) return;
            event.preventDefault();
            event.stopImmediatePropagation();
            var parent = invalid.parentElement;
            while (parent && parent !== form) {
                if (parent.classList.contains('mdui-panel-item')) {
                    parent.classList.add('mdui-panel-item-open');
                    var body = parent.querySelector(':scope > .mdui-panel-item-body');
                    if (body) body.style.height = 'auto';
                }
                parent = parent.parentElement;
            }
            invalid.setCustomValidity('请填写完整的 http://、https:// 链接或站内地址，不能包含空格；也可以留空');
            requestAnimationFrame(function () {
                invalid.scrollIntoView({block: 'center'});
                invalid.focus();
                invalid.reportValidity();
            });
        }, true);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeValidation);
    else initializeValidation();
})();

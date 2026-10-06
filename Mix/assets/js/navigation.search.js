(function () {
    'use strict';
    if (window.MixNavigationSearch) return;
    var closeTimer = null;
    var hover = window.matchMedia('(hover: hover) and (pointer: fine)');

    function cancelClose() {
        if (closeTimer !== null) window.clearTimeout(closeTimer);
        closeTimer = null;
    }

    function position(panel) {
        panel.style.setProperty('--mix-search-shift', '0px');
        if (window.innerWidth <= 600) return;
        var bounds = panel.getBoundingClientRect();
        var shift = Math.max(16 - bounds.left, 0) + Math.min(window.innerWidth - 16 - bounds.right, 0);
        panel.style.setProperty('--mix-search-shift', shift + 'px');
    }

    function open(toggle, focus) {
        var panel = document.getElementById(toggle.getAttribute('aria-controls'));
        if (!panel) return;
        cancelClose();
        panel.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
        position(panel);
        if (focus) panel.querySelector('input').focus({preventScroll: true});
    }

    function leave(group) {
        cancelClose();
        closeTimer = window.setTimeout(function () {
            closeTimer = null;
            var focused = document.activeElement;
            if ((group.contains(focused) && !focused.matches('[data-mix-search-toggle]')) || (hover.matches && group.matches(':hover'))) return;
            close(false);
        }, 140);
    }

    function close(restoreFocus) {
        cancelClose();
        document.querySelectorAll('[data-mix-search-toggle]').forEach(function (toggle) {
            var panel = document.getElementById(toggle.getAttribute('aria-controls'));
            if (!panel || panel.hidden) return;
            panel.hidden = true;
            toggle.setAttribute('aria-expanded', 'false');
            if (restoreFocus) toggle.focus({preventScroll: true});
        });
    }

    document.addEventListener('click', function (event) {
        var toggle = event.target.closest('[data-mix-search-toggle]');
        if (toggle) {
            var panel = document.getElementById(toggle.getAttribute('aria-controls'));
            if (!panel) return;
            var opening = panel.hidden || hover.matches;
            close(false);
            if (opening) open(toggle, true);
        } else if (!event.target.closest('.mix-nav-search')) {
            close(false);
        }
    });
    document.addEventListener('pointerover', function (event) {
        if (!hover.matches) return;
        var group = event.target.closest('.mix-nav-search');
        if (!group) return;
        cancelClose();
        if (event.relatedTarget && group.contains(event.relatedTarget)) return;
        open(group.querySelector('[data-mix-search-toggle]'), false);
    });
    document.addEventListener('pointerout', function (event) {
        if (!hover.matches) return;
        var group = event.target.closest('.mix-nav-search');
        if (!group || (event.relatedTarget && group.contains(event.relatedTarget))) return;
        leave(group);
    });
    document.addEventListener('focusout', function (event) {
        var group = event.target.closest('.mix-nav-search');
        if (group) leave(group);
    });
    window.addEventListener('resize', function () {
        document.querySelectorAll('[data-mix-search-panel]').forEach(function (panel) {
            if (!panel.hidden) position(panel);
        });
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') close(true);
    });
    document.addEventListener('submit', function (event) {
        if (event.target.matches('.mix-nav-search-form')) close(false);
    });
    document.addEventListener('pjax:send', function () { close(false); });
    if (window.jQuery) window.jQuery(document).on('pjax:send.mixNavigationSearch', function () { close(false); });
    window.MixNavigationSearch = {close: close};
})();

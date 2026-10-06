(function () {
    'use strict';
    if (window.MixArticleNavigation) return;
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var mobileDrawer = window.matchMedia('(max-width: 600px), (max-height: 500px) and (pointer: coarse)');
    var hoverTimer = null, closeTimer = null, hoverOpened = false;
    var regionSelector = '.mix-article-navigation, .mix-article-navigation-toggle';
    function cancelTimers() {
        clearTimeout(hoverTimer); clearTimeout(closeTimer);
        hoverTimer = null; closeTimer = null;
    }
    function drawer() { return document.querySelector('.mix-article-navigation'); }
    function positionDrawer() {
        var trigger = document.querySelector('.mix-article-navigation-toggle');
        var tools = document.querySelector('.action');
        if (trigger && trigger._mixInitialized) {
            if (mobileDrawer.matches && tools && trigger.parentElement !== tools) tools.insertBefore(trigger, tools.firstChild);
            else if (!mobileDrawer.matches && trigger.parentElement !== document.body) document.body.appendChild(trigger);
        }
        var header = document.getElementById('header');
        var top = Math.max(16, (header ? header.getBoundingClientRect().bottom : 56) + 16);
        document.querySelectorAll(regionSelector).forEach(function (element) {
            element.style.setProperty('--mix-navigation-top', top + 'px');
        });
        var navigation = drawer();
        if (trigger && navigation) navigation.style.setProperty('--mix-navigation-offset', (trigger.getBoundingClientRect().height + 10) + 'px');
        if (navigation && window.visualViewport) {
            var viewport = window.visualViewport;
            navigation.style.setProperty('--mix-sheet-height', Math.max(80, Math.min(viewport.height * .65, viewport.height - 80)) + 'px');
            navigation.style.setProperty('--mix-sheet-lift', Math.max(0, window.innerHeight - viewport.height - viewport.offsetTop) + 'px');
        }
    }
    function toggleDrawer(open, restoreFocus, focusFilter) {
        cancelTimers();
        var navigation = drawer();
        var trigger = document.querySelector('.mix-article-navigation-toggle');
        if (!navigation || !trigger) return;
        if (!open || focusFilter !== false) hoverOpened = false;
        else if (!navigation.classList.contains('is-open')) hoverOpened = true;
        navigation.classList.toggle('is-open', open);
        navigation.setAttribute('aria-hidden', String(!open));
        trigger.setAttribute('aria-expanded', String(open));
        if (open) {
            navigation.removeAttribute('inert');
            positionDrawer();
            if (!navigation._mixRevealed) {
                var panel = navigation.querySelector('.mix-article-navigation-panel');
                var current = panel.querySelector('.mix-article-post-link.is-current');
                if (current) {
                    var bounds = panel.getBoundingClientRect(), post = current.getBoundingClientRect();
                    if (post.bottom > bounds.bottom - 8) panel.scrollTop += post.bottom - bounds.bottom + 8;
                    else if (post.top < bounds.top + 8) panel.scrollTop -= bounds.top + 8 - post.top;
                }
                navigation._mixRevealed = true;
            }
            if (mobileDrawer.matches) {
                var menu = document.getElementById('headerr');
                if (menu) menu.classList.remove('Header_show__3R4Sq', 'global-show');
            }
            if (focusFilter !== false && !mobileDrawer.matches) navigation.querySelector('.mix-tree-filter').focus({preventScroll: true});
        } else {
            if (restoreFocus) trigger.focus({preventScroll: true});
            else if (navigation.contains(document.activeElement)) document.activeElement.blur();
            navigation.setAttribute('inert', '');
        }
    }
    document.addEventListener('pointerover', function (event) {
        if (event.pointerType !== 'mouse' || !event.target.closest(regionSelector)) return;
        if (event.relatedTarget && event.relatedTarget.closest && event.relatedTarget.closest(regionSelector)) {
            clearTimeout(closeTimer);
            closeTimer = null;
            return;
        }
        cancelTimers();
        hoverTimer = setTimeout(function () { toggleDrawer(true, false, false); }, 80);
    });
    document.addEventListener('pointerout', function (event) {
        if (event.pointerType !== 'mouse' || !event.target.closest(regionSelector)) return;
        if (event.relatedTarget && event.relatedTarget.closest && event.relatedTarget.closest(regionSelector)) return;
        cancelTimers();
        closeTimer = setTimeout(function () {
            var navigation = drawer();
            if (navigation && !navigation.contains(document.activeElement)) toggleDrawer(false, false);
        }, 200);
    });
    function setOpen(branch, open, animate) {
        var content = branch.querySelector(':scope > .mix-category-contents');
        var height = branch.open && content ? content.getBoundingClientRect().height : 0;
        if (branch._mixAnimation) {
            branch._mixAnimation.onfinish = null;
            branch._mixAnimation.cancel();
            branch._mixAnimation = null;
        }
        branch._mixTargetOpen = open;
        if (!content || !animate || reducedMotion.matches || !content.animate) {
            branch.open = open;
            branch.removeAttribute('data-closing');
            return;
        }
        branch.open = true;
        if (open) branch.removeAttribute('data-closing');
        else branch.setAttribute('data-closing', '');
        var target = open ? content.scrollHeight : 0;
        var animation = content.animate([{height: height + 'px'}, {height: target + 'px'}], {duration: 200, easing: 'cubic-bezier(.2,.7,.2,1)', fill: 'both'});
        branch._mixAnimation = animation;
        animation.onfinish = function () {
            branch.open = open;
            branch.removeAttribute('data-closing');
            branch._mixAnimation = null;
            animation.cancel();
        };
    }
    function filter(panel, query) {
        var terms = query.trim().toLocaleLowerCase().split(/\s+/).filter(Boolean);
        var searching = terms.length > 0;
        if (!searching && !panel._mixFiltering) return;
        var branches = Array.from(panel.querySelectorAll('details'));
        if (searching && !panel._mixFiltering) {
            branches.forEach(function (branch) { branch._mixBeforeFilter = branch._mixTargetOpen === undefined ? branch.open : branch._mixTargetOpen; });
        }
        panel._mixFiltering = searching;
        var matches = 0;
        panel.querySelectorAll('.mix-article-post-link').forEach(function (link) {
            var title = link.textContent.toLocaleLowerCase();
            var match = !searching || terms.every(function (term) { return title.includes(term); });
            link.parentElement.hidden = !match;
            if (match) matches++;
        });
        // Children first, so a parent stays visible whenever a descendant matches.
        branches.reverse().forEach(function (branch) {
            var links = branch.querySelectorAll('.mix-article-post-link');
            var match = Array.from(links).some(function (link) { return !link.parentElement.hidden; });
            branch.parentElement.hidden = searching && !match;
            setOpen(branch, searching ? match : !!branch._mixBeforeFilter, false);
        });
        var empty = panel.querySelector('.mix-tree-empty');
        if (empty) empty.hidden = !searching || matches > 0;
    }
    document.addEventListener('input', function (event) {
        if (!event.target.matches('.mix-tree-filter')) return;
        filter(event.target.closest('.mix-article-navigation').querySelector('.mix-article-navigation-panel'), event.target.value);
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && drawer() && drawer().classList.contains('is-open')) {
            event.preventDefault();
            toggleDrawer(false, true);
            return;
        }
        if (!event.target.matches('.mix-tree-filter') || event.key !== 'Enter') return;
        event.preventDefault();
    });
    document.addEventListener('click', function (event) {
        if (event.target.closest('.mix-article-navigation-toggle')) {
            event.preventDefault();
            toggleDrawer(hoverOpened || !drawer().classList.contains('is-open'), false);
            return;
        }
        var navigation = drawer();
        if (navigation && navigation.classList.contains('is-open') && (!navigation.contains(event.target) || event.target.closest('.mix-article-post-link'))) {
            toggleDrawer(false, false);
        }
        var summary = event.target.closest('.mix-article-navigation summary');
        if (!summary) return;
        event.preventDefault();
        event.stopPropagation();
        var branch = summary.parentElement;
        var open = branch._mixTargetOpen === undefined ? branch.open : branch._mixTargetOpen;
        setOpen(branch, !open, true);
    }, true);
    function init() {
        var incoming = document.querySelector('main.mix-reading-page .mix-article-navigation');
        if (incoming || !document.querySelector('main.mix-reading-page')) cleanup();
        var navigation = drawer();
        var trigger = document.querySelector('.mix-article-navigation-toggle');
        if (navigation && trigger) {
            trigger._mixInitialized = true;
            // Fixed drawers must escape any transformed Pjax or article ancestors.
            if (navigation.parentElement !== document.body) document.body.appendChild(navigation);
            if (trigger.parentElement !== document.body) document.body.appendChild(trigger);
            trigger.hidden = false;
            positionDrawer();
        }
        document.querySelectorAll('.mix-article-navigation details').forEach(function (branch) {
            if (!branch._mixAnimation) branch._mixTargetOpen = branch.open;
        });
    }
    // Remove detached article UI before Pjax installs the next page.
    function cleanup() {
        cancelTimers();
        hoverOpened = false;
        document.querySelectorAll('body > .mix-article-navigation, body > .mix-article-navigation-toggle, .action > .mix-article-navigation-toggle').forEach(function (element) { element.remove(); });
    }
    window.MixArticleNavigation = {init: init};
    window.addEventListener('resize', positionDrawer);
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', positionDrawer);
        window.visualViewport.addEventListener('scroll', positionDrawer);
    }
    document.addEventListener('focusin', function (event) {
        var navigation = drawer();
        if (navigation && navigation.classList.contains('is-open') && !navigation.contains(event.target) && !event.target.closest('.mix-article-navigation-toggle')) toggleDrawer(false, false);
    });
    document.addEventListener('pjax:beforeReplace', cleanup);
    document.addEventListener('pjax:complete', init);
    if (window.jQuery) window.jQuery(document).on('pjax:beforeReplace.mixArticleNavigation', cleanup).on('pjax:complete.mixArticleNavigation', init);
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();

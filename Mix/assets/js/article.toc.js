(function () {
    'use strict';
    if (window.MixArticleToc) return;
    var entries = [], nav = null, active = null, frame = null, dirty = true, observer = null;
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    function offset() {
        var header = document.getElementById('header');
        return (header ? header.getBoundingClientRect().height : 56) + 24;
    }
    function select(entry) {
        if (!entry || active === entry) return;
        if (active) {
            active.link.classList.remove('is-active');
            active.link.removeAttribute('aria-current');
        }
        active = entry;
        active.link.classList.add('is-active');
        active.link.setAttribute('aria-current', 'location');
        var bounds = nav.getBoundingClientRect(), linkBounds = active.link.getBoundingClientRect();
        if (bounds.height && linkBounds.top < bounds.top + 4) nav.scrollTop -= bounds.top + 4 - linkBounds.top;
        else if (bounds.height && linkBounds.bottom > bounds.bottom - 4) nav.scrollTop += linkBounds.bottom - bounds.bottom + 4;
    }
    function update() {
        frame = null;
        if (!nav || !nav.isConnected || !entries.length) return;
        if (dirty) {
            entries.forEach(function (entry) { entry.top = entry.heading.getBoundingClientRect().top + window.scrollY; });
            dirty = false;
        }
        var position = window.scrollY + offset() + 2, low = 0, high = entries.length - 1;
        while (low < high) {
            var mid = Math.ceil((low + high) / 2);
            if (entries[mid].top <= position) low = mid;
            else high = mid - 1;
        }
        select(entries[low]);
    }
    function schedule(measure) {
        if (!nav || !entries.length) return;
        if (measure) dirty = true;
        if (frame === null) frame = window.requestAnimationFrame(update);
    }
    function revealCurrentPost() {
        var categoryPanel = document.querySelector('.mix-article-navigation-panel');
        var currentPost = categoryPanel && categoryPanel.querySelector('.mix-article-post-link.is-current');
        if (currentPost && currentPost.getClientRects().length) {
            var panelBounds = categoryPanel.getBoundingClientRect(), postBounds = currentPost.getBoundingClientRect();
            if (panelBounds.height && postBounds.bottom > panelBounds.bottom - 8) categoryPanel.scrollTop += postBounds.bottom - panelBounds.bottom + 8;
            else if (panelBounds.height && postBounds.top < panelBounds.top + 8) categoryPanel.scrollTop -= panelBounds.top + 8 - postBounds.top;
        }
    }
    function init() {
        if (observer) observer.disconnect();
        revealCurrentPost();
        nav = document.querySelector('.mix-toc');
        entries = []; active = null;
        if (window.ResizeObserver) {
            observer = new ResizeObserver(function () { schedule(true); });
            var content = document.getElementById('write');
            if (content) observer.observe(content);
            var article = document.querySelector('main.is-article');
            if (article) observer.observe(article);
        }
        if (!nav) return;
        nav.querySelectorAll('.mix-toc-link').forEach(function (link) {
            var anchor = document.getElementsByName(link.hash.slice(1))[0] || document.getElementById(link.hash.slice(1));
            if (!anchor) return;
            link.classList.remove('is-active');
            link.removeAttribute('aria-current');
            entries.push({link: link, heading: anchor.closest('h1,h2,h3,h4,h5,h6') || anchor, top: 0});
        });
        schedule(true);
    }
    document.addEventListener('click', function (event) {
        var link = event.target.closest('.mix-toc-link');
        if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        var entry = entries.find(function (item) { return item.link === link; });
        if (!entry) return;
        event.preventDefault();
        event.stopPropagation();
        if (location.hash !== link.hash) history.pushState(null, '', link.hash);
        window.scrollTo({top: Math.max(0, entry.heading.getBoundingClientRect().top + window.scrollY - offset()), behavior: reducedMotion.matches ? 'auto' : 'smooth'});
    }, true);
    window.addEventListener('scroll', function () { schedule(false); }, {passive: true});
    window.addEventListener('resize', function () { revealCurrentPost(); schedule(true); });
    window.addEventListener('hashchange', function () { schedule(true); });
    window.addEventListener('pageshow', init);
    document.addEventListener('load', function (event) { if (event.target.tagName === 'IMG') schedule(true); }, true);
    document.addEventListener('pjax:complete', init);
    if (window.jQuery) window.jQuery(document).on('pjax:complete.mixToc', init);
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(function () { schedule(true); });
    window.MixArticleToc = {init: init};
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();

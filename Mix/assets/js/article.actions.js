(function () {
    'use strict';
    if (window.MixArticleActions) return;
    var current = null, controller = null;
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    function cleanup() {
        if (controller) controller.abort();
        controller = null;
        document.querySelectorAll('.action > .mix-article-actions').forEach(function (element) { element.remove(); });
        current = null;
    }
    function paint(group, data) {
        var button = group.querySelector('.mix-like-button');
        button.setAttribute('aria-pressed', String(data.liked));
        var icon = button.querySelector('i');
        icon.classList.toggle('far', !data.liked);
        icon.classList.toggle('fas', data.liked);
        button.title = data.liked ? '取消点赞' : '点赞';
        button.setAttribute('aria-label', button.title + '，共 ' + data.count + ' 个赞');
        group.querySelector('.mix-like-count').textContent = data.count > 99 ? '99+' : String(data.count);
        group._liked = data.liked;
        if (Number.isInteger(data.views) && data.views >= 0 && window.MixVisitorTracking) window.MixVisitorTracking.paint(group, data.views);
    }
    async function request(group, liked) {
        var button = group.querySelector('.mix-like-button');
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        controller = new AbortController();
        var activeController = controller;
        var timeout = setTimeout(function () { activeController.abort(); }, 6000);
        try {
            var options = { method: liked === undefined ? 'GET' : 'POST', credentials: 'same-origin', cache: 'no-store', headers: { 'X-Mix-Like': '1' }, signal: activeController.signal };
            if (liked !== undefined) {
                options.headers['Content-Type'] = 'application/json';
                options.body = JSON.stringify({ liked: liked });
            }
            var response = await fetch(group.dataset.endpoint, options);
            if (!response.ok) throw new Error('Like request failed');
            var data = await response.json();
            if (!data.ok || typeof data.liked !== 'boolean' || !Number.isInteger(data.count) || data.count < 0) throw new Error('Invalid like response');
            if (current !== group) return;
            paint(group, data);
            if (liked === true && !reducedMotion.matches && button.animate) {
                button.querySelector('i').animate([{ transform: 'scale(1)' }, { transform: 'scale(1.22)' }, { transform: 'scale(1)' }], { duration: 320, easing: 'ease-out' });
            }
        } catch (error) {
            if (current !== group) return;
            button.title = '点赞暂时不可用，点击重试';
            button.setAttribute('aria-label', button.title);
            if (liked !== undefined && window.ks && ks.notice) ks.notice('点赞暂时不可用，请稍后重试', { color: 'yellow', time: 2000 });
            if (group._liked === undefined) group.querySelector('.mix-like-count').textContent = '—';
        } finally {
            clearTimeout(timeout);
            if (controller === activeController) controller = null;
            if (current === group) {
                button.disabled = false;
                button.removeAttribute('aria-busy');
            }
        }
    }
    function init() {
        var incoming = document.querySelector('main [data-mix-article-actions]');
        var tools = document.querySelector('.action');
        if (!incoming) {
            var main = document.querySelector('main');
            if (!current || !main || main.dataset.mixActionsCid !== current.dataset.cid) cleanup();
            return;
        }
        cleanup();
        if (!tools) return;
        current = incoming;
        incoming.closest('main').dataset.mixActionsCid = incoming.dataset.cid;
        var navigation = tools.querySelector('.mix-article-navigation-toggle');
        tools.insertBefore(incoming, navigation ? navigation.nextSibling : tools.firstChild);
        incoming.hidden = false;
        incoming.querySelector('.mix-like-button').addEventListener('click', function () {
            // Retry a failed initial state request before permitting mutations.
            request(incoming, incoming._liked === undefined ? undefined : !incoming._liked);
        });
        var comment = incoming.querySelector('.mix-comment-button');
        var target = document.querySelector('main .mix-comment-section');
        if (comment && !target) comment.remove();
        else if (comment) comment.addEventListener('click', function () {
            var header = document.getElementById('header');
            var offset = Math.max(0, header ? header.getBoundingClientRect().bottom : 0) + 20;
            var top = Math.max(0, target.getBoundingClientRect().top + window.scrollY - offset);
            window.scrollTo({ top: top, behavior: reducedMotion.matches ? 'auto' : 'smooth' });
            var heading = target.querySelector('h1');
            if (heading) {
                heading.setAttribute('tabindex', '-1');
                heading.focus({ preventScroll: true });
            }
        });
        request(incoming);
    }
    document.addEventListener('pjax:beforeReplace', cleanup);
    document.addEventListener('mix:comments-updated', function (event) {
        var button = current && current.querySelector('.mix-comment-button');
        var target = document.querySelector('main .mix-comment-section');
        if (!button || !target || event.detail.cid !== target.dataset.cid || !Number.isInteger(event.detail.count) || event.detail.count < 0) return;
        var count = event.detail.count;
        button.querySelector('.mix-comment-count').textContent = count > 99 ? '99+' : String(count);
        button.title = '前往评论区，共 ' + count + ' 条评论';
        button.setAttribute('aria-label', button.title);
    });
    document.addEventListener('pjax:complete', init);
    if (window.jQuery) window.jQuery(document).on('pjax:beforeReplace.mixArticleActions', cleanup).on('pjax:complete.mixArticleActions', init);
    window.MixArticleActions = { init: init };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();

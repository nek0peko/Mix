(function () {
    'use strict';
    if (window.MixVisitorTracking || !window.fetch || !window.AbortController || !window.crypto) return;
    var script = document.querySelector('script[data-mix-visit-endpoint]');
    if (!script) return;
    var endpoint = script.dataset.mixVisitEndpoint;
    var visits = new WeakMap(), active = null;

    function paint(group, count) {
        if (!group || !Number.isInteger(count) || count < 0) return;
        // A delayed initial likes response must not undo a newer visit count.
        count = Math.max(count, group._views || 0);
        group._views = count;
        var button = group.querySelector('.mix-view-button');
        if (!button) return;
        button.querySelector('.mix-view-count').textContent = count > 99 ? '99+' : String(count);
        button.title = '浏览次数，共 ' + count + ' 次浏览';
        button.setAttribute('aria-label', button.title);
    }
    function groupFor(main) {
        var group = document.querySelector('[data-mix-article-actions]');
        return group && group.dataset.cid === main.dataset.mixActionsCid ? group : null;
    }
    function cancel() {
        if (!active) return;
        active.controller.abort();
        clearTimeout(active.timeout);
        clearTimeout(active.retry);
        active = null;
    }
    async function send(main, entry) {
        if (!main.isConnected || entry.done) return;
        var controller = new AbortController();
        var task = { controller: controller, timeout: setTimeout(function () { controller.abort(); }, 6000) };
        active = task;
        entry.attempts++;
        try {
            var response = await fetch(endpoint, { method: 'POST', credentials: 'same-origin', cache: 'no-store', mode: 'same-origin', headers: { 'Content-Type': 'application/json', 'X-Mix-Visit': '1' }, body: JSON.stringify(entry.data), signal: controller.signal });
            if (!response.ok) {
                if (response.status >= 400 && response.status < 500) entry.done = true;
                throw new Error('Visit unavailable');
            }
            var data = await response.json();
            if (!data.ok || !Number.isInteger(data.views) || data.views < 0) throw new Error('Invalid visit response');
            entry.done = true;
            if (main.isConnected) {
                paint(groupFor(main), data.views);
                if (window.MixOnlinePresence && typeof window.MixOnlinePresence.setToday === 'function') window.MixOnlinePresence.setToday(data.today_uv);
            }
        } catch (error) {
            if (active !== task || !main.isConnected) return;
            var group = groupFor(main);
            if (group && group._views === undefined) {
                group.querySelector('.mix-view-count').textContent = '—';
                group.querySelector('.mix-view-button').title = '浏览统计暂时不可用';
            }
            if (!entry.done && entry.attempts < 3) task.retry = setTimeout(function () { send(main, entry); }, 3000 * entry.attempts);
        } finally {
            clearTimeout(task.timeout);
        }
    }
    function init() {
        var main = document.querySelector('main');
        if (!main || visits.has(main)) return;
        cancel();
        var bytes = new Uint8Array(16);
        crypto.getRandomValues(bytes);
        var id = Array.from(bytes, function (byte) { return byte.toString(16).padStart(2, '0'); }).join('');
        var marker = main.querySelector('[data-mix-article-actions]');
        var cid = Number(main.dataset.mixActionsCid || (marker && marker.dataset.cid) || 0);
        var entry = { data: { visit: id, cid: cid, path: location.pathname }, attempts: 0, done: false };
        visits.set(main, entry);
        send(main, entry);
    }
    document.addEventListener('pjax:beforeReplace', cancel);
    document.addEventListener('pjax:complete', init);
    if (window.jQuery) window.jQuery(document).on('pjax:beforeReplace.mixVisits', cancel).on('pjax:complete.mixVisits', init);
    window.addEventListener('pagehide', cancel);
    window.MixVisitorTracking = { paint: paint };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();

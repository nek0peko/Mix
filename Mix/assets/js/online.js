(function () {
    'use strict';
    if (window.MixOnlinePresence) {
        window.MixOnlinePresence.refresh();
        return;
    }
    if (!window.fetch || !window.AbortController) return;

    var interval = 15000;
    var retryDelay = interval;
    var timer = null;
    var controller = null;
    var timeout = null;
    var requestId = 0;
    var stopped = false;
    var count = null;
    var today = null;
    var suffix = / · \d+ 人在线$/;

    function marker() { return document.querySelector('[data-mix-online]'); }

    function display(value) {
        count = value;
        var element = marker();
        if (element) {
            element.hidden = element.dataset.display === '0' || value === null;
            element.dataset.count = value === null ? '' : String(value);
            var number = element.querySelector('[data-mix-online-count]');
            if (number) number.textContent = value === null ? '' : String(value);
        }
        var daily = document.querySelector('[data-mix-today]');
        if (daily) {
            daily.hidden = !element || element.dataset.display === '0' || today === null;
            daily.dataset.count = today === null ? '' : String(today);
            daily.querySelector('[data-mix-today-count]').textContent = today === null ? '' : String(today);
        }
        var title = document.title.replace(suffix, '');
        document.title = title + (value === null || !element || element.dataset.display === '0' ? '' : ' · ' + value + ' 人在线');
    }

    function canRefresh() {
        return !stopped && !!marker() && !document.hidden && navigator.onLine !== false;
    }

    function schedule(delay) {
        if (timer !== null) window.clearTimeout(timer);
        timer = null;
        if (canRefresh()) timer = window.setTimeout(update, delay);
    }

    function cancel() {
        requestId++;
        if (timer !== null) window.clearTimeout(timer);
        if (timeout !== null) window.clearTimeout(timeout);
        timer = timeout = null;
        if (controller) controller.abort();
        controller = null;
    }

    function update() {
        timer = null;
        if (!canRefresh() || controller) return;
        var element = marker();
        var currentId = ++requestId;
        controller = new AbortController();
        var currentController = controller;
        timeout = window.setTimeout(function () { currentController.abort(); }, 5000);
        window.fetch(element.dataset.endpoint, {
            method: 'POST',
            credentials: 'same-origin',
            mode: 'same-origin',
            cache: 'no-store',
            headers: {'X-Mix-Online': '1'},
            signal: currentController.signal
        }).then(function (response) {
            if (!response.ok) throw new Error('Online count unavailable');
            return response.json();
        }).then(function (result) {
            if (currentId !== requestId) return;
            if (result.enabled === false) {
                stopped = true;
                display(null);
                return;
            }
            if (result.enabled !== true || !Number.isInteger(result.count) || result.count < 0) {
                throw new Error('Invalid online count');
            }
            if (typeof result.display === 'boolean') element.dataset.display = result.display ? '1' : '0';
            today = Number.isInteger(result.today_uv) && result.today_uv >= 0 ? result.today_uv : null;
            display(result.count);
            retryDelay = interval;
        }).catch(function () {
            if (currentId !== requestId) return;
            today = null;
            display(null);
            retryDelay = Math.min(retryDelay * 2, 60000);
        }).then(function () {
            if (currentId !== requestId) return;
            window.clearTimeout(timeout);
            timeout = null;
            controller = null;
            schedule(retryDelay);
        });
    }

    function refresh() {
        if (!marker()) {
            cancel();
            display(null);
            return;
        }
        display(count);
        if (!canRefresh()) {
            cancel();
            return;
        }
        if (!controller) {
            if (timer !== null) window.clearTimeout(timer);
            timer = null;
            update();
        }
    }

    var initial = marker();
    if (initial && /^\d+$/.test(initial.dataset.count)) count = Number(initial.dataset.count);
    var initialDaily = document.querySelector('[data-mix-today]');
    if (initialDaily && /^\d+$/.test(initialDaily.dataset.count)) today = Number(initialDaily.dataset.count);
    window.MixOnlinePresence = {refresh: refresh, setToday: function (value) {
        if (Number.isInteger(value) && value >= 0) { today = value; display(count); }
    }};
    document.addEventListener('visibilitychange', refresh);
    document.addEventListener('pjax:complete', refresh);
    // jQuery Pjax uses its own event system rather than native DOM events.
    if (window.jQuery) window.jQuery(document).on('pjax:complete.mixOnline', refresh);
    window.addEventListener('offline', function () { cancel(); display(null); });
    window.addEventListener('online', refresh);
    window.addEventListener('pagehide', cancel);
    window.addEventListener('pageshow', refresh);
    refresh();
})();

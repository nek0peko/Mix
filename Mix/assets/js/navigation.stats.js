(function () {
    'use strict';
    if (window.MixNavigationStats) return;
    var active = null, cache = null, cachedAt = 0, pending = null, timer = null, closeTimer = null;
    var hover = window.matchMedia('(hover: hover) and (pointer: fine)');
    function cancelClose() {
        clearTimeout(closeTimer);
        closeTimer = null;
    }
    function close(focus) {
        cancelClose();
        if (!active) return;
        clearInterval(timer);
        active.panel.hidden = true;
        active.button.setAttribute('aria-expanded', 'false');
        active.home.appendChild(active.panel);
        if (focus) active.button.focus();
        active = null;
    }
    function position() {
        if (!active) return;
        var panel = active.panel;
        if (window.innerWidth <= 600) {
            panel.style.left = ''; panel.style.top = ''; panel.style.maxHeight = '';
            return;
        }
        var rect = active.button.getBoundingClientRect();
        panel.style.left = Math.max(12, Math.min(rect.left + rect.width / 2 - panel.offsetWidth / 2, window.innerWidth - panel.offsetWidth - 12)) + 'px';
        panel.style.top = rect.bottom + 8 + 'px';
        panel.style.maxHeight = Math.max(80, window.innerHeight - rect.bottom - 20) + 'px';
    }
    function open(button, focus) {
        cancelClose();
        if (active && active.button === button) {
            if (focus) active.panel.focus({preventScroll: true});
            return;
        }
        close();
        var panel = document.getElementById(button.getAttribute('aria-controls'));
        if (!panel) return;
        active = {button: button, panel: panel, home: panel.parentNode};
        if (window.MixNavigationSearch) window.MixNavigationSearch.close();
        document.body.appendChild(panel); panel.hidden = false;
        button.setAttribute('aria-expanded', 'true');
        load(); position();
        if (focus) panel.focus({preventScroll: true});
        timer = setInterval(function () { if (!document.hidden) load(); }, 30000);
    }
    function inside(node) {
        return active && node instanceof Node && (active.button.contains(node) || active.panel.contains(node));
    }
    function leave() {
        cancelClose();
        closeTimer = setTimeout(function () {
            if (!active || active.button.matches(':hover') || active.panel.matches(':hover') || (document.activeElement !== active.panel && active.panel.contains(document.activeElement))) return;
            close();
        }, 220);
    }
    function element(tag, text, cls) {
        var node = document.createElement(tag);
        if (text !== undefined) node.textContent = text;
        if (cls) node.className = cls;
        return node;
    }
    function render(data) {
        if (!active) return;
        var container = active.panel.querySelector('[data-mix-stats-content]');
        container.replaceChildren();
        var totals = element('div', undefined, 'mix-stats-totals');
        [['今日旅人 / UV', data.today.uv], ['今日浏览 / PV', data.today.pv]].forEach(function (item) {
            var block = element('div');
            block.appendChild(element('span', item[0]));
            block.appendChild(element('strong', item[1].toLocaleString()));
            totals.appendChild(block);
        });
        container.appendChild(totals);
        var caption = element('div', '近 7 天 · ', 'mix-stats-caption');
        caption.appendChild(element('span', 'UV', 'mix-stats-legend-uv'));
        caption.appendChild(document.createTextNode(' / '));
        caption.appendChild(element('span', 'PV', 'mix-stats-legend-pv'));
        container.appendChild(caption);
        var ns = 'http://www.w3.org/2000/svg', svg = document.createElementNS(ns, 'svg');
        svg.setAttribute('viewBox', '0 0 350 160');
        svg.setAttribute('role', 'img');
        svg.setAttribute('aria-label', '近 7 天访客与浏览趋势，使用左右方向键查看每日 UV 和 PV');
        svg.setAttribute('tabindex', '0');
        var max = Math.max(1, ...data.days.map(function (d) { return Math.max(d.uv, d.pv); }));
        function shape(tag, attrs) {
            var node = document.createElementNS(ns, tag);
            Object.keys(attrs).forEach(function (key) { node.setAttribute(key, attrs[key]); });
            svg.appendChild(node); return node;
        }
        [0, 1, 2].forEach(function (i) {
            shape('line', {x1: 12, x2: 338, y1: 12 + i * 60, y2: 12 + i * 60, class: 'mix-stats-grid'});
        });
        ['pv', 'uv'].forEach(function (key) {
            var points = data.days.map(function (d, i) { return (12 + i * 54.3) + ',' + (132 - d[key] / max * 120); });
            shape('polyline', {points: points.join(' '), class: 'mix-stats-line mix-stats-' + key});
            data.days.forEach(function (d, i) {
                shape('circle', {cx: 12 + i * 54.3, cy: 132 - d[key] / max * 120, r: 3, class: 'mix-stats-dot mix-stats-' + key});

            });
        });
        data.days.forEach(function (d, i) {
            shape('text', {x: 12 + i * 54.3, y: 155, 'text-anchor': 'middle', class: 'mix-stats-date'}).textContent = d.date.slice(5);
        });
        var chart = element('div', undefined, 'mix-stats-chart');
        chart.appendChild(svg);
        var guide = shape('line', {x1: 12, x2: 12, y1: 8, y2: 132, class: 'mix-stats-guide', visibility: 'hidden'});
        var markers = ['uv', 'pv'].map(function (key) {
            return shape('circle', {cx: 12, cy: 132, r: 4.5, class: 'mix-stats-marker mix-stats-' + key, visibility: 'hidden'});
        });
        var tooltip = element('div', undefined, 'mix-stats-tooltip');
        tooltip.setAttribute('role', 'status');
        tooltip.hidden = true;
        var date = element('strong');
        tooltip.appendChild(date);
        var values = {};
        ['uv', 'pv'].forEach(function (key) {
            var row = element('div', undefined, 'mix-stats-tooltip-row');
            row.appendChild(element('span', key.toUpperCase(), 'mix-stats-legend-' + key));
            values[key] = element('b'); row.appendChild(values[key]); tooltip.appendChild(row);
        });
        chart.appendChild(tooltip); container.appendChild(chart);
        var selected = 6;
        function select(index) {
            selected = Math.max(0, Math.min(6, index));
            var day = data.days[selected], x = 12 + selected * 54.3;
            guide.setAttribute('x1', x); guide.setAttribute('x2', x); guide.setAttribute('visibility', 'visible');
            markers.forEach(function (marker, i) {
                var key = i === 0 ? 'uv' : 'pv';
                marker.setAttribute('cx', x); marker.setAttribute('cy', 132 - day[key] / max * 120); marker.setAttribute('visibility', 'visible');
                values[key].textContent = day[key].toLocaleString();
            });
            date.textContent = day.date; tooltip.hidden = false;
            var width = chart.clientWidth, anchor = x / 350 * width;
            var left = anchor + 12;
            if (left + tooltip.offsetWidth > width - 4) left = anchor - tooltip.offsetWidth - 12;
            tooltip.style.left = Math.max(4, Math.min(left, width - tooltip.offsetWidth - 4)) + 'px';
        }
        function hide() {
            tooltip.hidden = true;
            guide.setAttribute('visibility', 'hidden');
            markers.forEach(function (marker) { marker.setAttribute('visibility', 'hidden'); });
        }
        function point(event) {
            var bounds = svg.getBoundingClientRect();
            select(Math.round(((event.clientX - bounds.left) / bounds.width * 350 - 12) / 54.3));
        }
        svg.addEventListener('pointermove', point);
        svg.addEventListener('pointerdown', point);
        svg.addEventListener('pointerleave', function (event) {
            if (event.pointerType !== 'touch' && document.activeElement !== svg) hide();
        });
        svg.addEventListener('focus', function () { select(selected); });
        svg.addEventListener('blur', hide);
        svg.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key === 'Home' || event.key === 'End') {
                event.preventDefault();
                select(event.key === 'Home' ? 0 : event.key === 'End' ? 6 : selected + (event.key === 'ArrowLeft' ? -1 : 1));
            }
        });
        position();
    }
    function load() {
        if (!active) return;
        if (cache && Date.now() - cachedAt < 30000) { render(cache); return; }
        var container = active.panel.querySelector('[data-mix-stats-content]');
        if (!cache) container.textContent = '正在读取旅人足迹…';
        if (!pending) {
            var controller = new AbortController(), timeout = setTimeout(function () { controller.abort(); }, 6000);
            pending = fetch(active.panel.dataset.endpoint, {credentials: 'same-origin', signal: controller.signal})
                .then(function (response) { if (!response.ok) throw new Error(); return response.json(); })
                .then(function (data) {
                    function count(n) { return Number.isSafeInteger(n) && n >= 0; }
                    if (!data.ok || !data.today || !count(data.today.uv) || !count(data.today.pv) || !Array.isArray(data.days) || data.days.length !== 7 || !data.days.every(function (d) { return /^\d{4}-\d{2}-\d{2}$/.test(d.date) && count(d.uv) && count(d.pv); })) throw new Error();
                    cache = data; cachedAt = Date.now(); return data;
                }).finally(function () { clearTimeout(timeout); pending = null; });
        }
        pending.then(render).catch(function () {
            if (!active) return;
            var box = active.panel.querySelector('[data-mix-stats-content]');
            box.replaceChildren(element('p', '暂时无法读取统计'));
            var retry = element('button', '重试'); retry.type = 'button'; retry.onclick = function (event) { event.stopPropagation(); load(); }; box.appendChild(retry); position();
        });
    }
    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-mix-stats-toggle]');
        if (button) {
            if (active && active.button === button && !(hover.matches && window.innerWidth > 600)) close();
            else open(button, true);
        } else if (active && (event.target.closest('[data-mix-stats-close]') || !active.panel.contains(event.target))) close();
    });
    document.addEventListener('pointerover', function (event) {
        if (!hover.matches || window.innerWidth <= 600) return;
        var button = event.target.closest('[data-mix-stats-toggle]');
        if (button) open(button, false);
        else if (inside(event.target)) cancelClose();
        else if (event.target.closest('.mix-nav-search')) close();
    });
    document.addEventListener('pointerout', function (event) {
        if (hover.matches && window.innerWidth > 600 && inside(event.target) && !inside(event.relatedTarget)) leave();
    });
    document.addEventListener('focusout', function (event) {
        if (inside(event.target) && !inside(event.relatedTarget)) leave();
    });
    document.addEventListener('keydown', function (event) { if (event.key === 'Escape') close(true); });
    window.addEventListener('resize', position);
    window.addEventListener('scroll', position, {passive: true});
    document.addEventListener('pjax:send', function () { close(); });
    if (window.jQuery) window.jQuery(document).on('pjax:send.mixStats', function () { close(); });
    window.MixNavigationStats = {close: close};
}());

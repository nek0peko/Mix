(function () {
    'use strict';
    if (window.MixMouseInput) return;
    var root = document.documentElement;
    var lastTouch = 0;
    function hide() {
        root.classList.add('mix-touch-input');
        var instance = window.IMouse && window.IMouse.default && window.IMouse.default.instance;
        if (!instance || typeof instance.setState !== 'function') return;
        if (instance.setSteadyHoverTimeout) {
            clearTimeout(instance.setSteadyHoverTimeout);
            instance.setSteadyHoverTimeout = null;
        }
        instance.setState({isVisible: false, isActive: false, hoverTarget: null, isSteadyHover: false});
    }
    function touch() { lastTouch = Date.now(); hide(); }
    function mouse(event) {
        if (event.pointerType !== 'mouse') return;
        root.classList.remove('mix-touch-input');
    }
    if (window.PointerEvent) {
        document.addEventListener('pointerdown', function (event) { if (event.pointerType === 'touch' || event.pointerType === 'pen') touch(); }, {capture: true, passive: true});
        document.addEventListener('pointermove', mouse, {capture: true, passive: true});
        document.addEventListener('pointercancel', hide, {passive: true});
    } else {
        document.addEventListener('mousemove', function (event) {
            if (event.sourceCapabilities && event.sourceCapabilities.firesTouchEvents) return;
            if (Date.now() - lastTouch > 1000) root.classList.remove('mix-touch-input');
        }, {passive: true});
    }
    document.addEventListener('touchstart', touch, {capture: true, passive: true});
    window.addEventListener('blur', hide);
    window.addEventListener('pagehide', hide);
    document.addEventListener('visibilitychange', function () { if (document.hidden) hide(); });
    window.MixMouseInput = {hide: hide};
})();

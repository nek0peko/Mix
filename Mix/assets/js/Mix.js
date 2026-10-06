/**
 * FontZoom 函数
 * 函数变量：size
 * 调用方法：FontZoom(size)
 * 函数用途：用于调整文章文字大小
 */
function FontZoom(size) {
    var text = document.getElementById("write");
    text.style.fontSize = size + "px";
}

/**
 * 用于激活menu菜单栏
 * btn_active 为按钮，双向激活
 * Header_head-menu__ofiV5 为 菜单 单
 */

// 原生js编写技术
//1.0下拉菜单
if (window.MIX_CONFIG.SIDEBAR == 1) {
    document.getElementById("btn_active").onclick = function () {
        name1 = document.getElementById("header").className;
        if (name1 == 'assets') {
            document.getElementById('header').className = 'assets active';
        } else {
            document.getElementById('header').className = 'assets';
        }
    }
    document.getElementById("Header_head-menu__ofiV5").onclick = function (event) {
        if (event.target.closest('.mix-nav-search')) return;
        name1 = document.getElementById("header").className;
        if (name1 != 'assets') {
            document.getElementById('header').className = 'assets';
        }
    }
} else if (window.MIX_CONFIG.SIDEBAR == 2) {
//2.0右侧展开菜单
    document.getElementById("btn_sidebar").onclick = function () {
        if (document.getElementById("headerr").className == 'Header_drawer__iQn1p global-drawer') {
            document.getElementById('headerr').className = 'Header_drawer__iQn1p global-drawer Header_show__3R4Sq global-show';
            // document.getElementById("overlay").className = 'display_yes';
        }
    }
    document.getElementById("close").onclick = function () {
        if (document.getElementById("headerr").className == 'Header_drawer__iQn1p global-drawer Header_show__3R4Sq global-show') {
            document.getElementById('headerr').className = 'Header_drawer__iQn1p global-drawer';
            // document.getElementById("overlay").className = 'display_yes display_none';
        }
    }
    document.getElementById("headerr").onclick = function (event) {
        if (event.target.closest('.mix-nav-search-drawer')) return;
        if (document.getElementById("headerr").className == 'Header_drawer__iQn1p global-drawer Header_show__3R4Sq global-show') {
            document.getElementById('headerr').className = 'Header_drawer__iQn1p global-drawer';
            // document.getElementById("overlay").className = 'display_yes display_none'
        }
    }
}
/**
 * 回到顶部 按钮
 * 绑定回到顶部动画
 */
document.getElementById('top').onclick = function () {
    window.scrollTo({
        top: 0,
        behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'
    });
}
/**
 * 夜晚模式 按钮
 * 绑定动画以及变化元素
 */
var Time = new Date();
var NowTime = Time.getHours();

function setDarkStyle($need) {
    document.getElementById('html').classList.remove('light');
    document.getElementById('html').classList.add('dark');
    localStorage.setItem("html_style", "dark");
    if ($need) {
        ks.notice(
            "⭐️夜间模式",
            {
                color: "black",
                time: "1500",
            }
        )
    }
}

function setLightStyle($need) {
    document.getElementById('html').classList.remove('dark');
    document.getElementById('html').classList.add('light');
    localStorage.setItem("html_style", "light");
    if ($need) {
        ks.notice(
            "🌤日间模式",
            {
                color: "blue",
                time: "1500",
            }
        )
    }
}

var mixModeTransition = null;
var mixRequestedMode = null;
var mixModeRequest = 0;
document.getElementById('dark_button').onclick = function () {
    var root = document.documentElement;
    var current = mixRequestedMode || (root.classList.contains('dark') ? 'dark' : 'light');
    var target = current === 'dark' ? 'light' : 'dark';
    mixRequestedMode = target;
    var request = ++mixModeRequest;
    var applyMode = function () {
        if (request !== mixModeRequest) return;
        if (target === 'dark') setDarkStyle('yes');
        else setLightStyle('yes');
    };
    if (mixModeTransition) mixModeTransition.skipTransition();
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        applyMode();
        mixRequestedMode = null;
        root.classList.remove('mix-mode-transition', 'mix-mode-fallback');
        return;
    }
    root.classList.add('mix-mode-transition');
    if (typeof document.startViewTransition === 'function') {
        // Snapshot both themes so background images crossfade along with the UI.
        var transition = document.startViewTransition(applyMode);
        mixModeTransition = transition;
        transition.finished.catch(function () {}).then(function () {
            if (mixModeTransition !== transition) return;
            mixModeTransition = null;
            mixRequestedMode = null;
            root.classList.remove('mix-mode-transition');
        });
    } else {
        root.classList.add('mix-mode-fallback');
        applyMode();
        mixRequestedMode = null;
        window.setTimeout(function () {
            if (request === mixModeRequest) root.classList.remove('mix-mode-transition', 'mix-mode-fallback');
        }, 420);
    }
}

if (localStorage.getItem("html_style") == 'dark') {
    setDarkStyle("yes")
    console.log('%cNight Mode Now ~ 🌙', 'color: #00009C')
} else {
    setLightStyle("yes")
    console.log('%cDay Mode Now ~ 🌞', 'color: #FF7F00')
}

/**
 * 获取页面滚动条进度
 * @method getWebScrollProgress
 */
var getWebScrollProgress = function () {
    var pageHeight = document.body.scrollHeight || document.documentElement.scrollHeight; // 页面总高度
    // var clientHeight = $(window).height() || document.documentElement.clientHeight; // 可见区域高度
    var clientHeight = document.documentElement.clientHeight;
    var scrollTop = document.body.scrollTop || document.documentElement.scrollTop; //滚动的高度位置
    var progress = Math.round(((scrollTop) / (pageHeight - clientHeight)) * 100); // 计算百分比
    // var progressId = document.getElementById('progress');
    document.getElementById('progress').style.width = progress + "%"
}
getWebScrollProgress(); //首次加载，渲染进度条
window.onscroll = function () { //监听滚动事件
    getWebScrollProgress();
};

// Delegate status hints so cards replaced by Pjax keep the same behavior.
(function () {
    if (window.mixCategoryTooltipBound) return;
    window.mixCategoryTooltipBound = true;
    var tooltip = document.createElement('div');
    tooltip.className = 'mix-category-tooltip';
    tooltip.hidden = true;
    tooltip.setAttribute('aria-hidden', 'true');
    document.body.appendChild(tooltip);
    var active = null;
    var width = 0;
    var height = 0;
    var frame = 0;
    var point = { x: 0, y: 0 };
    function position() {
        frame = 0;
        if (!active || !active.isConnected) return hide();
        var x = Math.max(8, Math.min(point.x + 12, window.innerWidth - width - 8));
        var y = point.y + 16;
        if (y + height > window.innerHeight - 8) y = point.y - height - 12;
        y = Math.max(8, Math.min(y, window.innerHeight - height - 8));
        tooltip.style.transform = 'translate3d(' + x + 'px,' + y + 'px,0)';
    }
    function show(icon, x, y) {
        active = icon;
        tooltip.textContent = icon.getAttribute('aria-label');
        tooltip.hidden = false;
        width = tooltip.offsetWidth;
        height = tooltip.offsetHeight;
        point.x = x;
        point.y = y;
        position();
    }
    function hide() {
        active = null;
        tooltip.hidden = true;
        if (frame) cancelAnimationFrame(frame);
        frame = 0;
    }
    function iconAt(target) {
        return target instanceof Element ? target.closest('.mix-category-heading .mix-category-visibility') : null;
    }
    document.addEventListener('pointerover', function (event) {
        if (event.pointerType === 'touch') return;
        var icon = iconAt(event.target);
        if (icon && icon !== active) show(icon, event.clientX, event.clientY);
    });
    document.addEventListener('pointermove', function (event) {
        if (!active || event.pointerType === 'touch') return;
        point.x = event.clientX;
        point.y = event.clientY;
        if (!frame) frame = requestAnimationFrame(position);
    });
    document.addEventListener('pointerout', function (event) {
        if (active && iconAt(event.target) === active && !active.contains(event.relatedTarget)) hide();
    });
    document.addEventListener('focusin', function (event) {
        var icon = iconAt(event.target);
        if (!icon) return;
        var rect = icon.getBoundingClientRect();
        show(icon, rect.left, rect.bottom);
    });
    document.addEventListener('focusout', function (event) {
        if (iconAt(event.target) === active) hide();
    });
    document.addEventListener('keydown', function (event) { if (event.key === 'Escape') hide(); });
    document.addEventListener('pjax:send', hide);
    document.addEventListener('pjax:complete', hide);
    window.addEventListener('scroll', hide, true);
    window.addEventListener('resize', hide);
    window.addEventListener('blur', hide);
})();

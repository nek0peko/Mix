<?php

/*
 * 模板编辑外观设置
 * themeConfig($form){}控制
 */

function themeConfig($form)
{
    $form->setAttribute('id', 'mix-theme-settings');
    // Supply switch defaults when opening backups created before these fields existed.
    if (Helper::options()->FriendsEnabled === null) {
        Helper::options()->FriendsEnabled = ['enabled'];
    }
    if (Helper::options()->IMouseEnabled === null) {
        $mouseEnabled = Helper::options()->Show_what === null || in_array('ShowIMouse', mixEnabledComponents(Helper::options()), true);
        Helper::options()->IMouseEnabled = $mouseEnabled ? ['enabled'] : [];
    }
    Helper::options()->HyperlinkModules = json_encode(MixHyperlinks::modules(Helper::options()), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $form->addItem(new CustomLabel(AdminSetting::Welcome(Helper::options()->HeaderPhoto, Helper::options()->HeaderName)));
    Backup::echoBackup();
    $form->addItem(new CustomLabel('<div class="mdui-panel" mdui-panel="">'));

    /* ---------------------这是一条分割线--------------------- */

    $form->addItem(new Title('初级设置', '博客标题、博主名称、博主介绍、头像、ICP 备案号'));

    // Website Title
    $WebsiteTitle = new Text('WebsiteTitle', NULL, _t('Create Your World!'), _t('博客标题'), _t('填写完整的浏览器标签页标题；内页会在前面添加页面标题。留空使用默认值：Create Your World!'));
    $form->addInput($WebsiteTitle);

    // Blog Name
    $HeaderName = new Text('HeaderName', NULL, _t('nek0peko'), _t('博主名称'), _t('首页显示的博主名称，填写纯文字。默认值：nek0peko'));
    $form->addInput($HeaderName);

    // Blog Description
    $HeaderMore = new Text('HeaderMore', NULL, _t('Modify your name and photo and get started.'), _t('博主介绍'), _t('显示在首页博主名称下方，填写纯文字；留空不显示介绍'));
    $form->addInput($HeaderMore);

    // Blog Image
    $HeaderPhoto = new Text('HeaderPhoto', NULL, _t('https://pic1.zhimg.com/80/v2-d381f60fe0f96f9a22039312f0ce3653_1440w.png'), _t('头像图片'), _t('填写完整的头像图片地址；此字段直接使用所填地址，不会自动上传图片'));
    $HeaderPhoto->addRule(['MixHyperlinks', 'validUrl'], _t('请填写完整的 http://、https:// 地址或以 / 开头的站内地址，也可以留空'));
    $form->addInput($HeaderPhoto);

    // ICP 备案号
    $ICPNumber = new Text('ICPNumber', NULL, _t(''), _t('ICP 备案号'), _t('显示在博客底部，留空则不显示'));
    $form->addInput($ICPNumber);

    /* ---------------------这是一条分割线--------------------- */

    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Title('图标设置', '首页图标与跳转链接'));
    $SocialIcons = new Typecho_Widget_Helper_Form_Element_Hidden('SocialIcons', null, json_encode(MixSocialIcons::items(Helper::options()), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    $SocialIcons->addRule(['MixSocialIcons', 'valid'], _t('请检查图标地址和跳转链接，使用完整的 http://、https:// 或以 / 开头的站内地址'));
    $form->addInput($SocialIcons);
    $form->addItem(new CustomLabel('<div id="mix-icon-editor" class="mix-hyperlink-editor"><p class="description">图标地址填写 SVG、PNG 等图片链接；跳转链接为空时不显示图标</p><div class="mix-icon-list"></div><button type="button" class="mdui-btn mix-add-icon">添加图标</button></div>'));

    /* ---------------------这是一条分割线--------------------- */

    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Title('背景设置', '日间与夜间背景图'));
    // 手机端菜单背景
    $BackGroundImage = new Text('BackGroundImage', NULL, NULL, _t('日间背景图'), _t('用于手机端顶部菜单背景；填写图片 URL，留空使用默认背景'));
    $BackGroundImage->addRule(['MixHyperlinks', 'validUrl'], _t('请填写完整的 http://、https:// 地址或以 / 开头的站内地址，也可以留空'));
    $form->addInput($BackGroundImage);
    $BackGroundImageDark = new Text('BackGroundImageDark', NULL, NULL, _t('夜间背景图'), _t('用于手机端夜间模式顶部菜单背景；填写图片 URL，留空使用默认背景'));
    $BackGroundImageDark->addRule(['MixHyperlinks', 'validUrl'], _t('请填写完整的 http://、https:// 地址或以 / 开头的站内地址，也可以留空'));
    $form->addInput($BackGroundImageDark);

    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Title('导航栏设置', '顶部导航图标、右侧模块与友链页面链接'));
    // 顶部导航栏图标
    $HeadNavPhoto = new Textarea('HeadNavPhoto', NULL, _t(''), _t('顶部导航图标'), _t('完整的图片 HTML，例如 &lt;img src=&quot;https://example.com/logo.png&quot; alt=&quot;Logo&quot;&gt;。留空使用默认内容'));
    $form->addInput($HeadNavPhoto);

    $form->addItem(new CustomLabel('<div class="mdui-panel" mdui-panel=""><div class="mdui-panel-item"><div class="mdui-panel-item-header">顶部右侧模块</div><div class="mdui-panel-item-body">'));
    $headnavItems = new Typecho_Widget_Helper_Form_Element_Hidden('headnavItems', null, '{"name":"主题","link":"https://github.com/nek0peko/Mix","class":"fab fa-github","target":"_blank"}');
    $headnavItems->addRule(['MixNavigation', 'valid'], _t('请检查导航链接，填写 https://、http:// 或站内地址'));
    $form->addInput($headnavItems);
    $form->addItem(new CustomLabel('<div id="mix-navigation-editor" class="mix-hyperlink-editor"><p class="description">图标使用 <a href="https://fontawesome.com/v5/search?m=free" target="_blank" rel="noopener noreferrer">Font Awesome 类名</a>，可留空</p><div class="mix-navigation-list"></div><button type="button" class="mdui-btn mix-add-navigation">添加模块</button></div></div></div></div>'));

    if (Admin_Helper::isPluginAvailable('Links_Plugin', 'Links')) {
        $FriendURL = new Text('FriendURL', NULL, _t(''), _t('友链页面链接'), _t('在 Typecho 后台创建独立页面，页面模板选择“友链页面”，发布后将该页面地址填在这里；留空不显示跳转入口'));
        $FriendURL->addRule(['MixHyperlinks', 'validUrl'], _t('请填写完整的 http://、https:// 地址或以 / 开头的站内地址，也可以留空'));
    $form->addInput($FriendURL);
    } else {
        $form->addInput(new Typecho_Widget_Helper_Form_Element_Hidden('FriendURL', null, Helper::options()->FriendURL));
    }

    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Title('样式设置', '首页布局与手机端菜单'));
    $showIndexStyle = new Select('showIndexStyle', array(
        '1' => '小卡片',
        '2' => '纯文字'
    ), '1', _t('首页文章显示样式'), _t('默认值：小卡片。纯文字样式按文章列表展示'));
    $form->addInput($showIndexStyle);

    $sideBarStyle = new Select('sideBarStyle', array(
        '1' => '下展开样式',
        '2' => '右侧展开样式'
    ), '2', _t('手机端菜单展开样式'), _t('默认值：右侧展开样式。仅影响手机端菜单的展开方式'));
    $form->addInput($sideBarStyle);

    /* ---------------------这是一条分割线--------------------- */

    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Title('部件设置', '顶部博主信息、转载授权、评论区与在线人数'));
    $onlineStatsFile = htmlspecialchars(__TYPECHO_ROOT_DIR__ . __TYPECHO_THEME_DIR__ . '/Mix/online.txt', ENT_QUOTES, 'UTF-8');
    $Show_what = new Checkbox('Show_what',
        array(
            'ShowHeadSVG' => _t('顶部博主信息'),
            'ShowCopyRight' => '显示文章转载授权',
            'ShowComment' => '显示评论区',
            'ShowAly' => _t('在线人数统计')
        ),
        Helper::options()->Show_what_1 !== null ? mixEnabledComponents(Helper::options()) : array('ShowHeadSVG', 'ShowCopyRight', 'ShowComment', 'ShowAly'), null, _t('在线人数显示在浏览器标题和博客底部右侧，按最近 30 秒内访问过的浏览器统计，页面加载或站内跳转时更新。如需赋予统计文件读写权限，执行 <code>chown xxx:xxx ' . $onlineStatsFile . '</code> 和 <code>chmod 600 ' . $onlineStatsFile . '</code>；请将 xxx 替换为执行 PHP 的系统账号，例如 apache 或 www-data'), true);
    $form->addInput($Show_what->multiMode());

    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Title('超链设置', '首页自定义超链模块'));
    $FriendsEnabled = new Checkbox('FriendsEnabled', ['enabled' => _t('启用模块')], mixFriendsEnabled(Helper::options()) ? ['enabled'] : [], null, null, true);
    if (Admin_Helper::isPluginAvailable('Links_Plugin', 'Links')) {
        $form->addItem(new CustomLabel('<div class="mix-hyperlink-editor mix-friends-editor"><section id="mix-friends-module" class="mix-hyperlink-module mix-friends-module"><div class="mix-hyperlink-module-heading"><div class="mix-friends-title-slot"></div>'));
        $form->addInput($FriendsEnabled->multiMode());
        $form->addItem(new CustomLabel('<span class="mix-builtin-badge">内置</span></div></section></div>'));
    } else {
        $form->addItem(new CustomLabel('<div hidden>'));
        $form->addInput($FriendsEnabled->multiMode());
        $form->addItem(new CustomLabel('</div>'));
    }
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Hidden('FriendsModuleTitle', null, '友情链接'));
    $FriendsModulePosition = new Typecho_Widget_Helper_Form_Element_Hidden('FriendsModulePosition', null, '-1');
    $FriendsModulePosition->addRule(function ($value) { return filter_var($value, FILTER_VALIDATE_INT) !== false && (int) $value >= -1; }, _t('友链模块顺序无效'));
    $form->addInput($FriendsModulePosition);
    $HyperlinkModules = new Typecho_Widget_Helper_Form_Element_Hidden('HyperlinkModules', null, json_encode(MixHyperlinks::modules(Helper::options()), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    $HyperlinkModules->addRule(['MixHyperlinks', 'valid'], _t('超链设置未能保存，请检查链接地址，使用 http://、https:// 或以 / 开头的站内地址'));
    $form->addInput($HyperlinkModules);
    $form->addItem(new CustomLabel('<div id="mix-hyperlink-editor" class="mix-hyperlink-editor"><div class="mix-hyperlink-modules"></div><button type="button" class="mdui-btn mix-add-module">添加模块</button></div>'));

    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Title('IMouse 设置', '跟随鼠标移动、点击与悬停变化的光标效果'));
    $form->addItem(new CustomLabel('<div id="mix-imouse-toggle" class="mix-imouse-toggle">'));
    $IMouseEnabled = new Checkbox('IMouseEnabled', ['enabled' => _t('启用 IMouse 🖱️')], in_array('ShowIMouse', mixEnabledComponents(Helper::options()), true) ? ['enabled'] : [], null, null, true);
    $form->addInput($IMouseEnabled->multiMode());
    $form->addItem(new CustomLabel('</div>'));
    $IMouseDefaultBackgroundColor = new Text('IMouseDefaultBackgroundColor', NULL, _t('\'rgba(1, 80, 111, .1)\''), _t('默认光标背景颜色'), _t('填写带英文引号的 CSS 颜色值，默认值：\'rgba(1, 80, 111, .1)\''));
    $form->addInput($IMouseDefaultBackgroundColor);
    $IMouseActiveBackgroundColor = new Text('IMouseActiveBackgroundColor', NULL, _t('\'rgba(1, 80, 111, .15)\''), _t('按下时的光标背景颜色'), _t('填写带英文引号的 CSS 颜色值，默认值：\'rgba(1, 80, 111, .15)\''));
    $form->addInput($IMouseActiveBackgroundColor);
    $IMouseDefaultSize = new Text('IMouseDefaultSize', NULL, _t('20'), _t('默认光标直径'), _t('填写数字，单位为 px；默认值：20'));
    $form->addInput($IMouseDefaultSize);
    $IMouseActiveSize = new Text('IMouseActiveSize', NULL, _t('15'), _t('按下时的光标直径'), _t('填写数字，单位为 px；默认值：15'));
    $form->addInput($IMouseActiveSize);
    $IMouseHoverPadding = new Text('IMouseHoverPadding', NULL, _t('8'), _t('悬停时的光标内边距'), _t('填写数字，单位为 px；默认值：8'));
    $form->addInput($IMouseHoverPadding);
    $IMouseActivePadding = new Text('IMouseActivePadding', NULL, _t('4'), _t('悬停并按下时的光标内边距'), _t('填写数字，单位为 px；默认值：4'));
    $form->addInput($IMouseActivePadding);
    $IMouseHoverRadius = new Text('IMouseHoverRadius', NULL, _t('8'), _t('悬停时的光标圆角半径'), _t('填写数字，单位为 px；默认值：8'));
    $form->addInput($IMouseHoverRadius);
    $IMouseActiveRadius = new Text('IMouseActiveRadius', NULL, _t('4'), _t('悬停并按下时的光标圆角半径'), _t('填写数字，单位为 px；默认值：4'));
    $form->addInput($IMouseActiveRadius);
    $IMouseSelectionWidth = new Text('IMouseSelectionWidth', NULL, _t('3'), _t('文字选择状态下的光标宽度'), _t('填写数字，单位为 px；默认值：3'));
    $form->addInput($IMouseSelectionWidth);
    $IMouseSelectionHeight = new Text('IMouseSelectionHeight', NULL, _t('40'), _t('文字选择状态下的光标高度'), _t('填写数字，单位为 px；默认值：40'));
    $form->addInput($IMouseSelectionHeight);
    $IMouseSelectionRadius = new Text('IMouseSelectionRadius', NULL, _t('2'), _t('文字选择状态下的光标圆角半径'), _t('填写数字，单位为 px；默认值：2'));
    $form->addInput($IMouseSelectionRadius);

    /* ---------------------这是一条分割线--------------------- */

    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Title('开发者设置', 'Debug 模式、自定义 CDN、Pjax、自定义 HTML / CSS / JS'));

    $debug = new Select('debug', array(
        '0' => '生产环境（默认）',
        '1' => '调试环境（调试推荐）',
        '2' => '开发环境（开发推荐）'
    ), '0', _t('Debug 模式'), _t('默认值：生产环境。调试环境输出浏览器控制台日志；开发环境增加调试提示，并在设置页显示 PHP 错误'));
    $form->addInput($debug);

    $CDNURL = new Text('CDNURL', NULL, _t(''), _t('自定义 CDN 加速链接'), _t('留空使用本地主题资源；填写后从该地址加载主题静态资源。填写包含 css、js 等目录的完整资源地址，例如 https://cdn.example.com/Mix/assets/'));
    $CDNURL->addRule('url', _t('请填写有效的完整 CDN 资源地址，或留空使用本地资源'));
    $form->addInput($CDNURL);

    $PjaxOption = new Select('PjaxOption', array(
        'jQueryPjax' => _t('jQuery 版 Pjax'),
        'MoOxPjax' => _t('MoOx 版 Pjax（默认）')
    ), 'MoOxPjax', _t('Pjax 实现'), _t('Pjax 在站内跳转时局部更新页面，减少整页刷新。默认值：MoOx 版 Pjax'));
    $form->addInput($PjaxOption);

    // Pjax重载
    $PjaxReLoad = new Textarea('PjaxReLoad', NULL, _t(''), _t('Pjax 重载函数'), _t('填写页面局部更新完成后需要执行的 JavaScript，不要添加 &lt;script&gt; 标签；例如初始化自定义组件。留空仍会执行主题内置的重载逻辑'));
    $form->addInput($PjaxReLoad);

    // 自定义 CSS
    $CSS = new Textarea('CSS', NULL, _t(''), _t('自定义 CSS'), _t('填写 CSS 规则，不要添加 &lt;style&gt; 标签；作用于网站前台，留空不添加样式'));
    $form->addInput($CSS);

    // 自定义 JavaScript
    $JavaScript = new Textarea('JavaScript', NULL, _t(''), _t('自定义 JavaScript'), _t('填写 JavaScript 代码，不要添加 &lt;script&gt; 标签；在前台页面底部执行，留空不添加代码'));
    $form->addInput($JavaScript);

    // 头部自定义输出HTML代码
    $HeaderHTML = new Textarea('HeaderHTML', NULL, _t(''), _t('头部 HTML'), _t('填写完整的 HTML，如 meta、link 或 script 标签，输出到 &lt;/head&gt; 前；留空不添加内容'));
    $form->addInput($HeaderHTML);

    // 底部自定义输出HTML代码
    $FooterHTML = new Textarea('FooterHTML', NULL, _t(''), _t('底部 HTML'), _t('填写完整的 HTML，在前台页面底部输出，可用于统计脚本等；留空不添加内容'));
    $form->addInput($FooterHTML);

    // 博客底部左侧信息
    $LeftHTML = new Textarea('LeftHTML', NULL, _t(''), _t('底部左侧 HTML'), _t('在博客底部左侧追加 HTML；留空不追加内容，版权信息和备案号仍会显示'));
    $form->addInput($LeftHTML);

    // 博客底部右侧信息
    $RightHTML = new Textarea('RightHTML', NULL, _t(''), _t('底部右侧 HTML'), _t('在博客底部右侧追加 HTML；留空不追加内容'));
    $form->addInput($RightHTML);



    $IndexAction = new Text('IndexAction', NULL, _t('fade-larger-small 1s'), _t('首页文章卡片动画'), _t('填写 CSS animation 值，如动画名称和持续时间。默认值：fade-larger-small 1s；填写 none 关闭动画'));
    $form->addInput($IndexAction);
    $LinksAction = new Text('LinksAction', NULL, _t('fade-in-top 1s'), _t('文章归档页、友链页链接动画'), _t('填写 CSS animation 值。默认值：fade-in-top 1s；填写 none 关闭动画'));
    $form->addInput($LinksAction);

    /* ---------------------这是一条分割线--------------------- */







    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
    $form->addItem(new CustomLabel(AdminSetting::Actions()));
}

/*
 * 编写文章设置
 * themeFields(Typecho_Widget_Helper_Layout $layout){}控制
 */
function themeFields(Typecho_Widget_Helper_Layout $layout)
{
    $PostChoice = new Typecho_Widget_Helper_Form_Element_Select('PostChoice', array(
        '0' => '文章样式',
        '1' => '日记样式'
    ), '0', _t('当前文章页面样式类型'), '<strong style="color:red;">该设置仅对该篇文章有效</strong></br>默认选项是「文章」样式</br> 选择「日记」当前文章页面样式将会改为日记样式</br>不建议文章使用日记样式，日记使用文章样式');
    $layout->addItem($PostChoice);
}

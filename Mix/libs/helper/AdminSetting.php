<?php

class AdminSetting
{

    /**
     * 输出到后台外观设置的css
     * @return string
     */
    public static function styleoutput()
    {
        $themeUrl = THEME_URL;
        $settingsVersions = [];
        foreach ([
            'css/admin/settings.scoped.css', 'css/admin/settings.polish.css',
            'js/admin/mdui.min.js', 'js/admin/settings.actions.js',
            'js/admin/settings.hyperlinks.js', 'js/admin/settings.icons.js',
            'js/admin/settings.navigation.js', 'js/admin/settings.validation.js'
        ] as $asset) {
            $path = __DIR__ . '/../../assets/' . $asset;
            $settingsVersions[basename($asset)] = filemtime($path) . '-' . filesize($path);
        }
        $randomColor = Admin_Helper::getBackgroundColor();
        //$randomColor[0] = "#fff";
        $styleHTML = <<<EOF
        <style>
        #mix-theme-settings{--randomColor0:{$randomColor[0]};--randomColor1:{$randomColor[1]};}
        </style>
            <link rel="stylesheet" href="{$themeUrl}assets/css/admin/settings.scoped.css?v={$settingsVersions['settings.scoped.css']}" type="text/css" />
            <link rel="stylesheet" href="{$themeUrl}assets/css/admin/settings.polish.css?v={$settingsVersions['settings.polish.css']}" type="text/css" />
            <script src="{$themeUrl}assets/js/admin/mdui.min.js?v={$settingsVersions['mdui.min.js']}" defer></script>
            <script src="{$themeUrl}assets/js/admin/settings.actions.js?v={$settingsVersions['settings.actions.js']}" defer></script>
            <script src="{$themeUrl}assets/js/admin/settings.hyperlinks.js?v={$settingsVersions['settings.hyperlinks.js']}" defer></script>
            <script src="{$themeUrl}assets/js/admin/settings.icons.js?v={$settingsVersions['settings.icons.js']}" defer></script>
            <script src="{$themeUrl}assets/js/admin/settings.navigation.js?v={$settingsVersions['settings.navigation.js']}" defer></script>
            <script src="{$themeUrl}assets/js/admin/settings.validation.js?v={$settingsVersions['settings.validation.js']}" defer></script>
EOF;
        return $styleHTML;
    }

    public static function Welcome($photo_src, $name)
    {
        $photo_src = htmlspecialchars((string) $photo_src, ENT_QUOTES, 'UTF-8');
        $db = Typecho_Db::get();
        $backupInfo = "";
        if ($db->fetchRow($db->select('name')->from('table.options')->where('name = ?', 'theme:Mixbf')->where('user = ?', 0))) {
            $backupInfo = '<div class="mdui-chip" style="color: rgb(26, 188, 156);"><span 
        class="mdui-chip-icon mdui-color-green"><i class="mdui-icon material-icons">&#xe8ba;</i></span><span 
        class="mdui-chip-title" title="已保存一份主题设置备份，可用于还原">设置已备份</span></div>';
        } else {
            $backupInfo = '<div class="mdui-chip" style="color: rgb(26, 188, 156);"><span 
        class="mdui-chip-icon mdui-color-red"><i class="mdui-icon material-icons">&#xe8ba;</i></span><span 
        class="mdui-chip-title" style="color: rgb(255, 82, 82);" title="暂无主题设置备份">设置未备份</span></div>';
        }
        $linksAvailable = Admin_Helper::isPluginAvailable("Links_Plugin", "Links");
        if (!$linksAvailable) {
            $pluginInfo = '<div class="mdui-chip" style="color: rgb(26, 188, 156);"><span 
        class="mdui-chip-icon mdui-color-red"><i class="mdui-icon material-icons">&#xe8ba;</i></span><span 
        class="mdui-chip-title" style="color: rgb(255, 82, 82);">友链插件未启用</span></div>';
        } else {
            $pluginInfo = '<div class="mdui-chip" style="color: rgb(26, 188, 156);"><span 
        class="mdui-chip-icon mdui-color-green"><i class="mdui-icon material-icons">&#xe8ba;</i></span><span 
        class="mdui-chip-title">友链插件已启用</span></div>';
        }
        if (!$linksAvailable) {
            $pluginInfo .= '<script>alert("友链插件未启用 \n请安装并启用 Links 友链插件")</script>';
        }

        $version = MIX_VERSION;
        $welcomeHTML = <<<EOF
<div class="mdui-card mix-welcome">
    <div id="Mix_header" class="mdui-card-header">
        <img class="mdui-card-header-avatar" src="$photo_src"/>
        <div class="mix-welcome-copy">
            <div class="mdui-card-header-title mix-version">Mix {$version}</div>
            <div class="mdui-card-header-subtitle"><span>欢迎使用 Mix 主题!!</span><a class="mix-community" href="https://qm.qq.com/q/72LJzXIEcE" target="_blank" rel="noopener noreferrer">点击加入主题交流群~</a></div>
        </div>
    </div>
    <div class="mdui-card-primary">
        <div class="mix-status-row">
            {$backupInfo}
            {$pluginInfo}
        </div>
    </div>

</div>
EOF;
        return $welcomeHTML;
    }

    public static function Actions()
    {
        $backupAction = htmlspecialchars(Helper::security()->getAdminUrl('options-theme.php'), ENT_QUOTES, 'UTF-8');
        return <<<EOF
<div class="mix-backup-actions">
    <button type="button" class="mdui-btn back_up" value="备份模板设置数据" data-mix-backup-action="{$backupAction}" data-confirm-message="将备份当前已保存的设置，并覆盖已有备份。页面上尚未保存的修改不会备份。确定继续吗？">备份当前设置</button>
    <button type="button" class="mdui-btn mix-danger recover_back_up" value="还原模板设置数据" data-mix-backup-action="{$backupAction}" data-confirm-message="从备份还原将覆盖当前已保存的主题设置，并丢弃页面上未保存的修改。这不是恢复默认设置。确定还原吗？">从备份还原</button>
    <button type="button" class="mdui-btn mix-danger un_back_up" value="删除现有Mix备份" data-mix-backup-action="{$backupAction}" data-confirm-message="确定删除主题设置备份吗？删除后无法再从这份备份还原，当前设置不受影响。">删除备份</button>
</div>
EOF;
    }

}

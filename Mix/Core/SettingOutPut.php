<?php
/*
 * @Name: SettingOutPut.php
 * @author: Wibus
 * @Date: 2021-03-15 22:59:46
 * @LastEditors: nek0peko
 * @LastEditTime: 2022-11-11 02:37:41
 */
if (preg_match("/options-theme.php/", $_SERVER['REQUEST_URI'])) {
    $stylehtml = AdminSetting::styleoutput();
    echo $stylehtml;
    if ($options->debug != 2) { //如果不是开发模式的话就不屏蔽
        error_reporting(0);
        ini_set('display_errors', 0);
    } elseif ($options->debug == 2) {
        echo "<script>
        document.addEventListener('DOMContentLoaded', function () { mdui.snackbar({
            message: '开发环境已启用：设置页会显示 PHP 错误。关闭请在开发者设置中将 Debug 模式改为生产环境。'
        }); });
        </script>";
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
    }
}

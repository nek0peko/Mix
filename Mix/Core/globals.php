<?php
/*
 * @Name: globals.php
 * @author: Wibus
 * @Date: 2021-03-15 22:51:31
 * @LastEditors: Wibus
 * @LastEditTime: 2021-03-27 15:04:32
 */
$GLOBALS['options'] = Typecho_Widget::widget('Widget_Options');
$cdnUrl = trim((string) $GLOBALS['options']->CDNURL);
if ($cdnUrl !== '') {
    $GLOBALS['assetURL'] = rtrim($cdnUrl, '/') . '/';
} else {
    $GLOBALS['assetURL'] = $GLOBALS['options']->themeUrl . '/assets/';
}
?>

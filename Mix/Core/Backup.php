<?php

/*
 * @Name: Backup.php
 * @author: Wibus
 * @Date: 2021-03-15 23:00:33
 * @LastEditors: Wibus
 * @LastEditTime: 2021-03-28 21:19:51
 */

class Backup
{

    static function update($v)
    {
        $API = 'https://bird.ioliu.cn/v2/?url=https://api.github.com/repos/wibus-wee/Mix/releases/latest';
        $message = file_get_contents($API);
        $message_json = json_decode($message);
        // $ver = $message_json->{'version'};
        // $mes = $message_json->{'mes'};
        $ver = $message_json->{'tag_name'};
        $mes = $message_json->{'url'}; //内容
        if ($v != $ver) {
            echo <<<EOF
            <script>
            mdui.snackbar({
                message: 'Mix更新{$ver}了！建议立即更新哟～',
                buttonText: '更新内容',
                onClick: function(){
                  mdui.alert('{$mes}');
                },
                onButtonClick: function(){
                  mdui.alert('{$mes}');
                },
              });
            </script>
EOF;
        }
    }

    static function echoBackup()
    {
        $action = $_POST['type'] ?? '';
        $actions = ['备份模板设置数据', '还原模板设置数据', '删除现有Mix备份'];
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST' || !in_array($action, $actions, true)) {
            return;
        }

        Typecho_Widget::widget('Widget_User')->pass('administrator');
        Helper::security()->protect();
        $name = Helper::options()->theme;
        $db = Typecho_Db::get();
        $currentName = 'theme:' . $name;
        $backupName = $currentName . 'bf';
        $current = $db->fetchRow($db->select()->from('table.options')->where('name = ?', $currentName)->where('user = ?', 0));
        $backup = $db->fetchRow($db->select()->from('table.options')->where('name = ?', $backupName)->where('user = ?', 0));

        if ($action === '备份模板设置数据') {
            if (!$current) {
                $message = '没有可备份的主题设置。';
            } elseif ($backup) {
                $db->query($db->update('table.options')->rows(['value' => $current['value']])->where('name = ?', $backupName)->where('user = ?', 0));
                $message = '主题设置备份已更新。';
            } else {
                $db->query($db->insert('table.options')->rows(['name' => $backupName, 'user' => 0, 'value' => $current['value']]));
                $message = '主题设置已备份。';
            }
        } elseif ($action === '还原模板设置数据') {
            if (!$backup) {
                $message = '没有可还原的主题设置备份。';
            } elseif (!$current) {
                $message = '当前主题设置不存在，无法还原。';
            } else {
                $restoredValue = $backup['value'];
                $restored = @unserialize($restoredValue, ['allowed_classes' => false]);
                if (is_array($restored) && array_key_exists('Show_what_1', $restored)) {
                    $restored['Show_what'] = array_values(array_unique(array_merge(
                        is_array($restored['Show_what'] ?? null) ? $restored['Show_what'] : [],
                        is_array($restored['Show_what_1']) ? $restored['Show_what_1'] : []
                    )));
                    unset($restored['Show_what_1']);
                    $restoredValue = serialize($restored);
                }
                $db->query($db->update('table.options')->rows(['value' => $restoredValue])->where('name = ?', $currentName)->where('user = ?', 0));
                $message = '已从备份还原主题设置。';
            }
        } else {
            if (!$backup) {
                $message = '没有可删除的主题设置备份。';
            } else {
                $db->query($db->delete('table.options')->where('name = ?', $backupName)->where('user = ?', 0));
                $message = '主题设置备份已删除，当前设置保持不变。';
            }
        }

        $messageJson = json_encode($message, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
        $urlJson = json_encode(Helper::options()->adminUrl . 'options-theme.php', JSON_HEX_TAG);
        echo '<script>document.addEventListener("DOMContentLoaded",function(){mdui.snackbar({message:' . $messageJson . '});window.setTimeout(function(){window.location.href=' . $urlJson . ';},1500);});</script>';
    }
}

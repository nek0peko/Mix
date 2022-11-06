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

        $str1 = explode('/themes/', Helper::options()->themeUrl);
        $str2 = explode('/', $str1[1]);
        $name = $str2[0];
        $db = Typecho_Db::get();
        $sjdq = $db->fetchRow($db->select()->from('table.options')->where('name = ?', 'theme:' . $name));
        $ysj = $sjdq['value'];
        if (isset($_POST['type'])) {
            if ($_POST["type"] == "备份模板设置数据") {
                if ($db->fetchRow($db->select()->from('table.options')->where('name = ?', 'theme:' . $name . 'bf'))) {
                    $update = $db->update('table.options')->rows(array('value' => $ysj))->where('name = ?', 'theme:' . $name . 'bf');
                    $updateRows = $db->query($update);
                    echo "<script>
        mdui.snackbar({
            message: '数据备份更新成功，自动刷新中...'
        });
        </script>";
                    ?>

                    <script language="JavaScript">window.setTimeout("location=\'<?php Helper::options()->adminUrl('options-theme.php'); ?>\'", 2500);</script>
                    <?php
                } else {
                    if ($ysj) {
                        $insert = $db->insert('table.options')
                            ->rows(array('name' => 'theme:' . $name . 'bf', 'user' => '0', 'value' => $ysj));
                        $insertId = $db->query($insert);
                        // echo '<div class="tongzhi col-mb-12 home">备份完成，请等待自动刷新！如果等不到请点击';
                        echo "<script>
            mdui.snackbar({
                message: '数据备份成功，自动刷新中...'
            });
            </script>";

                        ?>

                        <script language="JavaScript">window.setTimeout("location=\'<?php Helper::options()->adminUrl('options-theme.php'); ?>\'", 2500);</script>
                        <?php
                    }
                }
            }
            if ($_POST["type"] == "还原模板设置数据") {
                if ($db->fetchRow($db->select()->from('table.options')->where('name = ?', 'theme:' . $name . 'bf'))) {
                    $sjdub = $db->fetchRow($db->select()->from('table.options')->where('name = ?', 'theme:' . $name . 'bf'));
                    $bsj = $sjdub['value'];
                    $update = $db->update('table.options')->rows(array('value' => $bsj))->where('name = ?', 'theme:' . $name);
                    $updateRows = $db->query($update);
                    echo "<script>
            mdui.snackbar({
                message: '数据恢复成功，自动刷新中...'
            });
            </script>";
                }
                ?>

                <script language="JavaScript">window.setTimeout("location=\'<?php Helper::options()->adminUrl('options-theme.php'); ?>\'", 2000);</script>
                <?php
            } else {
                echo "<script>
            mdui.snackbar({
                message: '数据库中没有当前主题的备份数据！自动刷新中...'
            });
            </script>";
                ?>
                <script language="JavaScript">window.setTimeout("location=\'<?php Helper::options()->adminUrl('options-theme.php'); ?>\'", 2000);</script>
                <?php
            }
        }
        if ($_POST["type"] == "删除现有Mix备份") {
            if ($db->fetchRow($db->select()->from('table.options')->where('name = ?', 'theme:' . $name . 'bf'))) {
                $delete = $db->delete('table.options')->where('name = ?', 'theme:' . $name . 'bf');
                $deletedRows = $db->query($delete);
                echo "<script>
        mdui.snackbar({
            message: '已删除备份数据，自动刷新中...'
        });
        </script>";
                ?>

                <script language="JavaScript">window.setTimeout("location=\'<?php Helper::options()->adminUrl('options-theme.php'); ?>\'", 2500);</script>
                <?php
            } else {
                echo "<script>
            mdui.snackbar({
                message: '数据库中没有当前主题的备份数据！自动刷新中...'
            });
            </script>";
                ?>
                <script language="JavaScript">window.setTimeout("location=\'<?php Helper::options()->adminUrl('options-theme.php'); ?>\'", 2000);</script>
                <?php
            }
        }
    }
}
    

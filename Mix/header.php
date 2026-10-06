<?php
$mixOnlineCount = in_array('ShowAly', mixEnabledComponents($this->options), true) ? online_users() : null;
?>
<!DOCTYPE html>
<html id="html">
<?php require_once("Core/globals.php"); //$GLOBALS ?>
<head lang="zh-cn">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title><?php $this->archiveTitle(array(
            'category' => _t('分类 %s 下的文章'),
            'search' => _t('包含关键字 %s 的文章'),
            'tag' => _t('标签 %s 下的文章'),
            'author' => _t('%s 发布的文章')
        ), '', ' - '); ?><?php
        $websiteTitle = $this->options->WebsiteTitle;
        // Backups made before the merged option still use the original fields.
        if ($websiteTitle === null) {
            $suffix = (string) $this->options->HeaderDescription;
            $websiteTitle = (string) $this->options->title . ($suffix !== '' ? (string) $this->options->cut_off . $suffix : '');
        }
        $websiteTitle = trim((string) $websiteTitle);
        echo htmlspecialchars($websiteTitle !== '' ? $websiteTitle : 'Create Your World!', ENT_QUOTES, 'UTF-8');
        if ($mixOnlineCount !== null) {
            echo ' · ' . $mixOnlineCount . ' 人在线';
        }
        ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="next-head-count" content="9">

    <link rel="stylesheet" href="<?php echo $GLOBALS['assetURL'] ?>css/style1.css?v=<?php echo filemtime(__DIR__ . '/assets/css/style1.css'); ?>" data-n-g="">

<!--    <link rel="shortcut icon" href="--><?php //echo $GLOBALS['assetURL'] ?><!--img/favicon.ico" type="image/x-icon"/>-->
<!--    <link rel="apple-touch-icon" href="--><?php //echo $GLOBALS['assetURL'] ?><!--img/favicon.png"/>-->
    <link rel="shortcut icon" href="https://raw.githubusercontent.com/nek0peko/cdn-static/main/Mix/img/favicon.ico" type="image/x-icon"/>
    <link rel="apple-touch-icon" href="https://raw.githubusercontent.com/nek0peko/cdn-static/main/Mix/img/favicon.png"/>

    <meta itemprop="image" content="<?php echo htmlspecialchars((string) $this->options->HeaderPhoto, ENT_QUOTES, 'UTF-8'); ?>"/>
    <!--<link href="<?php echo $GLOBALS['assetURL'] ?>kico.css" rel="stylesheet" type="text/css">-->
    <!-- TODO: 当前CDN速度太慢，暂时将部分JS存在本地，后续更换CDN -->
    <script src="<?php echo $GLOBALS['assetURL'] ?>js/local/kico.min.js"></script>
    <!--    <script src="https://cdn.jsdelivr.net/gh/Dreamer-Paul/Kico-Style/kico.min.js"></script>-->
    <script src="<?php echo $GLOBALS['assetURL'] ?>js/pre.js?v=<?php echo filemtime(__DIR__ . '/assets/js/pre.js'); ?>"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.slim.min.js"></script>
    <style><?php $this->options->CSS(); ?></style>
    <?php if ($this->options->BackGroundImage): ?>
        <style>
            /* body, nav#Header_head-menu__ofiV5 {
              background: url(

            <?php echo json_encode(str_replace(['<', '>'], ['%3C', '%3E'], (string) $this->options->BackGroundImage), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>

                        ) top fixed!important;
                          } */
            @media all and (max-width: 600px) {
                nav#Header_head-menu__ofiV5 {
                    background: url(<?php echo json_encode(str_replace(['<', '>'], ['%3C', '%3E'], (string) $this->options->BackGroundImage), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>) top fixed !important;
                }
            }

        </style>
    <?php endif; ?>
    <?php if ($this->options->BackGroundImageDark): ?>
        <style>
            /* html.dark body, html.dark nav#Header_head-menu__ofiV5 {
              background: url(

            <?php echo json_encode(str_replace(['<', '>'], ['%3C', '%3E'], (string) $this->options->BackGroundImageDark), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>

                        ) top fixed!important;
                          } */
            @media all and (max-width: 600px) {
                html.dark nav#Header_head-menu__ofiV5 {
                    background: url(<?php echo json_encode(str_replace(['<', '>'], ['%3C', '%3E'], (string) $this->options->BackGroundImageDark), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>) top fixed !important;
                }
            }
        </style>
    <?php endif; ?>
    <?php $this->options->HeaderHTML(); ?>
    <?php $this->header(); ?>
    <script>
        window.MIX_CONFIG = {
            VERSION: <?php echo json_encode(MIX_VERSION); ?>,
            <?php if ($this->options->sideBarStyle == 1):?>
            SIDEBAR: 1,
            <?php elseif ($this->options->sideBarStyle == 2):?>
            SIDEBAR: 2,
            <?php endif;?>
        }
    </script>
</head>
<body class="loading">
<div id="progress" class="header-progress"></div>

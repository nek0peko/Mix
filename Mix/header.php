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
        ), '', ' - '); ?><?php $this->options->title(); ?><?php $this->options->cut_off(); ?><?php $this->options->HeaderDescription(); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="next-head-count" content="9">

    <link rel="stylesheet" href="<?php echo $GLOBALS['assetURL'] ?>css/style1.css" data-n-g="">

<!--    <link rel="shortcut icon" href="--><?php //echo $GLOBALS['assetURL'] ?><!--img/favicon.ico" type="image/x-icon"/>-->
<!--    <link rel="apple-touch-icon" href="--><?php //echo $GLOBALS['assetURL'] ?><!--img/favicon.png"/>-->
    <link rel="shortcut icon" href="https://cdn.jsdelivr.net/gh/nek0peko/cdn-static/Mix/img/favicon.ico" type="image/x-icon"/>
    <link rel="apple-touch-icon" href="https://cdn.jsdelivr.net/gh/nek0peko/cdn-static/Mix/img/favicon.png"/>

    <meta itemprop="image" content="<?php $this->options->HeaderPhoto(); ?>"/>
    <!--<link href="<?php echo $GLOBALS['assetURL'] ?>kico.css" rel="stylesheet" type="text/css">-->
    <!-- TODO: 当前CDN速度太慢，暂时将部分JS存在本地，后续更换CDN -->
    <script src="<?php echo $GLOBALS['assetURL'] ?>js/local/kico.min.js"></script>
    <!--    <script src="https://cdn.jsdelivr.net/gh/Dreamer-Paul/Kico-Style/kico.min.js"></script>-->
    <script src="<?php echo $GLOBALS['assetURL'] ?>js/pre.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style><?php $this->options->CSS(); ?></style>
    <?php if ($this->options->BackGroundImage): ?>
        <style>
            /* body, nav#Header_head-menu__ofiV5 {
              background: url(

            <?php $this->options->BackGroundImage() ?>

                        ) top fixed!important;
                          } */
            @media all and (max-width: 600px) {
                nav#Header_head-menu__ofiV5 {
                    background: url(<?php $this->options->BackGroundImage() ?>) top fixed !important;
                }
            }

        </style>
    <?php endif; ?>
    <?php if ($this->options->BackGroundImageDark): ?>
        <style>
            /* html.dark body, html.dark nav#Header_head-menu__ofiV5 {
              background: url(

            <?php $this->options->BackGroundImageDark() ?>

                        ) top fixed!important;
                          } */
            @media all and (max-width: 600px) {
                html.dark nav#Header_head-menu__ofiV5 {
                    background: url(<?php $this->options->BackGroundImageDark() ?>) top fixed !important;
                }
            }
        </style>
    <?php endif; ?>
    <?php $this->header(); ?>
    <script>
        window.MIX_CONFIG = {
            VERSION: '2.0.0',
            <?php if ($this->options->sideBarStyle == 1):?>
            SIDEBAR: 1,
            <?php elseif ($this->options->sideBarStyle == 2):?>
            SIDEBAR: 2,
            <?php endif;?>
        }
    </script>
</head>
<body class="loading">
<?php $this->options->HeaderHTML(); ?>
<div id="progress" class="header-progress"></div>
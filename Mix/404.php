<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
$this->need('component/headnav.php');
?>

<div id="main_load">
    <main>
        <?php
        if ($this->options->sideBarStyle == 2) {
            $this->need('component/sidebar.php');
        }
        ?>

        <div class="page">
            <h1>
                <div class="title-404-1">4</div>
                <div class="title-404-2">0</div>
                <div class="title-404-1">4</div>
            </h1>
            <div class="message-404">
                <p>该页面被魔法阵传送走啦Σ(ﾟдﾟ;)</p><br>
            </div>
            <div class="menhera">
<!--                <img src="--><?php //echo $GLOBALS['assetURL'] . 'img/menhera.png' ?><!--" alt="可爱的Menhera酱"/>-->
                <img src="<?php echo 'https://raw.githubusercontent.com/nek0peko/cdn-static/master/Mix/img/menhera.png' ?>" alt="可爱的Menhera酱"/>
            </div>

            <div class="wrapper-inner">
                <form id="search" method="post" action="<?php $this->options->siteUrl(); ?>" role="search">
                    <input type="text" class="main-search" id="s" name="s" class="text"
                           placeholder="<?php _e('输入关键词按下回车再召唤一个吧~'); ?>"/>
                </form>
            </div>

            <div class="wrapper-inner-1">
                <div class="main-1">
                    <div class="view">
                        <?php $this->widget('Widget_Metas_Tag_Cloud', 'sort=mid&ignoreZeroCount=1&desc=0&limit=30')->to($taglist); ?>
                        <?php while ($taglist->next()): ?>
                            <a class="tags" href="<?php $taglist->permalink(); ?>"
                               title="<?php $taglist->name(); ?>"><?php $taglist->name(); ?></a>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
    input {
        border-radius: 46px;
    }

    .page {
        display: flex;
        align-items: center;
        flex-direction: column;
    }

    .menhera {
        width: 35rem;
        height: 30rem;
    }

    .message-404 {
        color: #808080;
        font-size: 20px;
    }

    .title-404-1 {
        color: #FFC107;
        display: inline-block;
        font-size: 4em;
        line-height: 1em;
    }

    .title-404-2 {
        color: #808080;
        display: inline-block;
        font-size: 4em;
        line-height: 1em;
    }

    .wrapper-inner {
        display: flex;
        position: relative;
        align-items: center;
        flex-direction: column;
        /*width: 1440px;*/
        height: 150px;
        white-space: nowrap;
    }

    .main-search {
        box-sizing: border-box;
        display: flex;
        position: relative;
        align-items: center;
        flex-direction: row;
        margin-top: 50px;
        border-width: 1px;
        border-radius: 28px;
        /*padding-right: 661px;*/
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.5),
        inset 0 1px 3px 0 rgba(0, 0, 0, 0.5);
        border-style: solid;
        border-color: #979797;
        background-color: #ffffff;
        padding-left: 28px;
        height: 56px;
        font-weight: normal;
        line-height: 15px;
        width: 300px;
    }

    .wrapper-inner-1 {
        display: flex;
        position: relative;
        align-items: center;
        flex-direction: column;
        margin-top: -46px;
        /*width: 1440px;*/
        height: 200px;
    }

    .main-1 {
        display: flex;
        position: relative;
        align-items: flex-start;
        flex-direction: column;
        margin-top: 52px;
    }

    .tags {
        display: inline-block;
        min-width: 10px;
        padding: 13px 27px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        background-color: #777;
        border-radius: 10px;
        margin-left: 10px;
        margin-top: 3px;
    }
</style>

<?php $this->need('footer.php'); ?>

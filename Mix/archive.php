<!--libs.php-->
<?php require_once 'libs/libs.php';
$num = 0;
?>
<meta name="description" content="<?php $this->archiveTitle(array(
    'category' => _t('分类 %s 下的文章'),
    'search' => _t('包含关键字 %s 的文章'),
    'tag' => _t('标签 %s 下的文章'),
    'author' => _t('%s 发布的文章')
), '', ' - '); ?>">
<?php
// 头部必要元素
$this->need('header.php');
$this->need('component/headnav.php');
?>

<div id="main_load">
    <main class="is-article" id="article-wrap">
        <?php
        if ($this->options->sideBarStyle == 2) {
            $this->need('component/sidebar.php');
        }
        ?>
        <section class="post-title">
            <h1>
                <div class="texty mask-bottom" style="opacity: 1;">
                        <span style="opacity: 1; transform: translate(0px, 0%);"><?php $this->archiveTitle(array(
                                'category' => _t('- %s'),
                                'search' => _t('- 包含关键字“%s”的文章'),
                                'tag' => _t('- <span style="font-weight: bolder">%s</span> 标签下的文章'),
                                'author' => _t('- %s 发布的文章')
                            ), '', ' - '); ?></>
                </div>
            </h1>
            <h2>
                <div class="texty mask-bottom" style="opacity: 1;">
                    <span style="opacity: 1; transform: translate(0px, 0%);"><?php
                        if ($this->is('category')): echo $this->getDescription(); endif; ?>
                    </span>
                </div>
            </h2>
        </section>
        <article class="post-content paul-note" style="opacity: 1;">
            <article class="post-content paul-note article-list">
                <ul>
                    <?php while ($this->next()) : ?>
                        <li style="opacity: 1; transform: translate(0px, 0px);
                                animation: <?php $this->options->LinksAction(); ?>; ">
                            <a href="<?php $this->permalink() ?>" rel="noopener"><?php $this->title() ?></a>
                            <span class="meta"><?php $this->date(); ?></span></li>
                    <?php endwhile; ?>
                </ul>
            </article>
        </article>

        <div class="page-navigator">
            <?php $this->pageNav('&laquo; 前一页', '后一页 &raquo;', 5, '...', array(
                'wrapTag' => 'nav',
                'wrapClass' => 'pagination justify-content-center',
                'itemTag' => '',
                'textTag' => '',
                'currentClass' => 'active',
                'prevClass' => 'page-item',
                'nextClass' => 'page-item',
                'itemClass' => 'page-item',
                'linkClass' => 'page-link'
            )); ?>
        </div>
    </main>
</div>

<!--必要底部元素-->
<?php $this->need('footer.php'); ?>
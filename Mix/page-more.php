<?php
/*
 * @Name: page-more.php
 * @author: Wibus
 * @Date: 2021-03-15 22:51:30
 * @LastEditors: nek0peko
 * @LastEditTime: 2022-11-06 13:42:42
 */

/**
 * 首页文章更多
 *
 * @package custom
 */

//libs.php
require_once('libs/libs.php');

$cate = $_GET['cate'];
$slug = "slug=$cate";

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
            <div>
                <article class="post-content paul-note">
                    <article class="paul-note">

                        <?php $this->widget('Widget_Archive@indexc', 'pageSize=100000&type=category', $slug)->to($new); ?>
                        <?php while ($new->next()): ?>

                            <h1 class="PostBlock_head__38-FT">
                                <?php $new->date(); ?>
                                <small>By <?php $this->author(); ?></small>
                            </h1>
                            <div class="note-item PostBlock_text__Gsfei">
                                <h2 class="PostBlock_title__18Obo">
                                    <?php $new->title() ?>
                                </h2>
                                <article class="note-content">
                                    <div><?php $new->excerpt(300, '...'); ?></div>
                                    <div class="">
                                    </div>
                                </article>
                                <section class="PostBlock_navigator__25BE6">
                                    <a class="PostBlock_btn__2551h" href="<?php $new->permalink() ?>">查看原文</a>
                                </section>
                            </div>

                        <?php endwhile; ?>

                    </article>
                </article>
            </div>
        </main>
    </div>

<?php
// 底部必要元素
$this->need('footer.php');
?>
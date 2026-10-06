<?php
/**
 * 日记页面
 *
 * @package custom
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
$this->need('component/headnav.php');
?>
<div id="main_load">
    <main class="is-article is-note post-content paul-note mix-reading-page">
        <?php $this->need('component/post/category-navigation.php'); ?>
        <?php $this->need('component/post/dairy_post.php'); ?>
        <?php $this->need('component/post/article-actions.php'); ?>
        <?php if ($this->options->sideBarStyle == 2): ?>
            <?php $this->need('component/sidebar.php'); ?>
        <?php endif; ?>
    </main>
</div>
<?php $this->need('footer.php'); ?>

<?php
/**
 * 讨论页面
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
$this->need('component/headnav.php');
?>
<div id="main_load">
    <main class="mix-discussion-page">
        <link rel="stylesheet" href="<?php echo $GLOBALS['assetURL']; ?>css/discussion.css?v=<?php echo filemtime(__DIR__ . '/assets/css/discussion.css'); ?>">
        <?php if ($this->options->sideBarStyle == 2) $this->need('component/sidebar.php'); ?>
        <div class="mix-discussion-board">
        <section class="mix-discussion-notice" aria-labelledby="mix-discussion-title">
            <div class="mix-discussion-paper">
            <h1 id="mix-discussion-title"><?php $this->title(); ?></h1>
            <?php if (trim((string) $this->text) !== ''): ?>
                <div class="post-content paul-note mix-discussion-intro" id="write">
                    <?php Content::postContentHtml($this, $this->user->hasLogin()); ?>
                </div>
            <?php endif; ?>
            </div>
        </section>
        </div>
        <?php $this->need('comments.php'); ?>
        <?php $this->need('component/post/article-actions.php'); ?>
    </main>
</div>
<?php $this->need('footer.php'); ?>

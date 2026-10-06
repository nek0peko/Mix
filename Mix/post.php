<?php
$this->need('header.php');
$this->need('component/headnav.php');
?>
    <div id="main_load">
        <main class="is-article mix-reading-page">
            <?php $this->need('component/post/category-navigation.php'); ?>
            <?php
            if ($this->options->sideBarStyle == 2) {
                $this->need('component/sidebar.php');
            }
            ?>
            <!-- <script src="<?php echo $GLOBALS['assetURL'] ?>js/Typing.js"></script> -->
            <?php $this->need('component/post/article_post.php'); ?>
            <?php $this->need('component/post/article-actions.php'); ?>

        </main>
    </div>
    <!--必要底部元素-->
<?php $this->need('footer.php'); ?>

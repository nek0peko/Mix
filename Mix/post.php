<?php
$this->need('header.php');
$PostChoice = $this->fields->PostChoice;
$this->need('component/headnav.php');
if ($PostChoice == 0): ?>
    <div id="main_load">
        <main class="is-article">
            <?php
            if ($this->options->sideBarStyle == 2) {
                $this->need('component/sidebar.php');
            }
            ?>
            <!-- <script src="<?php echo $GLOBALS['assetURL'] ?>js/Typing.js"></script> -->
            <?php $this->need('component/post/article_post.php'); ?>

        </main>
    </div>
<?php elseif ($PostChoice == 1): ?>
    <div id="main_load">
        <main class="is-article is-note post-content paul-note">
            <?php $this->need('component/post/dairy_post.php'); ?>
            <?php
            if ($this->options->sideBarStyle == 2) {
                $this->need('component/sidebar.php');
            }
            ?>
        </main>
    </div>
<?php endif; ?>
    <!--必要底部元素-->
<?php $this->need('footer.php'); ?>
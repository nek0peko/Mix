<?php
/*
 * @Name: page-links.php
 * @author: Wibus
 * @Date: 2021-03-15 22:51:31
 * @LastEditors: Wibus
 * @LastEditTime: 2021-04-03 10:44:55
 */

/**
 * 友链页面
 *
 * @package custom
 */
?>

<!--头部必要元素-->
<?php $this->need('header.php'); ?>
<script>
    function getURL(url) {
        $.ajax({
            url: 'https://bird.ioliu.cn/v2/?url=' + url,
            type: 'GET',
            complete: function (response) {
                if (response.status === 200) {
                    alert('有效');
                } else {
                    alert('无效');
                }
            }
        });
    }
</script>

<?php
$this->need('component/headnav.php');
?>
<style>
    .links {
        animation: <?php $this->options->LinksAction(); ?>;
    }
</style>
<div id="main_load">
    <main class="is-article" id="article-wrap">
        <?php
        if ($this->options->sideBarStyle == 2) {
            $this->need('component/sidebar.php');
        }
        ?>
        <section class="post-title">
            <h1>
                <div>
                    <div class="texty mask-bottom" style="opacity: 1;"><span class=""
                                                                             style="opacity: 1; transform: translate(0px, 0%);"><?php $this->title() ?></span>
                    </div>
                </div>
            </h1>
            <h2>
                <div>
                    <div class="texty mask-bottom" style="opacity: 1;"><span class=""
                                                                             style="opacity: 1; transform: translate(0px, 0%);">海内存知己，天涯若比邻</span>
                    </div>
            </h2>
            <?php Content::postContentHtml($this, $this->user->hasLogin()); ?>
        </section>
        <div>
            <article class="post-content paul-note" style="opacity: 1;">
                <article class="post-content paul-note article-list">
                    <ul>
                        <div>
                            <?php Links_Plugin::output('<li class="links"><a href="{url}" title="{title}" target="_blank">{name}</a><span class="meta">{description}<span></li>'); ?>
                        </div>
                    </ul>
                </article>
            </article>
        </div>
        <?php $this->need('comments.php') ?>
    </main>
</div>

<!--必要底部元素-->
<?php $this->need('footer.php'); ?>

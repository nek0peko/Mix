<?php
/**
 * 友链页面
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$mixFriends = [];
if (Admin_Helper::isPluginAvailable('Links_Plugin', 'Links')) {
    $mixLinksDb = Typecho_Db::get();
    $mixFriends = $mixLinksDb->fetchAll($mixLinksDb->select()->from('table.links')->order('order', Typecho_Db::SORT_ASC));
    $mixFriends = array_values(array_filter($mixFriends, function ($friend) {
        return trim((string) $friend['name']) !== '' && trim((string) $friend['url']) !== ''
            && MixHyperlinks::validUrl(trim((string) $friend['url']));
    }));
}
$mixFriendEscape = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$mixCatAvatars = range(1, 10);
shuffle($mixCatAvatars);
$this->need('header.php');
$this->need('component/headnav.php');
?>
<div id="main_load">
    <main class="is-article mix-friends-page" id="article-wrap">
        <?php if ($this->options->sideBarStyle == 2) $this->need('component/sidebar.php'); ?>
        <section class="post-title mix-friends-heading">
            <div class="mix-friends-title-row">
                <span class="mix-friends-heading-icon" aria-hidden="true"><i class="fas fa-user-friends"></i></span>
                <h1><?php $this->title(); ?></h1>
                <span class="mix-friends-count"><?php echo count($mixFriends); ?> 位朋友</span>
            </div>
            <h2>海内存知己，天涯若比邻</h2>
        </section>
        <article class="post-content paul-note mix-friends-intro">
            <?php Content::postContentHtml($this, $this->user->hasLogin()); ?>
        </article>
        <?php if ($mixFriends): ?>
        <ul class="mix-friends-grid" aria-label="友情链接">
            <?php foreach ($mixFriends as $mixFriendIndex => $mixFriend):
                $mixFriendName = trim((string) $mixFriend['name']);
                $mixFriendUrl = trim((string) $mixFriend['url']);
                $mixFriendDescription = trim((string) $mixFriend['description']);
                $mixFriendFallback = 'https://raw.githubusercontent.com/nek0peko/cdn-static/main/Mix/img/icon/' . $mixCatAvatars[$mixFriendIndex % 10] . '.png';
                if (($mixFriendIndex + 1) % 10 === 0) shuffle($mixCatAvatars);
                $mixFriendImage = trim((string) $mixFriend['image']);
                if ($mixFriendImage === '' || !MixHyperlinks::validUrl($mixFriendImage)) $mixFriendImage = $mixFriendFallback;
            ?>
            <li class="mix-friend-item">
                <a class="mix-friend-card" href="<?php echo $mixFriendEscape($mixFriendUrl); ?>" target="_blank" rel="noopener noreferrer">
                    <span class="mix-friend-avatar" aria-hidden="true">
                        <i class="fas fa-paw"></i>
                        <img class="mix-friend-image" src="<?php echo $mixFriendEscape($mixFriendImage); ?>" alt="" width="64" height="64" loading="lazy" decoding="async" data-fallback="<?php echo $mixFriendEscape($mixFriendFallback); ?>" onerror="if(this.src!==this.dataset.fallback){this.src=this.dataset.fallback;}else{this.hidden=true;}">
                    </span>
                    <span class="mix-friend-details">
                        <span class="mix-friend-name"><?php echo $mixFriendEscape($mixFriendName); ?></span>
                        <?php if ($mixFriendDescription !== ''): ?>
                        <span class="mix-friend-description"><?php echo $mixFriendEscape($mixFriendDescription); ?></span>
                        <?php endif; ?>
                        <span class="mix-friend-url" title="<?php echo $mixFriendEscape($mixFriendUrl); ?>"><?php echo $mixFriendEscape($mixFriendUrl); ?></span>
                    </span>
                    <i class="fas fa-arrow-up mix-friend-arrow" aria-hidden="true"></i>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p class="mix-friends-empty">还没有添加友链</p>
        <?php endif; ?>
        <?php if (in_array('ShowComment', mixEnabledComponents($this->options), true)) $this->need('comments.php'); ?>
        <?php $this->need('component/post/article-actions.php'); ?>
    </main>
</div>
<?php $this->need('footer.php'); ?>

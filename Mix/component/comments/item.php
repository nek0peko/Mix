<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

function threadedComments($comments, $options)
{
    $class = $comments->levels > 0 ? ' comment-child' : ' comment-parent';
    if ($comments->authorId) {
        $class .= $comments->authorId == $comments->ownerId ? ' comment-by-author' : ' comment-by-user';
    }
    $author = htmlspecialchars((string) $comments->author, ENT_QUOTES, 'UTF-8');
    $url = trim((string) $comments->url);
    if ($url !== '' && MixHyperlinks::validUrl($url)) {
        $author = '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="external nofollow noopener noreferrer">' . $author . '</a>';
    }
    ?>
    <li id="li-<?php $comments->theId(); ?>" class="comment-body<?php echo $class; ?><?php
        if ($comments->levels > 0) $comments->levelsAlt(' comment-level-odd', ' comment-level-even');
        $comments->alt(' comment-odd', ' comment-even');
    ?>">
        <div id="<?php $comments->theId(); ?>">
            <div class="comments-body">
                <img class="avatar" src="https://raw.githubusercontent.com/nek0peko/cdn-static/main/Mix/img/icon/<?php echo rand(1, 10); ?>.png"
                     alt="<?php echo htmlspecialchars((string) $comments->author, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" decoding="async"/>
                <div class="line"></div>
                <div class="comment_main">
                    <div class="comment_meta">
                        <span class="comment_author"><?php echo $author; ?></span>
                        <span class="comment_time"><?php $comments->date('y-m-d'); ?></span>
                        <span class="comment_reply"><i class="fa fa-reply" aria-hidden="true"></i><?php $comments->reply(); ?></span>
                    </div>
                    <?php $comments->content(); ?>
                </div>
            </div>
        </div>
        <?php if ($comments->children): ?>
            <div class="comment-children"><?php $comments->threadedComments($options); ?></div>
        <?php endif; ?>
    </li>
    <?php
}

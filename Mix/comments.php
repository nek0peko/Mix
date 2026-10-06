<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
require_once __DIR__ . '/component/comments/item.php';
$mixCommentsDiscussion = (string) $this->template === 'page-cross.php';
$this->comments()->to($comments);
$mixCommentReply = false;
if ($mixCommentsDiscussion) include __DIR__ . '/component/comments/form.php';
?>
<article class="comment-list mix-comment-section<?php echo $mixCommentsDiscussion ? ' mix-discussion-comments' : ''; ?>"
         data-comment-count="<?php echo (int) $this->commentsNum; ?>" data-cid="<?php echo (int) $this->cid; ?>">
    <?php if ($mixCommentsDiscussion): ?>
        <h1 class="mix-discussion-list-heading"><span>讨论</span><span class="mix-discussion-total"><?php echo (int) $this->commentsNum; ?> 条</span></h1>
    <?php else: ?>
        <h1><?php $this->commentsNum(_t('暂无评论'), _t('仅有一条评论'), _t('已有 %d 条评论')); ?></h1>
    <?php endif; ?>
    <div class="reply" id="<?php $this->respondId(); ?>" style="display: none">
        <?php $mixCommentReply = true; include __DIR__ . '/component/comments/form.php'; ?>
    </div>
    <?php if ($comments->have()): ?>
        <?php $comments->listComments(); ?>
        <?php $comments->pageNav('&laquo;', '&raquo;'); ?>
    <?php elseif ($mixCommentsDiscussion): ?>
        <div class="mix-discussion-empty"><i class="far fa-comments" aria-hidden="true"></i><p>还没有讨论，来留下第一条留言吧</p></div>
    <?php endif; ?>
</article>
<?php
if (!$mixCommentsDiscussion) {
    $mixCommentReply = false;
    include __DIR__ . '/component/comments/form.php';
}
?>

<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<form method="post" action="<?php $this->commentUrl(); ?>" role="form" data-mix-comment-form no-pjax
      <?php if ($mixCommentReply): ?>class="reply_form"<?php else: ?>id="comment-form"<?php if ($mixCommentsDiscussion): ?> class="mix-discussion-composer"<?php endif; ?><?php endif; ?>>
    <?php if (!$mixCommentReply && $mixCommentsDiscussion && $this->allow('comment')): ?><h2 class="mix-discussion-compose-heading"><i class="far fa-comment-dots" aria-hidden="true"></i><span>参与讨论</span></h2><?php endif; ?>
    <?php if (!$mixCommentReply): ?><section class="post-form is-comment"><div class="note-comments"><div id="note-m"></div><?php endif; ?>
    <?php if ($this->allow('comment')): ?>
        <?php if ($this->user->hasLogin()): ?>
            <p><?php _e('登录身份: '); ?><a href="<?php $this->options->profileUrl(); ?>"><?php $this->user->screenName(); ?></a>.
                <a href="<?php $this->options->logoutUrl(); ?>" title="Logout"><?php _e('退出'); ?> &raquo;</a>
            </p>
        <?php else: ?>
            <?php if ($mixCommentReply): ?><div class="col-3"><?php endif; ?>
            <input type="text" name="author" placeholder="你叫什么~ (name)" value="<?php $this->remember('author'); ?>" required>
            <input type="text" name="mail" placeholder="邮箱~ (mail)" value="<?php $this->remember('mail'); ?>" required>
            <input type="text" name="url" placeholder="网站~ (website)" value="<?php $this->remember('url'); ?>">
            <?php if ($mixCommentReply): ?></div><?php endif; ?>
        <?php endif; ?>
        <textarea rows="<?php echo $mixCommentsDiscussion ? '4' : '8'; ?>" name="text" placeholder="<?php echo $mixCommentsDiscussion ? ($mixCommentReply ? '写下你的回复…' : '写下你的想法…') : ($mixCommentReply ? '回复内容 (comment here) ☆´∀｀☆' : '谢谢评论 (comment here) ☆´∀｀☆'); ?>" required><?php $this->remember('text'); ?></textarea>
        <div class="submit">
            <button type="submit" class="btn yellow"><i class="fa fa-paper-plane" aria-hidden="true"></i><span>提交</span></button>
            <?php if ($mixCommentReply): ?><span class="cancel-comment-reply"><?php $comments->cancelReply(); ?></span><?php endif; ?>
        </div>
    <?php else: ?>
        <p>评论功能暂时关闭</p>
    <?php endif; ?>
    <?php if (!$mixCommentReply): ?></div></section><?php endif; ?>
</form>

<?php if ($this->is('single')): ?>
<div class="mix-article-actions" data-mix-article-actions data-cid="<?php echo (int) $this->cid; ?>" data-endpoint="<?php echo htmlspecialchars(BLOG_URL_PHP . '?mix_like=1&cid=' . (int) $this->cid, ENT_QUOTES, 'UTF-8'); ?>" hidden>
    <button type="button" class="mix-view-button" aria-label="浏览次数" title="浏览次数" aria-disabled="true">
        <i class="far fa-eye" aria-hidden="true"></i>
        <span class="mix-view-count" aria-live="polite">…</span>
    </button>
    <button type="button" class="mix-like-button" aria-label="点赞" title="点赞" aria-pressed="false" disabled>
        <i class="far fa-heart" aria-hidden="true"></i>
        <span class="mix-like-count" aria-live="polite">…</span>
    </button>
    <?php if ($this->allow('comment') && ((string) $this->template === 'page-cross.php' || in_array('ShowComment', mixEnabledComponents($this->options), true))): ?>
    <button type="button" class="mix-comment-button" aria-label="前往评论区，共 <?php echo (int) $this->commentsNum; ?> 条评论" title="前往评论区，共 <?php echo (int) $this->commentsNum; ?> 条评论">
        <i class="far fa-comment" aria-hidden="true"></i>
        <span class="mix-comment-count"><?php echo (int) $this->commentsNum > 99 ? '99+' : (int) $this->commentsNum; ?></span>
    </button>
    <?php endif; ?>
</div>
<?php endif; ?>

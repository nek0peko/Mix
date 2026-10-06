<article class="note-article" style="opacity: 1; transform: translate(0px, 0px);">
    <h1><?php $this->date(); ?>&nbsp;&nbsp;<?php include __DIR__ . '/font-controls.php'; ?></h1>
    <h2 title="创建于 <?php $this->date(); ?>" style="text-align: center;"><?php $this->title() ?></h2>
    <div id="write">
        <div class="paragraph">
            <span class="indent"> <?php Content::postContentHtml($this, $this->user->hasLogin()); ?></span>
        </div>
    </div>
    <!--
       <section class="paul-more">
        <button class="btn green">前一篇</button>
        <button class="btn yellow">时间线</button>
       </section>
    -->
    <script>//ks.lazy("img");</script>
    <?php if (in_array('ShowComment', mixEnabledComponents($this->options), true)): ?>
        <?php $this->need('comments.php') ?>
    <?php endif; ?>
</article>
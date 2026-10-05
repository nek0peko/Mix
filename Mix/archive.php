<!--libs.php-->
<?php require_once 'libs/libs.php';
$mixCategory = $this->is('category');
$mixTag = $this->is('tag');
$mixCardArchive = $mixCategory || $mixTag;
$mixTagColor = $mixTag ? (int) ($this->getPageRow()['mid'] ?? 0) % 6 : 0;
$num = 0;
?>
<meta name="description" content="<?php $this->archiveTitle(array(
    'category' => _t('分类 %s 下的文章'),
    'search' => _t('包含关键字 %s 的文章'),
    'tag' => _t('标签 %s 下的文章'),
    'author' => _t('%s 发布的文章')
), '', ' - '); ?>">
<?php
// 头部必要元素
$this->need('header.php');
$this->need('component/headnav.php');
?>

<div id="main_load">
    <main class="is-article<?php echo $mixCardArchive ? ' mix-category' : ''; ?>" id="article-wrap">
        <?php
        if ($this->options->sideBarStyle == 2) {
            $this->need('component/sidebar.php');
        }
        ?>
        <section class="post-title">
            <h1>
                <div class="texty mask-bottom" style="opacity: 1;">
                        <span style="opacity: 1; transform: translate(0px, 0%);"><?php if ($mixTag): ?><span class="mix-tag-title-mark mix-category-tag--<?php echo $mixTagColor; ?>" aria-hidden="true">#</span><span class="mix-tag-title-name mix-category-tag--<?php echo $mixTagColor; ?>"><?php echo MixPostPreview::escapeText((string) $this->getArchiveTitle()); ?></span> 标签下的文章<?php else: ?><?php $this->archiveTitle(array(
                                'category' => _t('- %s'),
                                'search' => _t('- 包含关键字“%s”的文章'),
                                'tag' => _t('- %s 标签下的文章'),
                                'author' => _t('- %s 发布的文章')
                            ), '', ' - '); ?><?php endif; ?></span>
                        <?php if ($mixCardArchive): ?>
                            <span class="mix-category-count">共 <?php echo $this->getTotal(); ?> 篇</span>
                        <?php endif; ?>
                </div>
            </h1>
            <h2>
                <div class="texty mask-bottom" style="opacity: 1;">
                    <span style="opacity: 1; transform: translate(0px, 0%);"><?php
                        if ($this->is('category')): echo $this->getDescription(); endif; ?>
                    </span>
                </div>
            </h2>
        </section>
        <?php if ($mixCardArchive): ?>
        <section class="mix-category-list" aria-label="<?php echo $mixCategory ? '分类文章' : '标签文章'; ?>">
            <?php $mixHasPosts = false; ?>
            <?php while ($this->next()): ?>
                <?php
                $mixHasPosts = true;
                $preview = MixPostPreview::prepare((string) $this->text, (bool) $this->hidden || (string) $this->password !== '');
                $mixPrivate = $this->status === 'private';
                $mixPasswordProtected = (string) $this->password !== '';
                ?>
                <article class="mix-category-card<?php echo $preview['cover'] === '' ? ' mix-category-card--text' : ''; ?>">
                    <?php if ($preview['cover'] !== ''): ?>
                    <div class="mix-category-cover-wrap">
                        <img class="mix-category-cover" src="<?php echo htmlspecialchars($preview['cover'], ENT_QUOTES, 'UTF-8'); ?>" alt="" loading="lazy" decoding="async" onerror="this.closest('.mix-category-card').classList.add('mix-category-card--text'); this.parentElement.remove();">
                    </div>
                    <?php endif; ?>
                    <div class="mix-category-copy">
                        <div class="mix-category-heading">
                            <h2 class="mix-category-title"><a href="<?php $this->permalink(); ?>"><?php echo MixPostPreview::escapeText((string) $this->title); ?></a></h2>
                            <?php if ($mixPrivate): ?>
                            <span class="mix-category-visibility" tabindex="0" role="img" aria-label="私密文章">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.3A10.8 10.8 0 0 1 12 5c6 0 10 7 10 7a18.5 18.5 0 0 1-3.1 3.8M6.5 6.5A18.6 18.6 0 0 0 2 12s4 7 10 7a10.6 10.6 0 0 0 5.5-1.5"/></svg>
                            </span>
                            <?php endif; ?>
                            <?php if ($mixPasswordProtected): ?>
                            <span class="mix-category-visibility" tabindex="0" role="img" aria-label="密码保护">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/><path d="M12 14v3"/></svg>
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php if ($preview['summary'] !== ''): ?>
                        <p class="mix-category-summary"><?php echo htmlspecialchars($preview['summary'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                        <div class="mix-category-meta">
                            <time class="mix-category-date" datetime="<?php $this->date('c'); ?>"><?php $this->date('Y-m-d'); ?></time>
                            <?php $mixTags = array_slice(array_filter($this->tags, static function ($tag) { return trim((string) $tag['name']) !== ''; }), 0, 3); ?>
                            <?php if ($mixTags): ?>
                            <div class="mix-category-tags" aria-label="文章标签">
                                <?php foreach ($mixTags as $tag): ?>
                                    <a class="mix-category-tag mix-category-tag--<?php echo (int) $tag['mid'] % 6; ?>" href="<?php echo htmlspecialchars((string) $tag['permalink'], ENT_QUOTES, 'UTF-8'); ?>">#<?php echo MixPostPreview::escapeText((string) $tag['name']); ?></a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
            <?php if (!$mixHasPosts): ?><p class="mix-category-empty"><?php echo $mixCategory ? '这个分类下还没有文章' : '这个标签下还没有文章'; ?></p><?php endif; ?>
        </section>
        <?php else: ?>
        <article class="post-content paul-note" style="opacity: 1;">
            <article class="post-content paul-note article-list">
                <ul>
                    <?php while ($this->next()) : ?>
                        <li style="opacity: 1; transform: translate(0px, 0px);
                                animation: <?php $this->options->LinksAction(); ?>; ">
                            <a href="<?php $this->permalink() ?>" rel="noopener"><?php $this->title() ?></a>
                            <span class="meta"><?php $this->date(); ?></span></li>
                    <?php endwhile; ?>
                </ul>
            </article>
        </article>
        <?php endif; ?>

        <div class="page-navigator">
            <?php $this->pageNav('&laquo; 前一页', '后一页 &raquo;', 5, '...', array(
                'wrapTag' => 'nav',
                'wrapClass' => 'pagination justify-content-center',
                'itemTag' => '',
                'textTag' => '',
                'currentClass' => 'active',
                'prevClass' => 'page-item',
                'nextClass' => 'page-item',
                'itemClass' => 'page-item',
                'linkClass' => 'page-link'
            )); ?>
        </div>
    </main>
</div>

<!--必要底部元素-->
<?php $this->need('footer.php'); ?>

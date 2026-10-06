<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$mixCategoryNodes = [];
$this->widget('Widget_Metas_Category_List')->to($mixCategoryList);
while ($mixCategoryList->next()) {
    if (!mixCategoryHasPosts($this, $mixCategoryList->mid)) continue;
    $mixCategoryNodes[(int) $mixCategoryList->mid] = [
        'mid' => (int) $mixCategoryList->mid,
        'parent' => (int) $mixCategoryList->parent,
        'name' => (string) $mixCategoryList->name,
        'url' => (string) $mixCategoryList->permalink,
        'slug' => (string) $mixCategoryList->slug,
        'directory' => (array) $mixCategoryList->directory,
        'order' => (int) $mixCategoryList->order,
        'posts' => []
    ];
}
// Fetch only navigation metadata, without rendering full article bodies.
$mixNavDb = Typecho_Db::get();
$mixNavSelect = $mixNavDb->select('table.contents.cid', 'table.contents.title', 'table.contents.slug', 'table.contents.created', 'table.relationships.mid')
    ->from('table.contents')
    ->join('table.relationships', 'table.contents.cid = table.relationships.cid')
    ->join('table.metas', 'table.metas.mid = table.relationships.mid')
    ->where('table.metas.type = ?', 'category')
    ->where('table.contents.type = ?', 'post')
    ->where('table.contents.created < ?', $this->options->time)
    ->order('table.contents.created', Typecho_Db::SORT_DESC);
if ($this->user->hasLogin()) {
    $mixNavSelect->where('table.contents.status = ? OR (table.contents.status = ? AND table.contents.authorId = ?)', 'publish', 'private', $this->user->uid);
} else {
    $mixNavSelect->where('table.contents.status = ?', 'publish');
}
$mixNavArticles = [];
foreach ($mixNavDb->fetchAll($mixNavSelect) as $mixNavRow) {
    $cid = (int) $mixNavRow['cid'];
    if (!isset($mixNavArticles[$cid])) $mixNavArticles[$cid] = ['row' => $mixNavRow, 'categories' => []];
    if (isset($mixCategoryNodes[(int) $mixNavRow['mid']])) $mixNavArticles[$cid]['categories'][] = (int) $mixNavRow['mid'];
}
foreach ($mixNavArticles as $cid => $mixNavArticle) {
    $mids = $mixNavArticle['categories'];
    if (!$mids) continue;
    usort($mids, function ($a, $b) use ($mixCategoryNodes) {
        return ($mixCategoryNodes[$a]['order'] <=> $mixCategoryNodes[$b]['order']) ?: ($a <=> $b);
    });
    $primary = $mixCategoryNodes[$mids[0]];
    $row = $mixNavArticle['row'];
    $date = new Typecho_Date($row['created']);
    $route = ['cid' => $cid, 'slug' => urlencode($row['slug']), 'category' => urlencode($primary['slug']),
        'directory' => implode('/', array_map('urlencode', $primary['directory'])),
        'year' => $date->year, 'month' => $date->month, 'day' => $date->day];
    $url = Typecho_Common::url(Typecho_Router::url('post', $route), $this->options->index);
    foreach (array_unique($mids) as $mid) $mixCategoryNodes[$mid]['posts'][] = ['cid' => $cid, 'title' => $row['title'], 'url' => $url];
}
$mixCurrentPostId = (int) $this->cid;
$mixCurrentCategories = [];
$mixExpandedCategories = [];
foreach ((array) $this->categories as $mixCurrentCategory) {
    $mixCurrentId = (int) $mixCurrentCategory['mid'];
    $mixCurrentCategories[$mixCurrentId] = true;
    $mixPath = [];
    while (isset($mixCategoryNodes[$mixCurrentId]) && !isset($mixPath[$mixCurrentId])) {
        $mixPath[$mixCurrentId] = true;
        $mixExpandedCategories[$mixCurrentId] = true;
        $mixCurrentId = $mixCategoryNodes[$mixCurrentId]['parent'];
    }
}
$mixCategoryChildren = [];
foreach ($mixCategoryNodes as $mixCategoryNode) {
    $mixParentId = isset($mixCategoryNodes[$mixCategoryNode['parent']]) ? $mixCategoryNode['parent'] : 0;
    $mixCategoryChildren[$mixParentId][] = $mixCategoryNode['mid'];
}
$mixRenderCategoryTree = function ($parent, $path = []) use (&$mixRenderCategoryTree, $mixCategoryNodes, $mixCategoryChildren, $mixCurrentCategories, $mixExpandedCategories, $mixCurrentPostId) {
    if (empty($mixCategoryChildren[$parent])) return;
    echo '<ul class="mix-article-category-tree">';
    foreach ($mixCategoryChildren[$parent] as $mid) {
        if (isset($path[$mid])) continue;
        $node = $mixCategoryNodes[$mid];
        $current = isset($mixCurrentCategories[$mid]);
        $hasChildren = !empty($mixCategoryChildren[$mid]) || !empty($node['posts']);
        echo '<li>';
        if ($hasChildren) echo '<details' . (isset($mixExpandedCategories[$mid]) ? ' open' : '') . '><summary>';
        echo '<span class="mix-article-category-link' . ($current ? ' is-current' : '') . '"' . ($current ? ' aria-current="true"' : '') . '>' . htmlspecialchars($node['name'], ENT_QUOTES, 'UTF-8') . '</span>';
        if ($hasChildren) {
            echo '<i class="fas fa-chevron-right" aria-hidden="true"></i></summary><div class="mix-category-contents">';
            $nextPath = $path;
            $nextPath[$mid] = true;
            $mixRenderCategoryTree($mid, $nextPath);
            if ($node['posts']) {
                echo '<ul class="mix-article-post-list">';
                foreach ($node['posts'] as $post) {
                    $selected = $post['cid'] === $mixCurrentPostId;
                    $title = htmlspecialchars(html_entity_decode((string) $post['title'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
                    echo '<li><a class="mix-article-post-link' . ($selected ? ' is-current' : '') . '" href="' . htmlspecialchars($post['url'], ENT_QUOTES, 'UTF-8') . '"' . ($selected ? ' aria-current="page"' : '') . ' title="' . $title . '">' . $title . '</a></li>';
                }
                echo '</ul>';
            }
            echo '</div></details>';
        }
        echo '</li>';
    }
    echo '</ul>';
};
?>
<button type="button" class="mix-article-navigation-toggle" aria-label="文章导航" title="文章导航" aria-controls="mix-article-navigation" aria-expanded="false" hidden><i class="fas fa-stream" aria-hidden="true"></i></button>
<aside id="mix-article-navigation" class="mix-article-navigation" role="dialog" aria-label="文章导航" aria-modal="false" aria-hidden="true" inert>
    <div class="mix-search-form mix-article-search" role="search">
            <input type="search" class="mix-tree-filter" placeholder="筛选文章标题" aria-label="筛选文章标题" autocomplete="off">
    </div>
    <div class="mix-article-navigation-panel">
        <p class="mix-tree-empty" role="status" hidden>没有匹配的文章</p>
        <?php if ($mixCategoryNodes): ?>
        <nav aria-label="分类">
            <h2 class="mix-article-navigation-title"><i class="fas fa-folder-open" aria-hidden="true"></i> 分类</h2>
            <?php $mixRenderCategoryTree(0); ?>
        </nav>
        <?php endif; ?>
    </div>
</aside>

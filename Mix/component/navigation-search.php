<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
if (!mixNavigationSearchEnabled($this->options)) return;
$mixSearchPanelId = uniqid('mix-nav-search-');
$mixNavigationKeywords = $this->is('search') ? (string) $this->getArchiveTitle() : '';
?>
<div class="<?php echo $mixSearchDrawer ? 'Header_link-section__1JFc9 global-link-section mix-nav-search-drawer' : 'menu-link mix-nav-search'; ?>">
    <?php if ($mixSearchDrawer): ?>
    <div class="Header_parent__3EA6A global-parent"><i class="fas fa-search" aria-hidden="true"></i><span>搜索</span></div>
    <?php else: ?>
    <button class="mix-nav-search-toggle" type="button" data-mix-search-toggle aria-expanded="false" aria-controls="<?php echo $mixSearchPanelId; ?>"><i class="fas fa-search" aria-hidden="true"></i><span>搜索</span></button>
    <?php endif; ?>
    <div<?php if (!$mixSearchDrawer): ?> class="mix-nav-search-panel" id="<?php echo $mixSearchPanelId; ?>" data-mix-search-panel hidden<?php endif; ?>>
        <?php $mixSearchForm = ['class' => 'mix-search-form mix-nav-search-form', 'value' => $mixNavigationKeywords]; include __DIR__ . '/search-form.php'; ?>
    </div>
</div>

<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$mixStatsId = uniqid('mix-nav-stats-');
?>
<div class="<?php echo $mixStatsDrawer ? 'Header_link-section__1JFc9 global-link-section mix-nav-stats-drawer' : 'menu-link mix-nav-stats'; ?>">
    <button type="button" class="mix-nav-search-toggle mix-nav-stats-toggle<?php echo $mixStatsDrawer ? ' Header_parent__3EA6A global-parent' : ''; ?>" data-mix-stats-toggle aria-expanded="false" aria-controls="<?php echo $mixStatsId; ?>"><i class="fas fa-chart-line" aria-hidden="true"></i><span>统计</span></button>
    <section id="<?php echo $mixStatsId; ?>" class="mix-stats-panel" data-mix-stats-panel data-endpoint="<?php echo htmlspecialchars(BLOG_URL_PHP . '?mix_stats=1', ENT_QUOTES, 'UTF-8'); ?>" aria-label="访客统计" tabindex="-1" hidden>
        <div class="mix-stats-heading"><strong>旅人足迹</strong><button type="button" data-mix-stats-close aria-label="关闭统计">×</button></div>
        <div data-mix-stats-content aria-live="polite"></div>
    </section>
</div>

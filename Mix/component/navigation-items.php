<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
foreach (MixNavigation::visible($this->options) as $mixNavigationItem) {
    $mixNavigationKind = $mixNavigationItem['builtin'] ?? '';
    if ($mixNavigationKind === 'home' || $mixNavigationKind === 'articles') {
        include __DIR__ . ($mixNavigationDrawer ? '/navigation-system-drawer.php' : '/navigation-system-desktop.php');
    } elseif ($mixNavigationKind === 'search') {
        $mixSearchDrawer = $mixNavigationDrawer;
        include __DIR__ . '/navigation-search.php';
    } elseif ($mixNavigationKind === 'stats') {
        $mixStatsDrawer = $mixNavigationDrawer;
        include __DIR__ . '/navigation-stats.php';
    } else {
        if ($mixNavigationKind === 'friends') {
            $mixNavigationItem = ['name' => '友链', 'link' => trim((string) $this->options->FriendURL), 'class' => 'fa fa-users', 'target' => '_self'];
        }
        $item = json_decode(json_encode($mixNavigationItem));
        $sub = '';
        if (!empty($item->sub)) {
            foreach ($item->sub as $child) $sub .= $mixNavigationDrawer ? Content::returnHeadSideItem($child, false, '', 'yes') : Content::returnHeadItem($child, false, '', 'yes');
            $sub = '<div class="' . ($mixNavigationDrawer ? 'Header_children-wrapper__1z9Ni global-children-wrapper' : 'sub-menu') . '">' . $sub . '</div>';
        }
        echo $mixNavigationDrawer ? Content::returnHeadSideItem($item, $sub !== '', $sub) : Content::returnHeadItem($item, $sub !== '', $sub);
    }
}

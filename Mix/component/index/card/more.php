<?php
$hyperlinkModules = [];
foreach (MixHyperlinks::orderedModules($this->options) as $module) {
    if (!empty($module['friends'])) { $hyperlinkModules[] = $module; continue; }
    if (!$module['enabled'] || trim($module['title']) === '') continue;
    $cards = [];
    foreach ($module['items'] as $item) {
        if (trim($item['title']) !== '' && trim($item['url']) !== '' && MixHyperlinks::validUrl($item['url'])) {
            $cards[] = ['name' => $item['title'], 'link' => $item['url'], 'description' => $item['description']];
        }
    }
    if ($cards) {
        $hyperlinkModules[] = ['title' => trim($module['title']) !== '' ? $module['title'] : '了解更多', 'cards' => $cards];
    }
}
?>
<?php foreach ($hyperlinkModules as $module): ?>
<?php if (!empty($module['friends'])) { $this->need('component/index/card/friends.php'); continue; } ?>
<div class="assets news-item"
     style="opacity: 1; transform: translate(0px, 0px);animation: <?php $this->options->IndexAction(); ?>;">
    <div class="assets news-head">
        <h3 class="assets title"
            style="background-color: rgb(<?php echo mt_rand(50, 255); ?>, <?php echo mt_rand(50, 255); ?>, <?php echo mt_rand(50, 255); ?>);">
            <svg aria-hidden="true"
                 focusable="false" data-prefix="fas" data-icon="heart"
                 class="svg-inline--fa fa-heart fa-w-16 SectionNews_icon__w_rh8" role="img"
                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path fill="currentColor"
                      d="M462.3 62.6C407.5 15.9 326 24.3 275.7 76.2L256 96.5l-19.7-20.3C186.1 24.3 104.5 15.9 49.7 62.6c-62.8 53.6-66.1 149.8-9.9 207.9l193.5 199.8c12.5 12.9 32.8 12.9 45.3 0l193.5-199.8c56.3-58.1 53-154.3-9.8-207.9z">
                </path>
            </svg>
            <?php echo htmlspecialchars($module['title'], ENT_QUOTES, 'UTF-8'); ?>
        </h3>
    </div>
    <div class="assets news-body">
        <div class="assets row s">
            <?php foreach ($module['cards'] as $card): ?>
            <div class="col-6 col-m-3" style="margin-top: 2rem;">
                <a class="SectionNews_news-article__3ttyR" href="<?php echo htmlspecialchars($card['link'], ENT_QUOTES, 'UTF-8'); ?>" rel="noopener">
                    <div class="SectionNews_card-container__1nays">
                        <div class="SectionNews_card-cover-wrap__1DHPb">
                            <div>
                                <div style="position: relative; max-width: 100%; margin: auto;">
                                    <div class="lazyload-image">
                                        <img src="<?php echo htmlspecialchars(rand_thumb($GLOBALS['assetURL']), ENT_QUOTES, 'UTF-8'); ?>" alt="photo"/>
                                    </div>
                                    <div class="placeholder-image hide"
                                         style="max-width: 100%; position: absolute; filter: brightness(1.3); z-index: -1;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="SectionNews_card-header__2M67p"></div>
                        <div class="SectionNews_card-title__3k9WJ">
                            <h3><?php echo htmlspecialchars($card['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        </div>
                        <div class="SectionNews_card-body__1Tj-4">
                            <div class="SectionNews_text-mask__21UEm"><span><?php echo htmlspecialchars($card['description'], ENT_QUOTES, 'UTF-8'); ?></span></div>
                        </div>
                        <div class="SectionNews_text-shade__QzdgY"></div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php endforeach; ?>

<?php Typecho_Widget::widget('Widget_Stat')->to($stat); ?>
<?php $this->widget('Widget_Metas_Category_List')->to($categories); ?>
<?php while ($categories->next()): ?>
    <?php if (count($categories->children) === 0): ?>
        <?php $this->widget('Widget_Archive@category-' . $categories->mid, 'order=order&pageSize=4&type=category', 'mid=' . $categories->mid)->to($posts); ?>
        <?php if (!$posts->have()) continue; ?>

        <section class="paul-news" style="animation: <?php $this->options->IndexAction(); ?>;">
        <div class="demo-content">

        <div class="assets news-item" style="opacity: 1; transform: translate(0px, 0px);">
            <div class="assets news-head"><!--源：rgb(59, 14,163)-->
                <h3 class="assets title"
                    style="background-color: <?php echo mixHomeModuleColor('category:' . $categories->mid); ?>;">
                    <svg aria-hidden="true"
                         focusable="false" data-prefix="fas" data-icon="book-open"
                         class="svg-inline--fa fa-book-open fa-w-18 SectionNews_icon__w_rh8" role="img"
                         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                        <path fill="currentColor"
                              d="M542.22 32.05c-54.8 3.11-163.72 14.43-230.96 55.59-4.64 2.84-7.27 7.89-7.27 13.17v363.87c0 11.55 12.63 18.85 23.28 13.49 69.18-34.82 169.23-44.32 218.7-46.92 16.89-.89 30.02-14.43 30.02-30.66V62.75c.01-17.71-15.35-31.74-33.77-30.7zM264.73 87.64C197.5 46.48 88.58 35.17 33.78 32.05 15.36 31.01 0 45.04 0 62.75V400.6c0 16.24 13.13 29.78 30.02 30.66 49.49 2.6 149.59 12.11 218.77 46.95 10.62 5.35 23.21-1.94 23.21-13.46V100.63c0-5.29-2.62-10.14-7.27-12.99z">
                        </path>
                    </svg><?php $categories->name(); ?></h3>
                <h3 class="assets more"
                    style="background-color: <?php echo mixHomeModuleColor('category:' . $categories->mid); ?>;">
                    <a class="assets"
                       href="<?php echo htmlspecialchars($categories->permalink, ENT_QUOTES, 'UTF-8'); ?>" rel="noopener">
                        <svg aria-hidden="true" focusable="false" data-prefix="fas"
                             data-icon="chevron-right" class="svg-inline--fa fa-chevron-right fa-w-10 " role="img"
                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                            <path fill="currentColor"
                                  d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z">
                            </path>
                        </svg>
                    </a></h3>
            </div>
            <div class="assets news-body">
                <div class="assets row s">

                    <?php while ($posts->next()): ?>
                        <?php
                        $mixPrivate = $posts->status === 'private';
                        $mixPasswordProtected = (string) $posts->password !== '';
                        ?>
                        <div class="col-6 col-m-3"><a class="SectionNews_news-article__3ttyR"
                                                      href="<?php $posts->permalink(); ?>" rel="noopener">
                                <div class="SectionNews_card-container__1nays">
                                    <div class="SectionNews_card-cover-wrap__1DHPb">
                                        <div>
                                            <div style="position: relative; max-width: 100%; margin: auto;">
                                                <div class="lazyload-image">
                                                    <img src="<?php echo getFirstImg($posts->cid, $GLOBALS['assetURL']); ?>" alt="photo"></div>
                                                <div class="placeholder-image hide"
                                                     style="max-width: 100%; position: absolute; filter: brightness(1.3); z-index: -1;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if ($mixPrivate || $mixPasswordProtected): ?>
                                    <div class="mix-thumbnail-status">
                            <?php if ($mixPrivate): ?>
                            <span class="mix-category-visibility" role="img" aria-label="私密文章">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.3A10.8 10.8 0 0 1 12 5c6 0 10 7 10 7a18.5 18.5 0 0 1-3.1 3.8M6.5 6.5A18.6 18.6 0 0 0 2 12s4 7 10 7a10.6 10.6 0 0 0 5.5-1.5"/></svg>
                            </span>
                            <?php endif; ?>
                            <?php if ($mixPasswordProtected): ?>
                            <span class="mix-category-visibility" role="img" aria-label="密码保护">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/><path d="M12 14v3"/></svg>
                            </span>
                            <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                    <div class="SectionNews_card-header__2M67p"></div>
                                    <div class="SectionNews_card-body__1Tj-4">
                                        <div class="SectionNews_text-mask__21UEm"><span><?php $posts->title(); ?></span>
                                        </div>
                                    </div>
                                    <div class="SectionNews_text-shade__QzdgY"></div>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; ?>


                </div>
            </div>
        </div>
        </div>
        </section>
    <?php endif; ?>
<?php endwhile; ?>

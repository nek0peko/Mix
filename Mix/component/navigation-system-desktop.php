<?php if ($mixNavigationKind === 'home'): ?>
            <div class="has-child"><a href="<?php Helper::options()->siteUrl() ?>"><i
                            class="fa fa-dot-circle"></i><span>主页</span></a>
                <div class="sub-menu">
                    <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
                    <?php while ($pages->next()): ?>
                        <a href="<?php $pages->permalink(); ?>"><span><?php $pages->title(); ?></span></a>
                    <?php endwhile; ?>
                </div>
            </div>
<?php elseif ($mixNavigationKind === 'articles'): ?>
            <div class="has-child"><a href="#"><i class="fa fa-book"></i><span>文章</span></a>
                <?php $this->widget('Widget_Metas_Category_List')->to($category); ?>
                <?php if ($category->have()): ?>
                <div class="sub-menu">
                    <?php while ($category->next()): ?>
                        <?php if (!mixCategoryHasPosts($this, $category->mid)) continue; ?>
                        <a href="<?php $category->permalink(); ?>"><span><?php $category->name(); ?></span></a>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </div>
<?php endif; ?>

<?php if (in_array('ShowHeadSVG', mixEnabledComponents($this->options), true)): ?>
    <section class="paul-intro">
        <!--顶部最大的头像-->
        <div class="intro-avatar "><img src="<?php echo htmlspecialchars((string) $this->options->HeaderPhoto, ENT_QUOTES, 'UTF-8'); ?>" style="width:100%" alt="<?php echo htmlspecialchars((string) $this->options->HeaderName, ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="intro-info">
            <h1>
                <!--名字-->
                <div class="texty mask-bottom"><span class=""
                                                     style="opacity: 1; transform: translate(0px, 0%);"><?php echo htmlspecialchars((string) $this->options->HeaderName, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </h1>
            <?php if (trim((string) $this->options->HeaderMore) !== ''): ?>
            <p>
            <span style="opacity: 1; transform: translate(0px, 0%);"
                  class=""><?php echo htmlspecialchars((string) $this->options->HeaderMore, ENT_QUOTES, 'UTF-8'); ?></span>
            </p>
            <?php endif; ?>

            <div class="texty mask-bottom">
                <div class="social-icons" style="opacity: 1; transform: translate(0px, 0px);">
                    <?php foreach (MixSocialIcons::items($this->options) as $number => $socialIcon): ?>
                        <?php
                        $iconUrl = trim($socialIcon['icon']);
                        $targetUrl = trim($socialIcon['url']);
                        if ($iconUrl === '' || $targetUrl === '') continue;
                        $label = basename(parse_url($iconUrl, PHP_URL_PATH) ?: '', '.svg');
                        $label = in_array($label, ['bilibili', 'github', 'pixiv'], true) ? $label : '社交链接 ' . ($number + 1);
                        ?>
                        <a href="<?php echo htmlspecialchars($targetUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank"
                           ks-tag="bottom" ks-text="<?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>"
                           aria-label="<?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>" rel="noopener noreferrer">
                            <img class="mix-social-icon" src="<?php echo htmlspecialchars($iconUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="20" height="20"/>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </section>
<?php endif; ?>
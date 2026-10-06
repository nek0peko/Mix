<?php
/**
 * functions_helper.php
 * 用于为functions新增一些新玩意
 * @author Wibus
 */

Helper::options()->commentsAntiSpam = false; //关闭反垃圾
Helper::options()->commentsCheckReferer = false; //关闭检查评论来源URL与文章链接是否一致判断(否则会无法评论)
Helper::options()->commentsMaxNestingLevels = '999'; //最大嵌套层数
Helper::options()->commentsPageDisplay = 'first'; //强制评论第一页
Helper::options()->commentsOrder = 'DESC'; //将最新的评论展示在前
Helper::options()->commentsHTMLTagAllowed = '<a href=""> <img src=""> <img src="" class=""> <code> <del>';
Helper::options()->commentsMarkdown = true;

function mixEnabledComponents($options): array
{
    // Accept backups created before the two groups were merged.
    $components = is_array($options->Show_what) ? $options->Show_what : [];
    $legacy = is_array($options->Show_what_1) ? $options->Show_what_1 : [];
    $components = array_values(array_unique(array_merge($components, $legacy)));
    if ($options->IMouseEnabled !== null) {
        $components = array_values(array_diff($components, ['ShowIMouse']));
        if (is_array($options->IMouseEnabled) && in_array('enabled', $options->IMouseEnabled, true)) {
            $components[] = 'ShowIMouse';
        }
    }
    return $components;
}

function mixCategoryHasPosts($archive, $mid): bool
{
    // Reuse the same archive as the homepage, including Typecho's visibility rules.
    $archive->widget('Widget_Archive@category-' . (int) $mid, 'order=order&pageSize=4&type=category', 'mid=' . (int) $mid)->to($posts);
    return $posts->have();
}

function mixNavigationSearchEnabled($options): bool
{
    return $options->NavSearchEnabled === null
        || (is_array($options->NavSearchEnabled) && in_array('enabled', $options->NavSearchEnabled, true));
}

function mixFriendsEnabled($options): bool
{
    // Existing installations keep their friend links enabled until explicitly disabled.
    return $options->FriendsEnabled === null || (is_array($options->FriendsEnabled) && in_array('enabled', $options->FriendsEnabled, true));
}

function mixFriendsVisible($options): bool
{
    return Admin_Helper::isPluginAvailable('Links_Plugin', 'Links') && mixFriendsEnabled($options)
        && ($options->FriendsModuleTitle === null || trim((string) $options->FriendsModuleTitle) !== '');
}

/**
 * getFirstImg 使用与分类页相同的封面提取逻辑
 * 若无可展示的图片，则使用随机图片
 * @author nek0peko
 */
function getFirstImg($cid, $site_Url)
{
    $db = Typecho_Db::get();
    $rs = $db->fetchRow($db->select('table.contents.text', 'table.contents.password')
        ->from('table.contents')
        ->where('table.contents.cid=?', $cid)
        ->order('table.contents.cid', Typecho_Db::SORT_ASC)
        ->limit(1));

    $cover = MixPostPreview::cover((string) ($rs['text'] ?? ''), !empty($rs['password']));
    echo $cover !== '' ? htmlspecialchars($cover, ENT_QUOTES, 'UTF-8') : rand_thumb($site_Url);
}

/**
 * rand_thumb 随机图片
 * 随机图片须按"1.png","2.png","3.png"...的顺序命名
 * @author nek0peko
 */
function rand_thumb($site_Url): string
{
    return 'https://raw.githubusercontent.com/nek0peko/cdn-static/main/Mix/img/thumb/'
        . MixRandomThumb::next() . '.png';
}

function parse_RSS($url, $site)
{
    $rss = simplexml_load_file($url);
    $file = $rss->channel->item;
    $link = $rss->channel->link;
    global $body;

    if (isset($file)) {
        // $rand_arr = get_randoms(0, 14, 5);
        for ($i = 0; $i < 4; $i++) {
            if ($file[$i]) {
                // $body .= '
                // <div class="col-6 col-m-3">' . '<a href="' . $file[$i]->link . '" class="news-article" target="_blank">' . '<img src="' . $site . '/src/img/' . array_pop($rand_arr) . '.jpg">' . '<h4>' . $file[$i]->title . '</h4></a></div>
                // ';
                $img = rand_thumb($site);
                $body .= '
                <div class="col-6 col-m-3">' . '<a class="SectionNews_news-article__3ttyR" href="' . $file[$i]->link . '" target="_blank" rel="noopener">
                      <div class="SectionNews_card-container__1nays">
                        <div class="SectionNews_card-cover-wrap__1DHPb">
                          <div>
                            <div style="position: relative; max-width: 100%; margin: auto;">
                              <div class="lazyload-image"><img src="' . $img . '" alt="photo"></div>
                              <div class="placeholder-image hide" style="max-width: 100%; position: absolute; filter: brightness(1.3); z-index: -1;">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="SectionNews_card-header__2M67p"></div>
                        <div class="SectionNews_card-body__1Tj-4">
                          <div class="SectionNews_text-mask__21UEm"><span>' . $file[$i]->title . '</span></div>
                        </div>
                        <div class="SectionNews_text-shade__QzdgY"></div>
                      </div>
                    </a>
                  </div>
                ';
            } else {
                break;
            }
        }
    } else {
        echo "博客连接失败啦，一请检查是否开启 OpenSSL 支持，二请检查地址是否正确。";
        echo "使用 AppNode 或者 其他面板 的小伙伴请注意，请把网站的PHP设置 `allow_url_fopen = On`";
    }
    return $body;
}

/**
 * 实时人数显示
 */
function online_users(): ?int
{
    // Share successful counts and failures between the title and footer.
    static $count = null;
    static $initialized = false;
    if ($initialized) return $count;
    $initialized = true;
    $filename = __TYPECHO_ROOT_DIR__ . __TYPECHO_THEME_DIR__ . '/Mix/online.txt';
    $unavailable = static function ($reason) use ($filename) {
        error_log('[Mix] 在线人数统计不可用：' . $reason . '（' . $filename . '）');
        return null;
    };
    if (!is_file($filename)) return $unavailable('文件不存在');
    $handle = @fopen($filename, 'r+');
    if (!$handle) return $unavailable('无法读写文件，请检查权限');
    if (!@flock($handle, LOCK_EX | LOCK_NB)) {
        fclose($handle);
        return $unavailable('文件暂时被占用，已跳过统计');
    }
    $now = $_SERVER['REQUEST_TIME'] ?? time();
    $uid = $_COOKIE['Mix_OnLineCount'] ?? '';
    if (!is_string($uid) || !preg_match('/^[a-zA-Z0-9]{1,64}$/D', $uid)) {
        try {
            $uid = bin2hex(random_bytes(16));
        } catch (Throwable $error) {
            @flock($handle, LOCK_UN);
            fclose($handle);
            return $unavailable('无法生成访客标识');
        }
        if (!headers_sent()) {
            setcookie('Mix_OnLineCount', $uid, 0, '/', '', !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', true);
        }
    }
    $visitors = [];
    while (($line = @fgets($handle)) !== false) {
        $row = explode('|', trim($line));
        if (count($row) === 2 && preg_match('/^[a-zA-Z0-9]{1,64}$/D', $row[0]) && ctype_digit($row[1])) {
            $seen = (int) $row[1];
            if ($seen <= $now && $now - $seen <= 90) $visitors[$row[0]] = $seen;
        }
    }
    $visitors[$uid] = $now;
    $contents = '';
    foreach ($visitors as $visitor => $seen) $contents .= $visitor . '|' . $seen . "\n";
    $written = @rewind($handle) && @ftruncate($handle, 0) && @fwrite($handle, $contents) === strlen($contents) && @fflush($handle);
    @flock($handle, LOCK_UN);
    fclose($handle);
    if (!$written) return $unavailable('写入失败，已跳过统计');
    return $count = count($visitors);
}

function createCatalog($obj)
{
    //为文章标题添加锚点
    global $catalog;
    global $catalog_count;
    $catalog = array();
    $catalog_count = 0;
    $obj = preg_replace_callback('/<h([1-6])(.*?)>(.*?)<\/h\1>/i', function ($obj) {
        global $catalog;
        global $catalog_count;
        $catalog_count++;
        $catalog[] = array('text' => trim(strip_tags($obj[3])), 'depth' => $obj[1], 'count' => $catalog_count);
        return '<h' . $obj[1] . $obj[2] . '><a name="cl-' . $catalog_count . '"></a>' . $obj[3] . '</h' . $obj[1] . '>';
    }, $obj);
    return $obj;
}

function getCatalog()
{
    global $catalog;
    if (!$catalog) return;
    $baseDepth = min(array_column($catalog, 'depth'));
    echo '<nav class="container Toc_container__100rU mix-toc" aria-label="文章目录"><div class="Toc_anime-wrapper__1l8Kz">';
    foreach ($catalog as $index => $item) {
        $depth = (int) $item['depth'];
        $text = htmlspecialchars(html_entity_decode($item['text'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
        echo '<a href="#cl-' . (int) $item['count'] . '" data-index="' . $index . '" data-depth="' . $depth . '" class="Toc_toc-link__1Yat3 mix-toc-link" style="--mix-toc-indent:' . (($depth - $baseDepth) * 12) . 'px"><span class="Toc_a-pointer__3AN3u">' . $text . '</span></a>';
    }
    echo '</div></nav>';
}

function mixOnlineHeartbeat($options): void
{
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store, max-age=0');
    header('Pragma: no-cache');
    // A custom header keeps cross-origin forms from creating visitor heartbeats.
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        echo json_encode(['enabled' => false, 'count' => null]);
        exit;
    }
    if (($_SERVER['HTTP_X_MIX_ONLINE'] ?? '') !== '1') {
        http_response_code(400);
        echo json_encode(['enabled' => false, 'count' => null]);
        exit;
    }
    $enabled = in_array('ShowAly', mixEnabledComponents($options), true);
    $count = $enabled ? online_users() : null;
    if ($enabled && $count === null) http_response_code(503);
    echo json_encode(['enabled' => $enabled, 'count' => $count]);
    exit;
}

function themeInit($archive)
{
    if (isset($_GET['mix_online']) && $_GET['mix_online'] === '1') {
        mixOnlineHeartbeat(Helper::options());
    }
    if ($archive->is('single')) {
        $archive->content = createCatalog($archive->content);
    }
}

function debug($t, $debug)
{
    switch ($debug) {
        case 0:
            break;
        case 1:
            echo '<script>console.log("' . $t . '")</script>';
            break;
        case 2:
            echo '<script>
				console.log("' . $t . '");
				ks.notice("{$t}", {
				    color: "yellow"
				});
			</script>';
            break;
    };
}

;

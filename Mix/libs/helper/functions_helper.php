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

/**
 * getFirstImg 正则获取文章中的图片链接
 * 若无图片附件，则使用随机图片
 * @author nek0peko
 */
function getFirstImg($cid, $site_Url)
{
    $db = Typecho_Db::get();
    $rs = $db->fetchRow($db->select('table.contents.text')
        ->from('table.contents')
        ->where('table.contents.cid=?', $cid)
        ->order('table.contents.cid', Typecho_Db::SORT_ASC)
        ->limit(1));

    // TODO: 正则会匹配hplayer中的图片
    preg_match_all("/(https:\/\/)[^>]*?.(png|jpg)/i", $rs['text'], $thumbUrl);

    if (count($thumbUrl[0]) == 0) {
        echo rand_thumb($site_Url);
    } else {
        echo $thumbUrl[0][0];
    }
}

/**
 * rand_thumb 随机图片
 * 随机图片须按"1.png","2.png","3.png"...的顺序命名
 * @author nek0peko
 */
function rand_thumb($site_Url): string
{
    // 是否随机图片，如果为false，则固定为一张图
    $isRandom = true;
    // assets/img/thumb目录中随机图片数量
    $rand_num = 23;

    if ($isRandom) {
//        $img_url = $site_Url . 'img/thumb/' . rand(1, $rand_num) . '.png';
        $img_url = 'https://cdn.jsdelivr.net/gh/nek0peko/cdn-static/Mix/img/thumb/' . rand(1, $rand_num) . '.png';
    } else {
//        $img_url = $site_Url . 'img/thumb/1.png';
        $img_url = 'https://cdn.jsdelivr.net/gh/nek0peko/cdn-static/Mix/img/thumb/1.png';
    }

    return $img_url;
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
                $rand_num = 23; //随机图片数量，根据图片目录中图片实际数量设置
//                $img = $GLOBALS['assetURL'] . 'img/' . rand(1, $rand_num) . '.png';
                $img = 'https://cdn.jsdelivr.net/gh/nek0peko/cdn-static/Mix/img/' . rand(1, $rand_num) . '.png';
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
function online_users()
{
    $filename = __TYPECHO_ROOT_DIR__ . __TYPECHO_THEME_DIR__ . '/Mix/online.txt';
    $cookiename = 'Mix_OnLineCount'; //Cookie名称
    $onlinetime = 30; //在线有效时间
    $online = file($filename);
    $nowtime = $_SERVER['REQUEST_TIME'];
    $nowonline = array();
    foreach ($online as $line) {
        $row = explode('|', $line);
        $sesstime = trim($row[1]);
        if (($nowtime - $sesstime) <= $onlinetime) {
            $nowonline[$row[0]] = $sesstime;
        }
    }
    if (isset($_COOKIE[$cookiename])) {
        $uid = $_COOKIE[$cookiename];
    } else {
        $vid = 0;
        do {
            $vid++;
            $uid = 'U' . $vid;
        } while (array_key_exists($uid, $nowonline));
        setcookie($cookiename, $uid);
    }
    $nowonline[$uid] = $nowtime;
    $total_online = count($nowonline);
    if ($fp = @fopen($filename, 'w')) {
        if (flock($fp, LOCK_EX)) {
            rewind($fp);
            foreach ($nowonline as $fuid => $ftime) {
                $fline = $fuid . '|' . $ftime . "\n";
                @fputs($fp, $fline);
            }
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }
    echo "$total_online";
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
    //输出文章目录容器
    global $catalog;
    $index = '';
    if ($catalog) {
        $index = '<div>' . "\n";
        $prev_depth = '';
        $to_depth = 0;
        foreach ($catalog as $catalog_item) {
            $catalog_depth = $catalog_item['depth'];
            if ($prev_depth) {
                if ($catalog_depth == $prev_depth) {
                    $index .= '</a>' . "\n";
                } elseif ($catalog_depth > $prev_depth) {
                    $to_depth++;
                    $index .= '<div>' . "\n";
                } else {
                    $to_depth2 = ($to_depth > ($prev_depth - $catalog_depth)) ? ($prev_depth - $catalog_depth) : $to_depth;
                    if ($to_depth2) {
                        for ($i = 0; $i < $to_depth2; $i++) {
                            $index .= '</li>' . "\n" . '</ul>' . "\n";
                            $to_depth--;
                        }
                    }
                    $index .= '</li>';
                }
            }
            // $index .= '<li><a href="#cl-' . $catalog_item['count'] . '">' . $catalog_item['text'] . '</a>';
            $index .= '<a data-scroll="true" href="#cl-' . $catalog_item['count'] . '" data-index="0" class="Toc_toc-link__1Yat3" data-depth="2" style="opacity: 1; transform: translate(0px, 0px);" ><span class="Toc_a-pointer__3AN3u">' . $catalog_item['text'] . '</span>';
            $prev_depth = $catalog_item['depth'];
        }
        for ($i = 0; $i <= $to_depth; $i++) {
            $index .= '</a>' . "\n" . '</div>' . "\n";
        }
        $index = '<div class="container Toc_container__100rU" style="max-width: 184.5px;">' .
            '<div class="Toc_anime-wrapper__1l8Kz">' . "\n" .
            $index .
            '</div>' . "\n" .
            '</div>';
    }
    echo $index;
}

function themeInit($archive)
{
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

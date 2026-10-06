<?php

/** Shared cover extraction and plain-text previews for post lists. */
class MixPostPreview
{
    public static function escapeText(string $text): string
    {
        return htmlspecialchars(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES, 'UTF-8');
    }

    /** Highlight plain text, escaping both matched and unmatched segments. */
    public static function highlightText(string $text, string $keywords, bool $decodeEntities = true): string
    {
        if ($decodeEntities) $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $terms = preg_split('/\s+/u', trim($keywords), -1, PREG_SPLIT_NO_EMPTY);
        if (!$terms) return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $terms = array_values(array_unique($terms));
        // Prefer the longest match when terms share a prefix.
        usort($terms, static function ($a, $b) { return strlen($b) <=> strlen($a); });
        $pattern = '~(' . implode('|', array_map(static function ($term) { return preg_quote($term, '~'); }, $terms)) . ')~iu';
        $parts = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        if ($parts === false) return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $result = '';
        foreach ($parts as $index => $part) {
            $escaped = htmlspecialchars($part, ENT_QUOTES, 'UTF-8');
            $result .= $index % 2 ? '<mark class="mix-search-highlight">' . $escaped . '</mark>' : $escaped;
        }
        return $result;
    }

    public static function cover(string $source, bool $protected = false): string
    {
        return self::prepare($source, $protected, false)['cover'];
    }

    /** Covers for linked local posts/pages, without issuing HTTP requests. */
    public static function linkedCover(string $url, string $custom, string $assetUrl): string
    {
        $custom = trim($custom);
        if ($custom !== '' && MixHyperlinks::validImageUrl($custom)) return $custom;
        static $covers = [];
        if (!array_key_exists($url, $covers)) {
            $covers[$url] = '';
            $options = Helper::options();
            $target = parse_url($url);
            $base = parse_url($options->index);
            // Only same-site content is read; external sites never trigger HTTP requests.
            if (is_array($target) &&
                (!isset($target['host']) || (strcasecmp($target['host'], $base['host'] ?? '') === 0
                    && ($target['port'] ?? null) === ($base['port'] ?? null)))) {
                $path = rawurldecode($target['path'] ?? '');
                $prefix = rtrim(rawurldecode($base['path'] ?? ''), '/');
                if ($prefix !== '' && strpos($path, $prefix . '/') === 0) $path = substr($path, strlen($prefix));
                foreach (['post', 'page'] as $type) {
                    $route = Typecho_Router::get($type);
                    if (!$route || !preg_match($route['regx'], $path, $matches)) continue;
                    array_shift($matches);
                    $params = array_combine($route['params'], $matches);
                    if (!isset($params['cid']) && !isset($params['slug'])) continue;
                    $db = Typecho_Db::get();
                    $query = $db->select('text', 'password', 'status')->from('table.contents')->where('type = ?', $type);
                    if (isset($params['cid'])) $query->where('cid = ?', $params['cid']);
                    if (isset($params['slug'])) $query->where('slug = ?', $params['slug']);
                    $row = $db->fetchRow($query->limit(1));
                    if ($row && ($row['status'] === 'publish' || ($type === 'page' && $row['status'] === 'hidden'))) {
                        $covers[$url] = self::cover((string) $row['text'], (string) $row['password'] !== '');
                    }
                    break;
                }
            }
        }
        return $covers[$url] !== '' ? $covers[$url] : rand_thumb($assetUrl);
    }

    public static function prepare(string $source, bool $protected = false, bool $withSummary = true): array
    {
        if ($protected) return ['cover' => '', 'summary' => ''];

        // Remove hidden blocks before looking for either text or a cover, including
        // nested blocks and unfinished [hide] tags in a draft.
        $parts = preg_split('~(\[/?hide\b[^\]]*\])~iu', $source, -1, PREG_SPLIT_DELIM_CAPTURE);
        $visible = '';
        $depth = 0;
        foreach ($parts as $part) {
            if (preg_match('~^\[hide\b~iu', $part)) {
                $depth++;
            } elseif (preg_match('~^\[/hide\b~iu', $part)) {
                $depth = max(0, $depth - 1);
            } elseif ($depth === 0) {
                $visible .= $part;
            }
        }
        $visible = preg_replace('~<!--.*?-->|<(script|style|pre)\b[^>]*>.*?</\1\s*>|```.*?```|\~\~\~.*?\~\~\~|\[scode\b[^\]]*\].*?\[/scode\]~isu', '', $visible);

        $images = [];
        preg_match_all('~<img\b[^>]*\bsrc\s*=\s*(?:"([^"]*)"|\x27([^\x27]*)\x27|([^\s>]+))~iu', $visible, $html, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        foreach ($html as $image) {
            $url = ($image[1][0] ?? '') ?: (($image[2][0] ?? '') ?: ($image[3][0] ?? ''));
            $images[] = [$image[0][1], $url];
        }
        preg_match_all('~!\[[^\]]*\]\(\s*(?:<([^>]+)>|([^\s)]+))(?:\s+[^)]*)?\)~u', $visible, $markdown, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        foreach ($markdown as $image) $images[] = [$image[0][1], $image[1][0] ?: ($image[2][0] ?? '')];
        // Typecho also supports reference images: ![caption][id] plus [id]: URL.
        $references = [];
        preg_match_all('~^[\t ]{0,3}\[([^\]\r\n]+)\]:[\t ]*<?([^\s>]+)>?~mu', $visible, $definitions, PREG_SET_ORDER);
        foreach ($definitions as $definition) {
            $key = mb_strtolower(trim(preg_replace('~\s+~u', ' ', $definition[1])), 'UTF-8');
            if (!isset($references[$key])) $references[$key] = $definition[2];
        }
        preg_match_all('~!\[([^\]\r\n]*)\](?!\()(?:\[([^\]\r\n]*)\])?~u', $visible, $referencedImages, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        foreach ($referencedImages as $image) {
            $label = ($image[2][0] ?? '') ?: $image[1][0];
            $key = mb_strtolower(trim(preg_replace('~\s+~u', ' ', $label)), 'UTF-8');
            if (isset($references[$key])) $images[] = [$image[0][1], $references[$key]];
        }
        usort($images, static function ($a, $b) { return $a[0] <=> $b[0]; });
        $cover = '';
        foreach ($images as $image) {
            $url = html_entity_decode(trim($image[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($url !== '' && $url[0] !== '#' && MixHyperlinks::validUrl($url)) {
                $cover = $url;
                break;
            }
        }

        if (!$withSummary) return ['cover' => $cover, 'summary' => ''];

        // Player options and shortcode attributes are configuration, not excerpts.
        $summary = preg_replace('~\[(hplayer|vplayer|player)\b[^\]]*\].*?\[/\1\]~isu', '', $visible);
        $summary = preg_replace('~!\[[^\]]*\]\([^)]*\)|!\[[^\]]*\]\[[^\]]*\]|^\s*\[[^\]]+\]:[^\n]*~imu', '', $summary);
        $summary = preg_replace('~\[([^\]]+)\]\([^)]*\)~u', '$1', $summary);
        $summary = preg_replace('~\[([^\]]+)\]\[[^\]]*\]~u', '$1', $summary);
        $summary = preg_replace('~\[(?:/?[a-z][\w-]*)(?:\s+[^\]]*)?\]~iu', '', $summary);
        $summary = preg_replace('~</?(?:p|div|li|h[1-6]|br|blockquote)\b[^>]*>~iu', ' ', $summary);
        $summary = strip_tags($summary);
        $summary = html_entity_decode($summary, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $summary = preg_replace('~^\s*(?:#{1,6}\s+|>\s*|[-*+]\s+|\d+\.\s+)|(?:\*\*|__|\~\~|`)|^\s*(?:[-*_]{3,}|={3,})\s*$~mu', '', $summary);
        $summary = trim(preg_replace('~[\s\x{00a0}]+~u', ' ', $summary));
        // Bound the response size; the card's CSS decides where visible text ends.
        $summary = mb_substr($summary, 0, 2048, 'UTF-8');
        return ['cover' => $cover, 'summary' => $summary];
    }
}

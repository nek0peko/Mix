<?php

/** Shared cover extraction and plain-text previews for post lists. */
class MixPostPreview
{
    public static function escapeText(string $text): string
    {
        return htmlspecialchars(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES, 'UTF-8');
    }

    public static function cover(string $source, bool $protected = false): string
    {
        return self::prepare($source, $protected, false)['cover'];
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

<?php

class MixSocialIcons
{
    public static function defaults(): array
    {
        $base = 'https://raw.githubusercontent.com/nek0peko/cdn-static/main/Mix/img/social/';
        return [
            ['icon' => $base . 'bilibili.svg', 'url' => ''],
            ['icon' => $base . 'github.svg', 'url' => ''],
            ['icon' => $base . 'pixiv.svg', 'url' => '']
        ];
    }

    public static function valid($value): bool
    {
        if (!is_array(json_decode((string) $value))) {
            return false;
        }
        $icons = json_decode((string) $value, true);
        foreach ($icons as $item) {
            if (!is_array($item) || !is_string($item['icon'] ?? null) || !is_string($item['url'] ?? null)
                || !MixHyperlinks::validUrl($item['icon']) || !MixHyperlinks::validUrl($item['url'])) {
                return false;
            }
        }
        return true;
    }

    public static function items($options): array
    {
        if ($options->SocialIcons !== null) {
            return self::valid($options->SocialIcons) ? json_decode($options->SocialIcons, true) : [];
        }
        // Preserve original links when restoring a backup made before the list editor.
        $items = self::defaults();
        foreach (['HeaderBiliBili', 'HeaderGitHub', 'HeaderPixiv'] as $number => $field) {
            $url = trim((string) $options->$field);
            if (MixHyperlinks::validUrl($url)) {
                $items[$number]['url'] = $url;
            }
        }
        return $items;
    }
}

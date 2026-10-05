<?php

class MixHyperlinks
{
    public static function defaults(): array
    {
        return [[
            'title' => '了解更多',
            'enabled' => true,
            'items' => [[
                'title' => '主题交流群',
                'url' => 'https://qm.qq.com/q/72LJzXIEcE',
                'description' => '欢迎加入 Mix 交流群喵~'
            ]]
        ]];
    }

    public static function validUrl(string $url): bool
    {
        $url = trim($url);
        if ($url === '') {
            return true;
        }
        if (preg_match('/[\x00-\x20]/', $url)) {
            return false;
        }
        if ($url[0] === '#' || ($url[0] === '/' && substr($url, 0, 2) !== '//')) {
            return true;
        }
        return preg_match('~^https?://~i', $url) && filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public static function valid($value): bool
    {
        if (!is_array(json_decode((string) $value))) {
            return false;
        }
        $modules = json_decode((string) $value, true);
        if (!is_array($modules) || ($modules && array_keys($modules) !== range(0, count($modules) - 1))) {
            return false;
        }
        foreach ($modules as $module) {
            if (!is_array($module) || !is_string($module['title'] ?? null) || (array_key_exists('enabled', $module) && !is_bool($module['enabled'])) || !is_array($module['items'] ?? null)
                || ($module['items'] && array_keys($module['items']) !== range(0, count($module['items']) - 1))) {
                return false;
            }
            foreach ($module['items'] as $item) {
                if (!is_array($item) || !is_string($item['title'] ?? null)
                    || !is_string($item['url'] ?? null) || !is_string($item['description'] ?? null)
                    || !self::validUrl($item['url'])) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function orderedModules($options): array
    {
        $modules = self::modules($options);
        if (mixFriendsVisible($options)) {
            $position = $options->FriendsModulePosition;
            $position = $position === null || (int) $position < 0 ? count($modules) : min((int) $position, count($modules));
            array_splice($modules, max(0, $position), 0, [['friends' => true]]);
        }
        return $modules;
    }

    public static function enabled($options): bool
    {
        foreach (self::modules($options) as $module) {
            if ($module['enabled'] && trim($module['title']) !== '') return true;
        }
        return false;
    }

    public static function modules($options): array
    {
        if ($options->HyperlinkModules !== null) {
            $modules = self::valid($options->HyperlinkModules) ? json_decode($options->HyperlinkModules, true) : [];
            $legacyEnabled = $options->HyperlinksEnabled === null || (is_array($options->HyperlinksEnabled) && in_array('enabled', $options->HyperlinksEnabled, true));
            foreach ($modules as &$module) {
                $module['enabled'] = $module['enabled'] ?? $legacyEnabled;
            }
            unset($module);
            return $modules;
        }
        // Older backups still contain the original four-card configuration.
        $legacy = json_decode((string) $options->MoreJSON, true);
        $items = [];
        if (is_array($legacy)) {
            for ($number = 1; $number <= 4; $number++) {
                $title = $legacy['Name' . $number] ?? '';
                $url = $legacy['Link' . $number] ?? '';
                $description = $legacy['More' . $number] ?? '';
                if (is_string($title) && is_string($url) && is_string($description)
                    && trim($title) !== '' && trim($url) !== '' && self::validUrl($url)) {
                    $items[] = ['title' => $title, 'url' => $url, 'description' => $description];
                }
            }
        }
        return $items ? [['title' => '了解更多', 'enabled' => in_array('ShowMore', mixEnabledComponents($options), true), 'items' => $items]] : self::defaults();
    }
}

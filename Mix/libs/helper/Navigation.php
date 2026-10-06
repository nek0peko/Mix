<?php

class MixNavigation
{
    public static function systems(): array
    {
        return ['home' => '主页', 'articles' => '文章', 'search' => '搜索', 'stats' => '统计', 'friends' => '友链'];
    }

    public static function defaults(): array
    {
        $items = [];
        foreach (self::systems() as $key => $name) $items[] = ['builtin' => $key, 'enabled' => true];
        $items[] = ['name' => '开往', 'link' => 'https://travellings.link/', 'class' => 'fas fa-subway', 'target' => '_blank', 'enabled' => true];
        $items[] = ['name' => '主题', 'link' => 'https://github.com/nek0peko/Mix', 'class' => 'fab fa-github', 'target' => '_blank', 'enabled' => true];
        return $items;
    }

    public static function configured($options): array
    {
        $raw = $options->headnavItems;
        $items = $raw === null ? self::defaults() : (self::valid($raw) ? self::decode($raw) : []);
        $seen = [];
        foreach ($items as &$item) {
            if (isset($item['builtin'])) $seen[$item['builtin']] = true;
            $item['enabled'] = $item['enabled'] ?? true;
        }
        unset($item);
        $missing = [];
        foreach (self::systems() as $key => $name) {
            if (isset($seen[$key])) continue;
            $enabled = $key !== 'search' || $options->NavSearchEnabled === null || (is_array($options->NavSearchEnabled) && in_array('enabled', $options->NavSearchEnabled, true));
            $missing[] = ['builtin' => $key, 'enabled' => $enabled];
        }
        return array_merge($missing, $items);
    }

    public static function enabled($options, string $key): bool
    {
        foreach (self::configured($options) as $item) if (($item['builtin'] ?? '') === $key) return $item['enabled'];
        return false;
    }

    public static function visible($options): array
    {
        return array_values(array_filter(self::configured($options), function ($item) use ($options) {
            if (!$item['enabled']) return false;
            if (($item['builtin'] ?? '') !== 'friends') return true;
            return Admin_Helper::isPluginAvailable('Links_Plugin', 'Links') && trim((string) $options->FriendURL) !== '' && MixHyperlinks::validUrl((string) $options->FriendURL);
        }));
    }

    public static function decode($value): ?array
    {
        $text = trim((string) $value);
        if ($text === '') return [];
        $text = rtrim($text, ", \t\r\n");
        $items = json_decode(substr($text, 0, 1) === '[' ? $text : '[' . $text . ']', true);
        return is_array($items) && array_values($items) === $items ? $items : null;
    }

    public static function valid($value): bool
    {
        $items = self::decode($value);
        if ($items === null) return false;
        $seen = [];
        foreach ($items as $item) {
            if (!self::validItem($item)) return false;
            if (isset($item['builtin'])) {
                if (isset($seen[$item['builtin']])) return false;
                $seen[$item['builtin']] = true;
            }
            if (isset($item['sub'])) {
                if (!is_array($item['sub']) || array_values($item['sub']) !== $item['sub']) return false;
                foreach ($item['sub'] as $child) if (!self::validItem($child)) return false;
            }
        }
        return true;
    }

    private static function validItem($item): bool
    {
        if (!is_array($item)) return false;
        if (isset($item['enabled']) && !is_bool($item['enabled'])) return false;
        if (isset($item['builtin'])) return is_string($item['builtin']) && array_key_exists($item['builtin'], self::systems());
        foreach (['name', 'link', 'class', 'target', 'status'] as $key) {
            if (isset($item[$key]) && !is_string($item[$key])) return false;
        }
        return MixHyperlinks::validUrl($item['link'] ?? '');
    }

    public static function items($value): array
    {
        if (!self::valid($value)) return [];
        return json_decode(json_encode(self::decode($value))) ?: [];
    }
}

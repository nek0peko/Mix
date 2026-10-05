<?php

class MixNavigation
{
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
        foreach ($items as $item) {
            if (!self::validItem($item)) return false;
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

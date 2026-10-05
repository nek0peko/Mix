<?php

/** A shared shuffled ring: repeats stay exactly one full deck apart. */
class MixRandomThumb
{
    public static function next(int $count = 22, string $scope = ''): int
    {
        $count = max(1, $count);
        $key = hash('sha256', __DIR__ . ':' . $scope . ':' . $count);
        $path = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . '/mix-thumbs-' . $key . '.json';
        $file = @fopen($path, 'c+');
        if ($file !== false) {
            $locked = false;
            try {
                // Keep a busy or unavailable cache from holding up page rendering.
                for ($attempt = 0; $attempt < 20; $attempt++) {
                    if (@flock($file, LOCK_EX | LOCK_NB)) {
                        $locked = true;
                        break;
                    }
                    usleep(1000);
                }
                if ($locked) {
                    $state = json_decode(stream_get_contents($file, 16384), true);
                    $expected = range(1, $count);
                    $order = is_array($state) && isset($state['order']) && is_array($state['order']) ? $state['order'] : [];
                    $sorted = $order;
                    sort($sorted);
                    if ($sorted !== $expected || !isset($state['cursor']) || !is_int($state['cursor']) || $state['cursor'] < 0 || $state['cursor'] >= $count) {
                        shuffle($expected);
                        $state = ['order' => $expected, 'cursor' => 0];
                    }
                    $number = $state['order'][$state['cursor']];
                    $state['cursor'] = ($state['cursor'] + 1) % $count;
                    $json = json_encode($state);
                    rewind($file);
                    if (@fwrite($file, $json) === strlen($json) && @ftruncate($file, strlen($json)) && @fflush($file)) {
                        return $number;
                    }
                }
            } finally {
                if ($locked) @flock($file, LOCK_UN);
                fclose($file);
            }
        }

        // Without a writable cache, still avoid repeats within the current request.
        static $decks = [];
        static $reported = false;
        if (!$reported) {
            error_log('[Mix] Random thumbnail cache unavailable; using a request-local image sequence');
            $reported = true;
        }
        if (!isset($decks[$key])) {
            $order = range(1, $count);
            shuffle($order);
            $decks[$key] = ['order' => $order, 'cursor' => 0];
        }
        $deck = &$decks[$key];
        $number = $deck['order'][$deck['cursor']];
        $deck['cursor'] = ($deck['cursor'] + 1) % $count;
        return $number;
    }
}

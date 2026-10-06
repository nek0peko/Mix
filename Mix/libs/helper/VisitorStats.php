<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

class MixVisitorStats
{
    public static function visitor(): string
    {
        $token = $_COOKIE['mix_like_browser'] ?? '';
        if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/D', $token)) {
            $token = bin2hex(random_bytes(32));
            $cookie = 'mix_like_browser=' . $token . '; Max-Age=31536000; Path=/; HttpOnly; SameSite=Lax';
            if (Typecho_Request::getInstance()->isSecure()) $cookie .= '; Secure';
            if (!headers_sent()) header('Set-Cookie: ' . $cookie, false);
            $_COOKIE['mix_like_browser'] = $token;
        }
        return hash('sha256', $token);
    }

    public static function table($db, string $name): string
    {
        $table = $db->getPrefix() . $name;
        if (!preg_match('/^[a-zA-Z0-9_]+$/D', $table)) throw new RuntimeException('Invalid table prefix');
        return $table;
    }

    public static function row($db, $query): ?array
    {
        // Read through the write connection, including inside transactions.
        return $db->fetchRow($db->query($query->prepare($query), Typecho_Db::WRITE));
    }

    public static function ensure($db): void
    {
        static $ready = [];
        $key = spl_object_hash($db);
        if (isset($ready[$key])) return;
        $schemas = [
            'mix_article' => '(cid INTEGER NOT NULL, visitor VARCHAR(64) NOT NULL, liked INTEGER NOT NULL DEFAULT 0, views BIGINT NOT NULL DEFAULT 0, first_seen BIGINT NOT NULL DEFAULT 0, last_seen BIGINT NOT NULL DEFAULT 0, PRIMARY KEY (cid, visitor))',
            'mix_visitors' => '(visitor VARCHAR(64) NOT NULL PRIMARY KEY, ip VARCHAR(45) NOT NULL, first_seen BIGINT NOT NULL, last_seen BIGINT NOT NULL)',
            'mix_visits' => '(visit VARCHAR(32) NOT NULL PRIMARY KEY, visitor VARCHAR(64) NOT NULL, cid INTEGER NOT NULL DEFAULT 0, path VARCHAR(1024) NOT NULL, ip VARCHAR(45) NOT NULL, visited_at BIGINT NOT NULL)'
        ];
        foreach ($schemas as $name => $schema) {
            try {
                self::row($db, $db->select()->from('table.' . $name)->limit(1));
                continue;
            } catch (Exception $error) {
                $table = self::table($db, $name);
                $suffix = stripos($db->getAdapterName(), 'mysql') !== false ? ' ENGINE=InnoDB' : '';
                $db->query('CREATE TABLE IF NOT EXISTS ' . $table . ' ' . $schema . $suffix, Typecho_Db::WRITE);
                $indexes = $name === 'mix_visits' ? ['visited_at', 'visitor', 'cid'] : ($name === 'mix_visitors' ? ['last_seen'] : []);
                foreach ($indexes as $column) {
                    try { $db->query('CREATE INDEX ' . $table . '_' . $column . ' ON ' . $table . ' (' . $column . ')', Typecho_Db::WRITE); }
                    catch (Exception $indexError) {
                        // Another request may have created the same index concurrently.
                        if (stripos($indexError->getMessage(), 'duplicate') === false && stripos($indexError->getMessage(), 'already exists') === false) throw $indexError;
                    }
                }
            }
        }
        // Idempotent import: retain the old table as a backup and keep canceled
        // votes in the new table from being resurrected on subsequent requests.
        $old = self::table($db, 'mix_article_likes');
        try { self::row($db, $db->select('cid')->from('table.mix_article_likes')->limit(1)); }
        catch (Exception $error) { $old = null; }
        if ($old !== null) {
            $new = self::table($db, 'mix_article');
            $insert = stripos($db->getAdapterName(), 'mysql') !== false ? 'INSERT IGNORE' : 'INSERT OR IGNORE';
            $db->query($insert . ' INTO ' . $new . ' (cid, visitor, liked) SELECT old.cid, old.voter, 1 FROM ' . $old . ' old LEFT JOIN ' . $new . ' current ON current.cid = old.cid AND current.visitor = old.voter WHERE current.cid IS NULL', Typecho_Db::WRITE);
        }
        $ready[$key] = true;
    }

    public static function ip(): string
    {
        // Do not trust client-supplied forwarding headers. Use the server's peer IP.
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        return filter_var($ip, FILTER_VALIDATE_IP) !== false ? $ip : '';
    }

    public static function touch($db, string $visitor, int $now): void
    {
        if (stripos($db->getAdapterName(), 'mysql') !== false) {
            $table = self::table($db, 'mix_visitors');
            $ip = self::ip();
            $db->query("INSERT INTO $table (visitor, ip, first_seen, last_seen) VALUES ('$visitor', '$ip', $now, $now) ON DUPLICATE KEY UPDATE ip = VALUES(ip), last_seen = GREATEST(last_seen, VALUES(last_seen))", Typecho_Db::WRITE);
            return;
        }
        $row = self::row($db, $db->select('visitor')->from('table.mix_visitors')->where('visitor = ?', $visitor));
        if (!$row) {
            try {
                $db->query($db->insert('table.mix_visitors')->rows(['visitor' => $visitor, 'ip' => self::ip(), 'first_seen' => $now, 'last_seen' => $now]));
                return;
            } catch (Exception $error) {
                if (!self::row($db, $db->select('visitor')->from('table.mix_visitors')->where('visitor = ?', $visitor))) throw $error;
            }
        }
        $db->query($db->update('table.mix_visitors')->rows(['ip' => self::ip(), 'last_seen' => $now])->where('visitor = ?', $visitor));
    }

    public static function views($db, int $cid): int
    {
        $row = self::row($db, $db->select('SUM(views) AS total')->from('table.mix_article')->where('cid = ?', $cid));
        return (int) ($row['total'] ?? 0);
    }

    public static function record($db, string $visitor, int $cid, string $path, string $visit): int
    {
        if ($cid < 0 || !preg_match('/^[a-f0-9]{64}$/D', $visitor) || !preg_match('/^[a-f0-9]{32}$/D', $visit)) throw new InvalidArgumentException('Invalid visit identity');
        self::ensure($db);
        $now = time();
        $db->query('BEGIN', Typecho_Db::WRITE);
        try {
            if (self::row($db, $db->select('visit')->from('table.mix_visits')->where('visit = ?', $visit))) {
                $db->query('COMMIT', Typecho_Db::WRITE);
                return self::views($db, $cid);
            }
            $db->query($db->insert('table.mix_visits')->rows(['visit' => $visit, 'visitor' => $visitor, 'cid' => $cid, 'path' => $path, 'ip' => self::ip(), 'visited_at' => $now]));
            self::touch($db, $visitor, $now);
            if ($cid > 0) {
                $table = self::table($db, 'mix_article');
                if (stripos($db->getAdapterName(), 'mysql') !== false) {
                    $db->query("INSERT INTO $table (cid, visitor, views, first_seen, last_seen) VALUES ($cid, '$visitor', 1, $now, $now) ON DUPLICATE KEY UPDATE views = views + 1, first_seen = CASE WHEN first_seen = 0 THEN $now ELSE first_seen END, last_seen = GREATEST(last_seen, $now)", Typecho_Db::WRITE);
                } else {
                    $row = self::row($db, $db->select('cid')->from('table.mix_article')->where('cid = ?', $cid)->where('visitor = ?', $visitor));
                    if (!$row) $db->query($db->insert('table.mix_article')->rows(['cid' => $cid, 'visitor' => $visitor]));
                    $db->query("UPDATE $table SET views = views + 1, first_seen = CASE WHEN first_seen = 0 THEN $now ELSE first_seen END, last_seen = $now WHERE cid = $cid AND visitor = '$visitor'", Typecho_Db::WRITE);
                }
            }
            $db->query('COMMIT', Typecho_Db::WRITE);
        } catch (Throwable $error) {
            $db->query('ROLLBACK', Typecho_Db::WRITE);
            // Retried concurrent delivery of one visit does not count twice.
            if (!self::row($db, $db->select('visit')->from('table.mix_visits')->where('visit = ?', $visit))) throw $error;
        }
        return self::views($db, $cid);
    }

    public static function online(bool $heartbeat = false): ?int
    {
        try {
            $db = Typecho_Db::get();
            self::ensure($db);
            $now = time();
            if ($heartbeat) self::touch($db, self::visitor(), $now);
            $row = self::row($db, $db->select('COUNT(*) AS total')->from('table.mix_visitors')->where('last_seen >= ?', $now - 90));
            return (int) $row['total'];
        } catch (Throwable $error) {
            error_log('[Mix] Visitor statistics unavailable: ' . $error->getMessage());
            return null;
        }
    }

    public static function today(int $offset, ?int $now = null): ?int
    {
        try {
            $db = Typecho_Db::get();
            self::ensure($db);
            $now = $now ?? time();
            $start = intdiv($now + $offset, 86400) * 86400 - $offset;
            $row = self::row($db, $db->select('COUNT(DISTINCT visitor) AS total')->from('table.mix_visits')
                ->where('visited_at >= ?', $start)->where('visited_at < ?', $start + 86400));
            return (int) $row['total'];
        } catch (Throwable $error) {
            error_log('[Mix] Daily visitors unavailable: ' . $error->getMessage());
            return null;
        }
    }

    public static function summary($db, int $offset, ?int $now = null): array
    {
        $now = $now ?? time();
        $day = intdiv($now + $offset, 86400);
        $start = ($day - 6) * 86400 - $offset;
        $end = ($day + 1) * 86400 - $offset;
        $table = self::table($db, 'mix_visits');
        $bucket = stripos($db->getAdapterName(), 'mysql') !== false
            ? "FLOOR((visited_at + $offset) / 86400)"
            : "CAST((visited_at + $offset) / 86400 AS INTEGER)";
        $rows = $db->fetchAll($db->query("SELECT $bucket AS day, COUNT(DISTINCT visitor) AS uv, COUNT(*) AS pv FROM $table WHERE visited_at >= $start AND visited_at < $end GROUP BY $bucket", Typecho_Db::WRITE));
        $indexed = [];
        foreach ($rows as $row) $indexed[(int) $row['day']] = $row;
        $days = [];
        for ($current = $day - 6; $current <= $day; $current++) {
            $row = $indexed[$current] ?? [];
            $days[] = ['date' => gmdate('Y-m-d', $current * 86400), 'uv' => (int) ($row['uv'] ?? 0), 'pv' => (int) ($row['pv'] ?? 0)];
        }
        return ['today' => $days[6], 'days' => $days];
    }

    public static function handleSummary(): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store');
        header('X-Content-Type-Options: nosniff');
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') self::respond(405, ['ok' => false]);
        try {
            $db = Typecho_Db::get();
            self::ensure($db);
            self::respond(200, ['ok' => true] + self::summary($db, (int) Helper::options()->timezone));
        } catch (Throwable $error) {
            error_log('[Mix] Statistics summary unavailable: ' . $error->getMessage());
            self::respond(503, ['ok' => false]);
        }
    }

    public static function handle(): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store');
        header('X-Content-Type-Options: nosniff');
        try {
            if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') self::respond(405, ['ok' => false]);
            if (($_SERVER['HTTP_X_MIX_VISIT'] ?? '') !== '1') self::respond(403, ['ok' => false]);
            $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
            if ($origin !== '') {
                $source = parse_url($origin); $site = parse_url(Helper::options()->siteUrl);
                if (!$source || strtolower($source['host'] ?? '') !== strtolower($site['host'] ?? '') || ($source['scheme'] ?? '') !== ($site['scheme'] ?? '') || ($source['port'] ?? null) !== ($site['port'] ?? null)) self::respond(403, ['ok' => false]);
            }
            $body = file_get_contents('php://input', false, null, 0, 4097);
            $input = strlen($body) <= 4096 ? json_decode($body, true) : null;
            $cid = is_array($input) ? filter_var($input['cid'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) : false;
            if ($cid === false || !is_string($input['visit'] ?? null) || !preg_match('/^[a-f0-9]{32}$/D', $input['visit']) || !is_string($input['path'] ?? null) || strlen($input['path']) > 1024 || !preg_match('~^/(?!/)[^\x00-\x20?#]*$~D', $input['path'])) self::respond(400, ['ok' => false]);
            $db = Typecho_Db::get();
            if ($cid > 0) {
                $post = self::row($db, $db->select('type', 'status', 'authorId')->from('table.contents')->where('cid = ?', $cid));
                if (!$post || !in_array($post['type'], ['post', 'page'], true)) self::respond(404, ['ok' => false]);
                if ($post['status'] !== 'publish' && !($post['type'] === 'page' && $post['status'] === 'hidden')) {
                    $user = Typecho_Widget::widget('Widget_User');
                    if ($post['status'] !== 'private' || !$user->hasLogin() || ((int) $user->uid !== (int) $post['authorId'] && !$user->pass('editor', true))) self::respond(404, ['ok' => false]);
                }
            }
            self::ensure($db);
            $views = self::record($db, self::visitor(), (int) $cid, $input['path'], $input['visit']);
            self::respond(200, ['ok' => true, 'views' => $views, 'recorded' => true, 'today_uv' => self::today((int) Helper::options()->timezone)]);
        } catch (Throwable $error) {
            error_log('[Mix] Visitor statistics unavailable: ' . $error->getMessage());
            self::respond(503, ['ok' => false]);
        }
    }

    private static function respond(int $status, array $data): void
    {
        Typecho_Response::getInstance()->setStatus($status);
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}

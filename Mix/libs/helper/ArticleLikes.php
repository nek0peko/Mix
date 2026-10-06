<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

class MixArticleLikes
{
    public static function handle()
    {
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store');
        header('X-Content-Type-Options: nosniff');
        try {
            $method = $_SERVER['REQUEST_METHOD'] ?? '';
            if (!in_array($method, ['GET', 'POST'], true)) {
                header('Allow: GET, POST');
                self::respond(405);
            }
            // A custom header prevents cross-origin forms from changing votes.
            if (($_SERVER['HTTP_X_MIX_LIKE'] ?? '') !== '1') self::respond(403);
            $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
            $site = parse_url(Helper::options()->siteUrl);
            if ($origin !== '') {
                $source = parse_url($origin);
                if (!$source || strtolower($source['host'] ?? '') !== strtolower($site['host'] ?? '')
                    || ($source['scheme'] ?? '') !== ($site['scheme'] ?? '')
                    || ($source['port'] ?? null) !== ($site['port'] ?? null)) self::respond(403);
            }
            $cid = filter_var($_GET['cid'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if (!$cid) self::respond(400);
            $db = Typecho_Db::get();
            $post = MixVisitorStats::row($db, $db->select('cid', 'type', 'status', 'authorId')->from('table.contents')->where('cid = ?', $cid));
            if (!$post || !in_array($post['type'], ['post', 'page'], true)) self::respond(404);
            if ($post['status'] !== 'publish' && !($post['type'] === 'page' && $post['status'] === 'hidden')) {
                $user = Typecho_Widget::widget('Widget_User');
                if ($post['status'] !== 'private' || !$user->hasLogin()
                    || ((int) $user->uid !== (int) $post['authorId'] && !$user->pass('editor', true))) self::respond(404);
            }
            $desired = null;
            if ($method === 'POST') {
                $body = file_get_contents('php://input', false, null, 0, 1025);
                $input = strlen($body) <= 1024 ? json_decode($body, true) : null;
                if (!is_array($input) || !isset($input['liked']) || !is_bool($input['liked'])) self::respond(400);
                $desired = $input['liked'];
            }
            $voter = MixVisitorStats::visitor();
            MixVisitorStats::ensure($db);
            $existing = MixVisitorStats::row($db, $db->select('liked')->from('table.mix_article')->where('cid = ?', $cid)->where('visitor = ?', $voter));
            if ($desired !== null) {
                if (!$existing) {
                    try {
                        $db->query($db->insert('table.mix_article')->rows(['cid' => $cid, 'visitor' => $voter, 'liked' => (int) $desired]));
                    } catch (Exception $error) {
                        if (!MixVisitorStats::row($db, $db->select('cid')->from('table.mix_article')->where('cid = ?', $cid)->where('visitor = ?', $voter))) throw $error;
                    }
                }
                // Canceling a like preserves this visitor's browsing history.
                $db->query($db->update('table.mix_article')->rows(['liked' => (int) $desired])->where('cid = ?', $cid)->where('visitor = ?', $voter));
            }
            $existing = MixVisitorStats::row($db, $db->select('liked')->from('table.mix_article')->where('cid = ?', $cid)->where('visitor = ?', $voter));
            $liked = $existing && (bool) $existing['liked'];
            $count = MixVisitorStats::row($db, $db->select('SUM(liked) AS total')->from('table.mix_article')->where('cid = ?', $cid));
            echo json_encode(['ok' => true, 'liked' => $liked, 'count' => (int) ($count['total'] ?? 0), 'views' => MixVisitorStats::views($db, $cid)]);
            exit;
        } catch (Throwable $error) {
            error_log('[Mix] Article likes unavailable: ' . $error->getMessage());
            self::respond(503);
        }
    }

    private static function respond($status)
    {
        Typecho_Response::getInstance()->setStatus($status);
        http_response_code($status);
        echo json_encode(['ok' => false]);
        exit;
    }
}

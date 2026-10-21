<?php
// Password gate for private collections (favs/Private*.json).
//
// The password lives only on the server, in .env:
//   PRIVATE_PLAYLISTS_PASSWORD=your-password
// It is compared with hash_equals (constant time) and never sent to the browser.
//
// After a correct password, a PHP session marks the browser as unlocked.
// Private JSON is only served through private-favs.php while unlocked.

if (!defined('YT_PRIVATE_AUTH')) {
    define('YT_PRIVATE_AUTH', true);
    define('YT_APP_DIR', dirname(__DIR__));
    define('YT_FAVS_DIR', YT_APP_DIR . '/favs');
    define('YT_PRIVATE_SESSION_NAME', 'YTPRIVATESESS');
    define('YT_PRIVATE_UNLOCK_SECONDS', 12 * 60 * 60);
    define('YT_PRIVATE_MAX_FAILURES', 5);
    define('YT_PRIVATE_WINDOW_SECONDS', 15 * 60);
    define('YT_PRIVATE_LOCKOUT_SECONDS', 15 * 60);
}

function yt_private_load_env() {
    static $vars = null;
    if ($vars !== null) {
        return $vars;
    }
    $vars = [];
    $path = YT_APP_DIR . '/.env';
    if (!is_readable($path)) {
        return $vars;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return $vars;
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        $eqPos = strpos($line, '=');
        if ($eqPos === false) {
            continue;
        }
        $name = trim(substr($line, 0, $eqPos));
        $value = trim(substr($line, $eqPos + 1));
        if ($name === '') {
            continue;
        }
        if (strlen($value) >= 2 && (
            ($value[0] === '"' && substr($value, -1) === '"') ||
            ($value[0] === "'" && substr($value, -1) === "'")
        )) {
            $value = substr($value, 1, -1);
        }
        $vars[$name] = $value;
    }
    return $vars;
}

function yt_private_env($name) {
    $env = yt_private_load_env();
    if (isset($env[$name]) && trim((string) $env[$name]) !== '') {
        return trim((string) $env[$name]);
    }
    $value = getenv($name);
    if ($value !== false && trim((string) $value) !== '') {
        return trim((string) $value);
    }
    return '';
}

function yt_private_is_configured() {
    return yt_private_env('PRIVATE_PLAYLISTS_PASSWORD') !== '';
}

function yt_private_check_password($password) {
    $password = (string) $password;
    $expected = yt_private_env('PRIVATE_PLAYLISTS_PASSWORD');
    if ($expected === '') {
        return false;
    }
    return hash_equals($expected, $password);
}

function yt_private_is_https() {
    if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    return isset($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443';
}

function yt_private_cookie_path() {
    $dir = str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/')));
    return ($dir === '' || $dir === '.') ? '/' : rtrim($dir, '/') . '/';
}

// Starts the session. With $onlyIfCookie, no session (and no cookie) is created
// for visitors who have never unlocked.
function yt_private_start_session($onlyIfCookie = false) {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return true;
    }
    if ($onlyIfCookie && empty($_COOKIE[YT_PRIVATE_SESSION_NAME])) {
        return false;
    }
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name(YT_PRIVATE_SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => yt_private_cookie_path(),
        'secure' => yt_private_is_https(),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    return session_start();
}

function yt_private_is_unlocked() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return false;
    }
    $at = isset($_SESSION['yt_private_unlocked_at']) ? (int) $_SESSION['yt_private_unlocked_at'] : 0;
    if ($at <= 0) {
        return false;
    }
    if (time() - $at > YT_PRIVATE_UNLOCK_SECONDS) {
        unset($_SESSION['yt_private_unlocked_at']);
        return false;
    }
    return true;
}

function yt_private_unlock() {
    session_regenerate_id(true);
    $_SESSION['yt_private_unlocked_at'] = time();
}

function yt_private_lock() {
    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => $params['path'],
            'secure' => $params['secure'],
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_destroy();
    }
}

// Private collection names must start with "Private" (the same files .gitignore keeps out of git).
function yt_private_is_private_name($name) {
    return is_string($name) && strpos($name, 'Private') === 0;
}

// Returns the absolute path of a private collection, or null if the name is invalid or missing.
function yt_private_collection_path($name) {
    if (!yt_private_is_private_name($name) || strlen($name) > 200) {
        return null;
    }
    if (preg_match('/[\/\\\\\x00-\x1f]/', $name) || strpos($name, '..') !== false) {
        return null;
    }
    $path = YT_FAVS_DIR . '/' . $name . '.json';
    $real = realpath($path);
    $favs = realpath(YT_FAVS_DIR);
    if ($real === false || $favs === false || dirname($real) !== $favs || !is_file($real)) {
        return null;
    }
    return $real;
}

// Placeholder label for a locked collection: fixed-length random letters, so it
// reveals nothing about the real name (not even its length).
function yt_private_scramble_label() {
    $letters = 'abcdefghijklmnopqrstuvwxyz';
    $out = '';
    for ($i = 0; $i < 14; $i++) {
        if ($i === 5) {
            $out .= ' ';
        }
        $out .= $letters[random_int(0, 25)];
    }
    return ucfirst($out);
}

// ---- Brute-force throttling (per client IP, stored outside the web root) ----

function yt_private_throttle_file() {
    return rtrim(sys_get_temp_dir(), '/\\') . '/yt-private-throttle-' . sha1(YT_APP_DIR) . '.json';
}

function yt_private_client_key() {
    $ip = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : 'unknown';
    return hash('sha256', $ip);
}

// Runs $fn on the throttle table under an exclusive lock and saves the result.
function yt_private_with_throttle($fn) {
    $fp = @fopen(yt_private_throttle_file(), 'c+');
    if ($fp === false) {
        return $fn([])[1];
    }
    flock($fp, LOCK_EX);
    $raw = stream_get_contents($fp);
    $table = json_decode($raw ?: '[]', true);
    if (!is_array($table)) {
        $table = [];
    }
    $now = time();
    foreach ($table as $k => $row) {
        $last = max((int) ($row['first'] ?? 0) + YT_PRIVATE_WINDOW_SECONDS, (int) ($row['lockedUntil'] ?? 0));
        if ($last < $now) {
            unset($table[$k]);
        }
    }
    list($table, $result) = $fn($table);
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($table));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    return $result;
}

// Seconds the client must wait before trying again (0 = allowed).
function yt_private_lockout_remaining() {
    $key = yt_private_client_key();
    return yt_private_with_throttle(function ($table) use ($key) {
        $until = isset($table[$key]['lockedUntil']) ? (int) $table[$key]['lockedUntil'] : 0;
        return [$table, max(0, $until - time())];
    });
}

function yt_private_record_failure() {
    $key = yt_private_client_key();
    yt_private_with_throttle(function ($table) use ($key) {
        $now = time();
        $row = $table[$key] ?? ['first' => $now, 'fails' => 0, 'lockedUntil' => 0];
        if ($now - (int) $row['first'] > YT_PRIVATE_WINDOW_SECONDS) {
            $row = ['first' => $now, 'fails' => 0, 'lockedUntil' => 0];
        }
        $row['fails'] = (int) $row['fails'] + 1;
        if ($row['fails'] >= YT_PRIVATE_MAX_FAILURES) {
            $row['lockedUntil'] = $now + YT_PRIVATE_LOCKOUT_SECONDS;
        }
        $table[$key] = $row;
        return [$table, null];
    });
}

function yt_private_clear_failures() {
    $key = yt_private_client_key();
    yt_private_with_throttle(function ($table) use ($key) {
        unset($table[$key]);
        return [$table, null];
    });
}

<?php
// Endpoint for private collections (favs/Private*.json).
//   GET  ?action=status                 -> {"configured":bool,"unlocked":bool}
//   POST ?action=login   password=...   -> 200 unlocked, 401 wrong, 429 locked out, 503 not configured
//   POST ?action=logout                 -> 200
//   GET  ?action=collection&favs=NAME   -> the collection JSON, or 401 when locked
require __DIR__ . '/lib/private-auth.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');

function yt_private_respond($status, $data) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_SLASHES);
    exit;
}

$method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper((string) $_SERVER['REQUEST_METHOD']) : 'GET';
$action = isset($_GET['action']) ? (string) $_GET['action'] : '';

switch ($action) {
    case 'status':
        yt_private_start_session(true);
        $unlocked = yt_private_is_unlocked();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        yt_private_respond(200, ['configured' => yt_private_is_configured(), 'unlocked' => $unlocked]);

    case 'login':
        if ($method !== 'POST') {
            header('Allow: POST');
            yt_private_respond(405, ['error' => 'Use POST.']);
        }
        if (!yt_private_is_configured()) {
            yt_private_respond(503, ['error' => 'Private collections are not set up. Set PRIVATE_PLAYLISTS_PASSWORD_HASH in .env.']);
        }
        $wait = yt_private_lockout_remaining();
        if ($wait > 0) {
            header('Retry-After: ' . $wait);
            yt_private_respond(429, ['error' => 'Too many wrong passwords. Try again later.', 'retryAfter' => $wait]);
        }
        $password = isset($_POST['password']) ? (string) $_POST['password'] : '';
        if ($password !== '' && strlen($password) <= 1024 && yt_private_check_password($password)) {
            yt_private_clear_failures();
            yt_private_start_session();
            yt_private_unlock();
            session_write_close();
            yt_private_respond(200, ['unlocked' => true]);
        }
        yt_private_record_failure();
        sleep(1); // slow down guessing
        $wait = yt_private_lockout_remaining();
        if ($wait > 0) {
            header('Retry-After: ' . $wait);
            yt_private_respond(429, ['error' => 'Too many wrong passwords. Try again later.', 'retryAfter' => $wait]);
        }
        yt_private_respond(401, ['error' => 'Wrong password.']);

    case 'logout':
        if ($method !== 'POST') {
            header('Allow: POST');
            yt_private_respond(405, ['error' => 'Use POST.']);
        }
        if (yt_private_start_session(true)) {
            yt_private_lock();
        }
        yt_private_respond(200, ['unlocked' => false]);

    case 'collection':
        yt_private_start_session(true);
        $unlocked = yt_private_is_unlocked();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        if (!$unlocked) {
            yt_private_respond(401, ['error' => 'Locked. Enter the password to view private collections.']);
        }
        $name = isset($_GET['favs']) ? (string) $_GET['favs'] : '';
        $path = yt_private_collection_path($name);
        if ($path === null) {
            yt_private_respond(404, ['error' => 'Collection not found.']);
        }
        $json = file_get_contents($path);
        if ($json === false || json_decode($json) === null) {
            yt_private_respond(500, ['error' => 'Collection could not be read.']);
        }
        http_response_code(200);
        echo $json;
        exit;

    default:
        yt_private_respond(400, ['error' => 'Unknown action.']);
}

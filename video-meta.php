<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: private, max-age=300');

function loadEnvFile($path) {
    if (!is_readable($path)) {
        return [];
    }

    $vars = [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return [];
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

        if (
            strlen($value) >= 2 &&
            (
                ($value[0] === '"' && substr($value, -1) === '"') ||
                ($value[0] === "'" && substr($value, -1) === "'")
            )
        ) {
            $value = substr($value, 1, -1);
        }

        $vars[$name] = $value;
    }

    return $vars;
}

function getYoutubeApiKey() {
    $env = loadEnvFile(__DIR__ . '/.env');
    $apiKey = '';

    if (isset($env['YOUTUBE_API_KEY'])) {
        $apiKey = trim((string) $env['YOUTUBE_API_KEY']);
    } elseif (getenv('YOUTUBE_API_KEY') !== false) {
        $apiKey = trim((string) getenv('YOUTUBE_API_KEY'));
    }

    if ($apiKey === '' || $apiKey === 'YOUR_API_KEY_HERE') {
        return '';
    }

    return $apiKey;
}

$apiKey = getYoutubeApiKey();

if ($apiKey === '') {
    echo json_encode([
        'enabled' => false,
    ]);
    exit;
}

$videoId = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
if ($videoId === '') {
    echo json_encode([
        'enabled' => true,
    ]);
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_-]{11}$/', $videoId)) {
    http_response_code(400);
    echo json_encode([
        'enabled' => true,
        'error' => 'invalid_video_id',
    ]);
    exit;
}

$query = http_build_query([
    'part' => 'snippet',
    'id' => $videoId,
    'key' => $apiKey,
]);
$url = 'https://www.googleapis.com/youtube/v3/videos?' . $query;

// HTTP-referrer–restricted API keys reject empty referers from server-side calls.
$referer = '';
if (!empty($_SERVER['HTTP_REFERER'])) {
    $referer = (string) $_SERVER['HTTP_REFERER'];
} elseif (!empty($_SERVER['HTTP_HOST'])) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $referer = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/';
}

$raw = false;
if (function_exists('curl_init')) {
    $headers = [];
    if ($referer !== '') {
        $headers[] = 'Referer: ' . $referer;
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 8,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    $raw = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($raw === false || $httpCode < 200 || $httpCode >= 300) {
        http_response_code(502);
        echo json_encode([
            'enabled' => true,
            'error' => 'upstream_failed',
        ]);
        exit;
    }
} else {
    $httpHeader = "User-Agent: yt-shuffler\r\n";
    if ($referer !== '') {
        $httpHeader .= 'Referer: ' . $referer . "\r\n";
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 8,
            'ignore_errors' => true,
            'header' => $httpHeader,
        ],
    ]);
    $raw = @file_get_contents($url, false, $context);
    if ($raw === false) {
        http_response_code(502);
        echo json_encode([
            'enabled' => true,
            'error' => 'upstream_failed',
        ]);
        exit;
    }
}

$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(502);
    echo json_encode([
        'enabled' => true,
        'error' => 'invalid_upstream_response',
    ]);
    exit;
}

if (isset($data['error'])) {
    http_response_code(502);
    echo json_encode([
        'enabled' => true,
        'error' => 'api_error',
    ]);
    exit;
}

$snippet = [];
if (isset($data['items'][0]['snippet']) && is_array($data['items'][0]['snippet'])) {
    $snippet = $data['items'][0]['snippet'];
}

$publishedAt = null;
if (isset($snippet['publishedAt']) && is_string($snippet['publishedAt'])) {
    $publishedAt = $snippet['publishedAt'];
}

$title = null;
if (isset($snippet['title']) && is_string($snippet['title'])) {
    $title = $snippet['title'];
}

$channelTitle = null;
if (isset($snippet['channelTitle']) && is_string($snippet['channelTitle'])) {
    $channelTitle = $snippet['channelTitle'];
}

echo json_encode([
    'enabled' => true,
    'videoId' => $videoId,
    'publishedAt' => $publishedAt,
    'title' => $title,
    'channelTitle' => $channelTitle,
]);

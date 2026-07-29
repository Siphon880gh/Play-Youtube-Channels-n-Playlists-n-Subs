<?php
// Optional YouTube Data API helper for player UI extras (YOUTUBE_API_KEY in .env):
//   - ?id=VIDEO_ID  → upload date / title / channel for the current video
//   - ?ids=id1,id2  → batch titles for the playlist list (chunks of up to 50)
// Missing or invalid key returns JSON that lets the client degrade gracefully
// (hide upload date; playlist list keeps working with "Video N" placeholders).
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

function getRequestReferer() {
    // HTTP-referrer–restricted API keys reject empty referers from server-side calls.
    if (!empty($_SERVER['HTTP_REFERER'])) {
        return (string) $_SERVER['HTTP_REFERER'];
    }

    if (!empty($_SERVER['HTTP_HOST'])) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        return $scheme . '://' . $_SERVER['HTTP_HOST'] . '/';
    }

    return '';
}

function isValidYoutubeVideoId($videoId) {
    return is_string($videoId) && preg_match('/^[a-zA-Z0-9_-]{11}$/', $videoId) === 1;
}

function fetchYoutubeVideosSnippet($apiKey, array $videoIds) {
    if ($apiKey === '' || count($videoIds) === 0) {
        return [
            'ok' => false,
            'error' => 'invalid_request',
            'items' => [],
        ];
    }

    $query = http_build_query([
        'part' => 'snippet',
        'id' => implode(',', $videoIds),
        'key' => $apiKey,
    ]);
    $url = 'https://www.googleapis.com/youtube/v3/videos?' . $query;
    $referer = getRequestReferer();

    $raw = false;
    $httpCode = 0;

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
            return [
                'ok' => false,
                'error' => 'upstream_failed',
                'items' => [],
            ];
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
            return [
                'ok' => false,
                'error' => 'upstream_failed',
                'items' => [],
            ];
        }
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return [
            'ok' => false,
            'error' => 'invalid_upstream_response',
            'items' => [],
        ];
    }

    if (isset($data['error'])) {
        return [
            'ok' => false,
            'error' => 'api_error',
            'items' => [],
        ];
    }

    $items = [];
    if (isset($data['items']) && is_array($data['items'])) {
        $items = $data['items'];
    }

    return [
        'ok' => true,
        'error' => null,
        'items' => $items,
    ];
}

function snippetFieldsFromItem($item) {
    $snippet = [];
    if (isset($item['snippet']) && is_array($item['snippet'])) {
        $snippet = $item['snippet'];
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

    return [
        'publishedAt' => $publishedAt,
        'title' => $title,
        'channelTitle' => $channelTitle,
    ];
}

function parseRequestedVideoIds() {
    $ids = [];

    if (isset($_GET['ids'])) {
        $rawIds = trim((string) $_GET['ids']);
        if ($rawIds !== '') {
            foreach (explode(',', $rawIds) as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $ids[] = $part;
                }
            }
        }
    }

    if (isset($_GET['id'])) {
        $singleId = trim((string) $_GET['id']);
        if ($singleId !== '') {
            $ids[] = $singleId;
        }
    }

    $unique = [];
    foreach ($ids as $id) {
        if (!isset($unique[$id])) {
            $unique[$id] = true;
        }
    }

    return array_keys($unique);
}

$apiKey = getYoutubeApiKey();

if ($apiKey === '') {
    echo json_encode([
        'enabled' => false,
    ]);
    exit;
}

$videoIds = parseRequestedVideoIds();
if (count($videoIds) === 0) {
    echo json_encode([
        'enabled' => true,
    ]);
    exit;
}

foreach ($videoIds as $videoId) {
    if (!isValidYoutubeVideoId($videoId)) {
        http_response_code(400);
        echo json_encode([
            'enabled' => true,
            'error' => 'invalid_video_id',
        ]);
        exit;
    }
}

// Keep the single-id response shape for the upload-date path.
$isBatch = isset($_GET['ids']) || count($videoIds) > 1;
$videosById = [];
$chunks = array_chunk($videoIds, 50);

foreach ($chunks as $chunk) {
    $result = fetchYoutubeVideosSnippet($apiKey, $chunk);
    if (!$result['ok']) {
        http_response_code(502);
        echo json_encode([
            'enabled' => true,
            'error' => $result['error'],
        ]);
        exit;
    }

    foreach ($result['items'] as $item) {
        if (!isset($item['id']) || !is_string($item['id'])) {
            continue;
        }

        $fields = snippetFieldsFromItem($item);
        $videosById[$item['id']] = [
            'videoId' => $item['id'],
            'publishedAt' => $fields['publishedAt'],
            'title' => $fields['title'],
            'channelTitle' => $fields['channelTitle'],
        ];
    }
}

if (!$isBatch) {
    $videoId = $videoIds[0];
    $meta = isset($videosById[$videoId]) ? $videosById[$videoId] : [
        'videoId' => $videoId,
        'publishedAt' => null,
        'title' => null,
        'channelTitle' => null,
    ];

    echo json_encode([
        'enabled' => true,
        'videoId' => $meta['videoId'],
        'publishedAt' => $meta['publishedAt'],
        'title' => $meta['title'],
        'channelTitle' => $meta['channelTitle'],
    ]);
    exit;
}

$videos = [];
foreach ($videoIds as $videoId) {
    if (isset($videosById[$videoId])) {
        $videos[] = $videosById[$videoId];
    } else {
        $videos[] = [
            'videoId' => $videoId,
            'publishedAt' => null,
            'title' => null,
            'channelTitle' => null,
        ];
    }
}

echo json_encode([
    'enabled' => true,
    'videos' => $videos,
]);

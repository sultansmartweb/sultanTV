<?php
$Live = isset($_GET['ID']) ? intval($_GET['ID']) : 0;
$ts   = isset($_GET['ts']) ? $_GET['ts'] : null;

// .ts proxy bölümü
if ($ts) {
    $tsUrl = base64_decode($ts);
    if (!filter_var($tsUrl, FILTER_VALIDATE_URL)) {
        http_response_code(400);
        echo "Geçersiz TS bağlantısı.";
        exit;
    }

    $ch = curl_init($tsUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT      => 'iTunes-AppleTV/15.0',
        CURLOPT_HTTPHEADER     => ['Referer: https://www.giniko.com/'],
        CURLOPT_BINARYTRANSFER => true,
    ]);
    $segmentData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $segmentData) {
        header("Content-Type: video/MP2T");
        header("Content-Length: " . strlen($segmentData));
        echo $segmentData;
    } else {
        http_response_code(404);
        echo "❌ Segment alınamadı.";
    }
    exit;
}

// Adım 1: XML’den yayın URL’sini al
$xmlUrl = 'https://www.giniko.com/xml/secure/plist.php?ch=' . $Live;
$ch = curl_init($xmlUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT      => 'iTunes-AppleTV/15.0',
]);
$response = curl_exec($ch);
curl_close($ch);

// XML içinden yayın linkini al
preg_match('#<key>HlsStreamURL</key>\s*<string>(https?://[^<]+\.m3u8.*?)</string>#is', $response, $matches);
if (!isset($matches[1])) {
    http_response_code(404);
    echo "❌ Yayın URL'si bulunamadı.";
    exit;
}
$mainM3u8 = trim($matches[1]);

// Adım 2: master m3u8 dosyasını indir
$masterContent = file_get_contents($mainM3u8);
if (!$masterContent) {
    http_response_code(404);
    echo "❌ Master playlist alınamadı.";
    exit;
}

// Adım 3: içinden alt kaliteye giden m3u8 (örneğin mono.m3u8) linkini bul
preg_match('/^([^\n]+\.m3u8\?.+)$/mi', $masterContent, $streamMatch);
if (!isset($streamMatch[1])) {
    http_response_code(404);
    echo "❌ Alt yayın playlisti bulunamadı.";
    exit;
}
$subPlaylistPath = trim($streamMatch[1]);
$basePath = dirname($mainM3u8);
$subPlaylistUrl = $basePath . '/' . $subPlaylistPath;

// Adım 4: alt playlisti indir
$subContent = file_get_contents($subPlaylistUrl);
if (!$subContent) {
    http_response_code(404);
    echo "❌ Alt playlist alınamadı.";
    exit;
}

// Adım 5: .ts linklerini kendi scriptine yönlendir
$proxied = preg_replace_callback('/^(.*\.ts.*)$/m', function ($match) use ($subPlaylistUrl) {
    $base = dirname($subPlaylistUrl);
    $full = $base . '/' . ltrim($match[1], '/');
    return 'Giniko.php?ts=' . urlencode(base64_encode($full));
}, $subContent);

// Çıktı
header("Content-Type: application/vnd.apple.mpegurl");
echo $proxied;
?>
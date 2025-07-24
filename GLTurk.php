<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

function getGliptvStreamUrl($Live) {
    // APIKey ve APIPassword alma
    $ch = curl_init('https://api.gliptv.com/auth.aspx');
    curl_setopt_array($ch, [
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'username' => 'GLIPTVadministrator',
            'password' => 'GLWiz2009SNMP',
            'action' => 'maincheck',
            'appVersion' => '5.0'
        ]),
        CURLOPT_HTTPHEADER => [
            'User-Agent: okhttp/4.12.0',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
        ]
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) return null;

    preg_match('#"APIKey":"(.*?)"#', $response, $m1);
    preg_match('#"APIPassword":"(.*?)"#', $response, $m2);
    $APIKey = $m1[1] ?? null;
    $APIPassword = $m2[1] ?? null;
    if (!$APIKey || !$APIPassword) return null;

    $ch1 = curl_init('https://api.gliptv.com/auth.aspx');
    curl_setopt_array($ch1, [
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'username' => 'GLIPTVadministrator',
            'password' => 'GLWiz2009SNMP',
            'serialNumber' => 'b0pYL9ja03DCwYh',
            'macAddress' => 'lMlKxk6ZpSsWj72',
            'gmt' => 10800,
            'deviceType' => 10,
            'deviceModel' => 'okhttp/4.12.0',
            'deviceInfo' => 'null',
            'deviceFirmware' => 'NetRange',
            'appVersion' => '5.0',
            'applicationType' => 2,
            'APIKey' => $APIKey,
            'APIPassword' => $APIPassword,
            'action' => 'checkNewDevice'
        ]),
        CURLOPT_HTTPHEADER => [
            'User-Agent: Macintosh',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
        ]
    ]);
    $response1 = curl_exec($ch1);
    curl_close($ch1);

    preg_match('#"city":"(.*?)"#', $response1, $m3);
    preg_match('#"country":"(.*?)"#', $response1, $m4);
    $City = $m3[1] ?? 'Istanbul';
    $Country = $m4[1] ?? 'TR';

    $ch2 = curl_init('https://api.gliptv.com/auth.aspx');
    curl_setopt_array($ch2, [
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'username' => 'GLIPTVadministrator',
            'password' => 'GLWiz2009SNMP',
            'action' => 'getStreamURL',
            'PackageID' => 90,
            'ClusterName' => 'zixi-hls-box-GLTurk',
            'streamType' => 'live',
            'streamProtocal' => 'hls',
            'itemName' => $Live,
            'User_Country' => $Country,
            'User_Province' => $City,
            'DeviceType' => 8,
            'DeviceID' => 20811486,
            'BoxID' => 644201,
            'DUID' => 'UqGD2QMSNdkB8d8',
            'APIKey' => $APIKey,
            'APIPassword' => $APIPassword,
            'appVersion' => '5.0'
        ]),
        CURLOPT_HTTPHEADER => [
            'User-Agent' => 'okhttp/4.12.0',
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
        ]
    ]);
    $response2 = curl_exec($ch2);
    curl_close($ch2);

    preg_match('#"resp":"(.*?)"#', $response2, $m5);
    return $m5[1] ?? null;
}

// 2- Proxy işlevi: m3u8 ve segment proxyleme
function proxyContent($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "User-Agent: Mozilla/5.0 (compatible; Proxy/1.0)"
    ]);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        header("HTTP/1.1 502 Bad Gateway");
        echo "cURL Error: " . curl_error($ch);
        curl_close($ch);
        exit;
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    if ($http_code !== 200) {
        header("HTTP/1.1 $http_code");
        echo "HTTP Error: $http_code";
        exit;
    }

    if (
        strpos($content_type, 'application/vnd.apple.mpegurl') !== false ||
        strpos($content_type, 'application/x-mpegURL') !== false ||
        preg_match('/\.m3u8/', $url)
    ) {
        // m3u8 segment URL’lerini proxy’ye yönlendir
        $base_url = substr($url, 0, strrpos($url, '/') + 1);
        $lines = explode("\n", $response);
        foreach ($lines as &$line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0) continue;
            if (!preg_match('/^https?:\/\//', $line)) {
                $line = $base_url . $line;
            }
            $line = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?proxy_url=' . urlencode($line);
        }
        header("Content-Type: application/vnd.apple.mpegurl");
        echo implode("\n", $lines);
        exit;
    }

    // İçeriği orijinal content-type ile gönder
    if ($content_type) header("Content-Type: $content_type");
    else header("Content-Type: application/octet-stream");

    echo $response;
    exit;
}

// 3- Main (gelen parametreleri ayıkla)
if (isset($_GET['ID'])) {
    // Yayın ID’si varsa, önce yayın linkini al, sonra proxy’le
    $Live = $_GET['ID'];
    $streamUrl = getGliptvStreamUrl($Live);
    if (!$streamUrl) {
        header("HTTP/1.1 500 Internal Server Error");
        echo "Yayın linki alınamadı.";
        exit;
    }
    // streamUrl parametresi olarak ver proxy fonksiyonuna
    proxyContent($streamUrl);
}
elseif (isset($_GET['proxy_url'])) {
    // Segment veya m3u8 için proxy çağrısı
    $proxyUrl = $_GET['proxy_url'];
    proxyContent($proxyUrl);
}
else {
    header("HTTP/1.1 400 Bad Request");
    echo "Parametre eksik: ID veya proxy_url";
    exit;
}
?>
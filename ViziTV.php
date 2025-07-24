<?php
$Live = isset($_GET['ID']) ? intval($_GET['ID']) : 0;
if ($Live <= 0) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

$Play = json_encode([
  "channelId" => $Live,
  "platform" => "AndroidMobil",
  "liveOffset" => 75,
  "quality" => "-1"
]);

$ch = curl_init('https://app.vizi.tv/play');
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $Play);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer BISgwYOvA9j8xcKZ',
    'User-Agent: Dalvik/2.1.0 (Linux; U; Android 7.1.2; SM-G935F Build/N2G48H)',
    'Host: app.vizi.tv',
    'Connection: Keep-Alive',
]);

// Proxy kaldırıldı

$site = curl_exec($ch);
if(curl_errno($ch)){
    header("HTTP/1.1 502 Bad Gateway");
    echo json_encode(['error' => 'Request failed: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}
curl_close ($ch);

$site = str_replace('\\','',$site);
preg_match('#"sid":"(.*?)"#',$site,$icerik);
if(!isset($icerik[1])){
    header("HTTP/1.1 502 Bad Gateway");
    echo json_encode(['error' => 'SID not found in response']);
    exit;
}
$ID = $icerik[1];

$ch1 = curl_init('https://app.vizi.tv/playlist?sid='.$ID.'&quality=2');
curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch1, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer BISgwYOvA9j8xcKZ',
    'User-Agent: Dalvik/2.1.0 (Linux; U; Android 7.1.2; SM-G935F Build/N2G48H)',
    'Host: app.vizi.tv',
    'Connection: Keep-Alive',
]);

// Proxy kaldırıldı

$site1 = curl_exec($ch1);
if(curl_errno($ch1)){
    header("HTTP/1.1 502 Bad Gateway");
    echo json_encode(['error' => 'Request failed: ' . curl_error($ch1)]);
    curl_close($ch1);
    exit;
}
curl_close ($ch1);

header('Content-Type: application/json');
echo $site1;
?>
<?php
function decodeLink($Link) {
    $Link = strrev($Link);
    $step1 = base64_decode($Link);
    if ($step1 === false) return null;

    $key = 'K9L';
    $output = '';

    for ($i = 0; $i < strlen($step1); $i++) {
        $r = $key[$i % 3];
        $n = ord($step1[$i]) - (ord($r) % 5 + 1);
        $output .= chr($n);
    }

    $decoded = base64_decode($output);
    return $decoded ?: null;
}
$Live = $_GET['ID'];
$ch = curl_init('https://www.fullhdfilmizlesene.so/film/'.$Live.'');
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Host: www.fullhdfilmizlesene.so',
'Connection: keep-alive',
'User-Agent: iTunes-AppleTV/15.0',
'Accept: */*',
'Accept-Encoding: gzip, deflate',
'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
));
$site = curl_exec($ch);
curl_close ($ch);
preg_match('#vidid = \'(.*?)\'#',$site,$icerik);
$Data = $icerik[1];

$ch1 = curl_init('https://www.fullhdfilmizlesene.so/player/api.php?id='.$Data.'&type=t&name=atom&get=video&format=json');
curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch1, CURLOPT_ENCODING, false);
curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
'Host: www.fullhdfilmizlesene.so',
'Connection: keep-alive',
'User-Agent: iTunes-AppleTV/15.0',
'Accept: */*',
'Accept-Encoding: gzip, deflate',
'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
));
$site1 = curl_exec($ch1);
curl_close ($ch1);
$site1 = str_replace('\\','',$site1);
preg_match('#"html":"(.*?)"#',$site1,$icerik);
$Url = $icerik[1];

$ch2 = curl_init(''.$Url.'');
curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_ENCODING, false);
curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
'Connection: keep-alive',
'User-Agent: iTunes-AppleTV/15.0',
'Accept: */*',
'Referer: https://www.fullhdfilmizlesene.so/',
'Accept-Encoding: gzip, deflate',
'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
));
$site2 = curl_exec($ch2);
curl_close ($ch2);
preg_match('#"file":\s*av\([\'"]([^\'"]+)[\'"]\)#',$site2,$icerik);
$Link = $icerik[1];
$decodedLink = decodeLink($Link);
header ("Location: $decodedLink");
?>
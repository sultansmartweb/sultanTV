<?php
$Live = $_GET['ID'];
$ch = curl_init('https://bit.ly/m/taraftarium24w');
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Accept: */*',
'Accept-Encoding: gzip, deflate',
'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
'Connection: keep-alive',
'Host: bit.ly',
'Referer: https://google.com.tr/',
'User-Agent: iTunes-AppleTV/15.0',
));
$site = curl_exec($ch);
curl_close ($ch);
preg_match('#"sort_order": 1, "parent": "(.*?)", "content": {"title": "(.*?)", "target": "(.*?)"#',$site,$icerik);
$Data = $icerik[3];

$ch1 = curl_init('https://'.$Data.'');
curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch1, CURLOPT_ENCODING, false);
curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
'Accept: */*',
'Accept-Encoding: gzip, deflate',
'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
'Connection: keep-alive',
'User-Agent: iTunes-AppleTV/15.0',
));
$site1 = curl_exec($ch1);
curl_close ($ch1);
preg_match('#a href="(.*?)"#',$site1,$icerik);
$Url = $icerik[1];

$ch2 = curl_init(''.$Url.'');
curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_ENCODING, false);
curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
'Accept: */*',
'Accept-Encoding: gzip, deflate',
'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
'Connection: keep-alive',
'User-Agent: iTunes-AppleTV/15.0',
));
$site2 = curl_exec($ch2);
curl_close ($ch2);
preg_match('#\s*<div\s+id="24-7-tab"\s+class="tab-content">\s*<a\s+href="([^"]+)"#is',$site2,$icerik);
$DataUrl = $icerik[1];

$ch3 = curl_init(''.$Url.''.$DataUrl.'');
curl_setopt($ch3, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_ENCODING, false);
curl_setopt($ch3, CURLOPT_HTTPHEADER, array(
'Accept: */*',
'Accept-Encoding: gzip, deflate',
'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
'Connection: keep-alive',
'User-Agent: iTunes-AppleTV/15.0',
));
$site3 = curl_exec($ch3);
curl_close ($ch3);
preg_match('#const baseurl = "(.*?)"#',$site3,$icerik);
$Host = $icerik[1];
preg_match('#'.$Live.': "(.*?)"#',$site3,$icerik);
$Link = $icerik[1];
header ("Location: $Host$Link");
?>
<?php
$Live = $_GET['ID'];
$Login = '{"token":"ismutHocGgGl55lRoBiBw3haJe0XrgSpv1XxO8uDZrbwOH7i1GcfxVmRoxfZsN90_kUkQ0GXH15DEwE19TPsi2joyb3f46TGHTlFyAcLsgq2A8fmEWAZn6e-ssFPoam6LIbx","reason":"player.enter","locale":"tr","theme":"dark","metadata":{"device":{"type":"desktop","uniqueId":"cdac425d-2376-4f94-bf03-6ba6cf405823"},"os":{"name":"win32","version":"Windows 10 Pro","abis":["x64"],"host":"Noter"},"app":{"platform":"electron"},"version":{"package":"tv.vavoo.app","binary":"3.1.8","js":"3.1.8"}},"appFocusTime":21739,"playerActive":true,"playDuration":0,"devMode":false,"hasAddon":true,"castConnected":false,"package":"tv.vavoo.app","version":"3.1.8","process":"app","firstAppStart":1752255813067,"lastAppStart":1752255813067,"ipLocation":{"ip":"85.195.78.42","country":"DE","city":"Frankfurt am Main"},"adblockEnabled":true,"proxy":{"supported":["ss"],"engine":"ss","enabled":true,"autoServer":true,"id":"nl-ams"},"iap":{"supported":false}}';
$ch = curl_init('https://www.vavoo.tv/api/app/ping');
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $Login);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Host: www.vavoo.tv',
'Connection: close',
'accept: application/json',
'content-type: application/json; charset=utf-8',
'user-agent: electron-fetch/1.0 electron (+https://github.com/arantes555/electron-fetch)',
'Accept-Language: tr',
'Accept-Encoding: gzip, deflate',
));
$site = curl_exec($ch);
curl_close ($ch);
preg_match('#"addonSig":"(.*?)"#',$site,$icerik);
$Token = $icerik[1];

$Ticket = '{"language":"tr","region":"DE","url":"https://vavoo.to/vavoo-iptv/play/'.$Live.'","clientVersion":"3.0.2"}';
$ch1 = curl_init('https://vavoo.to/mediahubmx-resolve.json');
curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch1, CURLOPT_ENCODING, false);
curl_setopt($ch1, CURLOPT_POST, true);
curl_setopt($ch1, CURLOPT_POSTFIELDS, $Ticket);
curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
'Host: vavoo.to',
'Connection: close',
'content-type: application/json; charset=utf-8',
"mediahubmx-signature: $Token",
'user-agent: MediaHubMX/2',
'accept: */*',
'Accept-Language: tr',
'Accept-Encoding: gzip, deflate',
));
$site1 = curl_exec($ch1);
curl_close ($ch1);
preg_match('#"url":"(.*?)"#',$site1,$icerik);
$Link = $icerik[1];
header ("Location: $Link");
?>
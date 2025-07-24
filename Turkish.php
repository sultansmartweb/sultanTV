<?php
function decodeEncryptedUrl($encryptedString) {
    $parts = explode('|Xf|x', $encryptedString);
    if (count($parts) < 2) return false;

    $index = intval(str_replace('Äx', '', $parts[0]));
    $data = $parts[1];

    $source = ['€','$','Ă','Ä','Ë','Ģ','Ḩ','Ķ','Ḽ','Ņ','Ň','Š','Ț','Ž','Ә','Є','Б','Җ','Ч','Ж','Д','Ӡ','Ф','Ғ','Ӷ','Ы','И','К','Љ','Ө','Ў','Њ','Һ','Г','Ş'];
    $target = ['0','1','2','3','4','5','6','7','8','9','.','&','=','w','?','c','o','m','a','f','l','i','h','t','s',':','/','r','e','d','n','k','p','_','-'];

    $maxSourceIndex = count($source) - 1;
    $maxTargetIndex = count($target) - 1;

    for ($i = 0; $i <= $maxTargetIndex; $i++) {
        if ($index > $maxSourceIndex) {
            $index = 0;
        }
        $data = str_replace($source[$index], $target[$i], $data);
        $index++;
    }

    return $data;
}

$Live = $_GET['ID'];
$ch = curl_init($Live);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Connection: keep-alive',
    'User-Agent: okhttp/4.12.0',
    'Accept: */*',
));
$site = curl_exec($ch);
curl_close ($ch);
preg_match('#iframe width="100%" height="100%" src="(.*?)"#',$site,$icerik);
$Data = $icerik[1];

$ch1 = curl_init($Data);
curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch1, CURLOPT_ENCODING, '');
curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
    'Connection: keep-alive',
    'User-Agent: okhttp/4.12.0',
    'Accept: */*',
    "Referer: $Live",
));
$site1 = curl_exec($ch1);
curl_close ($ch1);
$site1 = str_replace("'+ulke+'",'TR',$site1);
$site1 = str_replace('\\','',$site1);
preg_match('#iframe width="100%" height="100%" rel="nofollow" src="(.*?)"#',$site1,$icerik);
$Url = $icerik[1];

$ch2 = curl_init($Url);
curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_ENCODING, '');
curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
    'Connection: keep-alive',
    'User-Agent: okhttp/4.12.0',
    'Accept: */*',
    "Referer: $Data",
));
$site2 = curl_exec($ch2);
curl_close ($ch2);
$site2 = str_replace('\\','',$site2);
preg_match('#file : \'(.*?)\'#',$site2,$icerik);
$encryptedLink = $icerik[1];
$decodedLink = decodeEncryptedUrl($encryptedLink);
$decodedLink = html_entity_decode($decodedLink, ENT_QUOTES | ENT_HTML5);
header ("Location: $decodedLink");
?>
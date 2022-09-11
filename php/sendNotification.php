<?php

/**
 * @param $http2ch          the curl connection
 * @param $http2_server     the Apple server url
 * @param $apple_cert       the path to the certificate
 * @param $app_bundle_id    the app bundle id
 * @param $message          the payload to send (JSON)
 * @param $token            the token of the device
 * @return mixed            the status code (see https://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/Chapters/APNsProviderAPI.html#//apple_ref/doc/uid/TP40008194-CH101-SW18)
 */
function sendHTTP2Push($http2ch, $http2_server, $apple_cert, $passphrase, $app_bundle_id, $message, $token)
{

    $milliseconds = round(microtime(true) * 1000);

    // url (endpoint)
    $url = "{$http2_server}/3/device/{$token}";

    // certificate
    $cert = realpath($apple_cert);

    // headers
    $headers = array(
        "apns-topic: {$app_bundle_id}",
        "User-Agent: My Sender"
    );

    // other curl options
    curl_setopt_array($http2ch, array(
        CURLOPT_URL => "{$url}",
        CURLOPT_PORT => 443,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => TRUE,
        CURLOPT_POSTFIELDS => $message,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSLCERT => $cert,
        CURLOPT_SSLCERTPASSWD => $passphrase,
        CURLOPT_HEADER => 1
    ));

    // go...
    $result = curl_exec($http2ch);
    if ($result === FALSE) {
        throw new Exception('Curl failed with error: ' . curl_error($http2ch));
    }

    // get respnse
    $status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);

    $duration = round(microtime(true) * 1000) - $milliseconds;

    //    echo $duration;

    return $status;
}

// open connection
if (!defined('CURL_HTTP_VERSION_2_0')) {
    define('CURL_HTTP_VERSION_2_0', 3);
}
$http2ch = curl_init();
curl_setopt($http2ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);

// send push
$apple_cert = 'newfile.crtAndKey.pem';
$passphrase = '123456';
$message = '{"aps":{"alert":"Hi!","sound":"default","content-available": 1}}';
$token = 'f25a3addef4ba8c89a90576d37af33344e3b8c66185c58cfab41e8fc9e09b774';
$http2_server = 'https://api.sandbox.push.apple.com';   // or 'api.push.apple.com' if production
$app_bundle_id = 'com.pucknavin.TestNotification11Sep';

// close connection
// for ($i = 0; $i < 20; $i++) {
//     $status = sendHTTP2Push($http2ch, $http2_server, $apple_cert, $app_bundle_id, $message, $token);
//     echo "Response from apple -> {$status}\n";
// }


$status = sendHTTP2Push($http2ch, $http2_server, $apple_cert, $passphrase, $app_bundle_id, $message, $token);
echo "Response from apple -> {$status}\n";



curl_close($http2ch);

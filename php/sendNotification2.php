<?php

require_once __DIR__ . '/vendor/autoload.php';

$passphrase = '123456';
$client = new \Apns\Client([__DIR__ . '/newfile.crtAndKey.pem', $passphrase], true); // true is for sandbox

$message = (new \Apns\Message())
    ->setDeviceIdentifier('f25a3addef4ba8c89a90576d37af33344e3b8c66185c58cfab41e8fc9e09b774')
    ->setAlert('Test message')
    ->setAPSBadge(1)
    ->setData([
        'Key1' => 'Value1',
        'Key2' => 'Value2',
        'Key3' => 'Value3',
    ])
    ->setTopic('com.pucknavin.TestNotification11Sep');

$client->send($message);

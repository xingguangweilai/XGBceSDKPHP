<?php
require '../XGBceSDKPHP/vendor/autoload.php';
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use BaiduBce\Log\LogFactory;
use BaiduBce\Log\MonoLogFactory;
use Monolog\Logger;
// 报告所有 PHP 错误
error_reporting(-1);

define('__SMS_CLIENT_ROOT', dirname(__DIR__));

// 设置SmsClient的Access Key ID、Secret Access Key和ENDPOINT
$SMS_TEST_CONFIG =
array(
    'credentials' => array(
        'accessKeyId' => 'AK',
        'secretAccessKey' => 'SK'
    ),
    'endpoint' => 'http://smsv3.bj.baidubce.com'
);

// 设置log的格式和级别
$__handler = new StreamHandler(STDERR, Logger::DEBUG);
$__handler->setFormatter(
    new LineFormatter(null, null, false, true)
);
LogFactory::setInstance(
    new MonoLogFactory(array($__handler))
);
LogFactory::setLogLevel(\Psr\Log\LogLevel::DEBUG);
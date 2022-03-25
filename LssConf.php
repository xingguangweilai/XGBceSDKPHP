<?php
require '../XGBceSDKPHP/vendor/autoload.php';
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use BaiduBce\Log\LogFactory;
use BaiduBce\Log\MonoLogFactory;
use Monolog\Logger;
// 报告所有 PHP 错误
error_reporting(-1);

define('__LSS_CLIENT_ROOT', dirname(__DIR__));

// 设置LssClient的Access Key ID、Secret Access Key和ENDPOINT
$LSS_TEST_CONFIG =
array(
    'credentials' => array(
        'accessKeyId' => 'AK',
        'secretAccessKey' => 'SK'
    ),
    'endpoint' => 'http://lss.bj.baidubce.com'
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
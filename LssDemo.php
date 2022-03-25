<?php
require 'LssConf.php';
require 'index.php';
require '../XGBceSDKPHP/src/BaiduBce/Services/Lss/LssClient.php';

use baidubce\services\Lss\LssClient;

//调用配置文件中的参数
global $LSS_TEST_CONFIG;
//新建LssClient
$client = new LssClient($LSS_TEST_CONFIG);

//指定Domain, App, Stream
$domain = 'lssplay.baidu.club';
$app = 'live';
$stream = 'livetest';
try{
    $response = $client->getStream($domain, $app, $stream);
    print json_encode($response);
}catch(Exception $e){
    print $e->getMessage();
}
<?php
require 'SmsConf.php';
require 'index.php';
require  '../XGBceSDKPHP/src/BaiduBce/Services/Sms/SmsClient.php';

use BaiduBce\Services\Sms\SmsClient;
use BaiduBce\Services\Sms\SMSContentType;
use BaiduBce\Services\Sms\SMSCountryType;
use BaiduBce\Services\Sms\SMSType;

//调用配置文件中的参数
global $SMS_TEST_CONFIG;
//新建SmsClient
$client = new SmsClient($SMS_TEST_CONFIG);

try{
    //发送短信
    // $response = $client->sendMessageV3(,'sms-tmpl-IYQpaG59486','sms-sign-RLOmyr26320',array('ticketNumber'=>'12306','ticketTitle'=>'测试'));

    //申请签名
    // $response=$client->createSignature('PHPAPI测试',SMSContentType::_ELSE,'e15d930e-b132-460d-a61b-6086f117df18');

    //删除签名
    // $response=$client->deleteSignature('sms-sign-LgtUpp35156');

    //查询签名
    // $response=$client->querySignature('sms-sign-RLOmyr26320');

    //创建模板
    // $response=$client->createTemplate('php api测试','您的验证码是：${code}',SMSType::COMMONVCODE,SMSCountryType::DOMESTIC,'验证码模板','e15d930e-b132-460d-a61b-6086f117df18');

    //查询模板
    // $response=$client->queryTemplate('sms-tmpl-mPYJpc96751');

    //删除模板
    // $response=$client->deleteTemplate('sms-tmpl-mPYJpc96751');

    // $response=$client->queryQuota();

    print json_encode($response);
}catch(Exception $e){
    print $e->getMessage();
}
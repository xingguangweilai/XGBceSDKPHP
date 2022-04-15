<?php

namespace BaiduBce\Services\Sms;

use BaiduBce\Auth\BceV1Signer;
use BaiduBce\Auth\SignOptions;
use BaiduBce\BceBaseClient;
use BaiduBce\Exception\BceClientException;
use BaiduBce\Http\BceHttpClient;
use BaiduBce\Http\HttpContentTypes;
use BaiduBce\Http\HttpHeaders;
use BaiduBce\Http\HttpMethod;
use BaiduBce\Util\DateUtils;

class SmsClient extends BceBaseClient
{
    private $signer;
    private $httpClient;
    private $prefix = '/sms/v3';

    function __construct(array $config)
    {
        parent::__construct($config, 'SmsClient');
        $this->signer = new BceV1Signer();
        $this->httpClient = new BceHttpClient();
    }

    /**
     *  发送短信
     * @param mixed $mobile        手机号码,支持单个或多个手机号，多个手机号之间以英文逗号分隔，一次请求最多支持200个手机号
     * @param mixed $template      短信模板ID，模板申请成功后自动创建，全局内唯一
     * @param mixed $signatureId   短信签名ID，签名表申请成功后自动创建，全局内唯一
     * @param mixed $contentVar   模板变量内容，用于替换短信模板中定义的变量
     * @param mixed $custom        用户自定义参数，格式为字符串，状态回调时会回传该值
     * @param mixed $userExtId      通道自定义扩展码，上行回调时会回传该值，其格式为纯数字串
     * @param mixed $clientToken  幂等性参数，避免client在http响应超时而重试时出现同一条短信多次发送的情况。如传入，则作为请求的messageId前缀，并在响应中回传
     */
    public function sendMessageV3($mobile, $template, $signatureId, $contentVar, $custom=null, $userExtId=null, $clientToken=null, $options = array())
    {
        if (empty($mobile)) {
            throw new BceClientException("The parameter mobile "
                . "should NOT be null or empty string");
        }
        if (empty($template)) {
            throw new BceClientException("The parameter template "
                . "should NOT be null or empty string");
        }
        if (empty($signatureId)) {
            throw new BceClientException("The parameter signatureId "
                . "should NOT be null or empty string");
        }
        if (empty($contentVar)) {
            throw new BceClientException("The parameter contentVar "
                . "should NOT be null or empty string");
        }

        list($config) = $this->parseOptions($options, 'config');

        $_body=array(
            'mobile' => $mobile,
            'template'=>$template,
            'signatureId'=>$signatureId,
            'contentVar'=>$contentVar
        );
        if(!empty($custom))
        {
            $_body['custom']=$custom;
        }
        if(!empty($userExtId))
        {
            $_body['userExtId']=$userExtId;
        }

        $argss=array(
            'config'=>$config,
            'body'=>json_encode($_body)
        );
        if(!empty($clientToken))
        {
            $params=array(
                'clientToken'=>$clientToken
            );
            $argss['params']=$params;
        }

        return $this->sendRequest(
            HttpMethod::POST,
            $argss,
            "/api/v3/sendSms"
        );
    }

    /**
     * 申请签名
     * @param mixed $content                    签名内容
     * @param mixed $contentType             签名类型
     * @param mixed $description               对于签名的描述
     * @param mixed $countryType             签名适用的国家类型
     * @param mixed $signatureFileBase64  签名的证明文件经过base64编码后的字符串。文件大小不超过2MB
     * @param mixed $signatureFileFormat  签名证明文件的格式，目前支持JPG、PNG、JPEG三种格式
     * @param mixed $clientToken               幂等性参数，避免client在http响应超时而重试时出现同一条短信多次发送的情况。如传入，则作为请求的messageId前缀，并在响应中回传
     */
    public function createSignature($content, $contentType, $clientToken=null, $description=null, $countryType=null, $signatureFileBase64=null, $signatureFileFormat=null, $options = array())
    {
        if (empty($content)) {
            throw new BceClientException("The parameter content "
                . "should NOT be null or empty string");
        }
        if (empty($contentType)) {
            throw new BceClientException("The parameter contentType "
                . "should NOT be null or empty string");
        }
        $_body=array('content'=>$content,'contentType'=>$contentType);
        if (!empty($description)) {
            $_body['description']=$description;
        }
        if (!empty($countryType)) {
            $_body['countryType']=$countryType;
        }
        if (!empty($signatureFileBase64)) {
            $_body['signatureFileBase64']=$signatureFileBase64;
        }
        if (!empty($signatureFileFormat)) {
            $_body['signatureFileFormat']=$signatureFileFormat;
        }
        list($config) = $this->parseOptions($options, 'config');
        $argss=array(
            'config'=>$config,
            'body'=>json_encode($_body)
        );
        if (!empty($clientToken)) {
            $params=array(
                'clientToken'=>$clientToken
            );
            $argss['params']=$params;
        }

        return $this->sendRequest(
            HttpMethod::POST,
            $argss,
            $this->prefix."/signatureApply"
        );
    }

    /**
     * 变更签名
     * @param mixed $signatureId               签名ID
     * @param mixed $content                    签名内容
     * @param mixed $contentType             签名类型
     * @param mixed $description               对于签名的描述
     * @param mixed $countryType             签名适用的国家类型
     * @param mixed $signatureFileBase64  签名的证明文件经过base64编码后的字符串。文件大小不超过2MB
     * @param mixed $signatureFileFormat  签名证明文件的格式，目前支持JPG、PNG、JPEG三种格式
     * @param mixed $clientToken               幂等性参数，避免client在http响应超时而重试时出现同一条短信多次发送的情况。如传入，则作为请求的messageId前缀，并在响应中回传
     */
    public function modifySignature($signatureId, $content, $contentType, $countryType, $description=null, $signatureFileBase64=null, $signatureFileFormat=null, $options = array())
    {
        if (empty($signatureId)) {
            throw new BceClientException("The parameter signatureId "
                . "should NOT be null or empty string");
        }
        if (empty($content)) {
            throw new BceClientException("The parameter content "
                . "should NOT be null or empty string");
        }
        if (empty($contentType)) {
            throw new BceClientException("The parameter contentType "
                . "should NOT be null or empty string");
        }
        if (empty($countryType)) {
            throw new BceClientException("The parameter countryType "
                . "should NOT be null or empty string");
        }
        $_body=array('content'=>$content,'contentType'=>$contentType,'countryType'=>$countryType);
        if (!empty($description)) {
            $_body['description']=$description;
        }
        if (!empty($signatureFileBase64)) {
            $_body['signatureFileBase64']=$signatureFileBase64;
        }
        if (!empty($signatureFileFormat)) {
            $_body['signatureFileFormat']=$signatureFileFormat;
        }
        list($config) = $this->parseOptions($options, 'config');
        $argss=array(
            'config'=>$config,
            'body'=>json_encode($_body)
        );

        return $this->sendRequest(
            HttpMethod::PUT,
            $argss,
            $this->prefix."/signatureApply/".$signatureId
        );
    }

    /**
     * 查询签名
     * @param mixed $signatureId 签名ID，唯一标识一个签名
     */
    public function querySignature($signatureId, $options = array())
    {
        if (empty($signatureId)) {
            throw new BceClientException("The parameter signatureId "
                . "should NOT be null or empty string");
        }
        list($config) = $this->parseOptions($options, 'config');
        $argss=array(
            'config'=>$config
        );

        return $this->sendRequest(
            HttpMethod::GET,
            $argss,
            $this->prefix."/signatureApply/".$signatureId
        );
    }

    /**
     * 删除签名
     * @param mixed $signatureId 签名ID，唯一标识一个签名
     */
    public function deleteSignature($signatureId, $options = array())
    {
        if (empty($signatureId)) {
            throw new BceClientException("The parameter signatureId "
                . "should NOT be null or empty string");
        }
        list($config) = $this->parseOptions($options, 'config');
        $argss=array(
            'config'=>$config
        );

        return $this->sendRequest(
            HttpMethod::DELETE,
            $argss,
            $this->prefix."/signatureApply/".$signatureId
        );
    }

    /**
     * 创建模板
     * @param mixed $name                    模板名称
     * @param mixed $content                 模板内容
     * @param mixed $smsType               短信类型
     * @param mixed $countryType          适用国家类型
     * @param mixed $description            模板描述
     * @param mixed $clientToken           幂等性参数，避免client在http响应超时而重试时出现同一条短信多次发送的情况。如传入，则作为请求的messageId前缀，并在响应中回传
     */
    public function createTemplate($name, $content, $smsType, $countryType, $description, $clientToken, $options = array())
    {
        if (empty($content)) {
            throw new BceClientException("The parameter content "
                . "should NOT be null or empty string");
        }
        if (empty($name)) {
            throw new BceClientException("The parameter name "
                . "should NOT be null or empty string");
        }
        if (empty($smsType)) {
            throw new BceClientException("The parameter smsType "
                . "should NOT be null or empty string");
        }
        if (empty($countryType)) {
            throw new BceClientException("The parameter countryType "
                . "should NOT be null or empty string");
        }
        if (empty($description)) {
            throw new BceClientException("The parameter description "
                . "should NOT be null or empty string");
        }
        $_body=array('content'=>$content,'smsType'=>$smsType,'name'=>$name,'countryType'=>$countryType);
        
        list($config) = $this->parseOptions($options, 'config');
        $argss=array(
            'config'=>$config,
            'body'=>json_encode($_body)
        );
        if (!empty($clientToken)) {
            $params=array(
                'clientToken'=>$clientToken
            );
            $argss['params']=$params;
        }

        return $this->sendRequest(
            HttpMethod::POST,
            $argss,
            $this->prefix."/template"
        );
    }

    /**
     * 变更模板
     * @param mixed $templateId            模板ID，唯一标识一个模板
     * @param mixed $name                    模板名称
     * @param mixed $content                 模板内容
     * @param mixed $smsType               短信类型
     * @param mixed $countryType          适用国家类型
     * @param mixed $description            模板描述
     * @param mixed $clientToken           幂等性参数，避免client在http响应超时而重试时出现同一条短信多次发送的情况。如传入，则作为请求的messageId前缀，并在响应中回传
     */
    public function modifyTemplate($templateId, $name, $content, $smsType, $countryType, $description=null, $clientToken=null, $options = array())
    {
        if (empty($templateId)) {
            throw new BceClientException("The parameter templateId "
                . "should NOT be null or empty string");
        }
        if (empty($content)) {
            throw new BceClientException("The parameter content "
                . "should NOT be null or empty string");
        }
        if (empty($name)) {
            throw new BceClientException("The parameter name "
                . "should NOT be null or empty string");
        }
        if (empty($smsType)) {
            throw new BceClientException("The parameter smsType "
                . "should NOT be null or empty string");
        }
        if (empty($countryType)) {
            throw new BceClientException("The parameter countryType "
                . "should NOT be null or empty string");
        }
        $_body=array('content'=>$content,'smsType'=>$smsType,'name'=>$name,'countryType'=>$countryType);
        
        list($config) = $this->parseOptions($options, 'config');
        $argss=array(
            'config'=>$config,
            'body'=>json_encode($_body)
        );
        if (!empty($clientToken)) {
            $params=array(
                'clientToken'=>$clientToken
            );
            $argss['params']=$params;
        }

        return $this->sendRequest(
            HttpMethod::PUT,
            $argss,
            $this->prefix."/template/".$templateId
        );
    }

     /**
     * 查询模板
     * @param mixed $templateId   模板ID，唯一标识一个模板
     */
    public function queryTemplate($templateId, $options = array())
    {
        if (empty($templateId)) {
            throw new BceClientException("The parameter templateId "
                . "should NOT be null or empty string");
        }
        list($config) = $this->parseOptions($options, 'config');
        $argss=array(
            'config'=>$config
        );

        return $this->sendRequest(
            HttpMethod::GET,
            $argss,
            $this->prefix."/template/".$templateId
        );
    }

     /**
     * 删除模板
     * @param mixed $templateId 模板ID，唯一标识一个模板
     */
    public function deleteTemplate($templateId, $options = array())
    {
        if (empty($templateId)) {
            throw new BceClientException("The parameter templateId "
                . "should NOT be null or empty string");
        }
        list($config) = $this->parseOptions($options, 'config');
        $argss=array(
            'config'=>$config
        );

        return $this->sendRequest(
            HttpMethod::DELETE,
            $argss,
            $this->prefix."/template/".$templateId
        );
    }

     /**
     * 查询配额及频控
     */
    public function queryQuota($options = array())
    {
        list($config) = $this->parseOptions($options, 'config');
        $argss=array(
            'config'=>$config
        );
        $argss['params']=array('userQuery'=>'');

        return $this->sendRequest(
            HttpMethod::GET,
            $argss,
            $this->prefix."/quota"
        );
    }

    /**
     * 变更配额或频控
     * @param mixed $quotaPerDay                                        日（自然日）发送配额
     * @param mixed $quotaPerMonth                                     月（自然月）发送配额
     * @param mixed $rateLimitPerMobilePerSignByMinute      单手机号单签名每分钟（60s）发送频率
     * @param mixed $rateLimitPerMobilePerSignByHour         单手机号单签名每小时（60mins）发送频率
     * @param mixed $rateLimitPerMobilePerSignByDay           单手机号单签名每天（24h）发送频率
     */
    public function modifyQuota($quotaPerDay=99, $quotaPerMonth=999, $rateLimitPerMobilePerSignByMinute=88, $rateLimitPerMobilePerSignByHour=77, $rateLimitPerMobilePerSignByDay=66, $options = array())
    {
        if (!is_int($quotaPerDay)) {
            throw new BceClientException("The parameter quotaPerDay "
                . "should be int");
        }
        if (!is_int($quotaPerMonth)) {
            throw new BceClientException("The parameter quotaPerMonth "
                . "should be int");
        }
        if (!is_int($rateLimitPerMobilePerSignByMinute)) {
            throw new BceClientException("The parameter rateLimitPerMobilePerSignByMinute "
                . "should be int");
        }
        if (!is_int($rateLimitPerMobilePerSignByHour)) {
            throw new BceClientException("The parameter rateLimitPerMobilePerSignByHour "
                . "should be int");
        }
        if (!is_int($rateLimitPerMobilePerSignByDay)) {
            throw new BceClientException("The parameter rateLimitPerMobilePerSignByDay "
                . "should be int");
        }
        $_body=array(
            'quotaPerDay'=>$quotaPerDay,
            'quotaPerMonth'=>$quotaPerMonth,
            'rateLimitPerMobilePerSignByMinute'=>$rateLimitPerMobilePerSignByMinute,
            'rateLimitPerMobilePerSignByHour'=>$rateLimitPerMobilePerSignByHour,
            'rateLimitPerMobilePerSignByDay'=>$rateLimitPerMobilePerSignByDay);
        
        list($config) = $this->parseOptions($options, 'config');
        $argss=array(
            'config'=>$config,
            'body'=>json_encode($_body)
        );

        return $this->sendRequest(
            HttpMethod::PUT,
            $argss,
            $this->prefix."/quota"
        );
    }

    private function sendRequest($httpMethod, array $varArgs, $requestPath = '/')
    {
        $defaultArgs = array(
            'config' => array(),
            'body' => null,
            'headers' => array(),
            'params' => array(),
        );

        $args = array_merge($defaultArgs, $varArgs);
        if (empty($args['config'])) {
            $config = $this->config;
        } else {
            $config = array_merge(
                array(),
                $this->config,
                $args['config']
            );
        }
        if (!isset($args['headers'][HttpHeaders::CONTENT_TYPE])) {
            $args['headers'][HttpHeaders::CONTENT_TYPE] = HttpContentTypes::JSON;
        }
        $path = $requestPath;
        $response = $this->httpClient->sendRequest(
            $config,
            $httpMethod,
            $path,
            $args['body'],
            $args['headers'],
            $args['params'],
            $this->signer,
            null,
            array(SignOptions::HEADERS_TO_SIGN=>array(HttpHeaders::HOST=>HttpHeaders::HOST,HttpHeaders::BCE_DATE=>HttpHeaders::BCE_DATE))
        );

        $result = $this->parseJsonResult($response['body']);

        return $result;
    }
}

/**签名类型 */
class SMSContentType{
    /**企业 */
    const ENTERPRISE='Enterprise';
    /**移动应用名称 */
    const MOBILEAPP='MobileApp';
    /**工信部备案的网站名称 */
    const WEB='Web';
    /**微信公众号名称 */
    const WEBCHATPUBLIC='WeChatPublic';
    /**商标名称 */
    const BRAND='Brand';
    /**其他 */
    const _ELSE='Else';
}

/**适用国家类型 */
class SMSCountryType{
    /**国内 */
    const DOMESTIC='DOMESTIC';
    /**国际/港澳台 */
    const INTERNATIONAL='INTERNATIONAL';
    /**全球均适用 */
    const _GLOBAL='GLOBAL';
}

/**短信类型 */
class SMSType{
    /**普通营销 */
    const COMMONSALE ='CommonSale';
    /**普通验证码 */
    const COMMONVCODE='CommonVcode';
    /**普通通知 */
    const COMMONNOTICE='CommonNotice';
    /**物流验证码 */
    const EXPRESSVCODE='ExpressVcode';
    /**物流通知 */
    const EXPRESSNOTICE='ExpressNotice';
    /**传统金融营销 */
    const FINANCESALE='FinanceSale';
    /**传统金融验证码 */
    const FINANCEVODE='FinanceVcode';
    /**传统金融通知 */
    const FINACENOTICE='FinanceNotice';
    /**互联网金融营销 */
    const ITFINSALE='ItfinSale';
    /**互联网金融验证码 */
    const ITFINVCODE='ItfinVcode';
    /**互联网金融通知 */
    const ITFINNOTICE='ItfinNotice';
    /**信用卡营销 */
    const CREDITCARDSALE='CreditcardSale';
    /**信用卡验证码 */
    const CREDITCARDVCODE='CreditcardVcode';
    /**信用卡通知 */
    const CREDITCARDNOTICE='CreditcardNotice';
    /**催收通知 */
    const COLLECTIONNOTICE='CollectionNotice';
    /**游戏营销 */
    const GAMESALE='GameSale';
    /**游戏验证码 */
    const GAMEVCODE='GameVcode';
    /**游戏通知 */
    const GAMENOTICE='GameNotice';
    /**小游戏营销 */
    const GAMESSALE='GamesSale';
    /**小游戏验证码 */
    const GAMESVCODE='GamesVcode';
    /**小游戏通知 */
    const GAMESNOTICE='GamesNotice';
    /**教育营销 */
    const EDUCATIONSALE='EducationSale';
    /**教育验证码 */
    const EDUCATIONVCODE='EducationVcode';
    /**教育通知 */
    const EDUCATIONNOTICE='EducationNotice';
    /**电商营销 */
    const ECSALE='EcSale';
    /**电商验证码 */
    const ECVCODE='EcVcode';
    /**电商通知 */
    const ECNOTICE='EcNotice';
}
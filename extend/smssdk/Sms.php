<?php

namespace smssdk;
ini_set("display_errors", "on");

use smssdk\lib\Api\Sms\Request\V20170525\QuerySendDetailsRequest;
use smssdk\lib\Api\Sms\Request\V20170525\SendBatchSmsRequest;
use smssdk\lib\Api\Sms\Request\V20170525\SendSmsRequest;
use smssdk\lib\Core\Config;
use smssdk\lib\Core\DefaultAcsClient;
use smssdk\lib\Core\Profile\DefaultProfile;
// 加载区域结点配置
Config::load();

/**
 * Class
 *
 * Created on 17/10/17.
 * 短信服务API产品的DEMO程序,，直接通过
 * 执行此文件即可体验语音服务产品API功能(只需要将AK替换成开通了云通信-短信服务产品功能的AK即可)
 * 备注:Demo工程编码采用UTF-8
 */
class Sms
{

    /**
     * @var null
     */
    static $acsClient = null;

    /**
     * @var string   $_accessKeyId
     */
    protected $_accessKeyId = '';

    /**
     * @var string  密钥
     */
    protected $_accessSecret = '';

    /**
     * @var string  签名名称
     */
    protected $_signName = '';
    /**
     * @var string 模板名称编号
     */
    protected $_templateCode = '';

    /**
     * Sms constructor.
     * @param Registry $doctrine
     * @param $accessKeyId
     * @param $accessSecret
     */
    public function __construct($accessKeyId,$accessSecret,$signName='')
    {
        $this->_accessKeyId = $accessKeyId;
        $this->_accessSecret = $accessSecret;
        $this->_signName = $signName;
    }

    /**
     * 设置短信模版 编号
     * @param $templateCode
     */
    public function setTemplateCode($templateCode)
    {
        $this->_templateCode = $templateCode;
    }

    /**
     * @param $signName  设置签名
     */
    public function setSignName($signName)
    {
        $this->_signName = $signName;
    }

    /**
     * 取得AcsClient
     *
     * @return DefaultAcsClient
     */
    public function getAcsClient()
    {
        $product = "Dysmsapi";

        $domain = "dysmsapi.aliyuncs.com";

        $accessKeyId = $this->_accessKeyId;

        $accessKeySecret = $this->_accessSecret;

        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";

        if (static::$acsClient == null) {

            //初始化acsClient,暂不支持region化
            $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

            // 增加服务结点
            DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

            // 初始化AcsClient用于发起请求
            static::$acsClient = new DefaultAcsClient($profile);
        }
        return static::$acsClient;
    }

    /**
     * 发送验证码
     */
    public function sendCode($cell)
    {
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        // 必填，设置短信接收号码
        $request->setPhoneNumbers($cell);

        // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $request->setSignName($this->_signName);

        // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $request->setTemplateCode($this->_templateCode);
        // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
        $code = mt_rand(100000, 999999);

        $request->setTemplateParam(
            json_encode(
                array("code" => $code),
                JSON_UNESCAPED_UNICODE
            )
        );

        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);
        $result = get_object_vars($acsResponse);
        //发送成功
        if ($result["Code"] == "OK") {
            //我的逻辑
            $data = ['phone' => $cell, 'code' => $code ,'create_time'=>time()];
            db('smscode_log')->insert($data);

            return ['code'=>200,'msg'=>'ok'];
        } else {
            return ['code'=>403,'msg'=>$result["Message"]];
        }
    }

    /**
     * 验证短信验证码
     * @param $cell
     * @param $code
     * @return bool
     */
    public function checkCode($cell, $code)
    {
        $codeLog =db('smscode_log')->where('phone',$cell)->order('id desc')->find();
        //$codeLog最后一条记录
        if ($codeLog == null) {
            return ['code'=>'500','msg'=>'请发送验证码'];
        }
        if ($codeLog['create_time'] < time() - 300) {
            return ['code'=>'500','msg'=>'验证码已过期'];
        }
        if ($codeLog['code'] != $code) {
            return ['code'=>$code,'msg'=>'验证码错误'];
        }

        return ['code'=>200,'msg'=>'ok'];
    }

    /**
     * 批量发送短信
     * @return stdClass
     */
    public static function sendBatchSms()
    {

        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendBatchSmsRequest();

        // 必填:待发送手机号。支持JSON格式的批量调用，批量上限为100个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式
        $request->setPhoneNumberJson(json_encode(array(
            "1500000000",
            "1500000001",
        ), JSON_UNESCAPED_UNICODE));

        // 必填:短信签名-支持不同的号码发送不同的短信签名
        $request->setSignNameJson(json_encode(array(
            "云通信",
            "云通信"
        ), JSON_UNESCAPED_UNICODE));

        // 必填:短信模板-可在短信控制台中找到
        $request->setTemplateCode("SMS_1000000");

        // 必填:模板中的变量替换JSON串,如模板内容为"亲爱的${name},您的验证码为${code}"时,此处的值为
        // 友情提示:如果JSON中需要带换行符,请参照标准的JSON协议对换行符的要求,比如短信内容中包含\r\n的情况在JSON中需要表示成\\r\\n,否则会导致JSON在服务端解析失败
        $request->setTemplateParamJson(json_encode(array(
            array(
                "name" => "Tom",
                "code" => "123",
            ),
            array(
                "name" => "Jack",
                "code" => "456",
            ),
        ), JSON_UNESCAPED_UNICODE));

        // 可选-上行短信扩展码(扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段)
        // $request->setSmsUpExtendCodeJson("[\"90997\",\"90998\"]");

        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);

        return $acsResponse;
    }

    /**
     * 短信发送记录查询
     * @return stdClass
     */
    public static function querySendDetails()
    {

        // 初始化QuerySendDetailsRequest实例用于设置短信查询的参数
        $request = new QuerySendDetailsRequest();

        // 必填，短信接收号码
        $request->setPhoneNumber("12345678901");

        // 必填，短信发送日期，格式Ymd，支持近30天记录查询
        $request->setSendDate("20170718");

        // 必填，分页大小
        $request->setPageSize(10);

        // 必填，当前页码
        $request->setCurrentPage(1);

        // 选填，短信发送流水号
        $request->setBizId("yourBizId");

        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);

        return $acsResponse;
    }

}

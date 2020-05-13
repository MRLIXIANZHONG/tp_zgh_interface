<?php
/**
 * Created by Ran.
 * Date: 2018/8/27
 * Time: 15:09
 */

namespace app\common\exception;


class VerifyCodeException extends BaseException {
    public $code = 401; //HTTP状态码，如404、202
    public $msg = "验证码错误或已经过期"; //错误的具体信息
    public $errorCode = 10002; //自动错误码
}
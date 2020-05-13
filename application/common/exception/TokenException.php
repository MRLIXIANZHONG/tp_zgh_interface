<?php
/**
 * Created by Ran.
 * Date: 2018/6/25
 * Time: 10:37
 */

namespace app\common\exception;


class TokenException extends BaseException {
    public $code = 401; //HTTP状态码，如404、202
    public $msg = "Token已经过期或无效Token"; //错误的具体信息
    public $errorCode = 300; //自动错误码
}
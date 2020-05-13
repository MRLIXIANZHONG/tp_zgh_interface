<?php
/**
 * Created by Ran.
 * Date: 2018/6/26
 * Time: 11:10
 */

namespace app\common\exception;


class UserException extends BaseException {
    public $code = 402; //HTTP状态码，如404、202
    public $msg = "用户id不匹配"; //错误的具体信息
    public $errorCode = 402; //自动错误码
}
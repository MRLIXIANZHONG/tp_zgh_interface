<?php
/**
 * Created by Ran.
 * Date: 2018/6/27
 * Time: 10:36
 */

namespace app\common\exception;


class OptionsException extends BaseException {
    public $code = 202; //HTTP状态码，如404、202
    public $msg = "options请求"; //错误的具体信息
    public $errorCode = 1007;
}
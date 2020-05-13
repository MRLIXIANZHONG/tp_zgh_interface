<?php
/**
 * Created by Ran.
 * Date: 2018/6/25
 * Time: 10:55
 */

namespace app\common\exception;


class FailException extends BaseException {
    public $code = 404; //HTTP状态码，如404、202
    public $msg = "操作失败"; //错误的具体信息
    public $errorCode = 404;
}
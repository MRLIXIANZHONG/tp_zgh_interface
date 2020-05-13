<?php
/**
 * Created by Ran.
 * Date: 2018/6/25
 * Time: 10:44
 */

namespace app\common\exception;


class ParameterException extends BaseException {
    public $code = 400;
    public $msg = "参数错误";
    public $errorCode = 1000;
}
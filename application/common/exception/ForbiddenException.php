<?php
/**
 * Created by Ran.
 * Date: 2018/6/25
 * Time: 10:53
 */

namespace app\common\exception;


class ForbiddenException extends BaseException {
    public $code = 408;
    public $msg = "非法请求";
    public $errorCode = 408;
}
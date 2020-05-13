<?php
/**
 * Created by PhpStorm.
 * User: lhl
 * Date: 2018\6\28 0028
 * Time: 9:14
 */

namespace app\common\exception;


class MaintainException extends BaseException
{
    public $code = 501; //HTTP状态码，如404、202
    public $msg = "维护期间,请稍后再试"; //错误的具体信息

}
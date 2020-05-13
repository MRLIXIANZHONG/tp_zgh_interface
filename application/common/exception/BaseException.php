<?php
/**
 * Created by Ran.
 * Date: 2018/6/25
 * Time: 10:22
 */

namespace app\common\exception;


use think\Exception;

class BaseException extends Exception {
    public $code = 400; //HTTP状态码，如404、202
    public $msg = "参数错误"; //错误的具体信息
    public $errorCode = 10000; //自动错误码


    /**
     * BaseException constructor.
     * 构造函数
     * @param array $params 关联数组
     */
    public function __construct($params = []){

        if(!is_array($params)){
            return ;
        }
        if(array_key_exists("code", $params)){
            $this->code = $params["code"];
        }
        if(array_key_exists("msg", $params)){
            $this->msg = $params["msg"];
        }
        if(array_key_exists("errorCode", $params)){
            $this->errorCode = $params["errorCode"];
        }
    }
}
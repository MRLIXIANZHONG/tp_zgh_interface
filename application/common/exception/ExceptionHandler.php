<?php
/**
 * Created by Ran.
 * Date: 2018/6/25
 * Time: 10:23
 */

namespace app\common\exception;


use think\Exception;
use think\exception\Handle;
use think\Log;
use think\Request;

class ExceptionHandler extends Handle {

    private $code;
    private $msg;
    private $errorCode;


    /**
     * 关于参数中的Exception，使用think\Exception时，会报出一个系统异常，
     * 而这个一样没有规避，我们解决这个问题就是使用\Exception这是RuntimeException和HTTPException的父类
     */
    public function render(\Exception $e) {
        //判断是哪种类型的异常
        if($e instanceof BaseException){
            //如果$e是BaseException的实例，则表示用户操作 --> 自定义异常
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->errorCode = $e->errorCode;
        }else{
            //如果是服务器异常，则返回500错误，并记录日志

            //如果处于调试模式，我们服务器端需要使用tp5的默认错误信息
            if(config("app_debug")){
                return parent::render($e);
            }
            $this->code = 500;
            $this->msg = "sorry，we make a mistake. (^o^)Y";
            $this->errorCode = 999;

            //记录日志
            $this->recordErrorToLog($e);
        }

        $req = Request::instance();
        $res = [
            "code" => $this->errorCode,
            "msg" => $this->msg,
            "request_url" => $req->url()
        ];

        return json($res, $this->code);
    }

    /**
     * 将异常写入到文件
     * @param \Exception $e
     */
    private function recordErrorToLog(\Exception $e){
        //由于在config中我们已经关闭了日志的默认行为，所以需要从新初始化日志
        Log::init([
            "type"=>"File",
            'path'  => LOG_PATH,
            'level' => ["error"]
        ]);
        Log::record($e->getMessage(), "error");
    }
}
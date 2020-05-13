<?php

namespace app\common\controller;
use app\common\exception\FailException;
use app\common\exception\OptionsException;
use app\common\exception\UserException;
use app\common\model\ApiMerchant as ApiMerchantModel;
use think\Config;
use think\Controller;
use think\Exception;

header("Access-Control-Allow-Origin:  *" );
header("Content-type: application/json");
header("Access-Control-Allow-Methods: GET, OPTIONS, POST, PUT");
header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding, X-Requested-With, Origin, Authorizationm, Token");

class Base extends Controller {
    /*
     * 初始化操作
     */
    protected  $uid;
    protected  $new_token;
    public function __construct()
    {
        parent::__construct();
        if (request()->isOptions()) throw new OptionsException();

        $token = Token::decode();

        $uid = $token['data']['uid'];
        $this->uid=$uid;
       // $jwt = $token;
        if($token['data']['new_token']){
            $this->new_token=$token['data']['new_token'];
         //   $jwt = $token['data']['new_token'];
        }


        // header中有无token
        try {

           $merchantData=ApiMerchantModel::where("id",$uid)->where("status",1)->where("mid",Config::get("mid"))->find();
           if(!$merchantData){
               throw new FailException(["code"=>201,"msg"=>"该商户已经被禁用","errorCode"=>404]);
           }


        } catch (\Exception $e) {

            $ret = ["code"=>201, "msg"=>"登陆过期","errorCode"=>300];
            throw new UserException($ret);

        }
//        if (!empty($jwt)) {
//
//            response()->header('aaatoken', $jwt)->send();
//
//        }
    }

}
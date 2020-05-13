<?php


namespace app\api\controller;


use app\common\controller\Basic;
use app\common\exception\FailException;
use app\common\request\UserRequest;
use app\servers\servers\Users as UsersSevers;
use think\Request;

class Users extends Basic
{
    public function register(Request $request){
        $userRequest=new UserRequest($request);

        $userSevers=new UsersSevers();
        $res=$userSevers->register($userRequest->otype,$userRequest->username,$userRequest->pwdword,md5($userRequest->pwdword),$userRequest->siteid,$userRequest->source);

        if($res['@CODE']==1008||$res['@CODE']==10082){
            throw new FailException(["code"=>201,"msg"=>"注册失败","errorCode"=>$res['@CODE']]);
        }

        if($res['@CODE']==10081){
            throw new FailException(["code"=>201,"msg"=>"该账号已经注册！","errorCode"=>$res['@CODE']]);
        }

    }

    public function smscode(Request $request){
        $userRequest=new UserRequest($request);

        $userSevers=new UsersSevers();
        $res=$userSevers->sendSms($userRequest->mobile,$this->ip,$userRequest->otype,$userRequest->siteid); //otype:1登录，5绑定，6注册
        return ['code'=>1000,'msg'=>'发送成功！'];

    }

    public function validatecodebyuser(Request $request){
        $userRequest=new UserRequest($request);

        $userSevers=new UsersSevers();
        $userdata=$userSevers->getUserByMobile($userRequest->mobile,$userRequest->code,$userRequest->otype,$userRequest->siteid,$userRequest->username,$this->ip);
        return ['code'=>1000,'msg'=>'操作成功','userdata'=>$userdata];

    }

    //用户提现
    public function withdrawal(Request $request){

        $userRequest=new UserRequest($request);//验证数据

        $userSevers=new UsersSevers();
        $userdata=$userSevers->set_withd($userRequest->user_id,$userRequest->money,$userRequest->siteid);
        if ($userdata['@CODE'] == 1008 || $userdata['@CODE'] == 100827){
            throw new FailException(["code"=>201,"msg"=>"参数错误","errorCode"=>$userdata['@CODE']]);
        }elseif($userdata['@CODE'] ==100821){
            throw new FailException(["code"=>201,"msg"=>"提现余额不够","errorCode"=>$userdata['@CODE']]);
        }
        return ['code'=>1000,'msg'=>'提现请求已提交等待后台审核','userdata'=>$userdata];
    }


    
    
}
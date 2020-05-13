<?php


namespace app\index\controller;


use app\common\exception\ErrorCode;
use app\common\exception\ParameterException;
use app\servers\servers\Users as UsersSevers;
use think\Controller;
use think\Cookie;
use think\Loader;
use think\Log;
use think\Db;
use think\Request;

class Users extends Controller
{

    public function bind_mobile(){
        return view();
    }

    public function sms_code(Request $request){

        $mobile=$request->param('mobile');
        $ip=$request->ip();
        $validate = Loader::validate("Api");
        if (!$validate->scene("tel_validate")->check(['mobile' => $mobile])) {
            $ret=['code' => 201, 'msg' => $validate->getError(),"errorCode"=>ErrorCode::PARAMETER_ERROR];  //无签名，签名错误
            throw new ParameterException($ret);
        }
        $siteid=Cookie::get("siteid");
        
        $user_severs=new UsersSevers();

        if($siteid){
            $user_severs->is_siteid($siteid);
        }

        $res= $user_severs->sendSms($mobile,$ip,5,$siteid);
        Log::info(json_encode($res));
        return ['code'=>1000,'msg'=>'发送成功！'];

    }

    public function bind_mobile_exe(Request $request){
        $mobile=$request->param('mobile');
        $code=$request->param('code');

        $validate = Loader::validate("Api");

        if (!$validate->scene("mobile_validate")->check(['mobile' => $mobile,'code'=>$code])) {
            $ret=['code' => 201, 'msg' => $validate->getError(),"errorCode"=>ErrorCode::PARAMETER_ERROR];  //无签名，签名错误
            throw new ParameterException($ret);
        }
        //验证手机号验证码

        $user_severs=new UsersSevers();
        $siteid=Cookie::get("siteid");
        if($siteid){
            $user_severs->is_siteid($siteid);
        }

        $is_mode_id=$user_severs->validateCode($mobile,$code,$siteid);


        $ip=$request->ip();
        $wechat_user=Cookie::get("wechat_user");
        $wechat_user=json_decode($wechat_user,true);
        $back_url=Cookie::get("target_url");


        if(empty($siteid)||empty($back_url)||empty($wechat_user)){
            $ret=['code' => 201, 'msg' =>'授权失败，请重新访问授权' ,"errorCode"=>ErrorCode::SERVER_ERROR];  //无签名，签名错误
            throw new ParameterException($ret);
        }

        $url= $user_severs->getUrl($mobile,$siteid,$back_url,$wechat_user,$ip);
        
        Db::table("sms_log")->where('id',$is_mode_id)->update([
           'status'=>1
        ]);

        return ["code"=>"1000","msg"=>"验证成功",'url'=>$url];


    }


    public function wechatbd(Request $request){
       $code= $request->param('code');
        return view('wechatbd', [
            'code'  => $code
        ]);
    }


    //用户提现页面
    public function user_wallet(Request $request){
        $data=$request->param();
        $this->assign('data',$data);
        return view('withdraw');
    }
    //用户提现条件判断
    public function check_user_wallet(Request $request){
        $data=$request->param();
        $user_server=new UsersSevers();
        $result=$user_server->get_withd($data);
        return $result;
    }


}
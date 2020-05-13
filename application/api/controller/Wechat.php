<?php
namespace app\api\controller;


use app\common\exception\ErrorCode;
use app\common\exception\ParameterException;
use EasyWeChat\Factory;
use app\servers\servers\Wechat as WechatServer;
use app\servers\servers\Users as UsersServer;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Loader;
use think\Log;
use think\Request;

class Wechat extends Controller
{
    protected $app;
    protected $oauth;
    public function __construct(){
        $wechatconfig_data=Db::table('wechat_config')->find();


        $config = [
            'app_id' => $wechatconfig_data['app_id'],
            'secret' =>  $wechatconfig_data['secret'],
            'token'=>$wechatconfig_data['token'],
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => '/api/wechat/oauth_callback',
            ],
        ];
        $this->app= Factory::officialAccount($config);
        $this->oauth=$this->app->oauth;
    }

    public function serve(Request $request){
        $oauth =  $this->oauth;
        $back_url=$request->param("back_url");

        $siteid=$request->param("siteid");
        $source=$request->param("source");
        $validate = Loader::validate("Api");

        if (!$validate->scene("back_url_validate")->check(['back_url' => $back_url,'siteid'=>$siteid,'source'=>$source])) {
            $ret=['code' => 201, 'msg' => $validate->getError(),"errorCode"=>ErrorCode::SERVER_ERROR];  //参数为空
            throw new ParameterException($ret);
        }

        $back_url= str_replace("@","&",$back_url);
        if($siteid){
            $usersever=new UsersServer();
            $usersever->is_siteid($siteid);
        }
        Cookie::set('siteid',$siteid);
        Cookie::set('target_url', $back_url);
        Cookie::set('source', $source);

        // 未授权
        $wechat_user=Cookie::get('wechat_user');

        if (empty($wechat_user)){
            $oauth->redirect()->send();
        }


        $user=Cookie::get('wechat_user');

        $user=json_decode($user,true);

        $wechats= new WechatServer();

        $url= $wechats->get_url($user,$siteid,$back_url,$source);

        return $this->redirect($url);

    }

    public function oauth_callback(){

        $oauth = $this->oauth;

        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();

        Cookie::set('wechat_user',json_encode($user));

        $siteid= Cookie::get('siteid');
        $back_url= Cookie::get('target_url');
        $source=Cookie::get('source');
        Log::info('back:'.$back_url);

        $wechats= new WechatServer();

        $url= $wechats->get_url($user,$siteid,$back_url,$source);

        return $this->redirect($url);

    }



}
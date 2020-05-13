<?php
namespace app\servers\servers;

use app\common\exception\ErrorCode;
use app\common\exception\FailException;
use app\common\request\UserRequest;
use app\servers\Servers;
use think\Config;
use think\Cookie;
use think\Db;
use think\Exception;

class Wechat extends Servers
{
    public function get_url($user,$siteid,$back_url,$source){
        if($source==5){
            return $back_url.'&openid='.$user['id'];
        }elseif($source==18){
            $uid=$this->getQuerystr($back_url,'uid');
            $uid=trim($uid)*1;
            $userdata=Db::table("users")->where("openid",$user['id'])->find();
            if($userdata){
                if($userdata['id']!=$uid){
                    return 'index/users/wechatbd?code=1001';  //该微信号已经绑定其他账户
                }else{
                    return 'index/users/wechatbd?code=1000';  //该微信号已经绑定成功
                }
            }

            $userdata2=Db::table("users")->where("id",$uid)->find();

            if(!$userdata2){
                return 'index/users/wechatbd?code=1003';  //用户不存在
            }

             Db::table("users")->where("id",$uid)->update([
                    'openid'=>$user['id'],
                    'nickname'=>$user['nickname'],
                    'avatar_wechat'=>$user['avatar'],
             ]);

            return 'index/users/wechatbd?code=1000';  //该微信号已经绑定成功
        }elseif($source==8){
            $reurl=$back_url;
            $url=parse_url ($back_url);
            $back_url='http://'.$url['host'];//获取域名
            $back_url=$back_url.'/m/user/setlogin.html?source='.$source.'&reurl='.$reurl;
            Cookie::set('target_url', $back_url);
        }
        $userdata=Db::table('user_weixin')->where("openid",$user['id'])->find();

        if(!empty($userdata)){

            $username=$userdata['username'];

            Db::startTrans();
            try {
                $userdata = Db::table('T_Users')->where("username", $username)->select();
                if (!$userdata) {
                    Db::rollback();
                }

                Db::table('T_Users')->where("username", $username)->update([
                    'nick' => $user['nickname'],
                    'sex' => $user['original']['sex'],
                    'userface' => $user['avatar'],
                    'address' => $user['original']['country'] . $user['original']['province'] . $user['original']['city']
                ]);

            }catch (\Exception $exception){
                Db::rollback();
            }

            Db::commit();

            $_token=Config::get('_token');

            $sign= UserRequest::get_sign([$username,$siteid],'login');

            return $back_url.'&username='.$username.'&siteid='.$siteid.'&sign='.$sign;

        }else{

            return '/index/users/bind_mobile';

        }
    }
}
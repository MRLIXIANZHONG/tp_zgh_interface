<?php
namespace app\servers\servers;

use app\common\exception\ErrorCode;
use app\common\exception\FailException;
use app\common\request\UserRequest;
use app\servers\Servers;
use app\servers\servers\Users as UsersSevers;
use fast\Random;
use think\Cache;
use think\Db;
use think\Log;

class Users extends Servers
{
   public function register($otype,$username,$pwdword,$md5pwd,$siteid,$source,$rolename='',$openid='',$qqopenid='',$unionid='',$email='',$tel='',$roleimg='',$type=0,$regip='',$lifeaddr='',$sex=1,$birthday=''){
      
       Db::query("call PH_User_UserReg_I('".$username."','".$pwdword."','".$md5pwd."',".
           "?".",".
           "?".",".
           "?".",".
           "?".",".
           "?".",".
           "?".",".
           "?".",".
          $siteid.",".
           $type.",".
           "?".",".
          $otype.",".
           "?".",".
           "?".",".
           "?".",".
           $source.","."
           @USERID,
           @CODE)",[$rolename,$qqopenid,$openid,$unionid,$email,$tel,$roleimg,$regip,$lifeaddr,$sex,$birthday]);
       $res=Db::query("select @USERID,@CODE");
       return $res[0];

   }


   public function getUrl($mobile,$siteid,$back_url,$wechat_user,$ip){

       $is_bd=Db::table("user_bd_mobile")->where("tel",$mobile)->find();
       $userSevers=new UsersSevers();
       if(!empty($is_bd)){     //如果已经绑定
           $username=$is_bd['username'];
           $sign= UserRequest::get_sign([$username,$siteid],'login');

           $wxuser=Db::table("user_weixin")->where("username",$username)->find();   //查看微信绑定

           if(!empty($wxuser)){   //已经绑定直接返回
               $url= $back_url.'&username='.$username.'&siteid='.$siteid.'&sign='.$sign;
           }else{    //未绑定开始绑定
        
               $userdata=Db::table("T_Users")->where("username",$username)->find();
               if($userdata){
                    $pwd=$userdata['password'];
                    $md5pwd=$userdata['pwd'];
               }else{
                   Db::table("user_bd_mobile")->where("tel",$mobile)->delete();
                   $username='wxid'.Random::numeric(4).time();
                   $pwd=Random::alnum(8);
                   $md5pwd=md5($pwd);
               }

               try {
               $res=$userSevers->register(4,$username,$pwd,$md5pwd,$siteid,4,$wechat_user['nickname'],$wechat_user['id'],'','','',$mobile,$wechat_user['avatar'],0,$ip,
                   $wechat_user['original']['country'].$wechat_user['original']['province'].$wechat_user['original']['city'],$wechat_user['original']['sex'],'');
               } catch (\Exception $e) {
                   throw new FailException(["code"=>201,"msg"=>$e->getMessage(),"errorCode"=>ErrorCode::SERVER_ERROR]);
               }
               if($res['@CODE']!=1000){
                   throw new FailException(["code"=>201,"msg"=>"注册失败".$res['@CODE'],"errorCode"=>$res['@CODE']]);
               }
               $url= $back_url.'&username='.$username.'&siteid='.$siteid.'&sign='.$sign;
           }

       }else{

           $username='wxid'.Random::numeric(4).time();
           $pwd=Random::alnum(8);
           $md5pwd=md5($pwd);

           $sign=UserRequest::get_sign([$username,$siteid],'login');

           try {

               $res=$userSevers->register(4,$username,$pwd,$md5pwd,$siteid,4,$wechat_user['nickname'],$wechat_user['id'],'','','',$mobile,$wechat_user['avatar'],0,$ip,
                   $wechat_user['original']['country'].$wechat_user['original']['province'].$wechat_user['original']['city'],$wechat_user['original']['sex'],'');

           } catch (\Exception $e) {
               throw new FailException(["code"=>201,"msg"=>$e->getMessage(),"errorCode"=>ErrorCode::SERVER_ERROR]);
           }
           if($res['@CODE']!=1000){
               throw new FailException(["code"=>201,"msg"=>"注册失败".$res['@CODE'],"errorCode"=>$res['@CODE']]);
           }



           $url= $back_url.'&username='.$username.'&siteid='.$siteid.'&sign='.$sign;
       }
       return $url;

   }

   public function sendSms($mobile,$ip,$otype=5,$siteid=0){  //otype:1登录，5注册
     
       if($otype==1){
         $is_tel=Db::table("user_bd_mobile")->where('tel',$mobile)->find();
         if(empty($is_tel)){
             throw new FailException(["code"=>201,"msg"=>"该手机号尚未注册，请先注册","errorCode"=>ErrorCode::SERVER_ERROR]);
         }
       }

       if(Cache::get("sms_".$mobile)){
           throw new FailException(["code"=>201,"msg"=>"已发送，请稍后重试","errorCode"=>ErrorCode::SERVER_ERROR]);
       }
       Cache::set("sms_".$mobile,1);

       $is_sms_count=Db::table("sms_count")->where('siteid',$siteid)->find();

       if($is_sms_count['status']==0){
           Cache::rm("sms_".$mobile);
           throw new FailException(["code"=>201,"msg"=>"该站点短信已使用完，请联系平台","errorCode"=>ErrorCode::SERVER_ERROR]);
       }


       if($is_sms_count['msg_count']<=0){
           Cache::rm("sms_".$mobile);
           throw new FailException(["code"=>201,"msg"=>"该站点短信已使用完，请联系平台","errorCode"=>ErrorCode::SERVER_ERROR]);
       }


       $code=Random::numeric(6);
       $is_mode=Db::table("sms_log")->where('mobile',$mobile)->where('siteid',$siteid)->order("addtime desc")->find();
       if($is_mode){
            $xc=time()*1-strtotime($is_mode['addtime'])*1;
            if($xc<60){
                Cache::rm("sms_".$mobile);
                throw new FailException(["code"=>201,"msg"=>"已发送，请等待".(60-$xc)."秒后重试","errorCode"=>ErrorCode::SERVER_ERROR]);

            }
       }


       $request['appName'] = "WebApp";
       $request['Param']= '"phone":'.$mobile.',"otype":"10","siteID":"9998","tag":"【聚咖盟手机快速注册】","Content":"你的注册短信验证码为：'.$code.'","sign":"【城市通】","fromType":"1","api":"2","ip":"'.$ip.'"';
       $request['Method'] = "SmsSendAPI_SendSmsCode";//需传
       $request['version'] = "4.8";
       $request['requestTime']=date("Y-m-d H:i:s");
       $request['customerID']=8006;
       $request['customerKey'] = md5('32csd44fgdwertgyusdfsd1ewwejhhalsc1z5aWea2=' . $request['Method'] . $request['requestTime']);//普通MD5加密
       $body = '{"appName":"' . $request['appName'] . '","Param":{' . $request['Param'] . '},"requestTime":"' . $request['requestTime'] . '","customerKey":"' . $request['customerKey'] . '","Method":"' . $request['Method'] . '","Statis":{"SystemNo":"1"},"customerID":' . $request['customerID'] . ',"version":"' . $request['version'] . '"}';
       $post_arr = array('param'=>$body);
       $json = httpRequest('http://smssendapi.bccoo.cn/appserverapi.ashx',"POST", $post_arr);//调试请求连接
       Log::info($json);
       $res=json_decode($json,true);
       if(empty($res)){
           Cache::rm("sms_".$mobile);
           throw new FailException(["code"=>201,"msg"=>"发送失败，请重试","errorCode"=>ErrorCode::SERVER_ERROR]);
       }

       if(empty($res['MessageList'])){
           Cache::rm("sms_".$mobile);
           throw new FailException(["code"=>201,"msg"=>"发送失败，请重试","errorCode"=>ErrorCode::SERVER_ERROR]);

       }

       if($res['MessageList']['code']!=1000){
           Cache::rm("sms_".$mobile);
           throw new FailException(["code"=>201,"msg"=>$res['MessageList']['message'],"errorCode"=>ErrorCode::SERVER_ERROR]);

       }

       //TODO  调取短信
        Db::query("call PH_Sms_SendSMS(".$siteid.",'".$mobile."',".$code.",'".$ip."',@STATE)");
       $eres=Db::query("select @STATE");
       if($eres[0]['@STATE']!=1000){
           Cache::rm("sms_".$mobile);
           throw new FailException(["code"=>201,"msg"=>'数据库插入失败'.$eres[0]['@STATE'],"errorCode"=>ErrorCode::SERVER_ERROR]);
       }

       Cache::rm("sms_".$mobile);
       return $res;
   }


   public function validateCode($mobile,$code,$siteid=0){
       $is_mode=Db::table("sms_log")->where('mobile',$mobile)->where('status',0)->where('code',$code)->order("addtime desc")->find();
       if(!$is_mode){
           throw new FailException(["code"=>201,"msg"=>"验证码错误，请重试","errorCode"=>ErrorCode::SERVER_ERROR]);
       }
       $xc=time()*1-strtotime($is_mode['addtime'])*1;
       if($xc>1000){
           throw new FailException(["code"=>201,"msg"=>"验证码已超时，请重新发送","errorCode"=>ErrorCode::SERVER_ERROR]);
       }
        return $is_mode['id'];
   }


    public function getUserByMobile($mobile,$code,$otype=1,$siteid=0,$username='',$ip=''){    //otype:1登录，5绑定，6注册
        
        $is_mode_id=$this->validateCode($mobile,$code,$siteid);

        if($otype==1){
            $usernames=Db::table("user_bd_mobile")->where('tel',$mobile)->value('username');
            if(empty($usernames)){
                throw new FailException(["code"=>201,"msg"=>"不存在用户","errorCode"=>ErrorCode::SERVER_ERROR]);
            }
            $userdata= Db::table("T_Users")->where("username",$usernames)->field("username,siteid,sex,name,userface")->find();
            $userdata['mobile']=$mobile;
        }

        if($otype==5){

            $is_mobile=Db::table("user_bd_mobile")->where('tel',$mobile)->value('username');
            if(!empty($is_mobile)){
                throw new FailException(["code"=>201,"msg"=>"该手机号已经绑定！","errorCode"=>ErrorCode::SERVER_ERROR]);
            }
            $isuserdata= Db::table("T_Users")->where("username",$usernames)->find();
            if(empty($isuserdata)){
                throw new FailException(["code"=>201,"msg"=>"该账号不存在！","errorCode"=>ErrorCode::SERVER_ERROR]);
            }
            $res =$this->register($otype,$username,$isuserdata['password'],$isuserdata['pwd'],$siteid,$otype,'','','','','',$mobile,'',0,$ip,'',0,''); #TODO
            if($res['@CODE']==100850){
                throw new FailException(["code"=>201,"msg"=>"该手机号已经绑定一个账户！","errorCode"=>$res['@CODE']]);
            }
            if($res['@CODE']!=1000){
                throw new FailException(["code"=>201,"msg"=>"注册失败！","errorCode"=>$res['@CODE']]);
            }
            $userdata= Db::table("T_Users")->where("id",$res['@USERID'])->field("username,siteid,sex,name,userface")->find();

        }

        if($otype==6){
              $username='sid'.Random::numeric(4).time();
              $pwd=Random::alnum(8);
              $is_mobile=Db::table("user_bd_mobile")->where('tel',$mobile)->value('username');
              if(!empty($is_mobile)){
                  throw new FailException(["code"=>201,"msg"=>"该手机号已经绑定！","errorCode"=>ErrorCode::SERVER_ERROR]);
              }
//              $isuser= Db::table("T_Users")->where("username",$username)->field("username,siteid,sex,name,userface")->find();
//              if(!empty($isuser)){
//                  throw new FailException(["code"=>201,"msg"=>"该账户已经存在！","errorCode"=>ErrorCode::SERVER_ERROR]);
//              }
              $res =$this->register($otype,$username,$pwd,md5($pwd),$siteid,$otype,'','','','','',$mobile,'',0,$ip,'',0,''); #TODO
              if($res['@CODE']==100850){
                  throw new FailException(["code"=>201,"msg"=>"该手机号已经绑定一个账户！","errorCode"=>$res['@CODE']]);
              }
              if($res['@CODE']!=1000){
                    throw new FailException(["code"=>201,"msg"=>"注册失败！","errorCode"=>$res['@CODE']]);
              }

            $userdata= Db::table("T_Users")->where("id",$res['@USERID'])->field("username,siteid,sex,name,userface")->find();

        }


        Db::table("sms_log")->where('id',$is_mode_id)->update([
            'status'=>1
        ]);
        return $userdata;
    }

    public function is_siteid(Int $id){
        $is_siteid=Db::table('site_3e21')->where('siteid',$id)->find();
        if(empty($is_siteid)){
            $ret=['code' => 201, 'msg' => '该站点不存在',"errorCode"=>ErrorCode::SERVER_ERROR];
            throw new FailException($ret);
        }
    }


    //前台用户提现页面
    public function get_withd($data){
       //验证参数
        $user=Db::table("T_Users")->where('id',$data['user_id'])->where('state',1)->find();
        if(!$user){
            return ["code"=>202,"msg"=>"会员不存在，或已禁用","errorCode"=>ErrorCode::SERVER_ERROR];
        }
        //验证签名
        $ssign=md5($data['user_id'].$data['site_id'].'get_withdraw_url');
        if ($ssign !=$data['sign']){
            return ["code"=>202,"msg"=>"签名错误","errorCode"=>ErrorCode::SERVER_ERROR];
        }


        //1.查询是否绑定微信
        $wixing=Db::table('user_weixin')->where('username',$user['username'])->find();
        if ($wixing){

            //2.查询用户有没有提现配置
            $user_witdr=Db::table('T_UserWithdrawals_Set')->where('UserID',$data['user_id'])->find();
            if ($user_witdr){
                //判断是否有openid,没有就绑定openid
                if (!$user_witdr['OpenId']){
                    Db::table('T_UserWithdrawals_Set')->where('UserID',$data['user_id'])->update(array('OpenId'=>$wixing['openid']));
                }

            }else{
                //新建一个配置
                Db::table('T_UserWithdrawals_Set')->insertGetId([
                    'SiteID'=>$user['siteid'],
                    'UserID'=>$user['id'],
                    'UserName'=>$user['username'],
                    'OpenId'=>$wixing['openid'],
                ]);
            }

        }else{
            //拼接绑定地址
           // $back_url='http://'.$_SERVER['HTTP_HOST'].'/?site_id='.$data['site_id'].'&user_id='.$data['user_id'].'&sign='.$data['sign'];
            $back_url='http://auth.hd3360.cn/index/users/user_wallet/serve?site_id='.$data['site_id'].'&user_id='.$data['user_id'].'&sign='.$data['sign'];
            //$back_url=urlencode($back_url);
            $url=url('/api/wechat/serve',array('back_url'=>$back_url,'siteid'=>$data['site_id'],'source'=>8));

             return ['code' => 201, 'msg' => '没有绑定微信','url'=>$url,"errorCode"=>ErrorCode::SERVER_ERROR];


        }
        //查询会员余额
        $blance=Db::table('T_User_Wallet')->where('UserID',$data['user_id'])->value('balance');
        return ['code' => 200, 'msg' => '成功',"blance"=>$blance];
    }

    //用户提现
    public function set_withd($user_id,$money,$site_id){
       //验证用户钱够不够
        $user_money=Db::table('T_User_Wallet')->where('UserID',$user_id)->value('balance');
        if ($user_money <$money){
            $ret=['code' => 201, 'msg' => '用户余额小于提现金额',"errorCode"=>ErrorCode::SERVER_ERROR];
            throw new FailException($ret);
        }
        //查询用户信息
        $user_info=Db::table('T_Users')->where('id',$user_id)->find();
        //生成订单号
        $order_number=time().rand(100000,999999);
        //查询电话号码
        $tel=Db::table('user_bd_mobile')->where('username',$user_info['username'])->value('tel');
        //提现描述
        $meno='用户提现';
        $str="call PH_UserWithdrawals_WU($site_id,$user_id,1,'".$user_info['username']."',$money,'".$order_number."','".$tel."','".$meno."',0,0,0,0,@USERID,@CODE)";
        //调用存储过程添加提现记录
        Db::query($str);
        $res=Db::query("select @USERID,@CODE");
        return $res[0];
        /*'IN `SITE_ID` int(8),IN `USER_ID` int(8),IN `Source` int(8),IN `USER_NAME` varchar(255),IN `MONEY` float(8,2) ,IN  `ORDER_NUMBER` varchar(255),IN `TEL` varchar(255),IN `Memo` varchar(255),IN  `CUT_HEAD` float(8,2),IN  `CUT_ALLY` float(8,2),IN  `SCALE_HEAD` float(8,2),IN `SCALE_ALLY` float(8,2),OUT `CODE` int,OUT `INFOID` int';
        '1507,8257,1,wxidnxz3hbdw,2,2100212012111,15683408240,提现,0,0,0,0,@CODE,@INFOID';*/

    }


}
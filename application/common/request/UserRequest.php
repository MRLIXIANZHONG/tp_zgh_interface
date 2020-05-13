<?php
namespace app\common\request;

use app\common\exception\FailException;
use app\common\exception\ForbiddenException;
use app\common\exception\ParameterException;
use app\common\exception\ErrorCode;
use app\servers\servers\Users as UsersServers;
use think\Config;
use think\Loader;
use think\Request;

class UserRequest extends Request
{
  public  $username;
  public  $pwdword;
  public  $mobile;
  public  $siteid;
  public  $otype;
  public  $validate;
  public  $source;
  public  $code;
  public  $user_id;
  public  $money;

  public function __construct(Request $request)
  {
      parent::__construct();
      $this->username=$request->post('username');
      $this->pwdword=$request->post('pwdword');
      $this->mobile=$request->post('mobile');
      $this->siteid=$request->post('siteid');
      $this->otype=$request->param('otype');
      $this->sign=$request->param('sign');
      $this->source=$request->post('source');
      $this->code=$request->post('code');
      $this->user_id=$request->post('user_id');
      $this->money=$request->post('money');

      $action='rule_'.$this->capital_to_underline($request->action());
      $this->validate = Loader::validate("Api");
      $this->$action();
  }

  private function rule_register(){

      if (!$this->validate->scene("common_validate")->check(['otype' => $this->otype])) {
          $ret=['code' => 201, 'msg' => $this->validate->getError(),"errorCode"=>ErrorCode::PARAMETER_ERROR];  
          throw new ParameterException($ret);
      }

      if (!$this->validate->scene("register_validate")->check(['username' => $this->username,'password'=>$this->pwdword,'siteid'=>$this->siteid])) {
          $ret=['code' => 201, 'msg' => $this->validate->getError(),"errorCode"=>ErrorCode::PARAMETER_ERROR];
          throw new ParameterException($ret);
      }

      $user_server=new UsersServers();
      $user_server->is_siteid($this->siteid);

      self::validate_sign([$this->username,$this->pwdword,$this->otype,$this->siteid],$this->sign,'register');

  }

    //验证提现
    private function rule_withdrawal(){
        if (!$this->validate->scene("wallet_validate")->check(['user_id' => $this->user_id,'money'=>$this->money,'siteid'=>$this->siteid])) {
            $ret=['code' => 201, 'msg' => $this->validate->getError(),"errorCode"=>ErrorCode::PARAMETER_ERROR];
            throw new ParameterException($ret);
        }


        $user_server=new UsersServers();
        $user_server->is_siteid($this->siteid);

        self::validate_sign([$this->user_id,$this->money,$this->siteid],md5($this->sign),'Withdrawal');

    }


  private function rule_smscode(){

      if (!$this->validate->scene("common_validate")->check(['otype' => $this->otype])) {  //otype:1登录，5注册
          $ret=['code' => 201, 'msg' => $this->validate->getError(),"errorCode"=>ErrorCode::PARAMETER_ERROR]; 
          throw new ParameterException($ret);
      }

      if (!$this->validate->scene("tel_validate")->check(['mobile' => $this->mobile])) {
          $ret=['code' => 201, 'msg' => $this->validate->getError(),"errorCode"=>ErrorCode::PARAMETER_ERROR];
          throw new ParameterException($ret);
      }

      if($this->siteid){
          $user_server=new UsersServers();
          $user_server->is_siteid($this->siteid);
      }

      self::validate_sign([$this->otype,$this->mobile,$this->siteid],$this->sign,'smd_code');


  }
  private function rule_validatecodebyuser(){

        if (!$this->validate->scene("common_validate")->check(['otype' => $this->otype])) {
            $ret=['code' => 201, 'msg' => $this->validate->getError(),"errorCode"=>ErrorCode::PARAMETER_ERROR];  //无签名，签名错误
            throw new ParameterException($ret);
        }

        if (!$this->validate->scene("mobile_validate")->check(['mobile' => $this->mobile,'code' => $this->code])) {
            $ret=['code' => 201, 'msg' => $this->validate->getError(),"errorCode"=>ErrorCode::PARAMETER_ERROR];
            throw new ParameterException($ret);
        }

        $user_server=new UsersServers();
        if($this->otype==5){

            if (!$this->validate->scene("register_validate")->check(['username' => $this->username,'siteid'=>$this->siteid])) {
                $ret=['code' => 201, 'msg' => $this->validate->getError(),"errorCode"=>ErrorCode::PARAMETER_ERROR];
                throw new ParameterException($ret);
            }
            if($this->siteid){

                $user_server->is_siteid($this->siteid);
            }
            self::validate_sign([$this->otype,$this->mobile,$this->code,$this->username,$this->siteid],$this->sign,'validate_code');
        }else{
            if($this->siteid){
                $user_server->is_siteid($this->siteid);
            }
            self::validate_sign([$this->otype,$this->mobile,$this->code,$this->siteid],$this->sign,'validate_code');
        }

    }



  public static function validate_sign(Array $data,String $sign,$code){
      if(empty($data)){
        $ret=['code' => 201, 'msg' => '该参数不能为空',"errorCode"=>ErrorCode::PARAMETER_ERROR];
        throw new ParameterException($ret);
      }
      $str = join("", $data);
      if(md5($code.$str.Config::get('_token'))!=$sign){
          $ret=['code' => 201, 'msg' =>'签名验证失败'.md5($code.$str.Config::get('_token')),"errorCode"=>ErrorCode::TOKEN_ERROR];
          throw new ForbiddenException($ret);
      }
      return true;
  }

    public static function get_sign(Array $data,$code=''){
        if(empty($data)){
            $ret=['code' => 201, 'msg' => '该参数不能为空',"errorCode"=>ErrorCode::PARAMETER_ERROR];
            throw new ParameterException($ret);
        }
        $str = join("", $data);

        return md5($code.$str.Config::get('_token'));
    }

    //大写转换成小写加下划线
  private function capital_to_underline($str){
        $temp_array = array();
        for($i=0;$i<strlen($str);$i++){
            $ascii_code = ord($str[$i]);
            if($ascii_code >= 65 && $ascii_code <= 90){
                if($i == 0){
                    $temp_array[] = chr($ascii_code + 32);
                }else{
                    $temp_array[] = '_'.chr($ascii_code + 32);
                }
            }else{
                $temp_array[] = $str[$i];
            }
        }
        return implode('',$temp_array);
    }





}
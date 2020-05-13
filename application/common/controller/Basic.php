<?php
namespace app\common\controller;
use app\common\exception\ForbiddenException;
use app\common\exception\OptionsException;
use app\common\exception\ParameterException;
use app\common\exception\ErrorCode;
use think\Controller;
use think\Loader;

header("Access-Control-Allow-Origin:  *" );
header("Content-type: application/json");
header("Access-Control-Allow-Methods: GET, OPTIONS, POST, PUT");
header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding, X-Requested-With, Origin, Authorizationm, Token");
class Basic extends Controller
{
    protected  $ip;
    protected  $sign;
  public function __construct()
  {
      parent::__construct();
      if(request()->isOptions()) throw new OptionsException();  //检测预请求
      if(!request()->isPost()) throw new ForbiddenException(['code'=>201,'msg'=>"非法请求",'errorCode'=>ErrorCode::SERVER_ERROR]);
      $sign=request()->param('sign');
      $validate = Loader::validate("Api");
      if (!$validate->scene("sign_validate")->check(['sign' => $sign])) {
         $ret=['code' => 201, 'msg' => $validate->getError(),"errorCode"=>ErrorCode::TOKEN_ERROR];  //无签名，签名错误
         throw new ParameterException($ret);
      }

      if (!$validate->scene("ip_validate")->check(['ip' => request()->ip()])) {
          $ret=['code' => 201, 'msg' => $validate->getError(),"errorCode"=>ErrorCode::IP_IS_EXPIRED];
          throw new ForbiddenException($ret);
      }

      $this->ip=request()->ip();
      $this->sign=$sign;

  }
}
<?php
namespace app\common\controller;
use \Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use think\Cache;
use app\common\exception\TokenException;

class Token {

    /**
     * 获取Token
     * @param $uid ?uid=2
     * @return string
     */
    public static function getToken($uid, $time,$key="apimerchant") {

        $payload =  [
            'iss'=>'apimerchant.run',
            'iat'=> $time, // 签发时间
            'uid' => $uid,
            'nbf' => $time+10 , //在什么时间之后该jwt才可用
//            'exp' => time() + 7200, //过期时间-2小时
            'exp' => $time + 7200, //过期时间-2小时
        ];

        $jwt = JWT::encode($payload,$key);

        return $jwt;
    }

    /**
     * @param string $key
     * @return mixed
     * @throws TokenException
     */
    public static function decode($key="apimerchant") {

        $jwt = request()->header('token');


        if (empty($jwt)) {
            $ret = ["code"=>201,"errorCode"=>300, "msg"=>"Token不能为空"];
            throw new TokenException($ret);
        }
        $res['code'] = 200;
        $res['msg'] = "Token正确";

        JWT::$leeway = 60;
        try {
            $decoded = JWT::decode($jwt, $key, ['HS256']);
            $arr = (array)$decoded;
            $res['data'] = $arr;
            $res['data']['new_token']=0;
        } catch (ExpiredException $e) {
            try {

                // token过期
                sleep(rand(1,5)/100);
                // 刷新token
                $token = Token::getToken($e->getCode(), time(),$key);
                // 设置header

                header('token:'.$token);

                $res['data'] = ['uid'=>$e->getCode(), 'new_token'=>$token];

                // 将旧token存储在redis中,30秒内再次请求是有效的

                Cache::set('token_blacklist:'.$jwt, $token, 15811200);

            } catch (\Exception $es) {
                if($token == Cache::get('token_blacklist:'.$jwt)){
                    header('token:',$token); // 给当前的请求设置新的token,以备在本次请求中需要调用用户信息
                    $res['data'] = ['uid'=>$es->getCode(), 'new_token'=>$token];
                }else{
                    throw new TokenException(['code'=>201,'msg'=>'token过期','errorCode'=>300]);
                }
            }
            } catch(\Firebase\JWT\SignatureInvalidException $se) {  //签名不正确
            throw new TokenException(["code"=>201,"msg"=>"签名不正确","errorCode"=>302]);

            }catch(\Firebase\JWT\BeforeValidException $be) {  // 签名在某个时间点之后才能用
            throw new TokenException(["code"=>201,"msg"=>"签名在某个时间点之后才能用","errorCode"=>300]);

            }catch(\Exception $ee) {  //其他错误
            throw new TokenException(["code"=>201,"msg"=>$ee->getMessage(),"errorCode"=>300]);
            }


        return $res;
    }

    /**
     * 根据旧的Token获取数据
     * @return array
     * @throws TokenException
     */
    public static function olderToken() {
        $jwt = request()->header('Authorization');
        if (empty($jwt)) {
            return ["code"=>201,"errorCode"=>300,"msg"=>"Token不能为空"];

        }
        $decoded = JWT::Edecode($jwt, config('token_secret'), ['HS256']);
        $arr = (array)$decoded;
        return $arr;
    }

}
<?php
namespace app\common\validate;

use think\Validate;

class Api extends Validate
{
    protected $rule = [
        ['sign'  ,'require','校验值sign错误'],
        ['ip'  ,'require|ip','非法请求|非法请求'],
        ['apikey'  ,'require','缺少重要参数'],
        ['back_url','require|url','缺少重要参数,无法跳转|参数错误，无法跳转'],
        ['siteid'  ,'require|number','缺少重要参数siteid|参数siteid类型错误'],
        ['source','require|number','缺少重要参数source|source格式不对'],
        ['mobile'  ,'require|max:11|/^1[1-9]{1}[0-9]{9}$/','缺少重要参数|手机号格式不正确|手机号格式不正确'],
        ['code'  ,'require|min:6','请填写验证码|验证码不得少于6位数'],
        ['username'  ,'require|min:8|alphaNum','请输入用户名|用户名长度不能少于8个字符|用户名必须为由字母和数字构成'],
        ['password'  ,'require|alphaNum|min:8','请输入密码|密码必须为由字母和数字构成|密码长度不能少于8个字符'],
        ['otype','require','缺少重要参数'],
        ['user_id','require','缺少重要参数'],
        ['money','require','缺少重要参数']

    ];

    protected $scene = [
        'back_url_validate'=>    ['back_url','siteid','source'],
        'mobile_validate'=>    ['mobile','code'],
        'tel_validate'=>    ['mobile'],
        'sign_validate'       =>  ['sign'],
        'ip_validate'       =>  ['ip'],
        'apikey_validate'       =>  ['apikey'],
        'common_validate'=>['otype'],
        'register_validate' =>['username','password','siteid'],
        'wallet_validate' =>['user_id','money','siteid']

    ];
}
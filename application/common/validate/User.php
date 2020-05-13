<?php
namespace app\common\validate;
use think\Validate;

class User extends Validate
{
    protected $rule = [

        ['username'  ,'require|min:8|alphaNum','请输入用户名|用户名长度不能少于8个字符|用户名必须为由字母和数字构成'],
        ['password'  ,'require|alphaNum|min:8','请输入密码|密码必须为由字母和数字构成|密码长度不能少于8个字符'],
        ['siteid', 'require|integer','缺少站点参数|站点参数类型不正确'],
        ['otype','require','缺少重要参数']

    ];

    protected $scene = [
        'common'=>['otype'],
        'register' =>['username','password','siteid']
    ];
}
<?php
namespace app\common\validate;

use think\Validate;

class Common extends Validate
{
    protected $rule = [
        ['id'  ,'integer|require|gt:0','参数错误|参数错误|参数错误'],
        ['mobile'  ,'number|require|gt:0','手机参数错误|手机参数错误|手机参数错误'],
        ['cid'  ,'integer','参数错误'],
        ['address','require','请填写地址'],
        ['name'  ,'require','缺少重要参数'],
        ['lat'  ,'number|require','参数错误|参数错误'],
        ['lon'  ,'number|require','参数错误|参数错误'],
        ['raidus'  ,'number|require','参数错误|参数错误'],
        ['date','date','日期格式错误'],
    ];

    protected $scene = [
        'common'       =>  ['id'],
        'name'       =>  ['name'],
        'enroll'      =>  ['name','mobile'],
        'commonbynum'       =>  ['cid'],
        'nearby'       =>  ['lat','lon','raidus'],
        'qiandao'      => ['address','id','lat','lon'],
        'date'       =>    ['date']
    ];
}
<?php
return [
    'title' => "卡了惠APi接口文档",  //文档title
    'version'=>'1.0.1', //文档版本
    'copyright'=>'Powered By Wangchunjun', //版权信息
    'password' => 'klh123', //访问密码，为空不需要密码
    //静态资源路径--默认为云上路径，解决很多人nginx配置问题
    //可将assets目录拷贝到public下面，具体路径课自行配置
    'static_path' => '',
    'controller' => [
        //需要生成文档的类
        'app\\merchant\\controller\\Apimerchant',
        'app\\merchant\\controller\\Login',
    ],
    'filter_method' => [
        //过滤 不解析的方法名称
        '_empty'
    ],
    'return_format' => [
        //数据格式
        'code' => "200/300/301/302/404",
        '[code:200]'=>"成功",
        '[code:300]'=>"未登录或者登陆失效",
        '[code:301]'=>"token失效",
        '[code:302]'=>"账户不存在或者账户已被删除",
        '[code:303]'=>"密码错误",
        '[code:304]'=>"随msg变化的错误验证正则错误",
        '[code:404]'=>"非法操作，不符合逻辑操作",
        'msg' => "提示信息",


    ],
    'public_header' => [
        //全局公共头部参数
        //如：['name'=>'version', 'require'=>1, 'default'=>'', 'desc'=>'版本号(全局)']
    ],
    'public_param' => [
        //全局公共请求参数，设置了所以的接口会自动增加次参数
        //如：['name'=>'token', 'type'=>'string', 'require'=>1, 'default'=>'', 'other'=>'' ,'desc'=>'验证（全局）')']
    ],
];

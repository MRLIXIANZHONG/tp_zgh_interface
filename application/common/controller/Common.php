<?php
namespace app\common\controller;
use think\Controller;


header('Access-Control-Allow-Headers:*');
header('Access-Control-Allow-Origin:*');
header("Access-Control-Max-Age: 86400");
header("Access-Control-Allow-Methods:OPTIONS,GET, PUT, POST, DELETE");

class Common extends  Controller
{
    public function _initialize() {

    }





}
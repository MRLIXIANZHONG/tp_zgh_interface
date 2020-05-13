<?php
namespace app\servers;

use think\Controller;

class Servers extends Controller
{
	public function getQuerystr($url,$key){
    $res = '';
    $a = strpos($url,'?');
    if($a!==false){
        $str = substr($url,$a+1);
        $arr = explode('&',$str);
        foreach($arr as $k=>$v){
            $tmp = explode('=',$v);
            if(!empty($tmp[0]) && !empty($tmp[1])){
                $barr[$tmp[0]] = $tmp[1];
            }
        }
    }
    if(!empty($barr[$key])){
        $res = $barr[$key];
    }
    return $res;
}	
}
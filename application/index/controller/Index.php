<?php
namespace app\index\controller;
//use fast\Random;
//use think\Hook;
//use app\common\validate\Common;
use app\common\controller\Openssl as OpensslBase;
use app\common\exception\FailException;
use app\common\model\ApiMerchant;
use app\common\model\PartConfig;
use jianyan\excel\Excel;
use think\Db;
use think\Exception;
use think\Request;
use think\Cache;
use \Firebase\JWT\JWT;

class Index
{   
    public function index(){

         die();
    }
    public function index3333(){

//        echo date('Y-m-t 23:59:59', strtotime('-1 month'));
//        die();
//        $va='MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCG9Xr9OL73aY10cM+WX20BLPY6vx3pfnAyOYYb5du/ctogdmlZTV2LP+Qv/S2GYqJbOMkWqb2MtHdp8T1Cz2uvIAD4l6aRGNT0pq21aohfFlM/utjmusRz+yiWNtcQXgCg+uJjMQkTYSPBPhyQKYmhlbdoK/XI0jHfef9CLj0iGH6ysw4cxJSMMgBQiNs+RPMK0GWIk2qQatrr3Q3kcvo6nJVrQ7AFznCRCDRWM1wM0Nq3BIbbNd4hK73cEdP6l+B+zJPduRZQMU/HLmjeq8dWfVM7tMnRsS2+DJKEZb/oA4t21v34fF5k1xROeZVz48sy80xbeaYus1HuslIY5qpdAgMBAAECggEAQj2k8zryCG/nfTqNuUn3L7eI18bMgLmNiilP2MuWcz6FL5/PD4T4oAtpDeDfgnFRLSMPGC+SGMjPP9Gndu5l9Vvo/hs5J0if5cEVy9CRRNKpY8NrCLytBhxFjI04PBFXj6v0iCkeQkPQj93TmTK6GfRpRBknlz0MO5senbYiTWQo6O/4HsoHTjrGwxl+6UYeCtxE+fXbwYlmveJpGBrZgrI2R73eXfQH6TjpHlvQ4MeLGqHRDzFYLA14pzeHn+04ALU0SyCfx34hUepq/0B44Q7/0lV3FotblIiC33UvizcushQo5tudscmMkrkCtvR7mDYu07xY1JJey8gjSCLN4QKBgQD1WaAmxNt/EG0BGsL8TOjTT4qUrwsZV3L9UxQH+9k5Vyeakaipkmq9rU8cM1g3CpBwP6OrkyMSfs+6KvEpTEMpOAxc1wi8CoPYCJT2hu9faRTXOAceV4WoiKHp9alfJoC3vp9QGcyWt0WhBonGMUxv2dPhLUOY6sIqIdjrobDTKQKBgQCM0SsGfJ8oLw6lBpPBniFGyre18sMb1YCGtDmOVBqgw4egcz4Z7TDMKeLjshfRapFezkcIpxYab5A5bAfekQl8L3JhkceXk0gFzYQB2jggk1PE/ajV74GO701Du2Ptc1A2PkuA/e93msrN2qmQkEe8Z3EYmqbd1WW2VcqEdX2YFQKBgFiH3qIeppVS+AJOBc3Ecr7NFNRR1U9vnq6KK9A1sExghonfxVxq2NlYkBM887fot/XJAOYIUb96wroKhLrCKfnVn6Bgd2Zi5PAvKJFMX1OuNzbxoJ/yNOh7ZPFnE5ah7hBfWF0u8gPwXc+c7Fn3r/0sPKgkDSBAlY9eZ0efMI8BAoGAWu53KikvXXOFsWsd3tmOA3jESOoKs96asGGRCSemSkn5OtMwNu3DpRMnQIGwf7qhROdPV3cN/uQr7eik7EQcK+U3p2nLyUWCLL0ZkVipBeAdKAflDsEqmHJPIn3PeXYNRfnUJ+fSyxlZ/LRG6uNAb5kcnxa+ymES3TtpG0ADakECgYB41NY4R1FNr4ga9HbHMHSaBNiicElSxGdWxbCWPaF/9wOmtrKo1+bsG9/RTwRXFBgiS5+qS0FKOKPNjbStcNI4lCyYlKKzwGwLSQsBbNL1yst19XuylW7oaKrPve5Ev7W3bBsNsjCAxeHenaczSirouQeyx2+3YXoulRy04sJd5w==';
//        PartConfig::where("config_key","base_pre_key")->update(["config_value"=>$va]);
//        $va2='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAhvV6/Ti+92mNdHDPll9tASz2Or8d6X5wMjmGG+Xbv3LaIHZpWU1diz/kL/0thmKiWzjJFqm9jLR3afE9Qs9rryAA+JemkRjU9KattWqIXxZTP7rY5rrEc/soljbXEF4AoPriYzEJE2EjwT4ckCmJoZW3aCv1yNIx33n/Qi49Ihh+srMOHMSUjDIAUIjbPkTzCtBliJNqkGra690N5HL6OpyVa0OwBc5wkQg0VjNcDNDatwSG2zXeISu93BHT+pfgfsyT3bkWUDFPxy5o3qvHVn1TO7TJ0bEtvgyShGW/6AOLdtb9+HxeZNcUTnmVc+PLMvNMW3mmLrNR7rJSGOaqXQIDAQAB';
//        PartConfig::where("config_key","base_pub_key")->update(["config_value"=>$va2]);
        $private_key = file_get_contents('./cer/rsa_private_key.pem'); //获取密钥文件内容
        $public_key   = file_get_contents('./cer/rsa_public_key.pem'); //获取密钥文件内容
        $pi_key = openssl_pkey_get_private($private_key);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
        $pu_key = openssl_pkey_get_public($public_key);//这个函数可用来判断公钥是否是可用的

        $data = "123";//原始数据
        $encrypted = "";
        $decrypted = "";

        echo "source data:",$data,"\n";

        echo "private key encrypt:\n";

        openssl_private_encrypt($data,$encrypted,$pi_key);//私钥加密
        $encrypted = base64_encode($encrypted);//加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
        echo $encrypted,"\n";

        echo "public key decrypt:\n";

        openssl_public_decrypt(base64_decode($encrypted),$decrypted,$pu_key);//私钥加密的内容通过公钥可用解密出来
        echo $decrypted,"\n";

        echo "---------------------------------------\n";
        echo "public key encrypt:\n";

        openssl_public_encrypt($data,$encrypted,$pu_key);//公钥加密
        $encrypted = base64_encode($encrypted);
        echo $encrypted,"\n";

        echo "private key decrypt:\n";
        openssl_private_decrypt(base64_decode($encrypted),$decrypted,$pi_key);//私钥解密
        echo $decrypted,"\n";
    }

    public function index2(Request $request){
        $key = "tztx";
        $jwt=$request->get("jwt");
        JWT::$leeway = 60; // $leeway in seconds
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        print_r($decoded);


    }

    public function testOrder(Request $request){

            $apikey='c4ca4238a0b923820dcc509a6f75849b';
            $phonenum=$request->param("phonenum");
            $money=$request->param("money");
            $ordernum=$request->param("ordernum");
            $notify_url=$request->param("notify_url");
            $err_notify_url=$request->param("err_notify_url");
            $todata="apikey=".$apikey."&phonenum=".$phonenum."&money=".$money."&ordernum=".$ordernum."&suc_notify_url=".$notify_url."&err_notify_url=".$err_notify_url;

            $opensslBase=new OpensslBase();
            $priv_key = file_get_contents("./cer/m_private_key.pem");
            $priv_key =openssl_get_privatekey($priv_key);

            $m_public_key = file_get_contents("./cer/m_public_key.pem");

            ApiMerchant::where("id",1)->update([

                "m_pub_key"=>$m_public_key

            ]);
            echo $opensslBase->generateSign($todata, $priv_key);



    }

    public function testcheackOrder(){

        $apikey='c4ca4238a0b923820dcc509a6f75849b';
        $ordernum='1231231231231231244444';

        $todata="apikey=".$apikey."&ordernum=".$ordernum;
        $opensslBase=new OpensslBase();
        $priv_key = file_get_contents("./cer/m_private_key.pem");
        $priv_key =openssl_get_privatekey($priv_key);

//        $m_public_key = file_get_contents("./cer/m_public_key.pem");
//
//        ApiMerchant::where("id",1)->update([
//
//            "m_pub_key"=>$m_public_key
//
//        ]);
        echo $opensslBase->generateSign($todata, $priv_key);



    }


    public function test(Request $request){
        $privkeypass = 'tzld2018'; //私钥密码
        $pfxpath = "./cer/mhsh.pfx"; //密钥文件路径
        $priv_key = file_get_contents($pfxpath); //获取密钥文件内容

        openssl_pkcs12_read($priv_key, $certs, $privkeypass); //读取公钥、私钥
        $prikeyid = $certs['pkey']; //私钥

        $hex_encrypt_data = 'iGwdZghtXtALoN18sfF3djYeWtYW7Dq9ZTnpwQYFXhKTs9tXgxxT/OPEC9Vy9Bs4l9aK/LI7cDKlt3CiDR1wwwRLWcYUSztcdhk0K4lNWtsMZAmHBllvrxnpwW6tth+BETuU4RXVWfWkzcl8pWApvMNq13PxQRIPaPOEdZA2rcU=';


        openssl_private_decrypt(base64_decode($hex_encrypt_data), $decrypt_data, $prikeyid); //解密数据


        echo '解密后的数据：' . $decrypt_data;

    }

    public function  ccc(){

            $pub_key='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtlGln5Yy6h0QcMYjHrKS1Lyspi2uXXMOhW7Va03s0Sh+2ZffVq9Rk5Skh7Fv/Dljgor6+UMzMMw4KTYlsERB88xaGM2VWtM1XcohMKRySXrn67LlLfMkxXyp5LuJ9qR4slwFdVEfKF0yP4gAS22Vxxe+m69GnHEWLbn6KiWKXTVwV9pSWWJ1xjlj4O7SknyQfWP/vlBECC+g6whPFzCqZSfeh2TRtfBUwPuu1wi2CmeHpQP/ydrv6afOPTiatSV+6e4ZslWVsK7lUjf7rhY+e6nKu9cb4nxSDAHUuQLi/vL9Nu14Lb9ev7wje2WIi5bsDy7N2S9MZJewGOTOSU+LbQIDAQAB';
            $pubPem = chunk_split($pub_key, 64, "\n");
            $pubPem = "-----BEGIN PUBLIC KEY-----\n" . $pubPem . "-----END PUBLIC KEY-----\n";
            $opensslBase=new OpensslBase();
            $opensslBase->veritySign();
    }
    public function ccc2(){

        $apikey='c4ca4238a0b923820dcc509a6f75849b';
        $phonenum='18723335994';
        $money=1;
        $ordernum='12312312312312312';
        $todata="apikey=".$apikey."&phonenum=".$phonenum."&money=".$money."&ordernum=".$ordernum;


        $priv_key='MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC2UaWfljLqHRBwxiMespLUvKymLa5dcw6FbtVrTezRKH7Zl99Wr1GTlKSHsW/8OWOCivr5QzMwzDgpNiWwREHzzFoYzZVa0zVdyiEwpHJJeufrsuUt8yTFfKnku4n2pHiyXAV1UR8oXTI/iABLbZXHF76br0accRYtufoqJYpdNXBX2lJZYnXGOWPg7tKSfJB9Y/++UEQIL6DrCE8XMKplJ96HZNG18FTA+67XCLYKZ4elA//J2u/pp849OJq1JX7p7hmyVZWwruVSN/uuFj57qcq71xvifFIMAdS5AuL+8v027Xgtv16/vCN7ZYiLluwPLs3ZL0xkl7AY5M5JT4ttAgMBAAECggEAaOJTi5lWtQP/X0E9Fv3YJnZcREVnV+9G3VBDirWLlU/cmcUbAL1fCq2qcT2MlOdW7dSSSS91lY8Gh/7Uc7Dw2BYphrdOQq/atZgAvAD+lnMUVK3X1jVYZBwxiJqy5ab8oG773N/MMuZab94HfV0Pmzd3ugx9bKZKAghJeFGcZb3M0KxTG+YR7WpkVEPNP+WcxGXvWqqhvNz4U1iJxSGgqb/JatVaZwUE/6ymY1QfrPxNAer33bqVj3hJiDvpmbWOCyiYeAvYem/sL4jCb6fsDxH2Cb0yp+rc1ZPxwOJkgU90Sh7V5Ekt5g1XfurfQvxjvqbCqDAlQZPGl7z3II4cKQKBgQDsP2JlKtezllUowrkvGpFTZhkVA+tHgiAQ2smp9CegvdP8A0eteRI/SDY+t4hTjNJX6OnKxO1wo91Xhvc6IBG1hri/0M1rk+F7cdFLOCgt931U8zpVBNP+M0ZeX/VRlGlV3qidJEA1HlzYp1l5eW/RW5vKk/GtXJiQrk/Wpc6g/wKBgQDFj/rawIHkUUvuUU+UeluD23Qe8JfT75nlXzwZFjFKb3EyTxrram7VvDn0nIffyZBkiHUE7VX85wsA5nt87T7dW/BMZDm1TrTzkdjRPYrnanrWXjCRN3B7ECzEK5XnU49xCmEFVO1nwFcXys9ho64q9qSy6PwuncrkZnFlHJ3nkwKBgFO7c2hf8uuX0L8NpQzo4bEpcqJcLTu/BxZyNR+jhZgXL9cWYoU9cBY1xeQcsJjMRJEb4CEaAv31VjH7iAx16VDpWvnBS81hMH3MLV33nWYNXcKKIpaXi3uBOOOiJxSvAVaKAEER5B/vjwDK7496VwNg8KqwHOxQ8VH2Nh3hzoaJAoGBAKAVOr9mndnTxiMl38YAH4eIm182tNAWpi0mLhxlzyrxglexoa6AX67xBkUfUToUqdvMdoW3iqWS0We5WvavhvD3Po+n5trzG80BsR5bL+K0dTSevdvtgEc3pA6RArafDuwZ7OPVHUmkoO22eLQ1XTuS/4G5KB7d3TmViOzVc13hAoGAVrktCi5hde8fztQHaFz6t8fSpwDlr7oph9sAHzKroqRoLv+6wo5sfhRCxHR7h5GlntNKGJRLPX49km5f1OTwhfphB25/DydeZ3d0SLjwj5gW24oZikEpPyQfMzZTqMtrfuWHryZAPSle4SPH8dc686nNBxAKF/C2JP51+BtT4FE=';
        $priv_key = (wordwrap($priv_key, 64, "\n", true))."\n";
        $priv_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $priv_key . "-----END RSA PRIVATE KEY-----\n";
        $priv_key =openssl_get_privatekey($priv_key);

        $pub_key='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtlGln5Yy6h0QcMYjHrKS1Lyspi2uXXMOhW7Va03s0Sh+2ZffVq9Rk5Skh7Fv/Dljgor6+UMzMMw4KTYlsERB88xaGM2VWtM1XcohMKRySXrn67LlLfMkxXyp5LuJ9qR4slwFdVEfKF0yP4gAS22Vxxe+m69GnHEWLbn6KiWKXTVwV9pSWWJ1xjlj4O7SknyQfWP/vlBECC+g6whPFzCqZSfeh2TRtfBUwPuu1wi2CmeHpQP/ydrv6afOPTiatSV+6e4ZslWVsK7lUjf7rhY+e6nKu9cb4nxSDAHUuQLi/vL9Nu14Lb9ev7wje2WIi5bsDy7N2S9MZJewGOTOSU+LbQIDAQAB';
        $pub_key = chunk_split($pub_key, 64, "\n");
        $pub_key = "-----BEGIN PUBLIC KEY-----\n" . $pub_key . "-----END PUBLIC KEY-----\n";
        $opensslBase=new OpensslBase();
        $sgin=$opensslBase->generateSign($todata, $priv_key);

        $issign = $opensslBase->veritySign($todata, $sgin, openssl_get_publickey($pub_key));

        if(!$issign){

            echo "校验sign失败";
        }else{
            echo "成功";
        }


    }

    public function exeExcel(Request $request){

        $file = $request->file("excelname");

        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
           // echo $info->getExtension();
            $filePath=$info->getSaveName();
        }
      $ret = Excel::import(ROOT_PATH . 'public' . DS . 'uploads'.DS .$filePath);
      echo json_encode($ret);

    }


    public function getMonth(){
        $date="2019-01";
        $firstday = date("Y-m-01",strtotime($date));
        $lastday = date("Y-12-31 23:59:59",strtotime("$firstday +1 month -1 day"));
     return json_encode(array($firstday,$lastday));
  }




}

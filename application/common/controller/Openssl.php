<?php
namespace app\common\controller;

use think\Controller;

class Openssl extends Controller
{
    /**
     * 公钥加密（由于1024 bit的钥匙加密的最大长度为117字节，所以将待加密数据按117分割，分断加密之后，连接返加）
     * @param $data 待加密数据
     * @param $publicKey
     * @return string 返回加密后的字符串
     */
    public function encrytPublicKey($data,$publicKey){
        $encrypt='';
        foreach (str_split($data,117) as $item){
            $temp='';
            openssl_public_encrypt($item,$encrypt,$publicKey,OPENSSL_ALGO_SHA256);
            $encrypt.=$temp;
        }
        openssl_free_key($publicKey);
        return base64_encode($encrypt);
    }


    /**
     * 私钥解密（待解密字符串长度超过128之后， 按128切割之后分断解密）
     * @param $data 密钥串
     * @param $privateKey
     * @return string  解密之后的字符串
     */
    public function decryptPrivateKey($data,$privateKey){
        $data = base64_decode($data);
        $decrypt='';
        foreach (str_split($data,128) as $item){
            $temp='';
            openssl_private_decrypt($item,$temp,$privateKey,OPENSSL_ALGO_SHA256);
            $decrypt.=$temp;
        }
        openssl_free_key($privateKey);
        return $decrypt;
    }


    /**
     * 私钥生成签名
     * @param $data 待签名字符串
     * @param $privateKey
     * @return string 生成的签名
     */
    public function generateSign($data,$privateKey){
        $signature='';
        openssl_sign($data,$signature,$privateKey,OPENSSL_ALGO_SHA256);
        openssl_free_key($privateKey);
        return base64_encode($signature);
    }


    /**
     * 公钥验签
     * @param $data 待验签数据
     * @param $sign 签名字符串
     * @param $publicKey
     * @return bool
     */
    public function veritySign($data,$sign,$publicKey){
        $sign=base64_decode($sign);
        $result = openssl_verify($data,$sign,$publicKey,OPENSSL_ALGO_SHA256);
        openssl_free_key($publicKey);
        return (bool)$result;
    }


    /**
     * 如果公钥和私钥是以字符串的形式提供的， 那么需要将公私钥组成相应的格式（每行64个字符串）；
     *$data为待加密字符串；
     *$key 为公钥字符串
     */
    function encode_pay($data, $key)
    {
        $pay_public_key = "-----BEGIN PUBLIC KEY-----\r\n";
        foreach (str_split($key, 64) as $str) {
            $pay_public_key = $pay_public_key . $str . "\r\n";
        }
        $pay_public_key = $pay_public_key . "-----END PUBLIC KEY-----";
        $pu_key = openssl_pkey_get_public($pay_public_key);
        if ($pu_key == false) {
            echo "打开公钥出错";
            die;
        }
        $encryptData = '';
        $crypto = '';
        foreach (str_split($data, 117) as $chunk) {
            openssl_public_encrypt($chunk, $encryptData, $pu_key);
            $crypto = $crypto . $encryptData;
        }
        $crypto = base64_encode($crypto);
        return $crypto;

    }
}
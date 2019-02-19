<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;

class SmsApiController extends Controller
{
    # 短信接口
    # 创蓝发送短信接口URL
    const API_SEND_URL='http://smssh1.253.com/msg/send/json';

    /**
     * 发送短信
     *
     * @param string $mobile 		手机号码
     * @param string $msg 			短信内容
     * @param string $needstatus 	是否需要状态报告
     */
    static public  function sendSMS( $mobile, $msg, $needstatus = 'true') {

        # 创蓝接口参数
        $postArr = array (
            'account'  =>  config('app.smsApi.API_ACCOUNT'),
            'password' =>  config('app.smsApi.API_PASSWORD'),
            'msg' => urlencode($msg),
            'phone' => $mobile,
            'report' => $needstatus
        );
        $result = self::curlPost( self::API_SEND_URL, $postArr);
        return $result;
    }


    /**
     * 通过CURL发送HTTP请求
     * @param string $url  //请求URL
     * @param array $postFields //请求参数
     * @return mixed
     *
     */
    static private function curlPost($url,$postFields){
        $postFields = json_encode($postFields);
        $ch = curl_init ();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8'
            )
        );
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt( $ch, CURLOPT_TIMEOUT,60);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = curl_exec ( $ch );
        if (false == $ret) {
            $result = curl_error(  $ch);
        } else {
            $rsp = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "请求状态 ". $rsp . " " . curl_error($ch);
            } else {
                $result = $ret;
            }
        }
        curl_close ( $ch );
        return $result;
    }
}

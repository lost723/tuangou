<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class WXLoginController extends WXBaseController
{
    const CODE_TO_SESSION_URL = 'https://api.weixin.qq.com/sns/jscode2session?';

    /**
     * code 换取 SessionKey 并写入缓存 生命周期 2小时
     * @param Request $request
     * @return mixed|null
     */
    public function code2SessionKey($data)
    {
        $url = self::CODE_TO_SESSION_URL.'appid='.$this->appid.'&secret='.$this->secret.'&js_code='.$data.'&grant_type=authorization_code';
        $result = self::http_get($url);
        $result = json_decode($result,true);
        if(!array_key_exists('errcode', $result)) {

            Redis::setex('openid:'.$result['openid'].':sessionKey', 7200, $result['session_key']);

            return $result;
        }

        return null;

    }

    /**
     * 解析 小程序api getUserinfo 的加密参数  并写入小程序用户数据库
     * @param Request $request
     * @param iv openid encryptedData
     * @return \Illuminate\Http\JsonResponse
     */
    public function ParseUserinfo(array $data)
    {
        $openid = $data['openid'];
        $encryptedData = $data['encryptedData'];
        $iv = $data['iv'];
        $sessionKey = Redis::get('openid:'.$openid.':sessionKey');
        $userinfo = null;
        $wxBizDataCrypt = new WXBizDataCryptController($this->appid, $sessionKey);
        $wxBizDataCrypt->decryptData($encryptedData, $iv, $userinfo);
        $userinfo = json_decode($userinfo, true);
        return $userinfo;
    }
}

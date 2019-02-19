<?php

namespace App\Http\Controllers\Auth;

use App\Models\Auth\Customer;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MiniProgram\wxBizDataCrypt;
use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CustomerController extends Controller
{

    private $appid;
    private $secret;

    public function __construct()
    {
        $this->appid  = config('wx.minPro.appid');
        $this->secret = config('wx.minPro.secret');
        $this->middleware('auth', ['except' => ['login', 'register']]);
    }

    /**
     * 登录 返回登录状态 ，若未注册返回用户未注册
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        # 验证code
        $request->validate([
            'code'  =>  'string|required'
        ]);


        $customer = $this->code2SessionKey($request->all());
        if($customer) {
            # 检测用户是否已注册
            $result = Customer::where(['openId' => $customer['openid']])->first();

            if(!empty($result)) {
                # 用户已注册  返回 token
                if(! $token = auth()->login($result)) {
                    return $this->unauthed();
                }
                return $this->respondWithToken($token);
            }
            else {
                # 用户未注册
                $message = [
                    'openid'    =>  $customer['openid'],
                    'message'   =>  '用户未注册'
                ];
                return response()->json($message, 400);
            }
        }
        else {
             return $this->warning('code已过期,接口请求异常!');
        }
    }

    /**
     * 用户个人信息注册 将数据记录数据库中
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {

        $request->validate([
            'iv'            =>  'string|required',
            'openid'        =>  'string|required|max:32',
            'encryptedData' =>  'string|required'
        ]);

        try {
            # 注册用户信息
            if($this->ParseUserinfo($request->all())) {
            # 自动登录
                $customer = Customer::where('openId',$request->query('openid'))->first();
                $token = auth()->login($customer);
                return $this->respondWithToken($token);
            }
            else {
                return $this->warning('用户注册失败');
            }
        }
        catch (\Exception $e) {

            return $this->warning('用户注册失败!');
        }

    }

    /**
     *  返回用户个人信息 如果是团长同时返回团长信息
     *
     */
    public function me()
    {
        $customer = auth()->user();

        # 检测 团长 身份
//        $leader = $customer->leader;

        $customer = $customer->toArray();
//        if(empty($leader)) {
//            $customer['leader'] = null;
//        }



        return $this->ok($customer);

    }

    # 我的小区信息
    public function mycommunity()
    {
        $customer = auth()->user();

        if(0 < $customer['community_id']) {
            $fillable = ['id', 'name', 'address', 'longitude', 'latitude', 'road_id'];
            $community = Community::find($customer['community_id'], $fillable);
            $road = $community->Road;
            $community = $community->toArray();
            $community['road'] = $road->province.$road->city.$road->district.$road->name;

            unset($road);
            unset($fillable);
            unset($community['road_id']);
            return $this->ok($community);
        }
        return $this->warning('请检查参数是否正确！');

    }

    /**
     * 用户关联小区
     * @param community_id 关联的小区id
     * @return \Illuminate\Http\JsonResponse
     */
    public function relateCommunity()
    {
        $community_id  = request('community_id');
        if(0 < $community_id) {
            $customer = auth()->user();
            $customer->community_id = $community_id;
            if($customer->save()) {
                return $this->note('成功关联小区');
            }
            return $this->warning('关联小区失败');
        }
        return $this->warning('请检查传入参数是否正确');
    }


    /**
     * 刷新token
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }


    /**
     * code 换取 SessionKey 并写入缓存 生命周期 2小时
     * @param Request $request
     * @return mixed|null
     */
    private function code2SessionKey(array $data)
    {

        $js_code = $data['code'];
        $url =  'https://api.weixin.qq.com/sns/jscode2session?';
        $url .= 'appid='.$this->appid.'&secret='.$this->secret.'&js_code='.$js_code.'&grant_type=authorization_code';

        $result = $this->http_get($url);
        $result = json_decode($result,true);
        dump($result);
        if(!array_key_exists('errcode', $result)) {

            Redis::setex('openid:'.$result['openid'].':sessionKey', 7200, $result['session_key']);

            return $result;
        }

        return null;

    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return $this->ok([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * 更新个人信息接口 手机号 更换头像 等等
     */
    public function updateinfo()
    {

    }


    /**
     * 解析 小程序api getUserinfo 的加密参数  并写入小程序用户数据库
     * @param Request $request
     * @param iv openid encryptedData
     * @return \Illuminate\Http\JsonResponse
     */
    private function ParseUserinfo(array $data)
    {
        $openid = $data['openid'];
        $encryptedData = $data['encryptedData'];
        $iv = $data['iv'];
        $sessionKey = Redis::get('openid:'.$openid.':sessionKey');

        $wxBizDataCrypt = new WXBizDataCrypt($this->appid, $sessionKey);
        $wxBizDataCrypt->decryptData($encryptedData, $iv, $userinfo);

        $userinfo = json_decode($userinfo, true);
        if(empty($userinfo)) {
            return null;
        }


        return Customer::firstOrCreate(
            [
                'openId' => $userinfo['openId']
            ],
            [
                'unionId'  => array_key_exists('unionId', $userinfo)?$userinfo['unionId']:null,
                'nickName' => $userinfo['nickName'],
                'avatar'   => $userinfo['avatarUrl'],
                'nickName' => $userinfo['nickName'],
                'country'  => $userinfo['country'],
                'province' => $userinfo['province'],
                'city'     => $userinfo['city'],
                'gender'   => $userinfo['gender'],


            ]
        );

    }


    /**
     * 获取访问 微信API 接口凭证 token
     * @param flag  是否从从缓存中获取
     * @return |null
     */
//    private function getAccessToken($flag = true)
//    {
//
//        if($flag || !Redis::get('appid:'.$this->appid.':access_token')) {
//            $url  = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=';
//            $url .= $this->appid.'&secret='.$this->secret;
//            # 返回请求信息 access_token
//            $result = $this->http_get($url);
//            $result = json_decode($result,true);
//
//            if (!$result) {
//                return null;
//            }
//            else {
//                Redis::setex('appid:'.$this->appid.':access_token', 7200, $result['access_token']);
//                return $result['access_token'];
//            }
//        }
//
//        return Redis::get('appid:'.$this->appid.':access_token');
//    }
}

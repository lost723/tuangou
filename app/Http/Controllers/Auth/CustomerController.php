<?php

namespace App\Http\Controllers\Auth;

use App\Http\Resources\Customer\CustomerResource;
use App\Models\Auth\Customer;
use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CustomerController extends Controller
{
    public  $miniprogram;
    protected $config;
    public function __construct()
    {
        $this->config = config('wechat.mini_program.default');
        $this->miniprogram = Factory::miniProgram($this->config);
        $this->middleware('auth', ['except' => ['login', 'register', ]]);
    }

    /**
     * 登录 返回登录状态 ，若未注册返回用户未注册
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'code'  =>  'string|required'
            ]);
            $sessionArr = $this->miniprogram->auth->session($request->get('code'));
            if(!array_key_exists('openid', $sessionArr)) {
                throw new \Exception('code 已过期');
            }
            Redis::setex('openid:'.$sessionArr['openid'].':sessionKey', 7200, $sessionArr['session_key']);
            # 检测用户是否已注册
            $result = Customer::where(['openid' => $sessionArr['openid']])->first();
            if(!empty($result)) {
                $token = auth()->login($result);
                $result =  $this->getToken($token);
                return $this->okWithResource($result, 'token正常返回', 1);
            }
            else {
                # 用户未注册
                $result = [
                    'openid'    =>  $sessionArr['openid'],
                ];
                return $this->okWithResource($result, '用户未注册', 2);
            }
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 用户个人信息注册 将数据记录数据库中
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'iv'            =>  'string|required',
                'openid'        =>  'string|required|max:32',
                'encryptedData' =>  'string|required'
            ]);
            $all = $request->all();
            $session = Redis::get('openid:'.$all['openid'].':sessionKey');
            $data = $this->miniprogram->encryptor->decryptData($session, $all['iv'], $all['encryptedData']);
            $customer = $this->create($data);
            # 自动登录
            if(empty($customer)) {
                throw new \Exception('用户注册失败!');
            }
            $token = auth()->login($customer);
            $result = $this->getToken($token);
            return $this->okWithResource($result);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 返回用户个人信息
     * @return CustomerResource
     */
    public function me()
    {
        try{
            $user = auth()->user();
            $resource = new CustomerResource($user);
            return $this->okWithResource($resource);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 刷新token
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try{
            $token = $this->getToken(auth()->refresh());
            return $this->okWithResource($token, 'token正常已刷新', 1);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }

    }



    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return array
     */
    protected function getToken($token)
    {
        return [
                'token' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ],
        ];
    }

    /**
     * 更新个人信息接口 手机号 更换头像 等等
     */
    public function update()
    {
        $user = auth()->user();
        try{
            $user->update(request()->all());
            return $this->okWithResource([]);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }


    public function create($userinfo)
    {
        return Customer::firstOrCreate(
            [
                'openid' => $userinfo['openId'],
            ]
            ,
            [
//                'unionid'  => array_key_exists('unionId', $userinfo)?$userinfo['unionId']:null,
                'avatar'   => $userinfo['avatarUrl'],
                'nickname' => $userinfo['nickName'],
                'country'  => $userinfo['country'],
                'province' => $userinfo['province'],
                'city'     => $userinfo['city'],
                'gender'   => $userinfo['gender'],
            ]
        );
    }


    /**
     * 解析小程序端手机号 并更新数据表0
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function parseMobile(Request $request)
    {
        try{
            $request->validate([
                'iv'            =>  'string|required',
                'encryptedData' =>  'string|required'
            ]);
            $customer = auth()->user();
            $all = $request->all();
            $session = Redis::get('openid:'.$customer->openid.':sessionKey');
            $data = $this->miniprogram->encryptor->decryptData($session, $all['iv'], $all['encryptedData']);
            if($data['purePhoneNumber'] <> $customer->mobile) {
                $customer->mobile = $data['purePhoneNumber'];
                $customer->save();
            }
            unset($data['watermark']);
            return $this->okWithResource($data);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }
}

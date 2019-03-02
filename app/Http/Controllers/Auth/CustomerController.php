<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Common\WXLoginController;
use App\Http\Resources\CustomerResource;
use App\Models\Auth\Customer;
use App\Http\Controllers\Controller;
use App\Models\Community;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected  $wxLogin;
    public function __construct()
    {
        $this->wxLogin = new WXLoginController;
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
            $customer = $this->wxLogin->code2SessionKey($request->get('code'));
            if(empty($customer)) {
                throw new \Exception('code 已过期');
            }
            # 检测用户是否已注册
            $result = Customer::where(['openid' => $customer['openid']])->first();
            if(!empty($result)) {
                $token = auth()->login($result);
                return $this->respondWithToken($token);
            }
            else {
                # 用户未注册
                $message = [
                    'openid'    =>  $customer['openid'],
                    'message'   =>  '用户未注册'
                ];
                return $this->ok($message);
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

        $request->validate([
            'iv'            =>  'string|required',
            'openid'        =>  'string|required|max:32',
            'encryptedData' =>  'string|required'
        ]);

        try {
            # 注册用户信息
            $data = $this->wxLogin->ParseUserinfo($request->all());
            $this->created($data);
            # 自动登录
            $customer = Customer::where('openid',$request->query('openid'))->first();
            if(empty($customer)) {
                throw new \Exception('用户注册失败!');
            }
            $token = auth()->login($customer);
            return $this->respondWithToken($token);
        }
        catch (\Exception $e) {
            return $this->warning('用户注册失败!');
        }
    }

    /**
     * 返回用户个人信息
     * @return CustomerResource
     */
    public function me()
    {
        return new CustomerResource(auth()->user());
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
    public function update()
    {
        $user = auth()->user();
        try{
            $user->update(request()->all());
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }


    public function create($userinfo)
    {
        return Customer::firstOrCreate(
            [
                'openid' => $userinfo['openId']
            ],
            [
                'unionid'  => array_key_exists('unionId', $userinfo)?$userinfo['unionId']:null,
                'avatar'   => $userinfo['avatarUrl'],
                'nickname' => $userinfo['nickName'],
                'country'  => $userinfo['country'],
                'province' => $userinfo['province'],
                'city'     => $userinfo['city'],
                'gender'   => $userinfo['gender'],
            ]
        );
    }

}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Common\SmsApiController;
use Illuminate\Http\Request;
use App\Models\Auth\Distributor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class DistributorController extends Controller
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['login', 'register', 'getVerifycode', ]]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVerifycode(Request $request)
    {
        $request->validate([
            'mobile'    =>  'required|regex:/^1[34578][0-9]{9}$/'
        ]);
        # gernerate verifycode
        $code = "";
        for($i=0;$i<4;$i++){
            $code .= rand(0,9);
        }
        $mobile = $request->query('mobile');
        Redis::setex('code:'.$mobile, 360, $code);
        $msg = '[青橙链]您的验证码为'.$code.',请妥善保管！';
        $result = SmsApiController::sendSMS($mobile, $msg);
        if(!empty($result)) {
            return $this->note('验证码发送成功！');
        }
        else {
            return $this->warning('验证码发送失败！');
        }

    }


    /**
     * register a new trader.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function register(Request $request)
    {
        try{
            # 注册
            $this->registrationValidator($request->all())->validate();
            $this->create($request->all());
            # 自动登陆
            $credentials = request(['email', 'password']);
            $token = auth()->attempt($credentials);
            return $this->respondWithToken($token);
        }catch (\Exception $e){
            # echo $e->getTraceAsString();
            return $this->warning('注册失败');
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        # 验证码校验
//        $mobile = request('mobile');
//        $code   = request('code');
//        # 缓存中获取验证码
//        $verifycode = Redis::get('code:'.$mobile);
//
//        if(empty($code) || empty($verifycode) || strcmp($code, $verifycode))
//        {
//            return $this->warning('验证码错误!');
//        }
        #
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return $this->unauthed();
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->ok(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return $this->note('退出成功');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }


    public function password(){}

    public function findPassword(){}

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
     * 注册验证器
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function registrationValidator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:32',
            'email' => 'required|string|max:32|unique:distributors',
            'password' => 'required|string|min:6',
        ]);
    }

    /**
     * 验证通过之后，把用户添加到数据库
     *
     * @param  array  $data
     * @return \App\Models\Auth\User
     */
    protected function create(array $data)
    {
        return Distributor::firstOrCreate(
            [
                'email' => $data['email']
            ],
            [
                'name' => $data['name'],
                'password' => bcrypt($data['password']),
            ]
        );
    }
}

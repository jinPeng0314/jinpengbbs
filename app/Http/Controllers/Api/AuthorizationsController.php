<?php

namespace App\Http\Controllers\Api;

use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\WeappAuthorizationRequest;

class AuthorizationsController extends Controller
{
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        if (!in_array($type,['weixin'])){
            return $this->response->errorBadRequest();
        }
        $driver = \Socialite::driver($type);

        try{
            if ($code = $request->code){
                $response = $driver->getAccessTokenResponse($code);
                $token = array_get($response,'access_token');
            }else{
                $token = $request->access_token;
                if ($type == 'weixin'){
                    $driver->setOpenId($request->openid);
                }
            }
            $oauthUser = $driver->userFromToken($token);
        }catch (\Exception $e){
            return $this->response->errorUnauthorized('参数错误，未获取用户信息');
        }

        switch ($type){
            case 'weixin':
                $user = User::where('weixin_unionid', $oauthUser->offsetGet('unionid'))->first();
            // 没有用户，默认创建一个用户
            if (!$user){
                    $user = User::create([
                        'name' => $oauthUser->getNickname(),
                        'avatar' => $oauthUser->getAvatar(),
                        'weixin_openid' => $oauthUser->getId(),
                        'weixin_unionid' =>  $oauthUser->offsetGet('unionid'),
                    ]);
            }
            break;
        }
        $token = Auth::guard('api')->fromUser($user);
        return $this->respondWithToken($token)->setStatusCode(201);
    }

    public function store(AuthorizationRequest $request)
    {
        $userName = $request->username;
        //filter_var() 函数通过指定的过滤器过滤变量。
        //FILTER_VALIDATE_EMAIL 过滤器把值作为电子邮件地址来验证。
        filter_var($userName,FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $userName :
            $credentials['phone'] = $userName;
        $credentials['password'] = $request->password;

        if (!$token = \Auth::guard('api')->attempt($credentials)){
            return $this->response->errorUnauthorized(trans('auth.failed'));
        }

        return $this->respondWithToken($token)->setStatusCode(201);
    }

    /**
     * 小程序登录
     * @param WeappAuthorizationRequest $request
     */
    public function weappStore(WeappAuthorizationRequest $request)
    {
        $code = $request->code;

        //根据code获取微信openid和session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($code);

        //如果结果错误 说明code已过期或者不存在 返回401错误
        if (isset($data['errcode'])){
            return $this->response->errorUnauthorized('code 错误');
        }

        //找到openid对应的用户
        $user = User::where('weapp_openid',$data['openid'])->first();
        $attributes['session_key'] = $data['session_key'];

        //未找到对应用户则需要提交用户名密码进行用户绑定
        if (!$user){
            // 如果未提交用户名密码，403 错误提示
            if (!$request->username){
                return $this->response->errorForbidden('用户不存在');
            }
            $userName = $request->username;

            //用户名可以使邮箱或者电话号码
            filter_var($userName,FILTER_VALIDATE_EMAIL) ?
                $credentials['email'] = $userName :
                $credentials['phone'] = $userName;
            $credentials['password'] = $request->password;

            //验证用户名和密码是否正确
            if (!\Auth::guard('api')->once($credentials)){
                return $this->response->errorUnauthorized('用户名或密码错误');
            }

            //获取对应的用户
            $user = \Auth::guard('api')->getUser();
            $attributes['weapp_openid'] = $data['openid'];
        }
        //更新用户数据
        $user->update($attributes);

        //为对应用户创建 JWT
        $token = \Auth::guard('api')->fromUser($user);
        return $this->respondWithToken($token)->setStatusCode(201);
    }

    public function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * 刷新token
     * @return mixed
     */
    public function update()
    {
        $token = Auth::guard('api')->refresh();

        return $this->respondWithToken($token);
    }

    /**
     * 删除token
     * @return \Dingo\Api\Http\Response
     */
    public function destroy()
    {
        Auth::guard('api')->logout();

        return $this->response->noContent();
    }
}

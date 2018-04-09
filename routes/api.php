<?php

use Illuminate\Http\Request;

 $api = app('Dingo\Api\Routing\Router');
 $api->version('v1',[
     'namespace' => 'App\Http\Controllers\Api'
 ],function ($api){
     //短信验证码
     $api->post('verificationCodes','VerificationCodesController@store')
         ->name('api.verificationCodes.store');

     //用户注册
     $api->post('users','UsersController@store')
         ->name('api.users.store');
 });

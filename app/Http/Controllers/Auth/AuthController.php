<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Routing\Controller;
use User;

class AuthController extends Controller{
    /**
     * 处理登录认证
     *
     * @return Response
     */
    public function authenticate()
    {
        if (Auth::attempt(['user_id' => $email, 'password' => $password])) {
            // 认证通过...

            return redirect()->intended();
        }
    }
    public function create(Request $request){
        $this->validate($request,[
            'user_id'=>'required|unique:user',
            'password'=>'required|min:6',
            
        ]);
        $user=new User;
        $user->user_id=$request->user_id;
        $user->password=$request->password;
        $user->permession='2';
        $user->save();

    }
}
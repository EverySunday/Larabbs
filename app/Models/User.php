<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use Notifiable, MustVerifyEmailTrait;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function register(Request $request)
    {
        // 检验用户提交的数据是否有误
        $this->validator($request->all())->validate();

        // 创建用户同时触发用户注册成功的事件，并将用户传参
        event(new Registered($user = $this->create($request->all())));

        // 登录用户
        $this->guard()->login($user);

        // 调用钩子方法 `registered()`
        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }
}
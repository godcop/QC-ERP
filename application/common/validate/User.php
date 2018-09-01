<?php
/**
 * Created by PhpStorm.
 * User: yifeng
 * Date: 2017/8/11
 * Time: 15:53
 */

namespace app\common\validate;
use think\Validate;

class User extends Validate
{
    protected $rule =   [
        'username'  => 'require|max:10',
        'realname'   => 'require|max:10',
        'password'=>'require|min:6',
        'number'=>'require',
        'phone'=>'require|length:11',
        'vercode'=>'require|captcha',
        'old_password'=> 'require|min:6',
        're_password'=>'confirm:password',
    ];

    protected $message  =   [
        'username.require' => '系统账号不能为空',
        'username.max' => '系统账号不能超过10个字符',
        'realname.require' => '员工姓名不能为空',
        'realname.max' => '员工姓名不能超过10个字符',
        'password.require' => '登录密码不能为空',
        'password.min' => '登录密码不能少于6个字符',
        'number.require' => '员工工号不能为空',
        'phone.require' => '电话号码不能为空',
        'phone.length' => '电话号码必须为11位',
        'vercode.require'=>'验证码不能为空',
        'vercode.captcha'=>'验证码不正确',
        'old_password.require'=>'旧密码不能为空',
        'old_password.min'=>'旧密码不能少于6个字符',
        're_password.confirm'=>'两次密码输入不一致',
    ];

    protected $scene = [
        'add'  =>  ['username','realname','password','number','phone'],
        'login'=>['username','password','vercode'],
        'modify'  =>  ['password','old_password','re_password'],
        'editdo'=>['username','realname','password','number','phone'],
    ];
}
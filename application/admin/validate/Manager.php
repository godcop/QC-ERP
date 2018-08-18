<?php
/**
 * Created by PhpStorm.
 * User: yifeng
 * Date: 2017/8/6
 * Time: 21:27
 */

namespace app\admin\validate;
use think\Validate;

class Manager extends Validate
{
    protected $rule =   [
        'mn_user'  => 'require|max:25',
        'mn_passwd'   => 'require|min:6',
        'old_mn_passwd'=> 'require|min:6',
        're_mn_passwd'=>'confirm:mn_passwd',
        'vercode'=>'require|captcha'
    ];

    protected $message  =   [
        'mn_user.require' => '用户名不能为空',
        'mn_user.max'     => '用户名最多不能超过25个字符',
        'mn_passwd.require'   => '密码不能为空',
        'mn_passwd.min'  => '密码长度不能少于6位',
        'old_mn_passwd.require'=>'旧密码不能为空',
        'old_mn_passwd.min'=>'旧密码长度不能少于6位',
        're_mn_passwd.confirm'=>'两次密码输入不一致',
        'vercode.require'=>'验证码不能为空',
        'vercode.captcha'=>'验证码不正确'
    ];
    protected $scene = [
        'login'=>['mn_user','mn_passwd','vercode'],
        'modify'  =>  ['mn_passwd','old_mn_passwd','re_mn_passwd'],
    ];

}
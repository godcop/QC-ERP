<?php
/**
 * Created by PhpStorm.
 * User: yifeng
 * Date: 2017/8/6
 * Time: 15:46
 */
namespace app\index\controller;
use think\Controller;
class Login extends Controller
{
    public function index(){
        if(session('?loginname', '', 'user')!=1 || session('?loginid', '', 'user')!=1){
            $version=db('version')->order('id desc')->limit(1)->select();
            $this->assign('version',$version);
            return view();
        }
        $this->redirect('index/index');
    }

    public function goout(){
        session(null, 'user');
        return json_encode(1);
    }

    public function login(){
        $data=input('post.');
        $validate = validate('User');
        if(!$validate->scene('login')->check($data)){
            //$this->error($validate->getError(),null,null,2);
            return $validate->getError();
        }
        $result=db('user')->where('username',$data['username'])->field('Id,username,password')->find();
        if(!$result){
            return '用户名不存在';
        }
        if(md5(trim($data['password']))!=$result['password']){
            return '密码输入不正确';
        }
        
        //获取访客操作系统类型
        function GetOs() {
            if (!empty($_SERVER['HTTP_USER_AGENT'])) {
                $OS = $_SERVER['HTTP_USER_AGENT'];
                if (preg_match('/win/i', $OS)) {
                    $OS = 'Windows';
                } elseif (preg_match('/mac/i', $OS)) {
                    $OS = 'MAC';
                } elseif (preg_match('/linux/i', $OS)) {
                    $OS = 'Linux';
                } elseif (preg_match('/unix/i', $OS)) {
                    $OS = 'Unix';
                } elseif (preg_match('/bsd/i', $OS)) {
                    $OS = 'BSD';
                } else {
                    $OS = 'Other';
                }
                return $OS;
            } else {
                return "获取访客操作系统信息失败！";
            }
        }

        //获取访客浏览器类型
        function GetBrowser(){
            if(!empty($_SERVER['HTTP_USER_AGENT'])){
                $br = $_SERVER['HTTP_USER_AGENT'];
                if (preg_match('/MSIE/i',$br)) {
                    $br = 'MSIE';
                }elseif (preg_match('/Firefox/i',$br)) {
                    $br = 'Firefox';
                }elseif (preg_match('/Chrome/i',$br)) {
                    $br = 'Chrome';
                }elseif (preg_match('/Safari/i',$br)) {
                    $br = 'Safari';
                }elseif (preg_match('/Opera/i',$br)) {
                    $br = 'Opera';
                }else {
                    $br = 'Other';
                }
                return $br;
            }else{return "获取浏览器信息失败！";}
        }

        //获取访客浏览器语言
        function GetLang(){
            if(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
                $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
                $lang = substr($lang,0,5);
                if(preg_match("/zh-cn/i",$lang)){
                    $lang = "简体中文";
                }elseif(preg_match("/zh/i",$lang)){
                    $lang = "繁体中文";
                }else{
                    $lang = "English";
                }
                return $lang;

            }else{return "获取浏览器语言失败！";}
        }

        //获取真实ip
        function getIp()
        {
            if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP") , "unknown")) {
                $ip = getenv("HTTP_CLIENT_IP");
            } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR") , "unknown")) {
                $ip = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR") , "unknown")) {
                $ip = getenv("REMOTE_ADDR");
            } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
                $ip = $_SERVER['REMOTE_ADDR'];
            } else {
                $ip = "unknown";
            }
            return $ip;
        }

        //根据ip获取城市、网络运营商等信息
        /*function findCityByIp($ip){
            $data = file_get_contents('http://freeapi.ipip.net/'.$ip);
            return json_decode($data);
        }*/

        //定义各访客信息变量
        $u_os = GetOs();
        $u_browser = GetBrowser();
        $u_lang = GetLang();
        $u_ip = getIp();
        /*$mn_city = findCityByIp($mn_ip)[0] . findCityByIp($mn_ip)[1] . findCityByIp($mn_ip)[2] . findCityByIp($mn_ip)[3];
        if(findCityByIp($mn_ip)["data"]["city"] == "内网IP"){
            $mn_city = "内网IP";
        }else{
            $mn_city = $mn_citys;
        }
        $mn_isp = findCityByIp($mn_ip)[4];*/
        $u_time = date("Y-m-d H:i:s",time());

        //将各项登录信息写入数据库的具体操作
        $loginhistory["u_os"] = $u_os;
        $loginhistory["u_browser"] = $u_browser;
        $loginhistory["u_lang"] = $u_lang;
        $loginhistory["u_ip"] = $u_ip;
        //$loginhistory["u_city"] = $u_city;
        //$loginhistory["u_isp"] = $u_isp;
        $loginhistory["u_time"] = $u_time;
        $loginhistory["u_id"] = $result['Id'];
        db('loginhistory')->insert($loginhistory);

        //密码比对成功以及登录信息写入成功后，开始进行登录操作
        session('loginname', $result['username'], 'user');
        session('loginid', $result['Id'], 'user');
        db('user')->where('Id',$result['Id'])->update(['last_login_time' => date("Y-m-d H:i:s",time())]);
        return json_encode(1);
    }
}
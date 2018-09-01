<?php
namespace app\index\controller;
use think\Db;
class Index extends Common
{
    public function index()
    {
        $loginhistory=db('loginhistory')->where("u_id",session("loginid","",'user'))->order('id desc')->limit(2)->select();
        if(!array_key_exists(1, $loginhistory)){
            $loginhistory[1]= array(
                "Id" => "",
                "u_id" => "",
                "u_ip" => "还未登录过",
                "u_city" => "",
                "u_isp" => "",
                "u_time" => "",
                "u_browser" => "",
                "u_lang" => "",
                "u_os" => ""
            );
        }
        $this->assign('loginhistory',$loginhistory);
        return view();
    }

    //修改管理员密码
    public function modify(){
        if(request()->isPost()){
            $data=input('post.');
            $validate=validate("User");
            if(!$validate->scene('modify')->check($data)){
                $this->error($validate->getError());
            }
            $result=db('user')->where("Id",session("loginid","",'user'))->field("password")->find();
            if(md5($data['old_password'])!=$result['password']){
                $this->error("旧密码认证失败");
            }
            $res=db('user')->where("Id",session("loginid","",'user'))->setField('password',md5($data['password']));;
            if($res){
                session(null, 'user');
                $this->success("密码修改成功");
            }else{
                $this->error("密码修改失败");
            }
        }
        return view();
    }

    //加载欢迎界面
    public function  welcome(){
        $server=[
            'HTTP_HOST'=>$_SERVER['HTTP_HOST'],
            'SERVER_SOFTWARE'=>$_SERVER['SERVER_SOFTWARE'],
            'osname'=>php_uname(),
            'HTTP_ACCEPT_LANGUAGE'=>$_SERVER['HTTP_ACCEPT_LANGUAGE'],
            'SERVER_PORT'=>$_SERVER['SERVER_PORT'],
            'SERVER_NAME'=>$_SERVER['SERVER_NAME'],
        ];
        $version=Db::query("select version()");
        $server['mysqlversion']=$version[0]['version()'];
        $server['databasename'] =config('database')['database'];
        $server['phpversion']=phpversion();
        $server['maxupload']=ini_get('max_file_uploads');
        $this->assign('server',$server);

        //welcome.html中的系统版本信息变量
        $version=db('version')->order('id desc')->limit(1)->select();
        $this->assign('version',$version);
        return view();
    }
}

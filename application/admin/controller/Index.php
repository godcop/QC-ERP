<?php
namespace app\admin\controller;
use think\Db;
class Index extends Common
{
    public function index()
    {
        $mloginhistory=db('mloginhistory')->order('id desc')->limit(2)->select();
        //dump($mloginhistory);
        //die;
        $this->assign('mloginhistory',$mloginhistory);
        return view();
    }

    //修改管理员密码
    public function modify(){
        if(request()->isPost()){
            $data=input('post.');
            $validate=validate("Manager");
            if(!$validate->scene('modify')->check($data)){
                $this->error($validate->getError());
            }
            $result=db('manager')->where("Id",session("loginid","",'admin'))->field("mn_passwd")->find();
            if(md5($data['old_mn_passwd'])!=$result['mn_passwd']){
                $this->error("旧密码认证失败");
            }
            $res=db('manager')->where("Id",session("loginid","",'admin'))->setField('mn_passwd',md5($data['mn_passwd']));;
            if($res){
                session(null, 'admin');
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

    //获取管理员登录信息
    public function loginInfo(){

    }
}

<?php
/**
 * Created by PhpStorm.
 * User: yifeng
 * Date: 2017/8/10
 * Time: 22:57
 */

namespace app\admin\controller;
use think\Db;
class System extends Common
{
    //系统信息（默认）
    public function index(){
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

    // 版本设置（历史版本）
    public function versionview(){
        function version(){
            $version = db('version') -> select();
            $count=count($version);
            $limit= input('param.limit');
            //获取当前页数
            $page= input('param.page');
            //计算出从那条开始查询
            $tol=($page-1)*$limit;
            // 查询出当前页数显示的数据
            $version = db('version')->order('Id desc')->limit("$tol","$limit") -> select();
            return ["code"=>"0","msg"=>"","count"=>$count,"data"=>$version];
        }
        echo json_encode(version());
    }

    // 版本设置
    public function version(){
        if(request()->isPost()) {
            $version = input("post.");
            if(db("version")->insert($version)){
                return json_encode(200);
            } else {
                return json_encode(400);
            }


        }

        return view();

    }


}
<?php
/**
 * Created by PhpStorm.
 * User: yifeng
 * Date: 2017/8/10
 * Time: 22:57
 */

namespace app\admin\controller;
use think\Db;
class Xiangmu extends Common
{
    // 项目
    public function index(){
        if(request()->isPost()) {
            $data = input("post.");
            if(db("xiangmu")->insert($data)){
                return json_encode(200);
            }else{
                return json_encode(400);
            }
        }
        return view();
    }

    // 项目列表
    public function lists(){
        function lists(){
            $xiangmu = db('xiangmu') -> select();
            $count=count($xiangmu);
            $limit= input('param.limit');
            //获取当前页数
            $page= input('param.page');
            //计算出从那条开始查询
            $tol=($page-1)*$limit;
            // 查询出当前页数显示的数据
            $xiangmu = db('xiangmu')->order('Id desc')->limit("$tol","$limit") -> select();
            return ["code"=>"0","msg"=>"","count"=>$count,"data"=>$xiangmu];
        }
        echo json_encode(lists());
    }

    // 删除用户
    public function del(){
        $id=input('id');
        if(!db('xiangmu')->delete($id)){
            echo json_encode(array("code"=>400));
        }else{
            echo json_encode(array("code"=>200));
        }
    }
}
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
    // 默认页
    public function index(){
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
            $xiangmu = db('xiangmu')->order("Id")->limit("$tol","$limit") -> select();
            return ["code"=>"0","msg"=>"","count"=>$count,"data"=>$xiangmu];
        }
        echo json_encode(lists());
    }

    // 新增项目
    public function add(){
        if(request()->isPost()) {
            $data = input("post.");
            if($data["xm_name"]==null || $data["xm_abbreviated"]==null){
                return json("请完善相关信息后再提交！");
                die;
            }
            if(array_key_exists("xm_status", $data)){
                $data["xm_status"] = "销售中";
            }else{
                $data["xm_status"] = "已撤场";
            }
            //return json_encode($data);
            if(db("xiangmu")->insert($data)){
                return json_encode(200);
            }else{
                return json_encode(400);
            }
        }
        return view();
    }

    // 编辑项目
    public function edit(){
        $id = input("param.id");
        $editview = db("xiangmu") -> where("Id",$id) -> select();
        if(request()->isPost()) {
            $data = input("post.");
            if($data["xm_name"]==null || $data["xm_abbreviated"]==null){
                return json("请完善相关信息后再提交！");
                die;
            }
            if(array_key_exists("xm_status", $data)){
                $data["xm_status"] = "销售中";
            }else{
                $data["xm_status"] = "已撤场";
            }
            if(db("xiangmu")->where("Id",$id)->update($data)){
                return json_encode(200);
            }else {
                return json_encode(400);
            }
        }
        $this->assign("xm",$editview);
        return view();
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
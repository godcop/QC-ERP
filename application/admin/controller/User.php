<?php
/**
 * Created by PhpStorm.
 * User: yifeng
 * Date: 2017/8/10
 * Time: 22:57
 */

namespace app\admin\controller;
class User extends Common
{
    //员工列表
    public function index(){

        return view();
    }

    //获取用户列表
    public function getlist(){
        function pagedata(){
            //获取总条数
            $lists = db('user')->select();
            $count=count($lists);
            //获取每页显示的条数
            $limit= input('param.limit');
            //获取当前页数
            $page= input('param.page');
            //计算出从那条开始查询
            $tol=($page-1)*$limit;
            // 查询出当前页数显示的数据
            $list = db('user')->limit("$tol","$limit")->select();
            //返回数据
            return ["code"=>"0","msg"=>"","count"=>$count,"data"=>$list];
        }
        echo json_encode(pagedata());
    }

    // 手动添加员工
    public function add(){
        if(request()->isPost()) {
            $insertdata = input("post.");
            $validate = validate("User");
            if (!$validate->scene('editdo')->check($insertdata)) {
                return json_encode($this->error($validate->getError()));
            }
            $data = [
                "username" => $insertdata["username"],
                "realname" => $insertdata["realname"],
                "number" => $insertdata["number"],
                "phone" => $insertdata["phone"],
                "group" => $insertdata["group"],
                "sex" => $insertdata["sex"],
            ];
            if (array_key_exists("status", $insertdata)) {
                $data["status"] = "在职";
            } else {
                $data["status"] = "离职";
            }
            if (array_key_exists("note", $insertdata)) {
                $data["note"] = $insertdata["note"];
            } else {
                $data["note"] = $insertdata["无"];
            }
            if (array_key_exists("xiangmu", $insertdata)) {
                $data["xiangmu"] = implode(",", $insertdata["xiangmu"]);
            } else {
                $data["xiangmu"] = null;
            }
            if ($insertdata["password"] != "password" && $insertdata["password"] != null) {
                $data["password"] = md5($insertdata["password"]);
            }
            if (db('user')->where("username",$insertdata["username"])->find()){
                //return json_encode("用户名已存在");
                $this->error("用户名已存在");
            }else{
                if (db('user')->insert($data)) {
                    return json_encode(200);
                } else {
                    return json_encode(400);
                }
            }

        }
        return view();
    }

    //员工详情查看
    public function view(){
        $data = input("param.id");
        $userinfo = db('user')->where("Id",$data)->select();
        $user = [
            "realname" => $userinfo[0]["realname"],
            "username" => $userinfo[0]["username"],
            "number" => $userinfo[0]["number"],
            "last_login_time" => $userinfo[0]["last_login_time"],
            "login_count" => $userinfo[0]["login_count"],
            "last_edit_time" => $userinfo[0]["last_edit_time"],
            "note" => $userinfo[0]["note"],
            "group" => $userinfo[0]["group"],
            "phone" => $userinfo[0]["phone"],
            "xiangmu" => explode(",", $userinfo[0]["xiangmu"]),
        ];
        if($userinfo[0]["sex"] == "男" || $userinfo[0]["sex"] == null){
            $user["sex1"] = "checked";
            $user["sex2"] = "";
        }else{
            $user["sex1"] = "";
            $user["sex2"] = "checked";
        }
        if($userinfo[0]["status"] == "在职"){
            $user["status"] = "checked";
        }else{
            $user["status"] = "";
        }
        if($userinfo[0]["last_login_time"] == "/"){
            $user["last_login_time"] = "从未登录";
        }
        if($userinfo[0]["last_edit_time"] == "/"){
            $user["last_edit_time"] = "从未编辑";
        }

        $this->assign('user',$user);
        return view();
    }

    //员工信息编辑（预览操作）(可以和下面的editdo方法合并，用if判断是否是request()->isPost()提交)
    public function edit(){
        $data = input("param.id");
        $userinfo = db('user')->where("Id",$data)->select();
        $user = [
            "realname" => $userinfo[0]["realname"],
            "username" => $userinfo[0]["username"],
            "number" => $userinfo[0]["number"],
            "last_login_time" => $userinfo[0]["last_login_time"],
            "last_edit_time" => $userinfo[0]["last_edit_time"],
            "login_count" => $userinfo[0]["login_count"],
            "note" => $userinfo[0]["note"],
            "group" => $userinfo[0]["group"],
            "phone" => $userinfo[0]["phone"],
            "password" => $userinfo[0]["password"],
            "xiangmu" => explode(",", $userinfo[0]["xiangmu"]),
        ];
        if($userinfo[0]["sex"] == "男" || $userinfo[0]["sex"] == null){
            $user["sex1"] = "checked";
            $user["sex2"] = "";
        }else{
            $user["sex1"] = "";
            $user["sex2"] = "checked";
        }
        if($userinfo[0]["status"] == "在职"){
            $user["status"] = "checked";
        }else{
            $user["status"] = "";
        }
        if($userinfo[0]["last_login_time"] == "/"){
            $user["last_login_time"] = "从未登录";
        }
        if($userinfo[0]["last_edit_time"] == "/"){
            $user["last_edit_time"] = "从未编辑";
        }else{
            $user["last_edit_time"] = $userinfo[0]["last_edit_time"];
        }
        $this->assign('user',$user);
        return view();
    }

    //员工信息编辑（编辑操作）
    public function editdo(){
        $editdata = input("post.");
        $validate=validate("User");
        if(!$validate->scene('editdo')->check($editdata)){
            return json_encode($this->error($validate->getError()));
        }
        $data=[
            "last_edit_time" => date("Y-m-d H:i:s"),
            "username" => $editdata["username"],
            "realname" => $editdata["realname"],
            "number" => $editdata["number"],
            "phone" => $editdata["phone"],
            "group" => $editdata["group"],
            "sex" => $editdata["sex"],
        ];
        if(array_key_exists("status",$editdata)){
            $data["status"] = "在职";
        }else{
            $data["status"] = "离职";
        }
        if(array_key_exists("note",$editdata)){
            $data["note"] = $editdata["note"];
        }else{
            $data["note"] = $editdata["无"];
        }
        if(array_key_exists("xiangmu",$editdata)){
            $data["xiangmu"] = implode(",",$editdata["xiangmu"]);
        }else{
            $data["xiangmu"] = null;
        }
        if($editdata["password"] != "password" && $editdata["password"] != null){
            $data["password"] = md5($editdata["password"]);
        }
        $Id = $editdata["id"];
        if(db('user')->where("Id",$Id)->update($data)){
            return json_encode(200);
        }else{
            return json_encode(400);
        }
    }

    // 删除用户
    public function del(){
         $id=input('id');
        if(!db('user')->delete($id)){
            echo json_encode(array("code"=>400));
        }else{
            echo json_encode(array("code"=>200));
        }
    }

    //批量导入员工信息
    public function leadingin(){
        if(request()->isPost()){
            // 获取表单上传文件 例如上传了001.jpg
            $file = request()->file('fileexcel');
            $fileinfo=$this->upload($file);
            if(!$fileinfo['code']){
                return json($fileinfo['info']);
            }
            //文档处理
            $inputFileType=\PHPExcel_IOFactory::identify($fileinfo['info']);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($fileinfo['info']);
            $sheet=$objPHPExcel->getSheet(0);
            $column=$sheet->getHighestColumn();
            $columns=\PHPExcel_Cell::columnIndexFromString($column);
            if($columns!=7 && $columns!=6){
                return json("数据格式不正确");
            }
            if($columns==7){
                $data=$this->getexceldateone($sheet);
            }
            if($columns==6){
                $data=$this->getexceldatetwo($sheet);
            }
            if(!db('user')->insertAll($data)){
                return json("数据导入异常");
            }
            if(file_exists($fileinfo['info'])){
                unlink($fileinfo['info']);
            }
            return json("数据导入成功");
        }
        return view();
    }
    protected function getexceldateone($sheet){
        //获取当前工作表的行数
        $rows=$sheet->getHighestRow();
        //获取当前工作表的列（在这里获取到的是字母列），
        $column=$sheet->getHighestColumn();
        $columns=\PHPExcel_Cell::columnIndexFromString($column);
        $field=['name','sex','age','phone','weixin','qq','state'];
        $data=[];
        for($row=2;$row<=$rows;$row++){
            $row_data=[];
            for($col=0;$col<$columns;$col++){
                $value=$sheet->getCellByColumnAndRow($col,$row)->getValue();
                if($field[$col]=="sex"){
                    $value=($value=="男")?0:1;
                }
                if($field[$col]=="state"){
                    $value=($value=="在职")?1:0;
                }
                $row_data[$field[$col]]=$value;
            }
            $row_data['password']=md5('123456');
            $data[]=$row_data;
        }
        return $data;
    }
    protected function getexceldatetwo($sheet){
        //获取当前工作表的行数
        $rows=$sheet->getHighestRow();
        //获取当前工作表的列（在这里获取到的是字母列），
        $column=$sheet->getHighestColumn();
        $columns=\PHPExcel_Cell::columnIndexFromString($column);
        $field=['name','sex','age','phone','weixin','qq','state'];
        $data=[];
        for($row=2;$row<=$rows;$row+=3){
            $row_data=[];
            for($col=0;$col<$columns;$col++){
                $value=$sheet->getCellByColumnAndRow($col,$row)->getValue();
                if($col<3){
                    if($field[$col]=="sex"){
                        $value=($value=="男")?0:1;
                     }
                    $row_data[$field[$col]]=$value;
                }
                if($col==3) {
                    $row_data[$field[$col]]=$sheet->getCellByColumnAndRow($col+1,$row)->getValue();
                    $row_data[$field[$col+1]]=$sheet->getCellByColumnAndRow($col+1,$row+1)->getValue();
                    $row_data[$field[$col+2]]=$sheet->getCellByColumnAndRow($col+1,$row+2)->getValue();
                }
                if($col==5) {
                    $value=($value=="在职")?1:0;
                    $row_data[$field[$col+1]] =$value;
                }

            }
            $row_data['password']=md5('12345678');
            $data[]=$row_data;
        }
        return $data;
    }
    protected function upload($file)
    {
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        $msg=[];
        if($info){
            // 成功上传后 获取上传信息
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $msg['code']=1;
            $msg['info']=ROOT_PATH . 'public' . DS . 'uploads'.DS.$info->getSaveName();

        }else{
            // 上传失败获取错误信息
            $msg['code']=0;
            $msg['info']=$file->getError();
        }
        return $msg;
    }

//    批量导入
    public function expuser(){
        $phpexcel=new \PHPExcel();
        $phpexcel->setActiveSheetIndex(0);
        $sheet=$phpexcel->getActiveSheet();
        $res=db('user')->field("name,sex,age,phone,weixin,qq,ticheng,state")->select();
        $arr=[
            'name'=>"姓名",
            'sex'=>"性别",
            'age'=>"年龄",
            'phone'=>"手机号",
            'weixin'=>"微信",
            'qq'=>"QQ",
            'ticheng'=>"提成比例",
            'state'=>"状态",
        ];
        array_unshift($res,$arr);
        $currow=0;
        foreach ($res as $key=>$v){
            $currow=$key+1;
            $sheet->setCellValue('A'.$currow,$v['name'])
                ->setCellValue('B'.$currow,setsex($v['sex']))
                ->setCellValue('C'.$currow,$v['age'])
                ->setCellValue('D'.$currow,$v['phone'])
                ->setCellValue('E'.$currow,$v['weixin'])
                ->setCellValue('F'.$currow,$v['qq'])
                ->setCellValue('G'.$currow,$v['ticheng'])
                ->setCellValue('H'.$currow,setstate($v['state']));
        }
        $phpexcel->getActiveSheet()->getStyle('A1:H'.$currow)->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        header('Content-Type: application/vnd.ms-excel');//设置文档类型
        header('Content-Disposition: attachment;filename="员工信表.xlsx"');//设置文件名
        header('Cache-Control: max-age=0');
        $phpwriter = new \PHPExcel_Writer_Excel2007($phpexcel);
        $phpwriter->save('php://output');
        return;
    }

}
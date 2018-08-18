<?php
/**
 * Created by PhpStorm.
 * User: yifeng
 * Date: 2017/8/15
 * Time: 21:18
 */

namespace app\index\controller;
use think\Controller;
use app\admin\validate\User;
use think\Validate;
use think\Url;
class Login extends Controller
{
    public function index()
    {
        $phpexcel=new \PHPExcel();
        $file="./public/edit/demo.xlsx";
        if(!file_exists($file)){
            die("要操作的文件不存在！");
        }
//取文档的类型（与扩展名无关）
$filetype=\PHPExcel_IOFactory::identify($file);
//创建 一个特定的读取类
$excelread=\PHPExcel_IOFactory::createReader($filetype);
//加载文件
$phpexcel=$excelread->load($file);
//读取一个工作表，可以通过索引或名称
$sheet=$phpexcel->getSheet(0);
//获取当前工作表的行数
$rows=$sheet->getHighestRow();
//获取当前工作表的列（在这里获取到的是字母列），
$column=$sheet->getHighestColumn();
//把字母列转换成数字，这里获取的是列的数，并且列的索引
$columns=\PHPExcel_Cell::columnIndexFromString($column);
$arr=[];
//创建一个表头数组，个数与列数一致
$title=array("name","nianling","sex","phone");
//通过循环把表格中读取到的数据，存入二维数据，以便后期数据库的写入操作，行是从1开始的
for ($i=2;$i<=$rows;$i++){
    $arr_col=[];
    //列是从0开始的
    for ($col=0;$col<4;$col++){
        //把数字列转换成字母列，这里是通的列索引获取到对应的字母列
        $columnname=\PHPExcel_Cell::stringFromColumnIndex($col);
        $arr_col[$title[$col]]=$sheet->getCell($columnname.$i)->getValue();
    }
    $arr[]=$arr_col;
}

iconv_set_encoding('output_encoding', 'UTF-8');
//dump(mb_detect_encoding($arr[0]['name']));
//dump($arr[0]['name']);
file_put_contents('./1.txt',$arr[0]['name']);
// 
// dump($arr);
       // die;
        if (session('?loginname', '', 'user') != 1 || session('?loginid', '', 'user') != 1) {
            return view();
        }
        $this->redirect(url('index/login/index'));
    }

    public function goout()
    {
        session(null, 'user');
        $this->success("退出成功", 'login/index');
    }

    public function login()
    {
        $data = input('post.');
        $validate = validate('User');
        if (!$validate->scene('login')->check($data)) {
            $this->error($validate->getError(), null, null, 2);
        }
        $result = db('user')->where('phone', $data['phone'])->field('Id,phone,password')->find();
        if (!$result) {
            $this->error("用户名不存在");
        }
        if (md5(trim($data['password'])) != $result['password']) {
            $this->error("密码输入不正确");
        }
        session('loginname', $result['phone'], 'user');
        session('loginid', $result['Id'], 'user');
        $this->success('登录成功', 'index/index');
        return;
    }
}
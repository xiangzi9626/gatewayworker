<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Facade\Ignition\Support\Packagist\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class MyController extends Controller
{
    public function layout(){
        $my=(array)session("user");
        $user=DB::select("select * from user where id=?",[$my["id"]]);
        return view("user.edit_data",["user"=>(array)$user[0]]);
    }
    public function my_layout(){
        $my=(array)session("user");
        $user=DB::select("select * from user where id=?",[$my["id"]]);
        $msgCount=DB::select("select count(id) from user_chat where receive_id=? and send_id!=? and status=?",
            [$my["id"],$my["id"],0]);
        $msgCount=(array)$msgCount[0];
        return view("user.my",["user"=>(array)$user[0],"msgCount"=>$msgCount["count(id)"]]);
    }
    public function modifyPassword(){
       $password=$_POST["password"];
        $password2=$_POST["password2"];
        if (strlen($password)<6 || strlen($password)>30){
            echo "请输入6-30位密码";
            exit();
        }
        if ($password!=$password2){
            echo "两次密码输入不一致";
            exit();
        }
        $user=(array)session("user");
        $uid=$user["id"];
        try {
            DB::update("update user set password=? where id=?",[md5($password),$uid]);
            echo "ok";
        }catch (Exception $e){
            echo "提交失败请重试";
        }
    }
    public function editData(){
        $nickname=$_POST["nickname"];
        $age=str_replace(".","",$_POST["age"]);
        $sex=$_POST["sex"];
        $province=trim($_POST["province"]);
        $city=trim($_POST["city"]);
       if (empty(trim($nickname))){
           echo "昵称不能为空";
           exit();
       }
       if (strlen($nickname)>100){
           echo "昵称过长";
           exit();
       }
       if (preg_match("/^[0-9]+$/",$age,$arr)==1){
         if ($age<5 || $age>150){
             echo "年龄请输入5-150之间的数";
             exit();
         }
       }else{
           echo "年龄请输入5-150之间的数";
           exit();
       }
       if ($sex!="男" && $sex!="女"){
           $sex="男";
       }
       if (empty($province) || empty($city)){
           echo "请选择地区";
           exit();
       }else{
           $city=$province."/".$city;
       }
       $user=(array)session("user");
       $uid=$user["id"];
       try{
           DB::update("update user set nickname=?,age=?,sex=?,city=? where id=?",
               [$nickname,$age,$sex,$city,$uid]);
           $u=DB::select("select * from user where id=?",[$uid]);
           session(["user"=>$u[0]]);
            echo "ok";
       }catch (Exception $e){
           echo "提交失败请重试";
       }
    }
}

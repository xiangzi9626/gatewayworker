<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserRegisterController extends Controller
{
    public function register(){
       $username=trim($_POST["username"]);
        $password=$_POST["password"];
        $password2=$_POST["password2"];
        if(strlen($username)<6 || strlen($username)>20){
            echo "请输入6-20位用户名";
            exit();
        }
        if (strlen($password)<6 || strlen($password)>30){
            echo "请输入6-30位密码";
            exit();
        }
        if ($password!=$password2){
            echo "两次密码输入不一致";
            exit();
        }
        $time=date("Y-m-d H:i:s");
        if (empty(trim($password))){
            echo "密码不能为空";
            exit();
        }

        $sel=DB::table("user")->where("username",$username)->get();
        if (count($sel)>0){
            echo "用户名已存在不可用";
            exit();
        }

        $insert=DB::table("user")->insert([
            "username"=>$username,
            "password"=>md5($password),
            "level"=>3,
            "nickname"=>$username,
            "create_time"=>$time,
        ]);
        if ($insert>0){
            echo "ok";
            $user=DB::select("select * from user where username=? and password=?",[$username,md5($password)]);
            session(["user"=>$user[0]]);
        }else{
            echo "提交失败请重试";
        }
    }
}

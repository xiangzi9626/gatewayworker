<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class UserController extends Controller
{
   /* public function power($field){
        $aid=session("user")->id;
        if (session("user")->level!=1){
            $power=DB::select("select * from power where aid=? and `type`=? and `$field`=?",[$aid,"user",1]);
            if (count($power)<1){
                echo "你没有权限操作";
                exit();
            }else{
                $val=$power[0]->{$field};
                if ($val!=1){
                    echo "你没有权限操作";
                    exit();
                }
            }
        }
    }*/
    public function editUser(){
        $this->power("edit");
        $id=trim($_POST["id"]);
       $username=trim($_POST["username"]);
        $pwd1=trim($_POST["pwd1"]);
        $pwd2=trim($_POST["pwd2"]);
        if (empty($username)){
            echo "用户名不能为空";
          exit();
        }
        $u=DB::select("select id from user where id!=? and username=?",[$id,$username]);
        if (count($u)>0){
            echo "用户名已存在,不可用";
            exit();
        }
        if (empty($pwd1) && empty($pwd2)){
            try {
                DB::update("update user set username=? where id=?",[$username,$id]);
                echo "ok";
            }catch (Exception $e){
                echo "提交失败,请重试";
            }
        }else{
        if ($pwd1!=$pwd2){
            echo "两次密码输入不一致";
            exit();
        }
        $password=md5($pwd1);
            try {
                DB::update("update user set username=?,password=? where id=?",[$username,$password,$id]);
                echo "ok";
            }catch (Exception $e){
                echo "提交失败,请重试";
            }
        }
    }
  public function user_list(){
      $page=$_REQUEST["page"];
      $limit=$_REQUEST["limit"];
      $count=DB::table("user")
          ->select("id")
          ->where("level",">=",3)
          ->get()
          ->count();
      $user=DB::select("select * from user where level>=? limit ?,?",[3,($page-1)*$limit,$limit]);
      $arr=["code"=>0,"msg"=>"","count"=>$count,"data"=>$user];
      $arr=json_encode($arr,JSON_UNESCAPED_UNICODE);
      echo $arr;
  }
    public function delete_user(){
        $this->power("del");
        $id=$_POST["id"];
        $event=DB::select("select count(id) as num from product where uid=?",[$id]);
        if ($event[0]->num>0){
            echo "请先删除该用户创建的活动";
            exit();
        }
        DB::beginTransaction();
        try{
            DB::delete("delete from user where id=?",[$id]);
            DB::delete("delete from special where uid=?",[$id]);
            DB::commit();
            echo "ok";
            include $_SERVER["DOCUMENT_ROOT"] . "/app/lib/rescuive.php";
            $path = $_SERVER["DOCUMENT_ROOT"] . "/public/upload/product/" . $id;
            if (is_dir($path)){
            delAll($path);
            }
        }catch (Exception $e){
            DB::rollback();
            echo "删除失败请重试";
        }
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\DB;
use PharIo\Manifest\Exception;

class AdminController extends Controller
{
    public function edit_admin_show()
    {
        $user = DB::table("user")->where("id", $_GET["id"])->get();
        $user = (array)$user[0];
        return view("admin.edit_admin", ["user" => $user]);
    }

    public function edit_admin($id)
    {
        /* if (session("user")->level!=1){
            echo "不是超级管理员,权限不足";
            exit();
        }*/
        $data = file_get_contents("php://input");
        $data = json_decode($data, true);
        $username = trim($data["username"]);
        $phone = trim($data["phone"]);
        $password = trim($data["password"]);
        $password2 = trim($data["password2"]);
        if (!empty($password) || !empty($password2)) {
            if ($password != $password2) {
                echo "两次密码输入不一致";
                exit();
            }
        }
        if (empty($username)) {
            echo "账与不能为空";
            exit();
        }
        $u = DB::select("select id from user where id!=? and username=?", [$id, $username]);
        if (count($u) > 0) {
            echo "账号已存在,不可用";
            exit();
        }
        $p = DB::select("select id from user where id!=? and phone=?", [$id, $phone]);
        if (count($p) > 0) {
            echo "手机号已存在,不可用";
            exit();
        }
        try {
            if (empty($password)) {
                DB::table("user")
                    ->where("id", $id)
                    ->update([
                        "username" => $username,
                        "phone" => $phone,
                    ]);
            } else {
                DB::table("user")
                    ->where("id", $id)
                    ->update([
                        "username" => $username,
                        "password" => md5($password),
                        "phone" => $phone,
                    ]);
            }
            echo "ok";
        } catch (Exception $e) {
            echo "操作失败请重试";
        }
    }

    public function delete_admin($id)
    {
        /*  if (session("user")->level!=1){
              echo "不是超级管理员,权限不足";
              exit();
          }*/
        try {
            DB::delete("delete from user where id=?", [$id]);
            echo "ok";
        } catch (Exception $e) {
            echo "删除失败请重试";
        }
    }

    public function add_admin_show()
    {
        return view("admin.add_admin");
    }

    public function add_admin()
    {
        /* if (session("user")->level!=1){
             echo "不是超级管理员,权限不足";
             exit();
         }*/
        $data = file_get_contents("php://input");
        $data = json_decode($data, true);
        $username = trim($data["username"]);
        $password = trim($data["password"]);
        $password2 = trim($data["password2"]);
        if ($password != $password2) {
            echo "两次密码输入不一致";
            exit();
        }
        $phone = trim($data["phone"]);
        $time = date("Y-m-d H:i:s");
        if (empty($password)) {
            echo "密码不能为空";
            exit();
        }
        $sel = DB::table("user")->where("username", $username)->get();
        if (count($sel) > 0) {
            echo "账号已存在不可用";
            exit();
        }
        $phoneNum = DB::table("user")->where("phone", $phone)->get();
        if (count($phoneNum) > 0) {
            echo "手机号已存在不可用";
            exit();
        }
        $n = DB::table("user")->insert([
            "username" => $username,
            "password" => md5($password),
            "phone" => $phone,
            "level" => 2,
            "nickname" => $username,
            "create_time" => $time,
        ]);
        if ($n > 0) {
            echo "ok";
        } else {
            echo "添加失败请重试";
        }
    }

    public function admin_list()
    {
        $page = $_REQUEST["page"];
        $limit = $_REQUEST["limit"];
        $count = DB::table("user")
            ->select("id")
            ->where("level", "<", 3)
            ->get()
            ->count();
        $user = DB::select("select * from user where level<? limit ?,?", [3, ($page - 1) * $limit, $limit]);
        $arr = ["code" => 0, "msg" => "", "count" => $count, "data" => $user];
        $arr = json_encode($arr, JSON_UNESCAPED_UNICODE);
        echo $arr;
    }

    public function modify_admin_password()
    {
        $data = file_get_contents("php://input");
        $data = json_decode($data, true);
        $old_password = $data["old_password"];
        $new_password = $data["new_password"];
        $again_password = $data["again_password"];
        if (md5($old_password) !== session("user")->password) {
            echo "原密码错误";
            exit();
        }
        if (strlen($new_password) < 6 || strlen($new_password) > 30) {
            echo "请输入6-30位密码";
            exit();
        }
        if ($new_password != $again_password) {
            echo "新密码两次输入不一致";
            exit();
        }
        $user = (array)session("user");
        $uid = $user["id"];
        try {
            DB::update("update user set password=? where id=?", [md5($new_password), $uid]);
            session(["admin" => ""]);
            echo "ok";
        } catch (Exception $e) {
            echo "提交失败,请重试";
        }
    }
}

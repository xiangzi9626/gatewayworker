<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

class UserInfoController extends Controller
{
    public function getUserInfo(){
        $id=$_GET["id"];
        $session=(array)session("user");
        $uid=$session["id"];
        $user=DB::select("select * from user where id=?",[$id]);
        $user=(array)$user[0];
       $friend=DB::select("select * from friend where (uid=? and friend_id=?) or (uid=? and friend_id=?)",
            [$uid,$id,$id,$uid]);
        if (count($friend)==0){
            $user["friend"]="no";
        }else{
            $user["friend"]="yes";
        }
        $black=DB::select("select * from blacklist where uid=? and black_id=?",[$uid,$id]);
        return view("user.user_info",["user"=>$user,"black"=>$black]);
    }
    public function black(){
        $friend_id=$_POST["friend_id"];
        $black=$_POST["black"];
        $user=(array)session("user");
        $myId=$user["id"];
        try{
            if ($black=="0") {
                DB::delete("delete from blacklist where uid=? and black_id=?", [$myId, $friend_id]);
            }else{
                DB::insert("insert into blacklist(uid,black_id,time) values(?,?,?)",
                    [$myId,$friend_id,date("Y-m-d H:i:s")]);
            }
            echo "ok";
        }catch (Exception $e){
            echo "提交失败请重试";
        }
    }
    public function addFriend(){
        $friend_id=$_POST["friend_id"];
        $user=(array)session("user");
        $myId=$user["id"];
        $black=DB::select("select * from blacklist where uid=? and black_id=?",
            [$friend_id,$myId]);
        if (count($black)>0){
            echo "对方已把你加入黑名单";
            exit();
        }
        $friendCount=DB::select("select count(id) from friend where uid=? or friend_id=?",
            [$myId,$myId]);
        $friendCount=(array)$friendCount[0];
        if ($friendCount["count(id)"]>300){
            echo "好友数量达到上限";
            exit();
        }
        $friend=DB::select("select * from friend where (uid=? and friend_id=?) or (uid=? and friend_id=?)",
            [$myId,$friend_id,$friend_id,$myId]);
        if (count($friend)>0){
            echo "ok";
            exit();
        }
        try{
            DB::beginTransaction();
            DB::insert("insert into friend(uid,friend_id,time)values(?,?,?)",
                [$myId,$friend_id,date("Y-m-d H:i:s")]);
            DB::delete("delete from blacklist where uid=? and black_id=?",[$myId,$friend_id]);
            echo "ok";
            DB::commit();
        }catch (Exception $e){
            echo "添加失败请重试";
            DB::rollBack();
        }
    }
    public function delFriend(){
        $user=(array)session("user");
        $myId=$user["id"];
        $friend_id=$_POST["friend_id"];
         try {
                 DB::beginTransaction();
                    DB::delete("delete from user_chat where send_id=? and receive_id=?",
                        [$myId,$friend_id]);
                    DB::delete("delete from user_chat where send_id=? and receive_id=?",
                        [$friend_id,$myId]);
                    DB::delete("delete from friend where uid=? and friend_id=?",[$myId,$friend_id]);
             DB::delete("delete from friend where uid=? and friend_id=?",[$friend_id,$myId]);
                echo "ok";
                DB::commit();
            }catch (\PHPUnit\Exception $e) {
             echo "删除失败请重试";
             DB::rollBack();
         }
    }
}

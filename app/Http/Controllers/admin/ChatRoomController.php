<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PharIo\Manifest\Exception;

class ChatRoomController extends Controller
{
    public function chat_room_list(){
        $page = $_REQUEST["page"];
        $limit = $_REQUEST["limit"];
        $count = DB::table("chat_room")
            ->select("id")
            ->get()
            ->count();
        $res=DB::table("chat_room")
             ->orderBy("id","desc")
            ->offset(($page-1)*$limit)
            ->limit($limit)
            ->get();
        $arr = ["code" => 0, "msg" => "", "count" => $count, "data" => $res];
        $j = json_encode($arr, JSON_UNESCAPED_UNICODE);
        echo $j;
    }
    public function add(){
        $data=file_get_contents("php://input");
        $arr=json_decode($data,true);
        $arr["time"]=date("Y-m-d H:i:s");
        try {
            DB::table("chat_room")
                ->insert($arr);
            echo "ok";
        }catch (Exception $e){
            echo "操作失败请重试";
        }
    }
    public function delete(){
        $data=file_get_contents("php://input");
        $arr=json_decode($data,true);
        try {
            DB::table("chat_room")
                ->delete(["id"=>$arr["id"]]);
            echo "ok";
        }catch (Exception $e){
            echo "操作失败请重试";
        }
    }
    public function edit_chat_room_show(){
        $res=DB::table("chat_room")->where("id",$_GET["id"])->get();
        $res=(array)$res[0];
        return view("admin.edit_chat_room",["res"=>$res]);
    }
    public function edit($id){
        $data=file_get_contents("php://input");
        $arr=json_decode($data,true);
        if (empty(trim($arr["name"]))){
            echo "聊天室名称不能为空";
            exit();
        }
        try {
            DB::table("chat_room")->where("id",$id)->update($arr);
            echo "ok";
        }catch (Exception $e){
            echo "操作失败请重试";
        }
    }
}

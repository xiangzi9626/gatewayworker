<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use GatewayClient\Gateway;
use Illuminate\Support\Facades\DB;

require_once base_path("/lib/GatewayWorker/vendor/GatewayClient-3.0.13/Gateway.php");
require_once base_path("/lib/base64.php");
require_once base_path("/lib/Upload.class.php");
require_once base_path("/lib/UploadAudio.class.php");

class GroupChatController extends Controller
{
    public $ip = "127.0.0.1:1238";

    public function bind()
    {
        $group_id = session("group");
        Gateway::joinGroup($_POST["client_id"], $group_id);
    }
    public function deleteChat($gid){
        $str="";
        $data=DB::select("select id,type,content from group_chat where gid=? limit ?,?",[$gid,0,10]);
        for ($i=0;$i<count($data);$i++){
            if ($data[$i]->type=="text"){
                $str.=$data[$i]->id.",";
            }else if($data[$i]->type=="img" || $data[$i]->type=="audio"){
                $filePath=$_SERVER["DOCUMENT_ROOT"].$data[$i]->content;
                if (is_file($filePath)){
                    unlink($filePath);
                }
                if (!is_file($filePath)){
                    $str.=$data[$i]->id.",";
                }
            }
        }
        $str=substr($str,0,-1);
        DB::delete("delete from group_chat where id in ({$str})");
    }
    public function layout()
    {
        session(["group" => $_GET["id"]]);
        $count = DB::select("select count(id) from group_chat where gid=?", [$_GET["id"]]);
        $count = (array)$count[0];
        $limit = 0;
        if ($count["count(id)"] >= 100) {
          $this->deleteChat($_GET["id"]);
            $limit = $count["count(id)"] - 100;
        }
        $data = DB::select("select group_chat.*,user.username,user.nickname,user.head_img from group_chat left join user on group_chat.uid=user.id  where group_chat.gid=? order by group_chat.id asc limit ?,?",
            [$_GET["id"], $limit, 100]);
        return view("user.group_chat_layout", ["data" => $data]);
    }

    public function send()
    {
        // ??????GatewayWorker?????????Register??????ip????????????????????????????????????????????????(ip?????????0.0.0.0)
        Gateway::$registerAddress = $this->ip;
        if (strlen($_POST["msg"]) > 6000) {
            $msg["type"] = "error";
            $msg["msg"] = "???????????????????????????";
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            exit();
        }
        $time = date("Y-m-d H:i:s");
        $user = (array)session("user");
        $uid = $user["id"];
        if (empty(session("user")->nickname)) {
            $nickname = session("user")->username;
        } else {
            $nickname = session("user")->nickname;
        }
        $headImg = session("user")->head_img;
        $insert = DB::insert("insert into group_chat(uid,gid,content,type,time) values(?,?,?,?,?)",
            [$uid, $_POST["gid"], $_POST["msg"], "text", $time]);
        if ($insert > 0) {
            $msg["type"] = "text";
            $msg["msg"] = $_POST["msg"];
            $msg["uid"] = $uid;
            $msg["nickname"] = $nickname;
            $msg["headImg"] = $headImg;
            $jsonMsg = json_encode($msg, JSON_UNESCAPED_UNICODE);
            Gateway::sendToGroup(session("group"), $jsonMsg);
            echo $jsonMsg;
        } else {
            $msg["type"] = "error";
            $msg["msg"] = "????????????,?????????";
            $jsonMsg = json_encode($msg, JSON_UNESCAPED_UNICODE);
            echo $jsonMsg;
        }
    }

    public function upload()
    {
        // ??????GatewayWorker?????????Register??????ip????????????????????????????????????????????????(ip?????????0.0.0.0)
        Gateway::$registerAddress = $this->ip;
        $file = $_FILES["upload"];
        $user = (array)session("user");
        $uid = $user["id"];
        if (empty(session("user")->nickname)) {
            $nickname = session("user")->username;
        } else {
            $nickname = session("user")->nickname;
        }
        $headImg = session("user")->head_img;
        //???????????????base64
        //$base64 = base64($file["tmp_name"]);
        //$img = "<img class='msg_img' style='max-width: 60%;max-height:300px' src='data:image/jpg/png/gif;base64,$base64'>";

        //???????????????????????????
        $upload = new \Upload();
        $dir = $_SERVER["DOCUMENT_ROOT"] . "/upload/chat/group/img";
        $fileName = date("YmdHis") . rand(1000, 9999) . "-" . $uid;
        $arr = $upload->upload_img($dir, $file, $fileName);
        $src = "/upload/chat/group/img/" . $arr["pic"];//??????????????????
        //$img = "<img class='msg_img' style='max-width: 60%;max-height:300px' src='$src'>";
        $insert = DB::insert("insert into group_chat(uid,gid,type,content,time) values(?,?,?,?,?)",
            [$uid, $_POST["gid"], "img", $src, date("Y-m-d H:i:s")]);
        if ($insert > 0) {
            $msg["type"] = "img";
            $msg["msg"] = $src;
            $msg["uid"] = $uid;
            $msg["nickname"] = $nickname;
            $msg["headImg"] = $headImg;
            $jsonMsg = json_encode($msg, JSON_UNESCAPED_UNICODE);
            Gateway::sendToGroup(session("group"), $jsonMsg);
            echo $jsonMsg;
        } else {
            $msg["type"] = "error";
            $msg["msg"] = "?????????????????????";
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
    }

    public function uploadAudio()
    {
        // ??????GatewayWorker?????????Register??????ip????????????????????????????????????????????????(ip?????????0.0.0.0)
        Gateway::$registerAddress = $this->ip;
        $file = $_FILES["blob"];
        $user = (array)session("user");
        $uid = $user["id"];
        if (empty(session("user")->nickname)) {
            $nickname = session("user")->username;
        } else {
            $nickname = session("user")->nickname;
        }
        $headImg = session("user")->head_img;
        //?????????????????????
        $dir = $_SERVER["DOCUMENT_ROOT"] . "/upload/chat/group/audio";
        $fileName = date("YmdHis") . rand(1000, 9999) . "-" . $uid;
        $upload = new \UploadAudio();
        $arr = $upload->upload($dir, $file, $fileName);
        $src = "/upload/chat/group/audio/" . $arr["pic"]."?duration=".$_POST["duration"];//??????????????????
        /*$audio = "<span onclick='playAudio($src)' class='audioBox' style='width:100%;height:50px'>
<img src='/static/images/voiceplayer1.gif'/></span>";*/
        $insert = DB::insert("insert into group_chat(uid,gid,type,content,time) values(?,?,?,?,?)",
            [$uid, $_POST["gid"], "audio", $src, date("Y-m-d H:i:s")]);
        if ($insert > 0) {
            $msg["type"] = "audio";
            $msg["msg"] = $src;
            $msg["uid"] = $uid;
            $msg["nickname"] = $nickname;
            $msg["headImg"] = $headImg;
            $jsonMsg = json_encode($msg, JSON_UNESCAPED_UNICODE);
            Gateway::sendToGroup(session("group"), $jsonMsg);
            echo $jsonMsg;
        } else {
            $msg["type"] = "error";
            $msg["msg"] = "?????????????????????";
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
    }
}

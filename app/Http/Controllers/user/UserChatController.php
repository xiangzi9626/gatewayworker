<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use GatewayClient\Gateway;
use Illuminate\Support\Facades\DB;

require_once base_path("/lib/GatewayWorker/vendor/GatewayClient-3.0.13/Gateway.php");
require_once base_path("/lib/base64.php");
require_once base_path("/lib/Upload.class.php");
require_once base_path("/lib/UploadAudio.class.php");
class UserChatController extends Controller
{
    public $ip = "127.0.0.1:1238";

    public function bind()
    {
        $user = (array)session("user");
        $uid = $user["id"];
        Gateway::bindUid($_POST["client_id"], $uid);
    }
    public function deleteChat($send_id,$receive_id){
        $str="";
        $data=DB::select("select id,type,content from user_chat
where (send_id=? and receive_id=? and status=?) or (send_id=? and receive_id=? and status=?) limit ?,?",
            [$send_id,$receive_id,1,$receive_id,$send_id,1,0,10]);
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
        DB::delete("delete from user_chat where id in ({$str})");
    }
    public function layout()
    {
        $user = (array)session("user");
        $uid = $user["id"];
        DB::update("update user_chat set status=? where send_id=? and receive_id=?",
            [1, $_GET["id"], $uid]);
        $count = DB::select("select count(id) from user_chat where
 (send_id=? and receive_id=?) or (send_id=? and receive_id=?)",[$uid,$_GET["id"],$_GET["id"],$uid]);
        $count=(array)$count[0];
       if ($count["count(id)"] > 100) {
            $this->deleteChat($uid,$_GET["id"]);
        }
        $data = DB::select("select user_chat.*,user.username,user.nickname,user.head_img from user_chat left join user on user_chat.send_id=user.id
where (user_chat.send_id=? and receive_id=?) or (user_chat.send_id=? and receive_id=?)
order by user_chat.id asc", [$_GET["id"], $uid, $uid, $_GET["id"]]);
        return view("user.user_chat_layout", ["data" => $data]);
    }

    public function send()
    {
        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值(ip不能是0.0.0.0)
        Gateway::$registerAddress = $this->ip;
        if (strlen($_POST["msg"]) > 6000) {
            $msg["type"] = "error";
            $msg["msg"] = "内容过长请分开发送";
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            exit();
        }
        $time = date("Y-m-d H:i:s");
        $user = (array)session("user");
        $uid = $user["id"];
        $black = DB::select("select * from blacklist where uid=? and black_id=?",
            [$_POST["receive_id"], $uid]);
        if (count($black) > 0) {
            $msg["type"] = "error";
            $msg["msg"] = "对方已把你加入黑名单";
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            exit();
        }
        $sendCount = DB::select("select count(id) from user_chat where send_id=? and receive_id=? and status=?",
            [$uid, $_POST["receive_id"], 0]);
        $sendCount = (array)$sendCount[0];
        if ($sendCount["count(id)"] > 100) {
            $msg["type"] = "error";
            $msg["msg"] = "发送频繁请稍后再试";
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            exit();
        }
        if (empty(session("user")->nickname)) {
            $nickname = session("user")->username;
        } else {
            $nickname = session("user")->nickname;
        }
        $headImg = session("user")->head_img;
        $insert = DB::insert("insert into user_chat(send_id,receive_id,type,content,time) values(?,?,?,?,?)",
            [$uid, $_POST["receive_id"], "text", $_POST["msg"], $time]);
        if ($insert > 0) {
            $msg["type"] = "text";
            $msg["msg"] = $_POST["msg"];
            $msg["uid"] = $uid;
            $msg["nickname"] = $nickname;
            $msg["headImg"] = $headImg;
            $jsonMsg = json_encode($msg, JSON_UNESCAPED_UNICODE);
            Gateway::sendToUid([$uid, $_POST["receive_id"]], $jsonMsg);
            echo $jsonMsg;
        } else {
            $msg["type"] = "error";
            $msg["msg"] = "发送失败,请重试";
            $jsonMsg = json_encode($msg, JSON_UNESCAPED_UNICODE);
            echo $jsonMsg;
        }
    }

    public function upload()
    {
        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值(ip不能是0.0.0.0)
        Gateway::$registerAddress = $this->ip;
        $file = $_FILES["upload"];
        $user = (array)session("user");
        $uid = $user["id"];
        $black = DB::select("select * from blacklist where uid=? and black_id=?",
            [$_POST["receive_id"], $uid]);
        if (count($black) > 0) {
            $msg["type"] = "error";
            $msg["msg"] = "对方已把你加入黑名单";
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            exit();
        }
        $sendCount = DB::select("select count(id) from user_chat where send_id=? and receive_id=? and status=?",
            [$uid, $_POST["receive_id"], 0]);
        $sendCount = (array)$sendCount[0];
        if ($sendCount["count(id)"] > 500) {
            $msg["type"] = "error";
            $msg["msg"] = "发送频繁请稍后再试";
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            exit();
        }
        if (empty(session("user")->nickname)) {
            $nickname = session("user")->username;
        } else {
            $nickname = session("user")->nickname;
        }
        $headImg = session("user")->head_img;
        //把图片转成base64
        //$base64 = base64($file["tmp_name"]);
        //$img = "<img class='msg_img' style='max-width: 60%;max-height:300px' src='data:image/jpg/png/gif;base64,$base64'>";
//调用上传图片的方法
        $upload = new \Upload();
        $dir = $_SERVER["DOCUMENT_ROOT"] . "/upload/chat/user/img";
        $fileName = date("YmdHis") . rand(1000, 9999) . "-" . $uid;
        $arr = $upload->upload_img($dir, $file, $fileName);
        $src = "/upload/chat/user/img/" . $arr["pic"];//得到图片名称
        $insert = DB::insert("insert into user_chat(send_id,receive_id,type,content,time) values(?,?,?,?,?)",
            [$uid, $_POST["receive_id"], "img", $src, date("Y-m-d H:i:s")]);
        if ($insert > 0) {
            $msg["type"] = "img";
            $msg["msg"] = $src;
            $msg["uid"] = $uid;
            $msg["nickname"] = $nickname;
            $msg["headImg"] = $headImg;
            $jsonMsg = json_encode($msg, JSON_UNESCAPED_UNICODE);
            Gateway::sendToUid([$uid, $_POST["receive_id"]], $jsonMsg);
            echo $jsonMsg;
        } else {
            $msg["type"] = "error";
            $msg["msg"] = "发送失败请重试";
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
    }

    public function updateMsgStatus()
    {
        $user = (array)session("user");
        $receive_id = $user["id"];
        $send_id = $_POST["send_id"];
        DB::update("update user_chat set status=? where send_id=? and receive_id=? and status=?",
            [1, $send_id, $receive_id, 0]);
    }
    public function uploadAudio()
    {
        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值(ip不能是0.0.0.0)
        Gateway::$registerAddress = $this->ip;
        $file = $_FILES["blob"];
        $user = (array)session("user");
        $uid = $user["id"];
        $black = DB::select("select * from blacklist where uid=? and black_id=?",
            [$_POST["receive_id"], $uid]);
        if (count($black) > 0) {
            $msg["type"] = "error";
            $msg["msg"] = "对方已把你加入黑名单";
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            exit();
        }
        $sendCount = DB::select("select count(id) from user_chat where send_id=? and receive_id=? and status=?",
            [$uid, $_POST["receive_id"], 0]);
        $sendCount = (array)$sendCount[0];
        if ($sendCount["count(id)"] > 500) {
            $msg["type"] = "error";
            $msg["msg"] = "发送频繁请稍后再试";
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            exit();
        }
        if (empty(session("user")->nickname)) {
            $nickname = session("user")->username;
        } else {
            $nickname = session("user")->nickname;
        }
        $headImg = session("user")->head_img;
        //把图片转成base64
        //$base64 = base64($file["tmp_name"]);
        //$img = "<img class='msg_img' style='max-width: 60%;max-height:300px' src='data:image/jpg/png/gif;base64,$base64'>";
//调用上传图片的方法
        $upload = new \UploadAudio();
        $dir = $_SERVER["DOCUMENT_ROOT"] . "/upload/chat/user/audio";
        $fileName = date("YmdHis") . rand(1000, 9999) . "-" . $uid;
        $arr = $upload->upload($dir, $file, $fileName);
        $src = "/upload/chat/user/audio/" . $arr["pic"]."?duration=".$_POST["duration"];//得到图片名称
        $insert = DB::insert("insert into user_chat(send_id,receive_id,type,content,time) values(?,?,?,?,?)",
            [$uid, $_POST["receive_id"], "audio", $src, date("Y-m-d H:i:s")]);
        if ($insert > 0) {
            $msg["type"] = "audio";
            $msg["msg"] = $src;
            $msg["uid"] = $uid;
            $msg["nickname"] = $nickname;
            $msg["headImg"] = $headImg;
            $jsonMsg = json_encode($msg, JSON_UNESCAPED_UNICODE);
            Gateway::sendToUid([$uid, $_POST["receive_id"]], $jsonMsg);
            echo $jsonMsg;
        } else {
            $msg["type"] = "error";
            $msg["msg"] = "发送失败请重试";
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
    }
}

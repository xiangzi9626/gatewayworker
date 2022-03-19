<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MsgController extends Controller
{
    public function layout()
    {
        $user = (array)session("user");
        $uid = $user["id"];
        $msgCount = DB::select("select count(id) from user_chat where receive_id=? and status=?",
            [$uid,0]);
        $msgCount = (array)$msgCount[0];
        $data = DB::select("select max(user_chat.id) as maxId,count(user_chat.id) as countId,sum(user_chat.status) as status,sendUser.id as sendId,sendUser.username as sendUsername,sendUser.nickname as
    sendNickname,sendUser.head_img as sendHeadImg,receiveUser.id as receiveId,receiveUser.username as receiveUsername,receiveUser.nickname
as receiveNickname,receiveUser.head_img as receiveHeadImg from user_chat left join user as sendUser on
    user_chat.send_id=sendUser.id left join user as receiveUser on user_chat.receive_id=receiveUser.id
where user_chat.receive_id=? or user_chat.send_id=? group by user_chat.send_id,user_chat.receive_id order by maxId desc",
            [$uid,$uid]);
        $arr=array();
        $arr1=array();
        for ($i=0;$i<count($data);$i++){
            if ($data[$i]->sendId==$uid){
                $num=0;
                for ($j=0;$j<count($arr);$j++){
                    if ($data[$i]->receiveUsername==$arr[$j]["username"]){
                        $num++;
                        break;
                    }
                }
                if ($num==0){
                    $arr1["uid"]=$data[$i]->receiveId;
                    $arr1["username"]=$data[$i]->receiveUsername;
                    $arr1["nickname"]=$data[$i]->receiveNickname;
                    $arr1["headImg"]=$data[$i]->receiveHeadImg;
                    $arr1["msgCount"]=0;
                $arr[]=$arr1;
                }
            }else{
                $num=0;
                for ($j=0;$j<count($arr);$j++){
                    if ($data[$i]->sendUsername==$arr[$j]["username"]){
                        $num++;
                        break;
                    }
                }
                if ($num==0){
                    $arr1["uid"]=$data[$i]->sendId;
                    $arr1["username"]=$data[$i]->sendUsername;
                    $arr1["userId"]=$data[$i]->sendId;
                    $arr1["nickname"]=$data[$i]->sendNickname;
                    $arr1["headImg"]=$data[$i]->sendHeadImg;
                    $arr1["msgCount"]=$data[$i]->countId>$data[$i]->status?1:0;
                $arr[]=$arr1;
                }
            }
        }
        return view("user.msg", ["data" =>$arr,"msgCount" => $msgCount["count(id)"]]);
    }
}

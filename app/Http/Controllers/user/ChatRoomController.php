<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatRoomController extends Controller
{
    public function chat_room_list(){
        $arr["code"]=0;
        $arr["msg"]="";
        $count=DB::table("chat_room")->get()->count();
        $arr["count"]=$count;
        $data=DB::table("chat_room")->get();
        $arr["data"]=$data;
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
    }
}

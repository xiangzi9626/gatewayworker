<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserIndexController extends Controller
{
    public function index(){
        $user=(array)session("user");
        $uid=$user["id"];
        $msgCount=DB::select("select count(id) from user_chat where receive_id=? and send_id!=? and status=?",
            [$uid,$uid,0]);
        $msgCount=(array)$msgCount[0];
        return view("user.index",["msgCount"=>$msgCount["count(id)"]]);
    }
}

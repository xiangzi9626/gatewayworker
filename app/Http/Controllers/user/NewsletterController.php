<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\Exception;
use Symfony\Component\HttpKernel\EventListener\SaveSessionListener;

class NewsletterController extends Controller
{
    public function layout(){
        $user=(array)session("user");
        $uid=$user["id"];
        $friend=DB::select("select * from friend where uid=? or friend_id=? order by id desc",
            [$uid,$uid]);
        $arr=array();
        for ($i=0;$i<count($friend);$i++){
            if ($friend[$i]->uid==$uid){
                $arr[]=$friend[$i]->friend_id;
            }else{
                $arr[]=$friend[$i]->uid;
            }
        }
        $str=implode(",",$arr);
        $data=array();
        if (count($friend)>0) {
            $data = DB::select("select * from user where id in ($str)");
        }
        $search=array();
        if (isset($_GET["search"]) && !empty($_GET["search"])){
          $search=DB::select("select * from user where username like ? or nickname like ?",
              ["%{$_GET["search"]}%","%{$_GET["search"]}%"]);
        }
        $msgCount=DB::select("select count(id) from user_chat where receive_id=? and send_id!=? and status=?",
            [$uid,$uid,0]);
        $msgCount=(array)$msgCount[0];
         return view("user.newsletter",["data"=>$data,"search"=>$search,"msgCount"=>$msgCount["count(id)"]]);
    }
}

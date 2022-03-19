<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function report(){
        $user=(array)session("user");
        $uid=$user["id"];
        $report_id=$_POST["report_id"];
        $content=trim($_POST["content"]);
        if (strlen($content)==0){
            echo "内容不能为空";
            exit();
        }
        $insert=DB::insert("insert into report_user(uid,report_id,content,time) values(?,?,?,?)",
            [$uid,$report_id,$content,date("Y-m-d H:i:s")]);
        if ($insert>0){
            echo "ok";
        }else{
            echo "提交失败请重试";
        }
    }
}

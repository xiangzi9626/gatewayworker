<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Couchbase\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class TextController extends Controller
{
    public function power($field){
        $user=(array)session("user");
        $uid=$user["id"];
        if (session("user")->level!=1){
            $power=DB::select("select * from power where aid=? and `type`=? and `$field`=?",[$aid,"problem",1]);
            if (count($power)<1){
                echo "你没有权限操作";
                exit();
            }else{
                $val=$power[0]->{$field};
                if ($val!=1){
                    echo "你没有权限操作";
                    exit();
                }
            }
        }
    }
    public function upload(){
       // $this->power("add");
        $file = Input::file("upload");
        if ($file->isValid()){
            $realPath=$file->getRealPath();//临时文件绝对路径
            $ext = $file->getClientOriginalExtension(); // 文件后缀名
            if ($ext!="jpg" && $ext!="png"){
                echo "<span style='color:red;'>只支持jpg png 图片格式</span>";
                exit();
            }
            $newName=date("YmdHis").mt_rand(1000,9999).".".$ext;
            $file->move("{$_SERVER["DOCUMENT_ROOT"]}/public/upload/news",$newName);
            $pic="/public/upload/news/".$newName;
            $CKEditorFuncNum = $_GET["CKEditorFuncNum"];
            echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction('$CKEditorFuncNum',\"$pic\",'');</script>";
        }
    }
    public function delProblem(){
        $this->power("del");
        $id=$_POST["id"];
        if(preg_match("/^[0-9,]+$/",$id)!=1){
            echo "请示失败";
            exit();
        }
        $str="";
        $totalnews=DB::select("select content from text where id in ($id)");
        for ($i=0;$i<count($totalnews);$i++){
            $str.=$totalnews[$i]->content;
        }
        $n=DB::delete("delete from text where id in($id)");
        if ($n>0){
            echo "ok";
            preg_match_all("/[0-9]+[.jpg|.png]{4}/",$str,$arr);
            for ($i=0;$i<count($arr[0]);$i++){
                $path=$_SERVER["DOCUMENT_ROOT"]."/public/upload/news/".$arr[0][$i];
                if (is_file($path)){
                    unlink($path);
                }
            }
        }else{
            echo "删除失败请重试";
        }
    }
    public function upProblem(){
        $this->power("edit");
        $id=$_POST["id"];
        $title=trim($_POST["title"]);
        $content=$_POST["content"];
        try{
        DB::table("text")->where("id",$id)->update(["title"=>$title,"content"=>$content]);
        echo "ok";
        }catch (Exception $e){
            echo "提交失败,请重试";
        }
    }
    public function editProblem($id){
        $res=DB::table("text")->where("id",$id)->get();
        return view("admin.edit_problem",["res"=>$res]);
    }
    public function browseProblem(){
        $id=$_POST["id"];
        $row=DB::table("text")->where("id",$id)->get();
        return $row;
    }
    public function showProblem(){
        $res=DB::table("text")->where("type","=","problem")->orderBy("id","desc")->paginate(10);
        return view("admin.problem")->with("res",$res);
    }
    public function add_problem(){
        $this->power("add");
        $user=(array)session("user");
        $uid=$user["id"];
        $type="problem";
        $state=1;
        $title=trim($_POST["title"]);
        $content=$_POST["content"];
        $time=date("Y-m-d H:i:s");
        $insert=DB::insert("insert into text(uid,title,content,type,state,create_time) values(?,?,?,?,?,?)",
            [$uid,$title,$content,$type,$state,$time]);
        if ($insert>0){
            echo "ok";
        }else{
            echo "提交失败,请重试";
        }
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PharIo\Manifest\Exception;

class ArticleController extends Controller
{
    public function delete_article(){
$data=file_get_contents("php://input");
$arr=json_decode($data,true);
        try {
            DB::table("article")
                ->delete(["id"=>$arr["id"]]);
            echo "ok";
        }catch (Exception $e){
            echo "操作失败请重试";
        }
    }
    public function add_article_show(){
        return view("admin.add_article",["select"=>$this->class_select()]);
    }
    public function article_list(){
        $page = $_REQUEST["page"];
        $limit = $_REQUEST["limit"];
        $count = DB::table("article")
            ->select("id")
            ->get()
            ->count();
        //$res = DB::select("select * from article order by id desc limit ?,?", [($page - 1) * $limit, $limit]);
        $res=DB::table("article")
            ->rightJoin('class', 'article.cid', '=', 'class.id')
            ->select('article.id','article.title','article.create_time','class.title as className','status')
            ->orderBy("article.id","desc")
            ->offset(($page-1)*$limit)
            ->limit($limit)
            ->get();
        $arr = ["code" => 0, "msg" => "", "count" => $count, "data" => $res];
        $j = json_encode($arr, JSON_UNESCAPED_UNICODE);
        echo $j;
    }
    public function add_article(){
        $data=file_get_contents("php://input");
        $arr=json_decode($data,true);
        $arr["create_time"]=date("Y-m-d H:i:s");
         try {
            DB::table("article")
                ->insert($arr);
            echo "ok";
        }catch (Exception $e){
            echo "操作失败请重试";
        }
    }
    public function class_select(){
        $data=DB::table("class")
            ->select("id",'pid','title')
            ->get();
        if (count($data)==0){
            $select="";
        }else{
            $select="<select name='cid' id='cid'>";
            for ($i=0;$i<count($data);$i++){
                $cid=$data[$i]->id;
                $title=$data[$i]->title;
                $select.="<option value='$cid'>$title</option>";
            }
            $select.="</select>";
        }
        return $select;
    }
    public function edit_article_show(){
        $res=DB::table("article")->where("id",$_GET["id"])->get();
        $res=(array)$res[0];
        return view("admin.edit_article",["res"=>$res,"select"=>$this->class_select()]);
    }
    public function edit_article($id){
        $data=file_get_contents("php://input");
        $arr=json_decode($data,true);
        try {
            DB::table("article")->where("id",$id)->update($arr);
            echo "ok";
        }catch (Exception $e){
            echo "操作失败请重试";
        }
    }
    public function article_switch(){
        $data=file_get_contents("php://input");
        $arr=json_decode($data,true);
        try {
            DB::table("article")
                ->where('id',$arr["id"])
                ->update(['status'=>$arr["status"]]);
            echo "ok";
        }catch (Exception $e){
            echo "操作失败请重试";
        }
    }
}

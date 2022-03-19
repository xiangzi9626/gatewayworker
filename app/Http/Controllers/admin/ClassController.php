<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use http\Encoding\Stream\Debrotli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Monolog\Handler\IFTTTHandler;

class ClassController extends Controller
{
    public function edit_class($id){
        $data=file_get_contents("php://input");
        $arr=json_decode($data,true);
         $c=DB::table("class")
            ->select("id",'pid')
            ->where("id",$id)
            ->get();
        $child=DB::table("class")
            ->select("id",'pid')
            ->where("pid",$c[0]->id)
            ->get()->count();
        if ($c[0]->pid==0 && $child!=0 && $arr["pid"]!=0){
            echo "该分类下面有子分类不能改变上级分类";
            exit();
        }
        try {
            DB::table("class")
                ->where("id",$id)
                ->update($arr);
            echo "ok";
        }catch (Exception $e){
            echo "操作失败请重试";
        }
    }
    public function edit_class_show(){
        $id=$_GET["id"];
        $res=DB::table("class")->where("id",$id)->get();
        $res=(array)$res[0];
        if ($res["pid"]!=0){
            $f=DB::table("class")->where("id",$res["pid"])->get();
            $select='<select name="pid">';
            $title=$f[0]->title;
            $pid=$f[0]->id;
            $select.="<option value=$pid>";
            $select.=$title;
            $select.="</option>";
            $select.=$this->select();
            $select.='</select>';
        }else{
            $select='<select name="pid">';
            $select.=$this->select();
            $select.='</select>';
        }
        return view("admin.edit_class",["res"=>$res,"select"=>$select]);
    }
    public function delete_class(){
        $data=file_get_contents("php://input");
        $arr=json_decode($data,true);
        $count=DB::table("class")->where("pid",$arr["id"])->get()->count();
        if ($count>0){
            echo "请先删除子分类";
            exit();
        }
        try {
            DB::table("class")
                ->where("id",$arr["id"])
                ->delete();
            echo "ok";
        }catch (Exception $e){
            echo "操作失败请重试";
        }
    }
    public function add_class(){
        $data=file_get_contents("php://input");
        $arr=json_decode($data,true);
        try {
            DB::table("class")
                ->insert($arr);
           echo "ok";
        }catch (Exception $e){
            echo "操作失败请重试";
        }
    }
    public function add_class_show(){
        $select='<select name="pid">';
        $select.=$this->select();
        $select.='</select>';
        return view("admin.add_class",["select"=>$select]);
    }
    private function select(){
        $data=DB::table("class")
            ->select("id",'pid','title')
            ->get();
        $arr=array();
        for ($i=0;$i<count($data);$i++){
            $arr[]=(array)$data[$i];
        }
        if (count($arr)==0){
             $select="<option value='0'>";
            $select.="顶级分类";
            $select.="</option>";
            return $select;
        }
        include base_path("lib/PHPTree/PHPTree.class.php");
        $list=\PHPTree::makeTreeForHtml($arr);
        $select="<option value='0'>";
        $select.="顶级分类";
        $select.="</option>";
        foreach($list as $item) {
            $id=$item['id'];
            $select.="<option value='{$id}'>";
            $select.=str_repeat('....', $item['level']);
            $select.=$item['title'];
            $select.='</option>';
        }
        return $select;
    }
    public function class_list(){
        $list = DB::table('class')
            ->orderBy('id', 'desc')
            ->get();
        $arr=array("code"=>0,"msg"=>"","count"=>count($list),"data"=>$list);
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
    }
}

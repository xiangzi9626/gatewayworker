<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use think\Exception;

class MenuController extends Controller
{
    // 获取初始化数据
    public function getSystemInit(){
        $homeInfo = [
            'title' => '首页',
            'href'  => 'layuimini-2/admin/admin_list.html',
        ];
        $logoInfo = [
            'title' => '后台管理',
            'image' => '/layuimini-2/images/logo.png',
            'href'  => 'layuimini-2/admin/admin_list.html',
        ];
        $menuInfo = $this->getMenuList();
        $systemInit = [
            'homeInfo' => $homeInfo,
            'logoInfo' => $logoInfo,
            'menuInfo' => $menuInfo,
        ];
         return response()->json($systemInit);
    }

    // 获取菜单列表
    private function getMenuList(){
       $menuList = DB::table('system_menu')
           // ->select(['id','pid','title','icon','href','target'])
            ->where('status', 1)
            ->orderBy('sort', 'asc')
            ->get();
        $menuList = $this->buildMenuChild(0, $menuList);
        return $menuList;
    }

    //递归获取子菜单
    private function buildMenuChild($pid, $menuList){
        $treeList = [];
        foreach ($menuList as $v) {
            if ($pid == $v->pid) {
                $node = (array)$v;
                $child = $this->buildMenuChild($v->id, $menuList);
                if (!empty($child)) {
                    $node['child'] = $child;
                }
                // todo 后续此处加上用户的权限判断
                $treeList[] = $node;
            }
        }
        return $treeList;
    }
    public function menu_list(){
      $menuList = DB::table('system_menu')
           ->orderBy('sort', 'desc')
            ->get();
       $arr=array("code"=>0,"msg"=>"","count"=>count($menuList),"data"=>$menuList);
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
    }
    public function add_menu(){
      $data=file_get_contents("php://input");
     $arr=json_decode($data,true);
    $arr["create_at"]=date("Y-m-d H:i:s");
    $insert=DB::table("system_menu")->insert($arr);
   if ($insert>0){
       echo "ok";
   }else{
       echo "添加失败请重试";
   }
    }
    public function menu_switch(){
        $data=file_get_contents("php://input");
        $arr=json_decode($data,true);
        try {
            DB::table("system_menu")
                ->where('id',$arr["id"])
            ->update(['status'=>$arr["status"]]);
            echo "ok";
        }catch (Exception $e){
            echo "操作失败请重试";
        }
    }
    public function add_menu_show(){
        $data=DB::table("system_menu")
            ->select("id",'pid','title')
            ->get();
         $arr=array();
         for ($i=0;$i<count($data);$i++){
             $arr[]=(array)$data[$i];
         }
        if (count($arr)==0){
            $select='<select name="pid">';
            $select.="<option value='0'>";
            $select.="顶级分类";
            $select.="</option>";
            $select.="</select>";
            return view("admin.add_menu",["select"=>$select]);
        }
          include base_path("lib/PHPTree/PHPTree.class.php");
       $list=\PHPTree::makeTreeForHtml($arr);
       $select='<select name="pid">';
        $select.="<option value='0'>";
        $select.="顶级菜单";
        $select.="</option>";
            foreach($list as $item) {
                $id=$item['id'];
             $select.="<option value='{$id}'>";
                $select.=str_repeat('....', $item['level']);
                $select.=$item['title'];
                  $select.='</option>';
         }
        $select.='</select>';
      return view("admin.add_menu",["select"=>$select]);
    }
    public function edit_menu_show(){
         $menu=DB::table("system_menu")
            ->where("id",$_GET["id"])
            ->get();
        $menu=(array)$menu[0];
        //////////
        $data=DB::table("system_menu")
            ->select("id",'pid','title')
            ->get();
        $arr=array();
        for ($i=0;$i<count($data);$i++){
            $arr[]=(array)$data[$i];
        }
        include base_path("lib/PHPTree/PHPTree.class.php");
        $list=\PHPTree::makeTreeForHtml($arr);
        $select='<select name="pid">';
        if ($menu["pid"]==0){
        $select.="<option value='0'>";
        $select.="顶级菜单";
        $select.="</option>";
        }else{
            $f=DB::table("system_menu")
                ->select("id",'pid','title')
                ->where("id",$menu["pid"])
                ->get();
            $f=(array)$f[0];
            $select.="<option value='{$f["id"]}'>";
            $select.=$f["title"];
            $select.="</option>";
            $select.="<option value='0'>";
            $select.="顶级菜单";
            $select.="</option>";
        }
        foreach($list as $item) {
            $id=$item['id'];
            $select.="<option value='{$id}'>";
            $select.=str_repeat('....', $item['level']);
            $select.=$item['title'];
            $select.='</option>';
        }
        $select.='</select>';
        return view("admin.edit_menu",["menu"=>$menu,'select'=>$select]);
    }
    public function edit_menu($id){
        $data=file_get_contents("php://input");
        $arr=json_decode($data,true);
        $menu=DB::table("system_menu")
            ->select("id",'pid')
            ->where("id",$id)
            ->get();
        $child=DB::table("system_menu")
            ->select("id",'pid')
            ->where("pid",$menu[0]->id)
            ->get()->count();
        if ($menu[0]->pid==0 && $child!=0 && $arr["pid"]!=0){
            echo "该菜单下面有子菜单不能改变上级菜单";
            exit();
        }
        try {
            DB::table("system_menu")
                ->where('id',$id)
                ->update($arr);
            echo "ok";
        }catch (Exception $e){
            echo "操作失败请重试";
        }
    }
    public function delete_menu(){
       $data=file_get_contents("php://input");
        $arr=json_decode($data,true);
        $child=DB::table("system_menu")->where("pid",$arr["id"])->get()->count();
        if ($child>0){
            echo "请先删除子菜单";
            exit();
        }
        if ($arr["title"]=="菜单管理" || $arr["id"]==1){
            echo "该菜单不能删除";
            exit();
        }
        try {
            DB::table("system_menu")->where("id",$arr["id"])->delete();
            echo "ok";
        }catch (Exception $e){
            echo "操作失败请重试";
        }
    }
}

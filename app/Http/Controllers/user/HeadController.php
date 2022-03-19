<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class HeadController extends Controller
{
    //头像处理
    public function upload()
    {
        $user=(array)session("user");
        $uid=$user["id"];
        $base64_image_content = $_POST['imgBase'];
        //将base64编码转换为图片保存
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            $type = $result[2];
            $new_file = base_path("/public/upload/head/");
            if (!file_exists($new_file)) {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($new_file, 0700);
            }
            $img = $uid . ".{$type}";
            $new_file = $new_file . $img;
            //将图片保存到指定的位置
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))) {
                try {
                    DB::update("update user set head_img=? where id=?",["/upload/head/".$img,$uid]);
                    $u=DB::select("select * from user where id=?",[$uid]);
                    session(["user"=>$u[0]]);
                    echo "ok";
                }catch (Exception $e){
                    echo "上传失败";
                }
            } else {
                echo '上传失败';
            }
        } else {
            echo '上传失败';
        }
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use foo\Foo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClearController extends Controller
{
    public function clear_cache(){
       $files=Storage::files("/storage/framework/views");
       $fileArr=[];
       for($i=0;$i<count($files);$i++){
           if ($files[$i]=="storage/framework/views/.gitignore"){
               continue;
           }else{
               $fileArr[]=$files[$i];
           }
       }
        Storage::delete($fileArr);
       return '{"code": 1,"msg": "服务端清理缓存成功"}';
    }
}

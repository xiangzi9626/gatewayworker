<?php

namespace App\Http\Controllers\test;

use App\Http\Controllers\Controller;
use App\Http\Controllers\home\ExampleController;
use Egulias\EmailValidator\EmailParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class TestController extends Controller
{
public function test1(){
    return view("test.test1");
}
    public function test2(){
     Redis::set("aaa","bbbbb");
     $val=Redis::get("aa");
     if ($val==null){
         echo "null0";
     }else{
         echo "111";
     }
    }
}

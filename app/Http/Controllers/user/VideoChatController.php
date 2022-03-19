<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VideoChatController extends Controller
{
    public function videoChat(){
        return view("user.video_chat");
    }
    public function phoneChat(){
        return view("user.phone_chat");
    }
}

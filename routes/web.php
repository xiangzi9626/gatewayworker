<?php
Route::get("/test1","test\TestController@test1");
Route::get("/test2","test\TestController@test2");
///////////////////////////////
Route::get('/', function () {
    return view("index");
});
Route::get('/user/login', function () {
    if (session("user")){
        return redirect("/user/index");
    }
    return view("user.login");
});
Route::get("/user/register",function (){
    if (session("user")){
        return redirect("/user/index");
    }
    return view("user.register");
});
Route::post("user/login","user\UserLoginController@login");
Route::post("user/register","user\UserRegisterController@register");
Route::group(["middleware"=>"user_login"],function (){
    Route::get("user/phone_chat","user\VideoChatController@phoneChat");
    Route::get("user/video_chat","user\VideoChatController@videoChat");
    Route::post("/user/upload_user_audio","user\UserChatController@uploadAudio");
    Route::post("/user/upload_group_audio","user\GroupChatController@uploadAudio");
    Route::get("/user/index","user\UserIndexController@index");
    Route::post("/user/chat_room_list","user\ChatRoomController@chat_room_list");
    Route::get("user/group_chat_layout","user\GroupChatController@layout");
    Route::post("user/bindGroup","user\GroupChatController@bind");
    Route::post("user/send_group","user\GroupChatController@send");
    Route::post("user/upload","user\GroupChatController@upload");
    Route::get("user/user_info","user\UserInfoController@getUserInfo");
    Route::post("user/report","user\ReportController@report");
    Route::post("user/black","user\UserInfoController@black");
    Route::post("user/add_friend","user\UserInfoController@addFriend");
    Route::get("user/user_chat_layout","user\UserChatController@layout");
    Route::get("user/newsletter","user\NewsletterController@layout");
    Route::post("user/del_friend","user\UserInfoController@delFriend");
    Route::post("user/send_user_chat","user\UserChatController@send");
    Route::post("user/bind_user","user\UserChatController@bind");
    Route::post("user/user_upload","user\UserChatController@upload");
    Route::get("user/my","user\MyController@my_layout");
    Route::post("user/modify_password","user\MyController@modifyPassword");
    Route::post("user/edit_data","user\MyController@editData");
    Route::get("user/modify_password",function (){
        return view("user.modify_password");
    });
    Route::get("user/edit_data_layout","user\MyController@layout");
    Route::post("user/logout","user\UserLoginController@logout");
    Route::get("user/msg","user\MsgController@layout");
    Route::post("user/update_msg_status","user\UserChatController@updateMsgStatus");
    Route::get("/user/head_cut",function (){
        return view("user.head_cut");
    });
    Route::post("user/head_cut","user\HeadController@upload");
});
////////////////////////////////
///////////////后台////////////////
Route::get("admin/login",function (){
    if (session("user")){
        return redirect("/admin");
    }
    return view("admin.login");
});
Route::post("admin/login","admin\AdminLoginController@login");
Route::get("captcha","admin\AdminLoginController@captcha");
//////////管理后台////////////////////
Route::group(["middleware"=>"admin_login"],function (){
    Route::post("admin/logout","admin\AdminLoginController@logout");
Route::get('/admin', function () {
    return view('admin.index');
});
Route::get("admin/modify_admin_password_show",function (){
    return view("admin.modify_admin_password");
});
Route::post("admin/modify_admin_password","admin\AdminController@modify_admin_password");
Route::get("admin/add_admin_show","admin\AdminController@add_admin_show");
Route::post("admin/add_admin","admin\AdminController@add_admin");
Route::get("admin/edit_admin_show","admin\AdminController@edit_admin_show");
Route::post("admin/edit_admin/{id}","admin\AdminController@edit_admin");
Route::post("admin/delete_admin/{id}","admin\AdminController@delete_admin");
Route::get('admin/admin_list',"admin\AdminController@admin_list");
Route::get("admin/user_list","admin\UserController@user_list");
Route::any('admin/menu_list',"admin\MenuController@menu_list");
Route::get('admin/init',"admin\MenuController@getSystemInit");
Route::get("admin/clear_cache",'admin\ClearController@clear_cache');
Route::get("admin/add_menu_show","admin\MenuController@add_menu_show");
Route::post("admin/add_menu","admin\MenuController@add_menu");
Route::get("admin/edit_menu_show","admin\MenuController@edit_menu_show");
Route::post("admin/edit_menu/{id}","admin\MenuController@edit_menu");
Route::post("admin/delete_menu","admin\MenuController@delete_menu");
Route::post("admin/menu_switch","admin\MenuController@menu_switch");
Route::get("admin/article_list","admin\ArticleController@article_list");
Route::get("admin/add_article_show","admin\ArticleController@add_article_show");
Route::post("admin/add_article","admin\ArticleController@add_article");
Route::get("admin/edit_article_show","admin\ArticleController@edit_article_show");
Route::post("admin/edit_article/{id}","admin\ArticleController@edit_article");
Route::post("admin/article_switch","admin\ArticleController@article_switch");
Route::post("admin/delete_article","admin\ArticleController@delete_article");
Route::get("admin/class_list","admin\ClassController@class_list");
Route::get("admin/add_class_show","admin\ClassController@add_class_show");
Route::post("admin/add_class","admin\ClassController@add_class");
Route::get("admin/edit_class_show","admin\ClassController@edit_class_show");
Route::post("admin/edit_class/{id}","admin\ClassController@edit_class");
Route::post("admin/delete_class","admin\ClassController@delete_class");
Route::get("admin/class_select","admin\ArticleController@class_select");
Route::get("admin/chat_room","admin\ChatRoomController@chat_room_list");
Route::get("/admin/add_chat_room",function (){
    return view("admin.add_chat_room");
});
Route::post("/admin/delete_chatroom","admin\ChatRoomController@delete");
Route::post("/admin/add_chat_room","admin\ChatRoomController@add");
Route::get("/admin/edit_chat_room","admin\ChatRoomController@edit_chat_room_show");
Route::post("/admin/edit_chat_room/{id}","admin\ChatRoomController@edit");
});

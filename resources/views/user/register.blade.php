<!DOCTYPE html>
<html>
    <head>
        <title>欢迎注册</title>
        <meta charset="utf-8">
        <meta name="format-detection" content="telephone=no">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-touch-fullscreen" content="yes">
        <meta http-equiv="Access-Control-Allow-Origin" content="*">
        <link href="/static/user/css/register.css" type="text/css" rel="stylesheet">
        <link href="/static/user/css/global.css" type="text/css" rel="stylesheet">
        <script src="/common/js/jquery-3.2.1.js"></script>
        <script src="/common/js/ajax.js"></script>
        <script src="/common/js/layer3.1.1/layer.js"></script>
        <script>
            function register() {
               var username=document.getElementById("username");
               var password=document.getElementById("password");
               var password2=document.getElementById("password2");
                if (username.value === "" || /^\s+$/.test(username.value)) {
                    layer.msg("请输入用户名");
                    return false;
                }
                if (password.value === "" || /^\s+$/.test(password.value)) {
                    layer.msg("请输入密码");
                    return false;
                }
                if (password2.value === "" || /^\s+$/.test(password2.value)) {
                    layer.msg("请输入确认密码");
                    return false;
                }
                if (password.value!==password2.value){
                    layer.msg("两次密码输入不一致");
                    return false;
                }
                var string="username="+username.value+"&password="+password.value
                    +"&password2="+password2.value+"&_token={{csrf_token()}}";
                ajax("/user/register",data=string,function (str) {
                   if(str==="ok"){
                       layer.open({
                           "title":"提示",
                           "content":"恭喜你!注册成功",
                           btn:["立即登录"],
                           yes: function(index, layero){
                               window.location="/user/index";
                           }
                       })
                   }else{
                       layer.msg(str);
                   }
                })
            }
        </script>
    </head>
    <body>
        <div id="layout">
            @if(strpos($_SERVER['HTTP_USER_AGENT'],"MicroMessenger")===false)
            <span>欢迎注册</span>
            @endif
            <form  method="post" onsubmit="return false">
            <ul>
                <p id="err_msg"></p>
                <li><i class="un"><img src="/static/user/images/user_name.png"></i>
                    <input id="username" class="username" type="text" style="border: 1px solid red;" placeholder="请输入用户名"></li>
                <li><i class="pw"><img src="/static/user/images/pwd.png"></i>
                    <input id="password" class="pwd" type="password" placeholder="请输入密码" /></li>
                <li><i class="pw2"><img src="/static/user/images/pwd.png"></i>
                    <input id="password2" class="pwd2" type="password" placeholder="请输入确认密码" /></li>
                <div class="queren"><input class="fx" type="checkbox" checked="checked" /><p>我已阅读并同意《用户协议》</p></div>
            </ul>
                <div class="reg_btn">
                    <button onclick="register()" class="submit" type="submit">注册</button>
                    <a href="/user/login"><div class="reg-login"><p>已有帐号，立即登陆</p></div></a>
                </div>
            </form>
        </div>
    </body>
</html>

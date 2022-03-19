<!DOCTYPE html>
<html>
    <head>
        <title>用户登录</title>
         <meta charset="utf-8">
        <meta name="format-detection" content="telephone=no">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-touch-fullscreen" content="yes">
        <meta http-equiv="Access-Control-Allow-Origin" content="*">
        <link href="/static/user/css/login.css" type="text/css" rel="stylesheet">
        <link href="/static/user/css/global.css" type="text/css" rel="stylesheet">
        <script src="/common/js/jquery-3.2.1.js"></script>
        <script src="/common/js/ajax.js"></script>
        <script src="/common/js/layer3.1.1/layer.js"></script>
        <script src="/common/js/jquery.cookie.js"></script>
         <script type="text/javascript">
            function login() {
                var username = document.getElementById("username");
                var password = document.getElementById("password");
                //var captcha = document.getElementById("captcha");
                //var remember = document.getElementById("remember");
                if ((username.value === "" && password.value === "") || (/^\s+$/.test(username.value) && /^\s+$/.test(password.value))) {
                    layer.msg("请输入用户名和密码");
                } else if (username.value === "" || /^\s+$/.test(username.value)) {
                    layer.msg("请输入用户名");
                } else if (password.value === "" || /^\s+$/.test(password.value)) {
                    layer.msg("请输入密码");
                }else {
                    var string = "username=" + username.value + "&password=" + password.value +
                        "&_token={{csrf_token()}}";
                    ajax("/user/login", data = string, function (str) {
                        if (str === "ok") {
                            $.cookie('username', username.value, {expires: 30,path: '/'});
                            $.cookie('password', password.value, {expires: 30,path: '/'});
                           window.location = "/user/index";
                        } else {
                            layer.msg(str);
                            return false;
                        }
                    })
                }
            }
        </script>
    </head>
    <body>
        <div class="login">
            <div class="login-title">
                @if(strpos($_SERVER['HTTP_USER_AGENT'],"MicroMessenger")===false)
                <p>用户登录</p>
                <i></i>
                @endif
            </div>
            <form method="post" action="?" onsubmit="return false">
            <div class="login-bar">
                <ul>
                    <li><img src="/static/user/images/login_user.png"><input id="username" type="text" class="text" placeholder="请输入用户名" /></li>
                    <li><img src="/static/user/images/login_pwd.png"><input id="password" type="password" class="psd" placeholder="请输入确认密码" /></li>
                </ul>
            </div>
            <div class="login-btn">
                <button onclick="login()" class="submit" type="submit">登陆</button>
                <a href="/user/register"><div class="login-reg"><p>没有账号，先注册</p></div></a>
            </div>
            </form>
        </div>
        <script>
            if ($.cookie('username') != null) {
                var username = document.getElementById("username");
                var password=document.getElementById("password");
                username.value = $.cookie('username');
                password.value = $.cookie('password');
                login();
            }
        </script>
    </body>
</html>

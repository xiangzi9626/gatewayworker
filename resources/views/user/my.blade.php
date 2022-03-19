<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta HTTP-EQUIV="pragma" CONTENT="no-cache">
    <meta HTTP-EQUIV="Cache-Control" CONTENT="no-store, must-revalidate">
    <meta HTTP-EQUIV="expires" CONTENT="Wed, 26 Feb 1997 08:21:57 GMT">
    <meta HTTP-EQUIV="expires" CONTENT="0">
    <title>个人中心</title>
    <link rel="stylesheet" href="/common/bootstrap-4.6.1/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/common/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="/common/js/jquery-3.2.1.js"></script>
    <script src="/common/js/ajax.js"></script>
    <script src="/common/js/layer3.1.1/layer.js"></script>
    <script src="/common/js/jquery.cookie.js"></script>
    <script>
        function logout(){
            layer.open({
                "title":"退出登录",
                "content":"确定退出登录吗?",
                "btn":["确定","取消"],
                yes:function(){
                    ajax("/user/logout","_token={{csrf_token()}}",function (res){
                        $.cookie('username',null,{ path: '/'});
                        $.cookie('password',null,{ path: '/'});
                        window.location="/";
                    })
            }
            })
        }
    </script>
</head>
<body>
@if(strpos($_SERVER['HTTP_USER_AGENT'],"MicroMessenger")===false)
    <div class="container-fluid" style="font-size:20px;line-height:40px;height:40px;background: #0b2e13;text-align: center;color:#fff;font-weight:600;">
        个人中心
    </div>
@endif
<div id="content" class="container" style="text-align: left;">
    <div style="width:100%;margin-top: 20px;">
        <div style="float:left;">
            <a style="text-decoration: none" href="/user/user_info?id={{$user["id"]}}">
                <img style="width:50px;height:50px;" src="{{$user["head_img"]}}?t={{time()}}">
            </a>
        </div>
        <div style="float:left;margin-left:10px;">
        <span style="font-weight:800">
            @if(empty($user["nickname"]))
                {{$user["username"]}}
            @else
                {{$user["nickname"]}}
            @endif
        </span>

            @if($user["sex"]=="男")
                <span style="color: #0e5a91;font-weight:900"><i class="fa fa-neuter"></i></span>
            @else
                <span style="color: red;font-weight:900"><i class="fa fa-mars"></i></span>
            @endif

            <br>
            <span style="color: grey">
            用户ID {{$user["username"]}}
        </span>
        </div>
        <div style="width: 100%;height:1px;clear: both"></div>
        <div style="margin-top:20px;clear:both;color: #3F3F3F;">
            <a href="/user/head_cut?head_img={{$user["head_img"]}}&t={{time()}}" style="text-decoration:none;color: #3F3F3F;">
                <i class="fa fa-user-circle"></i>&nbsp;头像</a></div>
        <div style="margin-top:20px;clear:both;">
            <a href="/user/edit_data_layout" style="text-decoration:none;color: #3F3F3F;"><i class="fa fa-edit"></i>&nbsp;编辑资料</a></div>
        <div style="margin-top:20px;clear:both;">
            <a href="/user/modify_password" style="text-decoration:none;color: #3F3F3F"><i class="fa fa-key"></i>&nbsp;修改密码</a></div>
        <div style="width:100%;text-align:center;margin-top:20px;clear:both;color:darkred">
            <span onclick="logout()"><i class="fa fa-power-off"></i>&nbsp;退出登录</span>
        </div>
</div>
</div>
<footer class="container-fluid fixed-bottom" style="background:#fff;padding-bottom:10px;">
     <div class="row">
            <div class="col-3" style="text-align:center">
                <a href="/user/index" style="color:grey;width:100%;padding:15px 0">
                    <span class="fa fa-group" style="color:grey;width:100%;"></span>
                    <br>群聊</a>
            </div>
            <div class="col-3" style="text-align:center;color:grey">
                <a style="width:100%;padding:15px 0;color:grey" href="/user/msg" class="text-default">
                    <span class="fa fa-comment-o" style="width:100%;color:grey"></span><br>
                    消息
                </a>
                @if($msgCount>0 && $msgCount<=10)
                    <span style="top:-8px;position:absolute;" class="badge badge-danger">
                    {{$msgCount}}
                </span>
                @elseif($msgCount>0 && $msgCount>10)
                    <span style="top:-8px;position:absolute;" class="badge badge-danger">
                        9+
                    </span>
                @endif
            </div>
            <div class="col-3" style="text-align:center;color:grey">
                <a href="/user/newsletter" class="text-default" style="width:100%;padding:15px 0;color:grey">
                    <span class="fa fa-drivers-license-o" style="width:100%;"></span> <br>
                    通迅录
                </a>
            </div>
            <div class="col-3" style="text-align:center">
                <a href="/user/my" class="text-default" style="color:green;width:100%;padding:15px 0">
                    <span class="fa fa-user-o" style="width:100%;color:green;"></span> <br>
                    我的
                </a>
            </div>
        </div>
</footer>
<script>
    $(function (){
        $("#content").height(document.documentElement.clientHeight-105+"px");
    })
</script>
</body>
</html>

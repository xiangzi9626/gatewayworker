<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
    <meta http-equiv="Pragma" content="no-cache"/>
    <meta http-equiv="Expires" content="0"/>
    <title>消息</title>
    <link rel="stylesheet" href="/common/bootstrap-4.6.1/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/common/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="/common/js/jquery-3.2.1.js"></script>
    <script src="/common/js/ajax.js"></script>
    <script src="/common/js/layer3.1.1/layer.js"></script>
    <style>
        ul {
            width: 100%;
            list-style-type: none;
        }

        ul li {
            width: 100%;
            margin-top: 20px;
        }

        ul a {
            width: 100%;
        }

        ul img {
            width: 50px;
            height: 50px;
        }
    </style>
    <script>
        window.onload = function () {
            //创建websocket;
            var socket = null;
            if (window.location.protocol==="https:"){
                socket = new WebSocket("wss://"+document.domain+":8282");
            }else{
                socket = new WebSocket("ws://"+document.domain+":8282");
            }
           //当socket建立连接时触发的事件
            socket.addEventListener("open", function () {

            })
            //收到消息
            socket.addEventListener("message", function (e) {
                var obj = eval("(" + e.data + ")");
                switch (obj.type) {
                    case "init":
                        ajax("/user/bind_user", "_token={{csrf_token()}}&client_id=" + obj.client_id, function (res) {
                        })
                        break;
                    case "text":
                        var href = "/user/user_chat_layout?id=" + obj.uid + "&nickname=" + obj.nickname;
                        var str = '<a id=' + obj.uid + ' style="text-decoration:none;color:grey;" href=' + href + '>';
                        str += '<li>';
                        if (obj.uid !== "{{session("user")->id}}") {
                            str += '<span style="position:absolute">';
                            str += '<p style="border-radius:50%;top:-7px;left:42px;position:relative;width:15px;height:15px;background:red"></p>';
                            str += '</span>';
                        }
                        str += '<img src="' + obj.headImg + '">&nbsp;';
                        str += '<span>'
                        str += obj.nickname;
                        str += '</span>';
                        str += '</li></a>';
                        if (document.getElementById(obj.uid)) {
                            $("#" + obj.uid).remove();
                            $("#msgUl").prepend(str);
                        } else {
                            $("#msgUl").prepend(str);
                        }
                        break;
                    case "ping":
                        break;
                    case "error":
                        break;
                    default:
                        break;
                }

            })
            socket.addEventListener("close", function () {
                layer.open({
                    "title": "提示",
                    'content': "与服务器断开连接",
                    "btn": ["确定", "取消"],
                    yes: function () {
                        window.location = "/user/newsletter";
                    }
                })
            })
        }
    </script>
    <script>
        $(function () {
            $("#vueContainer").height(document.documentElement.clientHeight - 105 + "px");
        })
    </script>
</head>
<body>
@if(strpos($_SERVER['HTTP_USER_AGENT'],"MicroMessenger")===false)
    <div class="container-fluid"
         style="font-size:20px;line-height:40px;height:40px;background: #0b2e13;text-align: center;color:#fff;font-weight:600;">
        消息
    </div>
@endif
<div class="container" id="vueContainer" style="overflow:scroll;height:calc(100vh - 95px);">
    <ul class="col-12" id="msgUl" style="margin: 20px 0 60px 0;padding:0 0 20px 0">
        @for($i=0;$i<count($data);$i++)
            <a id="{{$data[$i]["uid"]}}" style="text-decoration: none;color: grey"
               href="/user/user_chat_layout?id={{$data[$i]["uid"]}}&nickname={{$data[$i]["nickname"]}}">
                <li>
                    @if($data[$i]["msgCount"]>0)
                        <span style="position:absolute;">
                          <p style="border-radius:50%;top:-7px;left:42px;position:relative;width:15px;height:15px;background:red"></p>
                          </span>
                    @endif
                    <img src="{{$data[$i]["headImg"]}}?t={{time()}}">&nbsp;
                    <span>{{$data[$i]["nickname"]}}</span>
                </li>
            </a>
        @endfor
    </ul>
</div>
<footer class="container-fluid fixed-bottom" style="background:#fff;padding-bottom:10px;">
    <div class="row">
        <div class="col-3" style="text-align:center">
            <a href="/user/index" style="color:grey;width:100%;padding:15px 0">
                <span class="fa fa-group" style="color:grey;width:100%;"></span>
                <br>群聊</a>
        </div>
        <div class="col-3" style="text-align:center;color:grey">
            <a style="width:100%;padding:15px 0;color:green" href="/user/msg" class="text-default">
                <span class="fa fa-comment-o" style="width:100%;color:green"></span><br>
                消息
            </a>
            @if($msgCount>0)
                <span style="top:-6px;position:absolute;" class="badge badge-danger">
                        <i class="fa fa-ellipsis-h"></i>
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
            <a href="/user/my" class="text-default" style="color:grey;width:100%;padding:15px 0">
                <span class="fa fa-user-o" style="width:100%;color:grey;"></span> <br>
                我的
            </a>
        </div>
    </div>
</footer>
</body>
</html>

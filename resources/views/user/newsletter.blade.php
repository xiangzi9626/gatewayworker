<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>通迅录</title>
    <link rel="stylesheet" href="/common/bootstrap-4.6.1/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/common/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/layuimini-2/lib/layui-v2.5.5/css/layui.css" media="all"/>
    <script src="/common/js/jquery-3.2.1.js"></script>
    <script src="/common/js/ajax.js"></script>
    <script src="/common/js/layer3.1.1/layer.js"></script>
    <style>
        ul{
            width:100%;
            list-style-type: none;
        }
        ul li{
            width:100%;
            margin-top: 20px;
        }
        ul a{
            width: 100%;
        }
        ul img{
            width:50px;
            height: 50px;
        }
    </style>
    <script>
        function search(){
            layer.open({
                "title":"查找好友",
                "type":1,
                "content":"<input id='search' class='form-control' placeholder='请入昵称/用户ID搜索'>",
                "btn":["查找","取消"],
                yes:function (){
                    var search=document.getElementById("search");
                    if (search.value === "" || /^\s+$/.test(search.value)) {
                        layer.msg("请输入关键字搜索");
                        return false;
                    }
                    window.location="/user/newsletter?search="+search.value;
                }
            })
        }
    </script>
</head>
<body>
@if(strpos($_SERVER['HTTP_USER_AGENT'],"MicroMessenger")===false)
<div class="container-fluid" style="font-size:20px;line-height:40px;height:40px;background: #0b2e13;text-align: center;color:#fff;font-weight:600;">
    <span>通迅录</span>
    <span onclick="search()" style="float:right;"><i class="fa fa-plus"></i></span>
    <span onclick="search()" style="float:right;margin-right:15px;"><i class="fa fa-search"></i></span>
</div>
    @else
    <div class="container-fluid" style="font-size:20px;line-height:40px;height:40px;text-align: center;color:#000000;font-weight:600;">
        <span onclick="search()" style="float:right;"><i class="fa fa-plus"></i></span>
        <span onclick="search()" style="float:right;margin-right:15px;"><i class="fa fa-search"></i></span>
    </div>
@endif
<div class="container" id="vueContainer" style="overflow:scroll;height:calc(100vh - 95px);">
    <ul class="col-12" style="margin: 60px 0 60px 0;padding:0 0 20px 0">
        @if(count($search)==0 && isset($_GET["search"]))
            <li style="color: grey">搜索结果</li>
            <li style="width:100%;text-align:center;color: grey">没有找到匹配用户</li>
            <li style="color: grey">我的好友</li>
            @elseif(count($search)>0 && isset($_GET["search"]))
            <li style="color: grey">搜索结果</li>
            @for($i=0;$i<count($search);$i++)
                <a style="text-decoration: none;" href="/user/user_info?id={{$search[$i]->id}}">
                    <li>
                        <img src="{{$search[$i]->head_img}}">&nbsp;
                        <span>{{$search[$i]->nickname}}</span>
                    </li></a>
            @endfor
            <li style="color: grey">我的好友</li>
        @endif
        @for($i=0;$i<count($data);$i++)
        <a style="text-decoration: none;" href="/user/user_info?id={{$data[$i]->id}}">
            <li>
                <img src="{{$data[$i]->head_img}}">&nbsp;
                <span>{{$data[$i]->nickname}}</span>
            </li></a>
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
            <div class="col-3" style="text-align:center;color:green">
                <a href="/user/newsletter" class="text-default" style="color:green;width:100%;padding:15px 0;">
                    <span class="fa fa-drivers-license-o" style="width:100%;"></span> <br>
                    通迅录
                </a>
            </div>
            <div class="col-3" style="text-align:center">
                <a href="/user/my" class="text-default" style="width:100%;color:grey;padding:15px 0">
                    <span class="fa fa-user-o" style="width:100%;color:grey"></span> <br>
                    我的
                </a>
            </div>
        </div>
</footer>
</body>
</html>


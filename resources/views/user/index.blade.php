<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>群聊</title>
    <link rel="stylesheet" href="/common/bootstrap-4.6.1/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/common/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/layuimini-2/lib/layui-v2.5.5/css/layui.css" media="all"/>
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
</head>
<body>
@if(strpos($_SERVER['HTTP_USER_AGENT'],"MicroMessenger")==false)
<div class="container-fluid" style="font-size:20px;line-height:40px;height:40px;background: #0b2e13;text-align: center;color:#fff;font-weight:600;">
   群聊
</div>
@endif
<div class="container" id="vueContainer" style="overflow:scroll;height:calc(100vh - 95px);">
    <ul class="col-12" style="margin:20px 0 60px 0;padding:0 0 20px 0">
        <a v-for="item in items" :href="'/user/group_chat_layout?id='+item.id+'&name='+item.name"><li><img src="/static/user/images/chat_room_icon.png">&nbsp;
                <span>@{{item.name}}</span>
            </li></a>
    </ul>
</div>
<footer class="container-fluid fixed-bottom" style="background:#fff;padding-bottom:10px;">
      <div class="row">
            <div class="col-3" style="text-align:center">
                <a href="javascript:void(0);" style="color:green;width:100%;padding:15px 0">
                    <span class="fa fa-group" style="color:green;width:100%;"></span>
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
                <a href="/user/my" class="text-default" style="width:100%;color:grey;padding:15px 0">
                    <span class="fa fa-user-o" style="width:100%;color:grey"></span> <br>
                    我的
                </a>
            </div>
        </div>
</footer>
<script src="/common/js/jquery-3.2.1.js"></script>
<script src="/layuimini-2/lib/layui-v2.5.5/layui.js"></script>
<script src="/common/js/vue.js"></script>
<script src="/common/js/list.js"></script>
</body>
</html>

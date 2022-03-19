<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>用户信息</title>
    <link rel="stylesheet" href="/common/bootstrap-4.6.1/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/common/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="/common/js/jquery-3.2.1.js"></script>
    <script src="/common/js/ajax.js"></script>
    <script src="/common/js/layer3.1.1/layer.js"></script>
    <script>
        function report(uid){
            layer.open({
                "title":"举报",
                "type":1,
                "content":"<textarea placeholder='请输入举报原因' style='margin:0 10px;width:100%;height:100%;' id='content'></textarea>",
                btn:["提交","取消"],
                "area":["90%","300px"],
                yes:function (){
                 var content=$("#content").val();
                    if (content === "" || /^\s+$/.test(content)){
                      layer.msg("内容不能为空");
                        return false;
                    }
                    ajax("/user/report","_token={{csrf_token()}}&report_id={{$_GET["id"]}}&content="+content,function (str){
                        if (str==="ok"){
                            layer.closeAll();
                            layer.msg("提交成功");
                        }else{
                            layer.msg(str);
                        }
                    })
                }
            })
        }
    </script>
    <script>
        function black(){
            var black=document.getElementById("black");
            var text=black.innerText;
            var val=1;
            if (text==="移出黑名单"){
                val=0;
            }
            ajax("/user/black","_token={{csrf_token()}}&friend_id={{$_GET["id"]}}&black="+val,function (str){
                if (str==="ok"){
                  if (text==="加入黑名单"){
                      layer.msg("加入黑名单成功");
                      black.innerText="移出黑名单";
                  }else{
                      layer.msg("移出黑名单成功");
                      black.innerText="加入黑名单";
                  }
                }else{
                    layer.msg(str);
                }
            })
        }
    </script>
    <script>
        function addFriend(){
            ajax("/user/add_friend","_token={{csrf_token()}}&friend_id={{$_GET["id"]}}",function (str){
                if (str==="ok"){
                    window.location="/user/user_chat_layout?id={{$_GET["id"]}}&nickname={{$user["nickname"]}}";
                }else{
                    layer.msg(str);
                }
            })
        }
    </script>
    <script>
        function sendInfo(){
            window.location="/user/user_chat_layout?id={{$_GET["id"]}}&headImg={{$user["head_img"]}}&nickname={{$user["nickname"]}}";
        }
    </script>
    <script>
        function delFriend(){
            layer.open({
                "title":"提示",
                "content":"确定删除好友吗?",
                "btn":["确定","取消"],
                yes:function (){
                  ajax("/user/del_friend","_token={{csrf_token()}}&friend_id={{$_GET["id"]}}",function (str){
                       if (str==="ok"){
                           window.history.back(-1);
                           layer.closeAll();
                           layer.msg("删除成功");
                       }else{
                           layer.msg(str);
                       }
                  })
                }
            })
        }
    </script>
</head>
<body>
@if(strpos($_SERVER['HTTP_USER_AGENT'],"MicroMessenger")===false)
<div class="container-fluid" style="font-size: 20px;line-height:40px;height:40px;background: #0b2e13;text-align: center;color:#fff;font-weight:600">
    <span onclick="window.history.back(-1);" style="float:left;">
        <i class="fa fa-angle-left"></i></span>用户信息
</div>
@endif
<div class="container" style="text-align: left;">
    <div style="width:100%;margin-top: 20px;">
        <div style="float:left;"><img style="width:50px;height:50px;" src="{{$user["head_img"]}}?t={{time()}}"></div>
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
        <div style="margin-top:20px;clear:both;color: grey">年龄&nbsp;:&nbsp;&nbsp;{{$user["age"]}}</div>
        <div style="margin-top:20px;clear:both;color: grey">性别&nbsp;:&nbsp;&nbsp;{{$user["sex"]}}</div>
        <div style="margin-top:20px;clear:both;color: grey">地区&nbsp;:&nbsp;&nbsp;{{$user["city"]}}</div>
        @if($_GET["id"]!=session("user")->id)
            @if($user["friend"]=="no")
        <div onclick="addFriend()" style="width:100%;margin-top:20px;clear:both;text-align:center">加为好友</div>
            @else
        <div onclick="sendInfo()" style="width:100%;margin-top:20px;clear:both;text-align:center">发信息</div>
            @endif
        <div style="width:100%;margin-top:20px;clear:both;text-align:center">
            <span onclick="report('{{$user["id"]}}')" style="padding:0 10px;">举报</span>
        </div>
                    <div onclick="black()" style="width:100%;margin-top:20px;clear:both;text-align:center">
                        <span  id="black">
                            @if(count($black)==0)
                            加入黑名单
                        @else
                            移出黑名单
                        @endif
                        </span>
                    </div>
                @if($user["friend"]=="yes")
                <div onclick="delFriend()" style="color:crimson;width:100%;margin-top:20px;clear:both;text-align:center">删除</div>
        @endif
        @else
           <!-- <div onclick="sendInfo()" style="width:100%;margin-top:20px;clear:both;text-align:center">发信息</div>-->
        @endif
    </div>
</div>
</body>
</html>

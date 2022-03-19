<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
    <meta http-equiv="Pragma" content="no-cache"/>
    <meta http-equiv="Expires" content="0"/>
    <title>{{$_GET["name"]}}</title>
    <link rel="stylesheet" href="/common/bootstrap-4.6.1/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/common/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/layuimini-2/lib/layui-v2.5.5/css/layui.css" media="all"/>
    <script>
        var domain = window.location.protocol + "//" + window.location.host;
    </script>

    <script src="/common/js/jquery-3.2.1.js"></script>
    <script src="/common/js/ajax.js"></script>
    <script src="/common/js/layer3.1.1/layer.js"></script>
    <script src="/common/js/jquery.form.min.js"></script>
    <script src="/static/js/recorder.js"></script>
    <script>
        var currentObj = null;
        var previous = null;

        function playAudio(obj, src) {
            var name = $(obj).prop("className");
            if (previous == null) {
                currentObj = obj;
                previous = obj;
                $(obj).attr("class", "fa fa-pause-circle-o");
            } else {
                previous = currentObj;
                currentObj = obj;
                $(previous).attr("class", "fa fa-play-circle-o");
                $(currentObj).attr("class", "fa fa-play-circle-o");
                $(obj).attr("class", "fa fa-pause-circle-o");
            }
            if (name === "fa fa-play-circle-o") {
                $("#audio").attr("src", src);
                $("#audio")[0].play();
                $(obj).attr("class", "fa fa-pause-circle-o");
            } else {
                $("#audio")[0].pause();
                $(obj).attr("class", "fa fa-play-circle-o");
            }
        }
    </script>
    <script>
        $(function () {
            var audio = document.getElementById("audio");
            audio.addEventListener('ended', function () {
                $(previous).attr("class", "fa fa-play-circle-o");
                $(currentObj).attr("class", "fa fa-play-circle-o");
            }, false);
        })
    </script>
    <script>
        $(function () {
            $('#emoji').click(function (e) {
                var face = document.getElementById("face");
                $("#moreDiv").css("display", "none");
                $("#touchBtn").css("display", "none");
                $("#msg-text").css("display", "block");
                $("#edit").css("display", "none");
                $("#mic").css("display", "block");
                if (face.style.display == "block") {
                    face.style.display = "none";
                } else {
                    face.style.display = "block";
                    face.scrollTop = 0;
                }
            })
        })
    </script>
    <script>
        $(function () {
            $("#more").click(function () {
                var moreDiv = document.getElementById("moreDiv");
                $("#face").css("display", "none");
                if (moreDiv.style.display === "block") {
                    moreDiv.style.display = "none";
                } else {
                    moreDiv.style.display = "block";
                }
            })
        })
    </script>
    <style>
        #content table {
            width: 90%;
            margin-top: 20px;
        }

        #content {
            background: #FFFFFF;
        }

        .msg-box-left {
            float: left;
            min-width: 80px;
            max-width: 100%;
            border-radius: 4px;
            background: #f6f6f6;
            padding: 5px;
            min-height: 40px;
            word-break: break-all;
        }

        .msg-box-right {
            float: right;
            min-width: 80px;
            max-width: 100%;
            border-radius: 4px;
            background: #3ab576;
            padding: 5px;
            min-height: 40px;
            word-break: break-all;
        }

        .td-left i {
            position: relative;
            left: 3px;
            top: 21px;
            color: #f6f6f6;
            font-size: 20px
        }

        .td-right i {
            position: relative;
            right: 4px;
            top: 21px;
            color: #3ab576;
            font-size: 20px
        }
    </style>
    <style>
        /* 录制提示信息 */
        .hide-tips-wrap {
           position: fixed;
            top: 50%;
            left: 50%;
            width: 150px;
            height: 150px;
            margin: -75px 0 0 -75px;
            border-radius: 10px;
            background: rgba(189, 189, 189, 0.56);
            text-align: center;
        }

        .hide-tips-wrap .tips-txt {
            display: block;
            color: #ffffff;
            margin-top: -35px;
            font-weight: 600;
        }
    </style>
    <script>
        $(function () {
            var msgText = document.getElementById("msg-text");
            msgText.oninput = function () {
                var send = document.getElementById("send");
                if (msgText.innerText === "" || /^\s+$/.test(msgText.innerText)) {
                    $("#send").css('display', "none");
                    $("#more").css("display", "block");
                } else {
                    $("#send").css('display', "block");
                    $("#more").css("display", "none");
                }
            }
        })
    </script>
    <script>
        $(function () {
            var text = document.getElementById("msg-text");
            var btn = document.getElementById("send");
            var content = document.getElementById("content");
            //创建websocket;
            var socket = null;
            if (window.location.protocol === "https:") {
                socket = new WebSocket("wss://" + document.domain + ":8282");
            } else {
                socket = new WebSocket("ws://" + document.domain + ":8282");
            }
            //当socket建立连接时触发的事件
            socket.addEventListener("open", function () {
                /*content.innerHTML="连接成功了";
                alert("连接成功");*/
            })
            //发送消息
            btn.onclick = function () {
                //socket.send(text.value);
                var str = "_token={{csrf_token()}}&msg=" + encodeURIComponent(text.innerHTML) + "&gid={{$_GET["id"]}}";
                ajax("/user/send_group", str, function (res) {
                    var json = eval("(" + res + ")");
                    if (json.type === "error") {
                        layer.msg(json.msg);
                    } else {
                        var msgText = document.getElementById("msg-text");
                        msgText.innerHTML = '';
                        $("#send").css("display", "none");
                        $("#more").css("display", "block");
                    }
                })
            }
            socket.addEventListener("message", function (e) {
                var obj = eval("(" + e.data + ")");
                switch (obj.type) {
                    case "init":
                        ajax("/user/bindGroup", "_token={{csrf_token()}}&client_id=" + obj.client_id, function (res) {
                        })
                        break;
                    case "ping":
                        break;
                    case "error":
                        break;
                    default:
                        var str = "";
                        /////自己///////
                        if (obj.uid ==={{session("user")->id}}) {
                            str += '<table style="text-align:right;float:right;" cellspacing="0" cellpadding="0">';
                            str += '<tr>';
                            str += '<td style="text-align: right;" valign="top">';
                            str += '<span style="color:grey;line-height:0;">';
                            str += obj.nickname;
                            str += '</span><br>';
                            if (obj.type === "text") {
                                str += '<p class="msg-box-right" style="text-align:right;">';
                                str += '<span style="text-align:left;display: inline-block;">';
                                str += obj.msg;
                                str += '</span>';
                                str += '</p>';
                            } else if (obj.type === "audio") {
                                var duration = obj.msg.slice(obj.msg.indexOf("=") + 1);
                                if (duration >= 10) {
                                    w = "100%";
                                } else if (duration > 3) {
                                    w = duration * 10 + "%";
                                } else {
                                    w = "80px";
                                }
                                str += '<p style="float:right;min-width:' + w + ';max-width:' + w + ';background:#3ab576;height:50px;">';
                                str += '<span style="line-height:0;color:purple;">' + duration + 's&nbsp;</span>';
                                str += '<i onclick="playAudio(' + "this,'" + obj.msg + "'" + ')" style="font-size:40px;color:purple;margin-top:5px" class="fa fa-play-circle-o"></i>';
                                str += '&nbsp;&nbsp;';
                                str += '</p>';
                            } else {
                                str += '<img class="msg_img" style="max-width: 60%;max-height:300px" src="' + obj.msg + '"/>';
                            }
                            str += '</td>';
                            str += '<td style="width:10px;" class="td-right" valign="top">';
                            if (obj.type === "text" || obj.type === "audio") {
                                str += '<p><i class="fa fa-caret-right"></i></p>';
                            }
                            str += '</td>';
                            str += '<td valign="top" style="padding:0 10px 0 0;width:50px">';
                            str += '<a href="/user/user_info?id=' + obj.uid + '">';
                            str += '<img style="width: 50px;height: 50px;" src="' + obj.headImg + '?t={{time()}}">';
                            str += '</a>';
                            str += '</td>';
                            str += '</tr>';
                            str += '</table>';
                            str += '<div style="width:100%;clear: both"></div>';
                            $("#content").append(str);
                        } else {
                            str = '<table border="0" cellspacing="0" cellpadding="0">';
                            str += '<tr>';
                            str += '<td valign="top" style="width:50px;padding:0 0 0 10px">';
                            str += '<a href="/user/user_info?id=' + obj.uid + '">';
                            str += '<img style="width: 50px;height: 50px;" src="' + obj.headImg + '?t={{time()}}">';
                            str += '</a></td>';
                            str += '<td style="width:10px;" class="td-left" valign="top">';
                            if (obj.type === "text" || obj.type === "audio") {
                                str += '<p><i class="fa fa-caret-left"></i></p>';
                            }
                            str += '</td>';
                            str += '<td valign="top">';
                            str += '<span style="color:grey;line-height:0;">' + obj.nickname + '</span><br>';
                            if (obj.type === "text") {
                                str += '<p class="msg-box-left">';
                                str += obj.msg;
                                str += '</p>';
                            } else if (obj.type === "audio") {
                                var duration = obj.msg.slice(obj.msg.indexOf("=") + 1);
                                if (duration >= 10) {
                                    w = "100%";
                                } else if (duration > 3) {
                                    w = duration * 10 + "%";
                                } else {
                                    w = "80px";
                                }
                                str += '<p class="msg-box-left">';
                                str += '<i onclick="playAudio(' + "this,'" + obj.msg + "'" + ')" style="font-size:40px;color:purple" class="fa fa-play-circle-o"></i>';
                                str += '<span style="line-height:0;color:purple;">' + duration + 's&nbsp;</span>';
                                str += "</p>";
                            } else {
                                str += '<img class="msg_img" style="max-width: 60%;max-height:300px" src="' + obj.msg + '"/>';
                            }
                            str += '</td></tr></table>';
                            $("#content").append(str);
                        }
                        var h = document.documentElement.scrollHeight || document.body.scrollHeight;
                        window.scrollTo(h, h);
                        var content = document.getElementById("content");
                        content.scrollTop = content.scrollHeight;
                        break;
                }

            })
            socket.addEventListener("close", function () {
                layer.open({
                    "title": "提示",
                    'content': "与服务器断开连接",
                    "btn": ["确定", "取消"],
                    yes: function () {
                        window.location = "/user/index";
                    }
                })
            })
        })
    </script>
    <script>
        $(function () {
            var topDiv = document.getElementById("topDiv");
            var content = document.getElementById("content");
            var msgText = document.getElementById("msg-text");
            topDiv.onclick = content.onclick = msgText.onclick = function () {
                $("#face").css("display", "none");
                $("#moreDiv").css("display", "none");
            }
        })
    </script>
    <script>
        function inputEmoji(numImg) {
            $("#face").css("display", "none");
            var html = $("#msg-text").html() + "<img style='width:20px;height:20px;' src='/common/emoji/" + numImg + ".png'>";
            $("#msg-text").html(html);
            $("#more").css("display", "none");
            $("#send").css("display", "block");
        }
    </script>
</head>
<body>
<!----//上传图片隐藏表单///---->
<form method="post" style="display: none;" id="uploadForm" enctype="multipart/form-data">
    <input accept="image/gif,image/jpeg,image/png,image/bmp" type="file" id="uploadImg" name="upload" style="display:none">
</form>
<form method="post" style="display: none;" id="cameraForm" enctype="multipart/form-data">
    <input type="file" capture="camera" accept="image/*" id="uploadCamera" name="upload" style="display:none">
</form>
@if(strpos($_SERVER['HTTP_USER_AGENT'],"MicroMessenger")===false)
    <div id="topDiv" class="container-fluid"
         style="font-weight:600;font-size:20px;line-height:40px;height:40px;background: #0b2e13;text-align: center;color:#fff;">
    <span onclick="window.history.back(-1);" style="float:left;">
        <i class="fa fa-angle-left"></i></span>{{$_GET["name"]}}
    </div>
@endif
<div id="content" style="width:100%;overflow:scroll;height: calc(100vh - 95px)">
    @if(count($data)>0)
        @for($i=0;$i<count($data);$i++)
            @php
                $nickname=$data[$i]->nickname;
  if (empty($nickname)){
      $nickname=$data[$i]->username;
  }
            @endphp
            @if($data[$i]->uid!=session("user")->id)
                <table style="text-align:left" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="width:50px;padding: 0 0 0 10px;" valign="top">
                            <a href="/user/user_info?id={{$data[$i]->uid}}">
                                <img style="width: 50px;height: 50px;" src="{{$data[$i]->head_img}}?t={{time()}}">
                            </a>
                        </td>
                        <td style="width:10px;" class="td-left" valign="top">
                            @if($data[$i]->type=="text" || $data[$i]->type=="audio")
                                <p><i class="fa fa-caret-left"></i></p>
                            @endif
                        </td>
                        <td valign="top">
                            <span style="color:grey;line-height:0;">{{$nickname}}</span><br>
                            @if($data[$i]->type=="text")
                                <p class="msg-box-left">
                                    {!! $data[$i]->content !!}
                                </p>
                            @elseif($data[$i]->type=="audio")
                                <?php
                                $duration = substr($data[$i]->content,strrpos($data[$i]->content,"=") + 1);
                                if ($duration >= 10) {
                                    $w = "100%";
                                } else if ($duration > 3) {
                                    $w = ($duration * 10) . "%";
                                } else {
                                    $w = "80px";
                                }
                                ?>
                                <p class="msg-box-left" style="min-width:{{$w}};">
                                    <i onclick='playAudio(this,"{{$data[$i]->content}}")'
                                       style='font-size:40px;color:purple' class='fa fa-play-circle-o'></i>
                                    <span style="line-height:0;color:purple;">{{$duration}}s&nbsp;</span>
                                </p>
                            @else
                                <img class='msg_img' style='max-width: 60%;max-height:300px'
                                     src="{!! $data[$i]->content !!}"/>
                            @endif
                        </td>
                    </tr>
                </table>
            @else
            <!----自己---->
                <table style="text-align:right;float:right;" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="text-align: right;" valign="top">
                            <span style="color:grey;line-height:0;">{{$nickname}}</span><br>
                            @if($data[$i]->type=="text")
                                <p class="msg-box-right" style="text-align:right;">
                       <span style="text-align:left;display: inline-block;">
                            {!! $data[$i]->content !!}
                       </span>
                                </p>
                            @elseif($data[$i]->type=="audio")
                                <?php
                                $duration = substr($data[$i]->content, strrpos($data[$i]->content, "=") + 1);
                                if ($duration >= 10) {
                                    $w = "100%";
                                } else if ($duration > 3) {
                                    $w = ($duration * 10) . "%";
                                } else {
                                    $w = "80px";
                                }
                                ?>
                                <p class="msg-box-right" style="min-width:{{$w}};text-align:right;">
                       <span style="text-align:left;display: inline-block;">
                                    <span style="line-height:0;color:purple;">{{$duration}}s&nbsp;</span>
                                <i onclick="playAudio(this,'{{$data[$i]->content}}')" style='font-size:40px;color:purple'
                                   class='fa fa-play-circle-o'></i>
                       </span>
                                </p>
                            @else
                                <img class='msg_img' style='max-width: 60%;max-height:300px'
                                     src="{!! $data[$i]->content !!}"/>
                            @endif
                        </td>
                        <td style="width:10px;" class="td-right" valign="top">
                            @if($data[$i]->type=="text" || $data[$i]->type=="audio")
                                <p><i class="fa fa-caret-right"></i></p>
                            @endif
                        </td>
                        <td valign="top" style="padding:0 10px 0 0;width:50px">
                            <a href="/user/user_info?id={{$data[$i]->uid}}">
                                <img style="width: 50px;height: 50px;" src="{{$data[$i]->head_img}}?t={{time()}}">
                            </a>
                        </td>
                    </tr>
                </table>
                <div style="width:100%;clear: both"></div>
            @endif
        @endfor
    @endif
</div>
<audio style="width:0;height:0" id="audio" src=""></audio>
<!-- 录制语音弹出层 -->
<div id="tips" class="hide-tips-wrap">
    <!-- <i class="tips-icon"></i>-->
    <img id="tips-icon" class="tips-icon" style="width:150px;height:150px" src="/static/images/load.gif"/>
    <span id="tips-txt" class="tips-txt">加载中...</span>
</div>
<!----底部----->
<footer class="fixed-bottom" style="width: 100%;background:#fff" id="footer">
    <!--表情---->
    <div id="face"
         style="text-align:center;width:100%;height:300px;overflow:scroll;padding:5px 0;display: none;border-width: 2px 2px 0 2px;border-color:grey;border-style: solid;">
        @for($i=0;$i<24;$i++)
            @for($j=1;$j<7;$j++)
                <div onclick="inputEmoji('{{$i*6+$j}}')" class="col-2" style="float:left;margin-top:10px;">
                    <img style="max-width:100%;max-height:100px;" src="/common/emoji/{{$i*6+$j}}.png">
                </div>
                @if($i*6+$j==143)
                    @break
                @endif
            @endfor
        @endfor
    </div>
    <!----更多功能--->
    <div class="container" style="padding:5px 0;border-width: 2px 2px 0 2px;border-color:grey;border-style: solid;display:none;text-align: center"
        id="moreDiv">
        <div class="row">
        <div onclick="$('#uploadImg').click();$('#moreDiv').css('display','none');" class="col-3"
             style="color: grey;line-height:25px;">
            <i class="fa fa-image"></i><br>图片
        </div>
        <div onclick="$('#uploadCamera').click();$('#moreDiv').css('display','none');" class="col-3" style="color: grey">
            <i class="fa fa-camera"></i><br>拍照
        </div>
            <div class="col-3" style="color: grey"></div>
        <div class="col-3" style="color: grey"></div>
    </div>
    </div>
    <!-----///////---->
    <table cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:50px">
                <span id="mic" style="margin:0 0 0 5px;font-size:50px;color:#9F9F9F"><i class="fa fa-feed"></i></span>
                <span id="edit" style="margin:0 0 0 5px;display:none;font-size:50px;color:#9F9F9F"><i
                        class="fa fa-edit"></i></span>
            </td>
            <td>
                <input type="button" id="touchBtn"
                       style="margin-left:5px;background:#cccccc;display:none;width:calc(100vw - 162px);height:50px;border:1px solid grey"
                       value="按住说话"/>
                <div contenteditable="true" id="msg-text"
                     style="margin-left:5px;width:calc(100vw - 152px);height:50px;border:1px solid grey"></div>
            </td>
            <td style="width:50px">
                <span id="emoji" style="margin-left:5px;font-size:50px;color:#9F9F9F"><i class="fa fa-frown-o"></i></span>
            </td>
            <td style="width:50px;">
                <button id="send"
                        style="margin:0 5px;display:none;width:40px;height:40px;color:#fff;background: #0E9A00;border-color:#0E9A00">
                    发送
                </button>
                <span id="more" style="margin:0 5px;font-size:50px;color:#9F9F9F;"><i class="fa fa-plus-square-o"></i></span>
            </td>
        </tr>
    </table>
</footer>
<script>
    //$("#content").height(document.documentElement.clientHeight-105+"px");
    var h = document.documentElement.scrollHeight || document.body.scrollHeight;
    window.scrollTo(h, h);
    var content = document.getElementById("content");
    content.scrollTop = content.scrollHeight;
</script>
<script>
    $("#mic").on("click", function () {
        $("#edit").css("display", "block");
        $("#mic").css("display", "none");
        $("#msg-text").css("display", "none");
        $("#face").css("display", "none");
        $("#touchBtn").css("display", "block");
    })
    $("#edit").on("click", function () {
        $("#edit").css("display", "none");
        $("#mic").css("display", "block");
        $("#msg-text").css("display", "block");
        $("#face").css("display", "none");
        $("#touchBtn").css("display", "none");
    })
</script>
<script>
    // 禁止浏览器鼠标右键功能
    document.oncontextmenu = function () {
        return false;
    }
    var audio_context;
    var recorder;

    function startUserMedia(stream) {
        var input = audio_context.createMediaStreamSource(stream);
        recorder = new Recorder(input);
    }

    function startRecording() {
        recorder && recorder.record();
    }

    function stopRecording() {
        recorder && recorder.stop();
        recorder.clear();
    }
</script>
<script>
    oBtn = document.getElementById("touchBtn");
    oHideTips = document.getElementById('tips');
    var duration = 0;//录音时长
    var interval;//定时器
    var timeOut;
    var overtime=false;//是否超时
    oBtn.addEventListener('touchstart', function () {
        duration = 0;
        startRecording();
        $("#tips-icon").attr("src", "/static/images/voice.gif");
        $("#tips-txt").text("松开发送消息");
        $("#audio")[0].pause();
        interval = setInterval(function () {
            if (duration>=120){
                overtime=true;
                recorder.exportWAV(function (blob) {
                    oHideTips.style.display="block";
                    $("#tips-icon").attr("src","/static/images/load.gif");
                    $("#tips-txt").text("发送中...");
                    var fd = new FormData();
                    fd.append("blob", blob);
                    fd.append("duration", duration);
                    fd.append("gid",{{$_GET["id"]}});
                    $.ajax({
                        type: "POST",
                        data: fd,
                        url: "/user/upload_group_audio?_token={{csrf_token()}}",
                        processData: false,
                        contentType: false,
                        success: function (res) {
                            oHideTips.style.display="none";
                            var json = eval("(" + res + ")");
                            if (json.type === "error") {
                                layer.msg(json.msg);
                            }
                        },
                        error:function (res){
                            oHideTips.style.display="none";
                        }
                    })
                });
                stopRecording();
                oHideTips.style.display="none";
                clearInterval(interval);
            }else{
                duration++;
            }
        }, 1000);
        oHideTips.style.display = 'block';
    });
    oBtn.addEventListener('touchmove', function (e) {
        e.preventDefault();
    });

    oBtn.addEventListener("touchend", function () {
        if (overtime){
            overtime=false;
            duration = 0;
            stopRecording();
            return false;
        }
        if (duration < 1) {
            duration = 0;
            stopRecording();
            $("#tips-icon").attr("src", "/static/images/mark.jpg");
            $("#tips-txt").text("录音时长过短");
            timeOut = setTimeout(function () {
                clearInterval(interval);
                clearTimeout(timeOut);
                oHideTips.style.display = "none";
            }, 1000);
            return false;
        }
        recorder.exportWAV(function (blob) {
            oHideTips.style.display="block";
            $("#tips-icon").attr("src","/static/images/load.gif");
            $("#tips-txt").text("发送中...");
            clearInterval(interval);
            var fd = new FormData();
            fd.append("blob", blob);
            fd.append("duration", duration);
            fd.append("gid",{{$_GET["id"]}});
            $.ajax({
                type: "POST",
                data: fd,
                url: "/user/upload_group_audio?_token={{csrf_token()}}",
                processData: false,
                contentType: false,
                success: function (res) {
                    oHideTips.style.display="none";
                    var json = eval("(" + res + ")");
                    if (json.type === "error") {
                        layer.msg(json.msg);
                    }
                },
                error:function (res){
                    oHideTips.style.display="none";
                }
            })
        });
        oHideTips.style.display = "none";
        stopRecording();
    })
    try {
        // webkit shim
        window.AudioContext = window.AudioContext || window.webkitAudioContext;
        navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia;
        window.URL = window.URL || window.webkitURL;
        audio_context = new AudioContext;
    } catch (e) {
        //alert('No web audio support in this browser!');
    }

    navigator.getUserMedia({audio: true}, startUserMedia, function (e) {
    });
</script>
<script>
    $(function () {
        $("#uploadImg").on("change", function () {
            var file = $("#uploadImg").get(0).files[0];
            var formData = new FormData();//*
            formData.append("upload",file);
            formData.append("gid",{{$_GET["id"]}});
            $.ajax({
                type:'post',
                url:'/user/upload?_token={{csrf_token()}}',
                data:formData,
                processData:false,//*
                contentType:false,//*
                success:function(data){
                    var obj = eval("(" + data + ")");
                    if (obj.type === "error") {
                        layer.msg(obj.msg);
                    }
                    $("#uploadImg").val("");
                }
            })
           /* $("#uploadForm").ajaxSubmit({
                dataType: "text",
                type:"POST",
                url:"/user/upload?_token={{csrf_token()}}",
                data: {"gid":{{$_GET["id"]}}, "_token": '{{csrf_token()}}'},
                success: function (str) {
                    var obj = eval("(" + str + ")");
                    if (obj.type === "error") {
                        layer.msg(obj.msg);
                    }
                    $("#uploadImg").val("");
                },
                error: function (e) {
                    $("#uploadImg").val("");
                }
            })*/
        })
    })
    //相机上传
    $("#uploadCamera").on("change", function () {
        var file = $("#uploadCamera").get(0).files[0];
        var formData = new FormData();//*
        formData.append("upload",file);
        formData.append("gid",{{$_GET["id"]}});
        $.ajax({
            type:'post',
            url:'/user/upload?_token={{csrf_token()}}',
            data:formData,
            processData:false,//*
            contentType:false,//*
            success:function(data){
                var obj = eval("(" + data + ")");
                if (obj.type === "error") {
                    layer.msg(obj.msg);
                }
                $("#uploadImg").val("");
            }
        })
    })
</script>
<script>
    $("#tips").css("display","none");
</script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="description" content="php web-rtc例子,一对一聊天-基于workerman">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
    <meta itemprop="description" content="Video chat using the reference WebRTC application">
    <meta itemprop="name" content="AppRTC">
    <meta name="mobile-web-app-capable" content="yes">
    <meta id="theme-color" name="theme-color" content="#1e1e1e">
    <title>视频通话</title>
    <link rel="stylesheet" href="/common/bootstrap-4.6.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/common/font-awesome-4.7.0/css/font-awesome.min.css">
    <style>
        body {
            background-color: #333;
            color: #fff;
            font-family: 'Roboto', 'Open Sans', 'Lucida Grande', sans-serif;
            height: 100%;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .videos {
            font-size: 0;
            height: 100%;
            pointer-events: none;
            position: absolute;
            transition: all 1s;
            width: 100%;
        }

        #localVideo {
            height: 100%;
            max-height: 100%;
            max-width: 100%;
            object-fit: cover;
            -moz-transform: scale(-1, 1);
            -ms-transform: scale(-1, 1);
            -o-transform: scale(-1, 1);
            -webkit-transform: scale(-1, 1);
            transform: scale(-1, 1);
            transition: opacity 1s;
            width: 100%;
        }

        #remoteVideo {
            display: block;
            height: 100%;
            max-height: 100%;
            max-width: 100%;
            object-fit: cover;
            position: absolute;
            -moz-transform: rotateY(180deg);
            -ms-transform: rotateY(180deg);
            -o-transform: rotateY(180deg);
            -webkit-transform: rotateY(180deg);
            transform: rotateY(180deg);
            transition: opacity 1s;
            width: 100%;
        }
        #headImgBox{
            position:absolute;
            color: #FFFFFF;
           left:50%;
            top:100px;
            transform:translate(-50%,-50%);
        }
        #headImgBox img{
            width:50px;
            height:50px;
        }
        #btnBox{
            position: absolute;
            left:20%;
            bottom:50px;
            text-align: center;
        }
        #btnBox1{
            position: absolute;
            right:20%;
            bottom:50px;
            text-align: center;
        }
        #btnBox div{
            color:#FFFFFF;
            font-size:20px;
            line-height:50px;
            text-align: center;
            width:50px;
            height:50px;
            background:red;
            border-radius: 50%;
         }
        #btnBox1 div{
            color:#FFFFFF;
            font-size:20px;
            line-height:50px;
            text-align: center;
            width:50px;
            height:50px;
            background:mediumspringgreen;
            border-radius: 50%;
        }
    </style>
</head>

<body>

<div class="videos">
    <video id="localVideo" autoplay></video>
    <video id="remoteVideo" autoplay class="hidden"></video>
</div>
<div id="headImgBox">
    <img src="{{$_GET["headImg"]}}"/><br>
    {{$_GET["nickname"]}}
</div>
<div id="btnBox">
    <div><i class="fa fa-power-off"></i></div>
     拒绝
</div>
<div id="btnBox1">
    <div><i class="fa fa-phone"></i></div>
    接听
</div>
<script src="/common/js/jquery-3.2.1.js"></script>
<script src="/common/bootstrap-4.6.1/dist/js/bootstrap.min.js"></script>
<script src="/static/js/adapter.js"></script>

<script type="text/javascript">
    var WS_ADDRESS = null;
    if (window.location.protocol === "https:") {
        WS_ADDRESS = new WebSocket("wss://" + document.domain + ":8282");
    } else {
        WS_ADDRESS = new WebSocket("ws://" + document.domain + ":8282");
    }
    // 房间id
    /*var cid = getUrlParam('cid');
    if (cid == '' || cid == null) {
        cid = Math.random().toString(36).substr(2);
        location.href = '?cid=' + cid;
    }*/
    var answer = 0;

    // 基于订阅，把房间id作为主题
    //var subject = 'private-video-room-'+cid;

    // 建立与workerman的连接
    var ws = new WebSocket(WS_ADDRESS);
    ws.onopen = function(){
        subscribe(subject);
        navigator.mediaDevices.getUserMedia({
            audio: true,
            video: false
        }).then(function (stream) {
            localVideo.srcObject = stream;
            localStream = stream;
            localVideo.addEventListener('loadedmetadata', function () {
                //publish('client-call', null)
            });
        }).catch(function (e) {
            alert(e);
        });
    };
    ws.onmessage = function(e){
        var package = JSON.parse(e.data);
        var data = package.data;
        switch (package.event) {
            case 'client-call':
                icecandidate(localStream);
                pc.createOffer({
                    offerToReceiveAudio: 1,
                    offerToReceiveVideo: 1
                }).then(function (desc) {
                    pc.setLocalDescription(desc).then(
                        function () {
                            publish('client-offer', pc.localDescription);
                        }
                    ).catch(function (e) {
                        alert(e);
                    });
                }).catch(function (e) {
                    alert(e);
                });
                break;
            case 'client-answer':
                pc.setRemoteDescription(new RTCSessionDescription(data),function(){}, function(e){
                    alert(e);
                });
                break;
            case 'client-offer':
                icecandidate(localStream);
                pc.setRemoteDescription(new RTCSessionDescription(data), function(){
                    if (!answer) {
                        pc.createAnswer(function (desc) {
                                pc.setLocalDescription(desc, function () {
                                    publish('client-answer', pc.localDescription);
                                }, function(e){
                                    alert(e);
                                });
                            }
                        ,function(e){
                            alert(e);
                        });
                        answer = 1;
                    }
                }, function(e){
                    alert(e);
                });
                break;
            case 'client-candidate':
                pc.addIceCandidate(new RTCIceCandidate(data), function(){}, function(e){alert(e);});
                break;
        }
    };

    var localVideo = document.getElementById('localVideo');
    var remoteVideo = document.getElementById('remoteVideo');

    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
    var configuration = {
        iceServers: [{
            urls: 'stun:stun.xten.com'
        }]
    };
    var pc, localStream;

    function icecandidate(localStream) {
        pc = new RTCPeerConnection(configuration);
        pc.onicecandidate = function (event) {
            if (event.candidate) {
                publish('client-candidate', event.candidate);
            }
        };
        try{
            pc.addStream(localStream);
        }catch(e){
            var tracks = localStream.getTracks();
            for(var i=0;i<tracks.length;i++){
                pc.addTrack(tracks[i], localStream);
            }
        }
        pc.onaddstream = function (e) {
            $('#remoteVideo').removeClass('hidden');
            $('#localVideo').remove();
            remoteVideo.srcObject = e.stream;
        };
    }

    function publish(event, data) {
        ws.send(JSON.stringify({
            cmd:'publish',
            subject: subject,
            event:event,
            data:data
        }));
    }

    function subscribe(subject) {
        ws.send(JSON.stringify({
            cmd:'subscribe',
            subject:subject
        }));
    }

    function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }

</script>
</body>
</html>

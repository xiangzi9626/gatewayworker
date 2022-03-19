
<!doctype html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=750,user-scalable=no">
    <title>修改头像</title>
    <link href="/common/cropper/css/bootstrap.min.css" rel="stylesheet">
    <link href="/common/cropper/css/cropper.css" rel="stylesheet">
    <link href="/common/cropper/css/main.css" rel="stylesheet">
    <link rel="stylesheet" href="/common/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="/common/cropper/js/jquery.min.js"></script>
    <script src="/common/js/layer3.1.1/layer.js"></script>
    <style>
        .tooltip-inner{
            display: none;}
        .btn{
            font-size: 30px;
            width:180px;
            height:92px;
            line-height: 77px;
            border-radius: 10px;
        }
        .back{
            background:#fff;
            background-image:url("{{$_GET["head_img"]}}");
            background-repeat:no-repeat;
            background-size:400px 400px;
            background-position:125px 30px;
            width:650px;
            height:650px;
            margin:20px auto;
        }
    </style>
</head>
<body>
@if(strpos($_SERVER['HTTP_USER_AGENT'],"MicroMessenger")==false)
<div style="width:100%;font-size:20px;line-height:40px;height:40px;background: #0b2e13;text-align: center;color:#fff;font-weight:600;">
    <span onclick="window.history.back(-1);" style="float:left;">
        <i class="fa fa-angle-left"></i></span>头像
</div>
@endif
<div class="htmleaf-container">
    <!-- Content -->
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <!-- <h3 class="page-header">Demo:</h3> -->
                <div class="img-container back">
                    <img id="image" alt="">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9 docs-buttons" style="text-align: center;">
                <!-- <h3 class="page-header">Toolbar:</h3> -->
                <div class="btn-group">
                    <button id="btn1" style="display:none" class="btn btn-primary" data-method="rotate" data-option="90" type="button" title="Rotate Right">
                        旋转90º
                    </button>
                </div>

                <div class="btn-group">

                    <label class="btn btn-primary btn-upload" for="inputImage" title="Upload image file">
                        <input class="sr-only" id="inputImage" name="file" type="file" accept="image/*">
                        更换头像
                    </label>

                </div>

                <div class="btn-group btn-group-crop">
                    <button id="btn3" style="display:none" class="btn btn-primary" data-method="getCroppedCanvas" data-option="{ &quot;width&quot;: 180, &quot;height&quot;: 90 }" type="button">
                        上传头像
                    </button>
                </div>

                <!-- Show the cropped image in modal -->
                <div class="modal fade docs-cropped" id="getCroppedCanvasModal" aria-hidden="true" aria-labelledby="getCroppedCanvasTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button class="close" data-dismiss="modal" type="button" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="getCroppedCanvasTitle">Cropped</h4>
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" data-dismiss="modal" type="button">Close</button>
                                <a class="btn btn-primary" id="download" href="javascript:void(0);">Download</a>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal -->

            </div><!-- /.docs-buttons -->
        </div>
    </div>
</div>
    <!-- Alert -->
    <div class="tip"></div>
    <script src="/common/cropper/js/bootstrap.min.js"></script>
    <script src="/common/cropper/js/cropper.js"></script>
    <script src="/common/cropper/js/main330.js"></script>
<script>
    $("#inputImage").on('change', function () {
       $("#btn1").css("display","block");
        $("#btn3").css("display","block");
    })
</script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>修改密码</title>
    <link rel="stylesheet" href="/common/bootstrap-4.6.1/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/common/font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="/common/js/jquery-3.2.1.js"></script>
    <script src="/common/js/ajax.js"></script>
    <script src="/common/js/layer3.1.1/layer.js"></script>
    <script>
        function modifyPassword(){
            var password=document.getElementById("password");
            var password2=document.getElementById("password2");
            if (password.value === "" || /^\s+$/.test(password.value)) {
                layer.msg("密码不能为空");
                return false;
            }
            if (password.value!==password2.value){
                layer.msg("两次密码输入不一致");
                return false;
            }
            ajax("/user/modify_password","_token={{csrf_token()}}&password="+password.value+"&password2="+password2.value,function (str){
                if (str==="ok"){
                    layer.open({
                        "title":"提示",
                        "content":"密码修改成功",
                        yes:function (){
                            window.location="/user/my";
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
@if(strpos($_SERVER['HTTP_USER_AGENT'],"MicroMessenger")===false)
<div class="container-fluid" style="font-size: 20px;line-height:40px;height:40px;background: #0b2e13;text-align: center;color:#fff;font-weight:600">
    <span onclick="window.history.back(-1);" style="float:left;">
        <i class="fa fa-angle-left"></i></span>修改密码
</div>
@endif
<div class="container" style="margin-top:30px;">
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text">新 密 码 :</span>
        </div>
        <input id="password" type="password" class="form-control" placeholder="请输入" aria-label="Username" aria-describedby="basic-addon1">
    </div>
    <div class="input-group mb-3" style="margin-top:30px;">
        <div class="input-group-prepend">
            <span class="input-group-text">确认密码</span>
        </div>
        <input id="password2" type="password" class="form-control" placeholder="请输入" aria-label="Username" aria-describedby="basic-addon1">
    </div>
    <div class="btn-group" role="group" aria-label="Basic example" style="width:100%;margin-top:30px;">
        <button onclick="modifyPassword()" style="width:100%;" type="button" class="btn btn-success">保存</button>
     </div>
</div>
</body>
</html>

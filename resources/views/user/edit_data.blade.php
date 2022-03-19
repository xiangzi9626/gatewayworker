<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>编辑资料</title>
    <link rel="stylesheet" href="/common/bootstrap-4.6.1/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/common/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/layuimini-2/lib/layui-v2.5.5/css/layui.css" media="all">
    <script src="/common/js/jquery-3.2.1.js"></script>
    <script src="/common/js/ajax.js"></script>
    <script src="/common/js/layer3.1.1/layer.js"></script>
    <script src="/layuimini-2/lib/layui-v2.5.5/layui.js" charset="utf-8"></script>
    <script src="/layuimini-2/js/lay-config.js?v=2.0.0" charset="utf-8"></script>
    <script>
        function save(){
            var nickname=document.getElementById("nickname");
            var age=document.getElementById("age");
            var sex1=document.getElementById("sex");
            var sex2=document.getElementById("sex1");
            var province=document.getElementById("province");
            var city=document.getElementById("city");
            if (nickname.value==="" || /^\s+$/.test(nickname.value)){
                layer.msg("昵称不能为空");
                return false;
            }
            if (age.value==="" ||  /^\s+$/.test(age.value)){
                layer.msg("年龄不能为空");
                return false;
            }
            if (province.value==="" && city.value===""){
                layer.msg("请选择地区");
                return false;
            }
            if (sex.checked===true){
                sex="男";
            }else{
                sex="女";
            }
            var str="_token={{csrf_token()}}&nickname="+encodeURIComponent(nickname.value)
                +"&age="+age.value+"&sex="+sex+"&province="+province.value+"&city="+city.value;
            ajax("/user/edit_data",str,function (res){
                if (res==="ok"){
                    layer.open({
                        "title":"提示",
                        "content":"资料保存成功",
                        yes:function (){
                            window.location="/user/my";
                        }
                    })
                }else{
                    layer.msg(res);
                }
            })
        }
    </script>
</head>
<body>
@if(strpos($_SERVER['HTTP_USER_AGENT'],"MicroMessenger")===false)
<div class="container-fluid" style="font-size: 20px;line-height:40px;height:40px;background: #0b2e13;text-align: center;color:#fff;font-weight:600">
    <span onclick="window.history.back(-1);" style="float:left;">
        <i class="fa fa-angle-left"></i></span>编辑资料
</div>
@endif
<div class="container" style="margin-top:20px;">
    <form onsubmit="return false">
        <div class="form-group">
            <label style="color: grey">昵称</label>
            <input value="{{$user["nickname"]}}" type="text" class="form-control" id="nickname">
        </div>
        <div class="form-group">
            <label style="color: grey">年龄</label>
            <input type="number" onkeyup="this.value=this.value.replace(/\D/g,'')" value="{{$user["age"]}}" class="form-control" id="age">
        </div>
        <div class="form-group">
            <label style="float:left;color: grey">性别</label>
        <div class="form-check" style="float:left;margin-left: 20px;">
            <input id="sex" @if($user["sex"]=="男") checked @endif style="vertical-align: center;" class="form-check-input" type="radio" name="sex" value="男">
            <label class="form-check-label">男</label>
        </div>
        <div class="form-check" style="float:left;margin-left: 20px;">
            <input id="sex1" @if($user["sex"]=="女") checked @endif style="vertical-align: center;" class="form-check-input" type="radio" name="sex" value="女">
            <label class="form-check-label">女</label>
        </div>
        </div>
        <!----省市联动--->
        <div style="clear: both;" class="form-group">
            <label style="color: grey;margin-top:5px;">地区</label>
         </div>
        <div id="area-picker" style="width:100%;margin-top: -20px;">
        <div class="layui-form">
            <div class="layui-input-inline" style="width:50%;float:left;z-index: 100;">
                <select id="province" name="province" class="province-selector" style="width: 100%;">
                    <option value="{{explode("/",$user["city"])[0]}}">{{explode("/",$user["city"])[0]}}</option>
                </select>
            </div>
            <div class="layui-input-inline" style="width:50%;float:left;z-index: 100;">
                <select id="city" name="city" class="city-selector" style="width: 100%;">
                    <option value="{{explode("/",$user["city"])[1]}}">{{explode("/",$user["city"])[1]}}</option>
                </select>
            </div>
        </div>
        </div>
            <button onclick="save()" style="width:100%;margin-top:20px;" type="button" class="btn btn-primary">保存</button>
    </form>
</div>
<script>
    $(function (){
        layui.use(['layer','form','layarea'],function (){
            var layarea = layui.layarea;
            layarea.render({
                elem: '#area-picker',
                 data: {
                     province: '{{explode("/",$user["city"])[0]}}',
                     city: '{{explode("/",$user["city"])[1]}}',
                   //  county: '',
                 },
                change:function (res){
                    //选择结果
                    //console.log(res);
                }
            })
        })
    })
</script>
</body>
</html>

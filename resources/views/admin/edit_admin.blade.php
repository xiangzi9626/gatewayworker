<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/layuimini-2/lib/layui-v2.5.5/css/layui.css" media="all">
    <link rel="stylesheet" href="/layuimini-2/css/public.css" media="all">
    <style>
        body {
            background-color: #ffffff;
        }
    </style>
</head>
<body>
<div class="layui-form layuimini-form">
    <div class="layui-form-item">
        <label class="layui-form-label required">管理员账号</label>
        <div class="layui-input-block">
            <input type="text" name="username" lay-verify="required" lay-reqtext="管理员账号不能为空" placeholder="请输入管理员账号" value="{{$user["username"]}}" class="layui-input">
         </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">密码</label>
        <div class="layui-input-block">
            <input type="password" name="password" placeholder="留空不修改密码" value="" class="layui-input">
            <tip>留空不修改密码</tip>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">重复密码</label>
        <div class="layui-input-block">
            <input type="password" name="password2" placeholder="留空不修改密码" value="" class="layui-input">
            <tip>留空不修改密码</tip>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">手机号</label>
        <div class="layui-input-block">
            <input type="number" name="phone" value="{{$user["phone"]}}" lay-verify="required" lay-reqtext="手机号不能为空" placeholder="请输入手机号" class="layui-input">
         </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label required">级别</label>
        <div class="layui-input-block">
            <input type="radio" name="level" value="{{$user["level"]}}" checked="" title="@if($user["level"]==1)超级管理员@else普通管理员@endif">
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="saveBtn">确认保存</button>
        </div>
    </div>
</div>
<script src="/layuimini-2/lib/layui-v2.5.5/layui.js" charset="utf-8"></script>
<script>
    layui.use(['form'], function () {
        var form = layui.form,
            layer = layui.layer,
            $ = layui.$;

        //监听提交
        form.on('submit(saveBtn)', function (data) {
            var str=JSON.stringify(data.field);
           var domain = window.location.protocol+"//"+document.domain;
            domain=domain+"/admin/edit_admin/{{$_GET["id"]}}?_token={{csrf_token()}}";
          $.ajax({
              type:'POST',
              url:domain,
              data:str,
              success:function (res){
                 if (res==="ok"){
                     layer.msg("添加成功");
                     window.parent.location.reload();
                 }else{
                     layer.msg(res);
                 }
              },
              error:function (msg){
                 /* var m=eval("("+msg+")");
                  alert(m);*/
              }
          })
            return false;
        });
    });
</script>
</body>
</html>

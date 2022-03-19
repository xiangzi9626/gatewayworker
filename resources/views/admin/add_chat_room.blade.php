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
        <label class="layui-form-label required">聊天室名称</label>
        <div class="layui-input-block">
            <input type="text" name="name" lay-verify="required" lay-reqtext="聊天室名称不能为空" placeholder="请输入" value="" class="layui-input">
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
            domain=domain+"/admin/add_chat_room?_token={{csrf_token()}}";
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
                  var m=eval("("+msg+")");
                  alert(m);
              }
          })
            return false;
        });
    });
</script>
</body>
</html>

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
        <label class="layui-form-label required">上级菜单</label>
        <div class="layui-input-block">
           {!! $select !!}
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">菜单名称</label>
        <div class="layui-input-block">
            <input type="text" name="title" lay-verify="required" lay-reqtext="菜单名不能为空" placeholder="请输入菜单名称" value="{{$menu['title']}}" class="layui-input">
            <tip>填写菜单名称。</tip>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">图标</label>
        <div class="layui-input-block">
            <input type="text" name="icon" lay-verify="required" lay-reqtext="图标属性不能为空" placeholder="请输入图标属性" value="{{$menu['icon']}}" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">菜单URL</label>
        <div class="layui-input-block">
            <input type="text" name="href" placeholder="请输入URL" value="{{$menu['href']}}" class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">target</label>
        <div class="layui-input-block">
            @if($menu["target"]=="_self")
            <input type="radio" name="target" value="_self" title="_self" checked>
            <input type="radio" name="target" value="_blank" title="_blank" >
            @else
                <input type="radio" name="target" value="_self" title="_self">
                <input type="radio" name="target" value="_blank" title="_blank" checked>
            @endif
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">排序号</label>
        <div class="layui-input-block">
            <input type="number" name="sort" placeholder="请输入数字" value="{{$menu["sort"]}}" class="layui-input" lay-verify="required" lay-reqtext="请输入排序数字">
            <tip>填写排序号</tip>
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
           domain=domain+"/admin/edit_menu/{{$_GET["id"]}}?_token={{csrf_token()}}";

          $.ajax({
              type:'POST',
              url:domain,
              data:str,
              success:function (res){
                 if (res==="ok"){
                     layer.msg("操作成功");
                     window.parent.location.reload();
                 }else{
                     layer.msg(res);
                 }
              },
              error:function (msg){

              }
          })
            return false;
        });
    });
</script>
</body>
</html>

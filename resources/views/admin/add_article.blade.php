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
    <link rel="stylesheet" href="/layuimini-2/lib/font-awesome-4.7.0/css/font-awesome.css">
   <style>
        body {
            background-color: #ffffff;
        }
    </style>
    <script src="/common/wangEditor-3.0.17/release/wangEditor.min.js"></script>
</head>
<body>
<div class="layui-form layuimini-form">
    <div class="layui-form-item">
        <label class="layui-form-label required">标题</label>
        <div class="layui-input-block">
            <input type="text" id="title" name="title" lay-verify="required" lay-reqtext="标题不能为空" placeholder="请输入标题" value="" class="layui-input">
         </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">分类</label>
        <div class="layui-input-block" style="z-index:10000!important;">
          @if(empty($select))
                <a href="/admin/add_class_show" class="layui-btn layui-btn-success"><i class="fa fa-plus"></i> 创建</a>
            @else
                {!! $select !!}
            @endif
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">内容</label>
        <div class="layui-input-block" style="z-index: 0!important;">
            <div id="editor"></div>
            <script type="text/javascript">
                var we= window.wangEditor;
                var editor = new we('#editor');
                editor.customConfig.menus = [
                    'bold',
                    'italic',
                    'image',
                    'link',
                    'video',
                    'emoticon',
                ]
                editor.customConfig.uploadImgShowBase64 = true;  // 使用 base64 保存图片
                //editor.customConfig.uploadImgMaxLength = 1;
                /* editor.customConfig.customAlert = function (info){
                     layer.msg(info);
                 }*/
                //editor.customConfig.uploadImgServer = '{{url("applets/upload_product_img")}}?_token={{csrf_token()}}'
                editor.create();
            </script>
            <style>
                .w-e-text-container{
                    width:90%!important;
                    height:500px!important;
                    z-index: 0!important;
                }
            </style>
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
            var title=$("#title").val();
            var content=editor.txt.html();
            var weText=editor.txt.text();
            var cid=0;
            if (document.getElementById("cid")){
                cid=$("#cid").val();
            }
            if (weText==="" || /^(&nbsp;+\s*)+$/.test(weText)){
                return layer.msg("内容不能为空");
            }
            var str={cid:cid,title:title,content:content};
             str=JSON.stringify(str);
           var domain = window.location.protocol+"//"+document.domain;
            domain=domain+"/admin/add_article?_token={{csrf_token()}}";
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

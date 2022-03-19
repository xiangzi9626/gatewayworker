//使用Vue渲染模板，初始化时是没有数据的，需要ajax请求拿到数据
var vue = new Vue({
    el: "#vueContainer",
    data: {
        items: null
    }
})
//使用layui分页
layui.use('laypage', function () {
    var laypage = layui.laypage;
    laypage.render({
        elem: 'pagination'
        , count: totalCount
        , layout: ['count', 'prev', 'page', 'next', 'limit', 'refresh', 'skip']
        , jump: function (obj,first) {
            //点击非第一页页码时的处理逻辑。比如这里调用了ajax方法，异步获取分页数据
            if (!first) {
                pagination(obj.curr, obj.limit);//第二个参数不能用变量pageSize，因为当切换每页大小的时候会出问题
            }
        }
    })
})

var pageIndex = 1;
var pageSize = 1000;
var totalCount = 0;
pagination(pageIndex, pageSize);
function pagination(pageIndex, pageSize) {
    //查询条件
    var param = {
        pageIndex: pageIndex,
        pageSize: pageSize,
        //其它查询条件可在下面添加
    }
    $.ajax({
        type: 'POST',
        url: '/user/chat_room_list',
        dataType: 'json',
        data: param,
        async: false,//这里设置为同步执行，目的是等数据加载完再调用layui分页组件，不然分页组件拿不到totalCount的值
        success: function (data) {
             vue.items = data.data;
            totalCount = data.count;
        }
    })
}

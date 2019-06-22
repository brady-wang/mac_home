<script src="/themes/lib/layui-2.2.5/layui.js"></script>

<style>
    .up_article{
        color:#0ff048;
        cursor:pointer;
    }

</style>

        <!--左边菜单结束-->

        <!--主体内容开始-->
        <div id="main" class="col-md-10">
            <div class="page-header">
                <h2>文章管理 <small>Article</small></h2>
                <p class="text-primary">发布、编辑、删除文章</p>
                <div>
                    <a type="button" class="btn btn-info glyphicon glyphicon-plus pull-left" href="/admin/articles/add">新建文章</a>
                </div>
            </div>

            <div class="panel">
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                        <th>#ID</th>
                        <th>文章标题</th>
                        <th>图片</th>
                        <th>类别</th>
                        <th>状态</th>
                        <th>标签</th>
                        <th>pv</th>
                        <th>is_hot</th>
                        <th>发布时间</th>
                        <th>修改时间</th>
                        <th>操作</th>
                        </thead>

                        <tbody>
                        <?php if(!empty($data['list'])) {
                            foreach($data['list'] as $key=>$value): ?>
                                <tr >
                                <td class=""><?php echo $value['id'] ?></td>

                                <td class=""><a href = "/index/Article/detail/<?php echo $value['id'] ?>" target="_blank"><?php echo $value['title'] ?></td>
                                <td class="">
                                <?php  if(!empty($value['image'])){?>
                                    <img style="width:60px;height:39px;" src="<?php echo $value['image'] ?>" alt="">
                                <?php };?>
                                </td>
                                <?php
                                $category_id = $value['category'];
                                $category_name = isset($all_category[$category_id]) ? $all_category[$category_id]['category'] : '无分类';
                                ?>
                                <td class=""><?php echo $category_name; ?></td>

                                <td class="">
                                    <div class="">
                                        <?php
                                        if($value['is_del'] == 0){
                                            echo "<span data-id = '".$value['id']."' class = 'up_article' style='color:#0ff048'>上架</span>";
                                        } else {
                                            echo "<span data-id = '".$value['id']."' class = 'up_article' style='color:#cdec1b'>下架</span>";
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td ><?php  $value['tag'];  ?></td>
                                <td ><?php echo $value['pv']; ?></td>
                                <td><?php echo $value['is_hot']; ?></td>
                                <td><?php echo $value['create_time'] ?></td>
                                <td ><?php echo $value['update_time'] ?></td>
                                <td >
                                    <div >
                                        <a href="/admin/Articles/edit/<?php echo $value['id'];?>" class = 'edit_article btn btn-info btn-xs'  >
                                            编辑
                                        </a>
                                        <a href="javascript:void(0)" data-id="<?php echo $value['id']?>" class="del_article btn btn-info btn-xs">
                                             删除
                                        </a>
                                    </div>
                                </td>
                                </tr>

                        <?php endforeach;
                            }
                        ?>
                        <tr>
                            <td colspan="12"><div  style="text-align: right"> <ul id="pageLimit"></ul> </div></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--主体内容结束-->


<script>
    $(function () {
//        layui.use('laypage', function () {
//            var laypage = layui.laypage;
//            laypage.render({
//                elem: 'pager'
//                , limit:<?php //echo $data['page_size'];?>
//                , limits: [10, 20, 30, 40, 50]
//                , count: <?php //echo $data['total_rows']; ?>
//                , curr:<?php //echo $data['page'];?>
//                , layout: ['count', 'prev', 'page', 'next', 'limit']
//                , jump: function (obj, first) {
//                    if (!first) {
//                        var page = obj.curr;
//                        var page_size = obj.limit;
//                        layer.msg(page);
//                        location.href = "/admin/Articles/index?page=" + page + "&page_size=" + page_size;
//
//                    }
//                }
//            });
//        });

        //删除文章
        $(".del_article ").click(function(){
            var id = $(this).attr('data-id');
            layer.confirm('确定删除该文章？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {'id': id},
                    url: "/admin/Articles/delete",
                    success: function (res) {
                        layer.msg(res.msg);
                        if (res.success == true) {
                            setTimeout('location.reload();', 2000);
                        }
                    }
                })
            }, function(){
            });
        })

        //上下架文章
        $(".up_article ").click(function(){
            var id = $(this).attr('data-id');
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {'id': id},
                url: "/admin/Articles/up_down_article",
                success: function (res) {
                    console.log(res)
                    layer.msg(res.msg);
                    if (res.success == true) {
                        setTimeout('location.reload()', 2000);
                    }
                }
            })

        })

    })
    //分页
    $('#pageLimit').bootstrapPaginator({
        currentPage: <?php echo $data['page']; ?>,//当前的请求页面。
        totalPages: <?php echo $data['total_rows']; ?>,//一共多少页。
        size:"normal",//应该是页眉的大小。
        bootstrapMajorVersion: 3,//bootstrap的版本要求。
        alignment:"right",
        totalPages:<?php echo $data['total_page']; ?>,
        useBootstrapTooltip:false,
        numberOfPages:5,//一页列出多少数据。
        tooltipTitles: function (type, page, current) {
            switch (type) {
                case "first":
                    return "";
                case "prev":
                    return "";
                case "next":
                    return "";
                case "last":
                    return "";
                case "page":
                    return  '';
            }
        },
        itemTexts: function (type, page, current) {//如下的代码是将页眉显示的中文显示我们自定义的中文。
            switch (type) {
                case "first": return "首页";
                case "prev": return "上一页";
                case "next": return "下一页";
                case "last": return "末页";
                case "page": return page;
            }
        },
        pageUrl: function(type, page, current){
            return "/admin/articles/index?page="+page;

        }
    });
</script>

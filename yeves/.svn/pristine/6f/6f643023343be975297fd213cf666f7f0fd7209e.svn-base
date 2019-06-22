
<section class="container">
    <div class="content-wrap">
        <div class="content">
            <div class="title">
            </div>
            <?php if(!empty($data)){
                foreach($data as $v):
            ?>
                    <article class="excerpt excerpt-1">
                        <header><a class="cat" href="program"><?php echo $v["cate_name"];?><i></i></a>
                            <h2><a href="/index/article/detail/<?php echo $v['id'];?>" title=""><?php echo $v['title'];?></a></h2>
                        </header>
                        <p class="meta">
                            <time class="time"><i class="glyphicon glyphicon-time"></i> <?php echo $v['create_time'];?></time>
                            <span class="views"><i class="glyphicon glyphicon-eye-open"></i> 共<?php echo $v['pv'];?>人围观</span> <a class="comment" href="/index/article/detail#comment"><i class="glyphicon glyphicon-comment"></i> <span id = "sourceId::yeves.cn<?php echo $v['id'];?>" class = "cy_cmt_count" ></span>个不明物体</a></p>

                        <script id="cy_cmt_num" src="https://changyan.sohu.com/upload/plugins/plugins.list.count.js?clientId=cytsceCWs">
                        </script>
                        <p class="note"><?php echo $v['description'];?> </p>
                    </article>

            <?php
                    endforeach;
            } ?>


            <script src="/themes/mall/js/bootstrap-paginator.js"></script>
            <div class="pagination" style="display: block;">
                <ul id="pageLimit">

                </ul>
            </div>
            <script>

                var currentPage = "<?php echo $page;?>";
                $('#pageLimit').bootstrapPaginator({
                    currentPage: currentPage,//当前的请求页面。
                    totalPages: <?php echo $total_page;?>,//一共多少页。
                    size: "normal",//应该是页眉的大小。
                    bootstrapMajorVersion: 3,//bootstrap的版本要求。
                    alignment: "right",
                    numberOfPages: 10,//一页列出多少数据。
                    itemTexts: function (type, page, current) {//如下的代码是将页眉显示的中文显示我们自定义的中文。
                        switch (type) {
                            case "first":
                                return "首页";
                            case "prev":
                                return "上一页";
                            case "next":
                                return "下一页";
                            case "last":
                                return "末页";
                            case "page":
                                return page;
                        }
                    },
                    onPageClicked: function (event, originalEvent, type, page) {
                        if (currentPage != page) {
                            var url = '/index/article?page=' + page;
                            location.href = url
                        }

                    }
                });
            </script>
        </div>
    </div>
    <aside class="sidebar" style="margin-top: 9px;">
        <!--        右边栏开始 -->
        <?php include "right.php";?>
        <!--        右边栏结束-->
    </aside>
</section>


<style>
    .index-right-img img {

        width: 210px;
        max-height: 140px;
        margin-top: 19px;
        border-radius: 7px;
    }
</style>
<section class="container">
    <div class="content-wrap">
        <div class="content">
            <div class="jumbotron">
                <h1>欢迎访问brady's博客</h1>
                <p>在这里可以看到前端技术，后端程序，网站内容管理系统等文章，还有我的程序人生！</p>
            </div>
            <div id="focusslide" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#focusslide" data-slide-to="0" class="active"></li>
                    <li data-target="#focusslide" data-slide-to="1"></li>
                    <li data-target="#focusslide" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                    <div class="item active"><a href="javascript:void();" target="_blank"><img
                                src="/themes/mall/images/banner/banner_04.jpg" alt="" class="img-responsive"></a>
                        <!--<div class="carousel-caption"> </div>-->
                    </div>
                    <div class="item"><a href="javascript:void();" target="_blank"><img src="/themes/mall/images/banner/banner_05.jpg"
                                                                      alt="" class="img-responsive"></a>
                        <!--<div class="carousel-caption"> </div>-->
                    </div>
                    <div class="item"><a href="javascript:void();" target="_blank"><img src="/themes/mall/images/banner/banner_07.jpg"
                                                                      alt="" class="img-responsive"></a>
                        <!--<div class="carousel-caption"> </div>-->
                    </div>
                </div>
                <a class="left carousel-control" href="#focusslide" role="button" data-slide="prev" rel="nofollow">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> <span
                        class="sr-only">上一个</span> </a> <a class="right carousel-control" href="#focusslide"
                                                           role="button" data-slide="next" rel="nofollow"> <span
                        class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> <span
                        class="sr-only">下一个</span> </a></div>
            <article class="excerpt-minic excerpt-minic-index contenttop" >

                <strong>博主置顶</strong>
                <p class="note">很多事情的保质期只有三分钟。过了三分钟，人不值得爱，爱不值得做，做什么都丧失了原有的新鲜，只剩下原本就苍白又苍老的微妙火光，新的人奔着这三分钟飞蛾扑火地冲过去。</p>
            </article>
            <div class="title">
                <h3>最新发布</h3>
                <div class="more">
                    <?php if(!empty($recent_tag)){ foreach($recent_tag as $v):?>
                        <a href="/index/article/tag/<?php echo $v['id']?>"><?php echo $v['tag_name'];?> </a>
                    <?php endforeach; }?>
                </div>
            </div>
            <div id="article">
                <?php if (!empty($data)) {
                    foreach ($data as $v):
                        if(empty($v['image'])){
                        ?>
                        <div class="container">
                            <div class="row">

                                <article class="excerpt excerpt-1">
                                    <div class="col-md-12">
                                        <header><a class="cat" href="#"><?php echo $v['cate_name']; ?><i></i></a>
                                            <h2><a href="/index/article/detail/<?php echo $v['id']; ?>"
                                                   title=""><?php echo $v['title']; ?></a></h2>
                                        </header>
                                        <p class="meta">
                                            <time class="time"><i class="glyphicon glyphicon-time"></i><?php echo $v['title']; ?>
                                            </time>
                                <span class="views"><i
                                        class="glyphicon glyphicon-eye-open"></i> 共<?php echo $v['pv']; ?>人围观</span> <a
                                                class="comment" href="#"><i class="glyphicon glyphicon-comment"></i> <span
                                                    id="sourceId::yeves.cn<?php echo $v['id']; ?>" class="cy_cmt_count"></span>个不明物体</a>
                                        </p>
                                        <script id="cy_cmt_num"
                                                src="https://changyan.sohu.com/upload/plugins/plugins.list.count.js?clientId=cytsceCWs">
                                        </script>
                                        <p class="note"><?php echo $v['description']; ?> ... </p>
                                    </div>
                                </article>

                            </div>

                        </div>

                    <?php }else {?>

                        <div class="container">
                            <div class="row">
                                <article class="excerpt excerpt-1">
                                    <div class="col-md-8">
                                            <header><a class="cat" href="#"><?php echo $v['cate_name']; ?><i></i></a>
                                                <h2><a href="/index/article/detail/<?php echo $v['id']; ?>"
                                                       title=""><?php echo $v['title']; ?></a></h2>
                                            </header>
                                            <p class="meta">
                                                <time class="time"><i class="glyphicon glyphicon-time"></i><?php echo $v['title']; ?>
                                                </time>
                                                <span class="views"><i
                                                        class="glyphicon glyphicon-eye-open"></i> 共<?php echo $v['pv']; ?>人围观</span> <a
                                                    class="comment" href="#"><i class="glyphicon glyphicon-comment"></i> <span
                                                        id="sourceId::yeves.cn<?php echo $v['id']; ?>" class="cy_cmt_count"></span>个不明物体</a>
                                            </p>
                                            <script id="cy_cmt_num"
                                                    src="https://changyan.sohu.com/upload/plugins/plugins.list.count.js?clientId=cytsceCWs">
                                            </script>
                                            <p class="note"><?php echo $v['description']; ?> ... </p>

                                    </div>

                                    <div class="col-md-4 index-right-img">
                                        <a href="/index/article/detail/<?php echo $v['id']; ?>"><img src="<?php echo $v['image'];?>" alt=""></a>
                                    </div>
                                </article>

                            </div>
                        </div>


                        <?php }
                    endforeach;
                }
                ?>
            </div>

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
                            var url = '/welcome?page=' + page;
                            location.href = url
                        }

                    }
                });
            </script>
        </div>
    </div>

    <aside class="sidebar">
        <div class="fixed">
            <div class="panel panel-default sitetip">

                    <strong>站点公告</strong>
                    <h3 class="title">网站已上线</h3>
                    <p class="overView">经过一段时间的coding  网站已上线。。。</p>

            </div>
        </div>
<!--        右边栏开始 -->
        <?php include "right.php";?>
<!--        右边栏结束-->
    </aside>
</section>

                                                                                                                                                                                  
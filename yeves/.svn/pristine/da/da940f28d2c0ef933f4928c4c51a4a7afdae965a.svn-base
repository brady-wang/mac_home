<style>

    .article_1h3,h4,h5,h6{
        margin:15px 0;
    }
    .article_1 h1,h2,h3,h4,h5,h6{
        margin:10px 0;
    }

    .article_1 li{
        margin-left:10px;
    }
    .article_1 pre{
        margin:10px 0;
    }
</style>
<section class="container">
    <div class="content-wrap">
        <div class="content">
            <header class="article-header">
                <h1 class="article-title"><?php echo $data['title'];?></h1>
                <div class="article-meta"> <span class="item article-meta-time">
          <time class="time" data-toggle="tooltip" data-placement="bottom" title="时间：<?php echo $data['create_time'];?>"><i class="glyphicon glyphicon-time"></i> <?php echo $data['create_time'];?></time>
          </span>
<!--                    <span class="item article-meta-source" data-toggle="tooltip" data-placement="bottom" title="来源：第一PHP社区"><i class="glyphicon glyphicon-globe"></i> 第一PHP社区</span> -->

                    <span class="item article-meta-category" data-toggle="tooltip" data-placement="bottom" title="栏目：<?php echo $data['cate_name'];?>"><i class="glyphicon glyphicon-list"></i> <a href="" title=""><?php echo $data['cate_name'];?></a></span>
                    <span class="item article-meta-views" data-toggle="tooltip" data-placement="bottom" title="查看：<?php echo $data['pv'];?>"><i class="glyphicon glyphicon-eye-open"></i> 共<?php echo $data['pv'];?>人围观</span>
                    <span class="item article-meta-comment" data-toggle="tooltip" data-placement="bottom"><i class="glyphicon glyphicon-comment"></i> <span href="#SOHUCS" id="changyan_count_unit"></span>个不明物体</span> </div>

                    <script type="text/javascript" src="https://assets.changyan.sohu.com/upload/plugins/plugins.count.js">
                    </script>
            </header>
            <article class="article_1">
                <?php echo $data['content'];?>
            </article>
            <div class="article-tags">标签：<a  rel="tag"><?php echo $data['tag'];?></a></div>

            <div class="title" id="comment">
                <!--PC版-->
                <div id="SOHUCS" sid="yeves.cn<?php echo $data['id']?>"></div>
                <script charset="utf-8" type="text/javascript" src="https://changyan.sohu.com/upload/changyan.js" ></script>
                <script type="text/javascript">
                    window.changyan.api.config({
                        appid: 'cytsceCWs',
                        conf: 'prod_e7b8f9ec9e9638f28a77e80b05006523'
                    });
                </script>
            </div>


        </div>
    </div>
    <aside class="sidebar">

        <!--        右边栏开始 -->
        <?php include "right.php";?>
        <!--        右边栏结束-->
    </aside>
</section>

<script type="text/javascript">
        $(document).ready(function() {
            //为超链接加上target='_blank'属性
            $('a[href^="http"]').each(function() {
                $(this).attr('target', '_blank');
            });
            $('a[href^="https"]').each(function() {
                $(this).attr('target', '_blank');
            });
        });
</script>


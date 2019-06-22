<div class="widget widget_sentence">
    <h3>每日一句</h3>
    <div class="widget-sentence-content">
        <h4><?php echo $daily['dateline'] ?></h4>
        <p>
            <a href="/index/Daily"><img style="width:321px;" src="<?php echo $daily['picture']; ?>" alt=""></a>
        </p>
        <br>
        <p><?php echo $daily['content'] ?></p>
        <p><?php echo $daily['note'] ?></p>
        <div class="check_more">
            <a  href="/index/Daily">查看更多</a>
        </div>

    </div>

</div>

<script type="text/javascript">
    $(document).ready(function(){
        /*多彩tag*/
        var tags_a = $("#tags").find("a");
        tags_a.each(function(){
            var x = 9;
            var y = 0;
            var rand = parseInt(Math.random() * (x - y + 1) + y);
            $(this).addClass("size"+rand);
        });

    });
</script>
<style type="text/css">

    .taglist{padding:20px 20px 30px 20px;}
    .taglist a{padding:3px;display:inline-block;white-space:nowrap;}
    a.size1{font-size:25px;padding:10px;color:#804D40;}
    a.size1:hover{color:#E13728;}
    a.size2{padding:7px;font-size:20px;color:#B9251A;}
    a.size2:hover{color:#E13728;}
    a.size3{padding:5px;font-size:35px;color:#C4876A;}
    a.size3:hover{color:#E13728;}
    a.size4{padding:5px;font-size:15px;color:#B46A47;}
    a.size4:hover{color:#E13728;}
    a.size5{padding:5px;font-size:25px;color:#E13728;}
    a.size5:hover{color:#B46A47;}
    a.size6{padding:0px;font-size:12px;color:#77625E}
    a.size6:hover{color:#E13728;}
</style>
<div class="widget widget_sentence">
    <h3>标签云</h3>

    <div class="widget-sentence-content">
        <div class="taglist" id="tags">
            <?php if(!empty($all_tag)){?>
                <?php foreach($all_tag as $v):?>
                    <a href="/index/article/tag/<?php echo $v['id']?>" class="<?php echo $v['tag_button_type']?>"><?php echo $v['tag_name']?></a>
                <?php endforeach;?>
            <?php  }?>
        </div>


    </div>

</div>
<div class="widget widget_hot">
    <h3>文章分类</h3>
    <ul>
        <?php foreach($sum_by_cate as $v):?>
            <li><a href="/index/article/category?id=<?php echo $v['category'];?>"><span class="text"><?php echo $v['cate_name'];?></span> <div style="float:right;margin-top: -5px;margin-top: -19px;" ><?php echo $v['number'];?>篇</div></a></li>
        <?php endforeach;?>
    </ul>
</div>

<div class="widget widget_hot">
    <h3>文章归档</h3>
    <?php foreach($sum_by_month as $v):?>
        <li><a href="/index/article/month/<?php echo $v['article_month'];?>"><span class="text"><?php echo $v['article_month'];?></span> <div style="float:right;margin-top: -5px;margin-top: -19px;" ><?php echo $v['number'];?>篇</div> </a> </li>
    <?php endforeach;?>
</div>
<div class="widget widget_hot">
    <h3>热门文章</h3>
    <?php if (!empty($hot_recommend)) {
        foreach ($hot_recommend as $v):
            ?>
            <ul>
                <li><a href="/index/article/detail/<?php echo $v['id']; ?>"><span
                            class="text"><?php echo $v["title"]; ?></span><span class="muted"><i
                                class="glyphicon glyphicon-time"></i> <?php echo $v["create_time"]; ?> </span><span
                            class="muted"><i
                                class="glyphicon glyphicon-eye-open"></i> <?php echo $v["pv"]; ?></span></a>
                </li>

            </ul>
            <?php
        endforeach;
    } ?>

</div>
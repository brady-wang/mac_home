<link rel="stylesheet" type="text/css" href="/themes/lib/layui/css/layui.css">
<section class="container">
    <div class="content-wrap">
        <div class="content">
            <ul class="layui-timeline">
                <?php if(!empty($list)){ foreach($list as $v):?>
                <li class="layui-timeline-item">
                    <i class="layui-icon layui-timeline-axis">&#xe63f;</i>
                    <div class="layui-timeline-content layui-text">
                        <h2 class="layui-timeline-title"><?php echo $v['dateline']?></h2>
                        <p class="daily-p">
                            <?php echo $v['content']?>
                        </p>
                        <p class="daily-p">
                            <?php echo $v['note']?>
                        </p>
                        <p class="daily-p">
                            <?php echo str_replace("词霸小编",'作者寄语',$v['translation'])?>
                        </p>
                    </div>
                </li>
                <?php endforeach; } ?>
                <li class="layui-timeline-item">
                    <i class="layui-icon layui-timeline-axis">&#xe63f;</i>
                    <div class="layui-timeline-content layui-text">
                        <div class="layui-timeline-title">过去</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</section>







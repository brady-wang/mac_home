<link rel="stylesheet" href="/themes/mall/css/archive2.css" />

<style>
    li{list-style:none;}
    .hk-archives ul{
        text-indent: 2em;
    }
    li {
        display: list-item;
        text-align: -webkit-match-parent;
    }

    .hk-archives span {
        line-height: 32px;
        font-size: 16px;
    }
    .hk-archives ul ul {
        margin-left: 30px;
    }
</style>
<div class="container">
    <div class="row" style="margin-left:20px;" >
        <div class="col-md-12">
            <div class="post-body content-wrap" >
                <div class="hk-archives">
                    <?php if(!empty($list)){
                        foreach($list as $year=>$v):
                        ?>
                            <h3><?php echo $year;?>年</h3>

                            <ul>
                                <?php foreach($v as $month=>$vv):?>
                                    <li><span><?php echo $month; ?>月</span>
                                        <ul>
                                            <?php   foreach($vv as $vvv): ?>
                                            <li><?php echo date("d",strtotime($vvv['create_time'])); ?>日：<a href="/index/article/detail/<?php echo $vvv['id']?>"><?php echo $vvv['title'];?></a>
                                                <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                    <?php
                        endforeach;//年
                        } ?>



                </div>
            </div>

        </div>

    </div>
</div>







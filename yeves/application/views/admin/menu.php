<style>
    #menu >.list-group > a{
        font-size: 1.3em;
    }

    .sub_menu  > a{
        font-size: 1.1em;
    }
</style>
<!--左边菜单开始-->
<div id="menu" class="col-md-2">
    <div class="list-group">
        <a href="/admin/articles" class="list-group-item <?php if($cur_title == 'Articles'){echo "active";}?>">文章管理</a>
        <a href="/admin/category" class="list-group-item <?php if($cur_title == 'Category'){echo "active";}?>">类别管理</a>
        <a href="/admin/tag" class="list-group-item <?php if($cur_title == 'Tag'){echo "active";}?>">标签管理</a>
        <a  data-toggle="collapse"  href="#setting"  role="button" aria-expanded="false" aria-controls="setting" class=" list-group-item "><span class="glyphicon glyphicon-chevron-down"></span>设置</a>
        <div class="collapse sub_menu <?php echo in_array($cur_title,['blog_info','site_info','change_password'])? "in" : ''; ?>" id="setting">
            <a href="/admin/Setting/blog_info" class="list-group-item <?php if($cur_title == 'user_info'){echo "active";}?>">资料设置</a>
            <a href="/admin/Setting/site_info" class="list-group-item <?php if($cur_title == 'site_info'){echo "active";}?>">网站设置</a>
            <a href="/admin/Setting/change_password" class="list-group-item <?php if($cur_title == 'change_password'){echo "active";}?>">密码修改</a>

        </div>
    </div>


</div>
<!--左边菜单结束-->
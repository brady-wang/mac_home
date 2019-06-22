<div class="container-fluid">
<div class="row-fluid">
    <div class="span3" id="sidebar">
        <ul class="nav nav-list bs-docs-sidenav nav-collapse collapse">
            <li  class = '<?php if($cur_title == 'user'){echo "active";}?>'>
                <a href="/admin/index"><i class="icon-chevron-right"></i> 用户管理</a>
            </li>
            <li class = '<?php if($cur_title == 'blog_info'){echo "active";}?>'>
                <a href="/admin/setting/blog_info"><i class="icon-chevron-right "></i> 站点设置</a>
            </li>

            <li class = '<?php if($cur_title == 'category'){echo "active";}?>'>
                <a href="/admin/category"><i class="icon-chevron-right"></i> 分类管理</a>
            </li>

            <li class = '<?php if($cur_title == 'tag'){echo "active";}?>'>
                <a href="/admin/tag"><i class="icon-chevron-right"></i> 标签管理</a>
            </li>

            <li class = '<?php if($cur_title == 'face'){echo "active";}?>'>
                <a href="/admin/setting/face"><i class="icon-chevron-right"></i> 头像</a>
            </li>


        </ul>
    </div>
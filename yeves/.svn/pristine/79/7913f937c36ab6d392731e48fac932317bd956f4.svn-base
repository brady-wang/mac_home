<script src="/themes/admin/js/category.js"></script>
<!--主体内容开始-->
<div id="main" class="col-md-10">
    <div class="page-header">
        <h2>分类管理 <small>Category</small></h2>
        <p class="text-primary">新增,删除分类</p>
    </div>
    <div>
        <div class="form-inline">
            <input type="text" class="form-control" id="category" placeholder="请输入分类名称">
            <a type="button" class="btn btn-info " id="add_category" href="javascript:void(0);"><span class="glyphicon glyphicon-plus pull-left"></span>新增</a>
        </div>

    </div>

    <div class="panel">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <th>#ID</th>
                    <th>分类</th>
                    <th>文章数</th>
                    <th>操作</th>
                </thead>

                <tbody>
                <?php foreach ($admin_category as  $key => $value): ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo $value['category'] ?></td>
                        <td><?php echo$value['number'];?></td>
                        <td>
                            <button class = 'edit_cate btn btn-info btn-xs' data-toggle="modal" data-target="#edit_cate_modal" data-cate_name="<?php echo $value['category'];?>" data-cate_id = '<?php echo $value['id'];?>'>编辑
                            </button>
                            <button class="btn btn-info btn-xs del_category"  data-cate_name="<?php echo $value['category'];?>" data-cate_id = '<?php echo $value['id'];?>' data-number = '<?php echo $value['number'];?>'
                               class="tpl-table-black-operation-del ">删除
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!--编辑分类模态框开始-->
<!-- Modal -->
<div class="modal fade" id="edit_cate_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">分类编辑</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="cate_name">分类名称</label>
                        <input type="text" name="cate_name" id="cate_name"  class="form-control" >
                        <input type="hidden" name="cate_id"  >
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary submit_edit_cate">提交</button>
            </div>
        </div>
    </div>
</div>
<!--编辑分类模态框结束-->

<script>



</script>
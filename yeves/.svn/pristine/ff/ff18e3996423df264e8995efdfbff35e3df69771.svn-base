<!--主体内容开始-->
<div id="main" class="col-md-10">
    <div class="page-header">
        <h2>标签管理 <small>Tag</small></h2>
        <p class="text-primary">(删除标签)</p>
    </div>

    <div class="panel">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <th>#id</th>
                    <th>标签</th>
                    <th>操作</th>
                </thead>

                <tbody>
                <?php foreach ($data as $key => $value): ?>
                    <tr class="gradeX">
                        <td><?php echo $value['id']?></td>
                        <td ><?php echo $value['tag_name']?></td>
                        <td >
                            <a data-id="<?php echo $value['id'];?>" id="del_tag"  class="btn btn-info btn-xs">
                                删除
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    $(function(){
        $("#del_tag").click(function(){
            var id = $(this).attr('data-id');

            var data = {};
            data.id = id;

//            $("#del_tag").attr('disabled', true);
//            $("#del_tag").html('处理中');
            $.ajax({
                type: 'post',
                url: '/admin/Tag/delete',
                data: data,
                dataType: 'json',
                success: function (res) {
                    layer.msg(res.msg);
                    if (res.success == true) {
                        setTimeout("location.reload();", 2000);
                    }
//                    else {
//                        $("#del_tag").removeAttr('disabled');
//                        $("#del_tag").html("删除");
//                    }
                }
            })
        })
    })
</script>
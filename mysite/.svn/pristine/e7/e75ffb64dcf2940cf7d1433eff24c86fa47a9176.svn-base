<script src="/themes/admin/js/category.js"></script>
<!--/span-->
<div class="span9" id="content">
	<div class="row-fluid">
		<div class="navbar">
			<div class="navbar-inner">
				<ul class="breadcrumb">
					<li>
						<a href="/admin/tag">标签管理</a> <span class="divider">/</span>
					</li>
					<li class="active">tag</li>
				</ul>
			</div>
		</div>
	</div>



	<div class="row-fluid">

		<div class="span12">
			<!-- block -->
			<div class="block">
				<div class="navbar navbar-inner block-header">
					<div class="muted pull-left">分类列表</div>

				</div>
				<div class="block-content collapse in">
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
                                    <button  data-id="<?php echo $value['id'];?>"   class="del_tag btn btn-primary btn-danger">
                                        删除
                                    </button>


                                </td>
                            </tr>
						<?php endforeach; ?>
                        </tbody>
                    </table>
				</div>
			</div>
			<!-- /block -->
		</div>
	</div>

    <script>
        $(function(){
            $(".del_tag").click(function(){
                var id = $(this).attr('data-id');


                layer.confirm('确定删除该标签?', {icon: 3, title: '提示'}, function (index) {
                    var data = {};
                    data.id = id;
                    $.ajax({
                        type: 'post',
                        url: '/admin/Tag/delete',
                        data: data,
                        dataType: 'json',
                        success: function (res) {
                            layer.msg(res.msg);
                            if (res.success == true) {
                                setTimeout("location.reload();", 2000);
                            } else {
                                curObj.removeAttr('disabled');
                                curObj.html("提交");
                            }
                        }
                    })


                });


            })
        })
    </script>


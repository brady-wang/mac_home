<script src="/themes/admin/js/category.js"></script>
<!--/span-->
<div class="span9" id="content">
	<div class="row-fluid">
		<div class="navbar">
			<div class="navbar-inner">
				<ul class="breadcrumb">
					<li>
						<a href="/admin/category">分类管理</a> <span class="divider">/</span>
					</li>
					<li class="active">category</li>
				</ul>
			</div>
		</div>
	</div>

		<div class="row-fluid">
			<div class="navbar">
				<div class="navbar-inner">
					<ul class="breadcrumb">
						<div class="form-inline">
							<input type="text" class="form-control" id="category" placeholder="请输入分类名称">
							<a type="button" class="btn btn-info " id="add_category" href="javascript:void(0);"><span class="glyphicon glyphicon-plus pull-left"></span>新增</a>
						</div>
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
						<tr>
							<th>#</th>
							<th>分类</th>
							<th>创建时间</th>
							<th>操作</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach($all_category as $value):?>
							<tr>
								<td><?php echo $value['id'];?></td>
								<td><?php echo $value['category'];?></td>

								<td><?php echo $value['create_time'];?></td>
								<td>
									<button class = 'edit_cate btn btn-info btn-xs' data-toggle="modal" data-target="#edit_cate_modal" data-cate_name="<?php echo $value['category'];?>" data-cate_id = '<?php echo $value['id'];?>'>编辑
									</button>
									<button class="btn btn-xs btn-danger del_category"  data-cate_name="<?php echo $value['category'];?>" data-cate_id = '<?php echo $value['id'];?>'>删除
									</button>
								</td>
							</tr>

						<?php endforeach;?>
						<tr>
							<td colspan="5"><div  style="text-align: right"> <div id="pageLimit"></div> </div></td>
						</tr>
						</tbody>

					</table>
				</div>
			</div>
			<!-- /block -->
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
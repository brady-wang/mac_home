<input type="hidden" id="page" value="<?php echo $page;?>">
<input type="hidden" id="total_rows" value="<?php echo $total_rows;?>">
<input type="hidden" id="total_page" value="<?php echo $total_page;?>">
<!--/span-->
                <div class="span9" id="content">
                    <div class="row-fluid">
                        	<div class="navbar">
                            	<div class="navbar-inner">
	                                <ul class="breadcrumb">
	                                    <li>
	                                        <a href="/admin/index">用户管理</a> <span class="divider">/</span>
	                                    </li>
	                                    <li class="active">index</li>
	                                </ul>
                            	</div>
                        	</div>
                    	</div>


                    <div class="row-fluid">

                        <div class="span12">
                            <!-- block -->
                            <div class="block">
                                <div class="navbar navbar-inner block-header">
                                    <div class="muted pull-left">用户列表</div>

                                </div>
                                <div class="block-content collapse in">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>用户名</th>
                                                <th>头像</th>
                                                <th>创建时间</th>
                                                <th>操作</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($list as $v):?>
                                        <tr>
                                            <td><?php echo $v['id'];?></td>
                                            <td><?php echo $v['username'];?></td>
                                            <td><img src = "<?php echo $v['face'];?>" style="width:80px;height:80px;" /></td>
                                            <td><?php echo $v['create_time'];?></td>
                                            <td><button class = 'edit_cate btn btn-info btn-xs edit_user'  data-id="<?php echo $v['id'];?>" >编辑</button > <button  class = 'edit_cate btn btn-danger btn-xs del_user' href="javascript:void(0)" data-id="<?php echo $v['id'];?>">删除</button></td>
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

                    <script>
                      $(function(){

                          //分页开始
                          var page,total_page,total_rows;
                          page = $("#page").val();
                          total_page = $("#total_page").val();
                          total_page = $("#total_page").val();
                          total_rows = $("#total_rows").val();
                          //分页
                          $('#pageLimit').bootstrapPaginator({
                              currentPage: page,//当前的请求页面。
                              totalPages: total_rows,//一共多少页。
                              size:"normal",//应该是页眉的大小。
                              bootstrapMajorVersion: 2,//bootstrap的版本要求。
                              alignment:"right",
                              totalPages:total_page,
                              useBootstrapTooltip:false,
                              numberOfPages:5,//一页列出多少数据。
                              tooltipTitles: function (type, page, current) {
                                  switch (type) {
                                      case "first":
                                          return "";
                                      case "prev":
                                          return "";
                                      case "next":
                                          return "";
                                      case "last":
                                          return "";
                                      case "page":
                                          return  '';
                                  }
                              },
                              itemTexts: function (type, page, current) {//如下的代码是将页眉显示的中文显示我们自定义的中文。
                                  switch (type) {
                                      case "first": return "首页";
                                      case "prev": return "上一页";
                                      case "next": return "下一页";
                                      case "last": return "末页";
                                      case "page": return page;
                                  }
                              },
                              pageUrl: function(type, page, current){
                                  return "/admin/index?page="+page;

                              }
                          });

                          //分页结束

                          //删除用户
                          $(".del_user").click(function(){
                              var id = $(this).attr('data-id');
                              var url = '/admin/index/del_user';
                              data = {'id':id};
                              var curObj = $(this);
                              curObj.html("处理中");
                              curObj.attr('disabled',true);
                              $.ajax({
                                  type:"post",
                                  url:url,
                                  dataType:'json',
                                  data:data,
                                  success:function(res){
                                      layer.msg(res.msg);
                                      if(res.success == true){
                                          setTimeout("location.reload()",1000);
                                          curObj.removeAttr('disabled');
                                          curObj.html("删除");
                                      } else {
                                          curObj.removeAttr('disabled');
                                          curObj.html("删除");
                                      }


                                  }
                              })

                          })

                          //编辑用户

                      })


                    </script>


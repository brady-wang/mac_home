
<!--/span-->
<div class="span9" id="content">
    <div class="row-fluid">
        <div class="navbar">
            <div class="navbar-inner">
                <ul class="breadcrumb">
                    <li>
                        <a href="/admin/index">站点设置</a> <span class="divider">/</span>
                    </li>
                    <li class="active">blog_info</li>
                </ul>
            </div>
        </div>
    </div>


    <div class="row-fluid">

        <div class="span12">
            <!-- block -->
            <div class="block">
                <div class="navbar navbar-inner block-header">
                    <div class="muted pull-left">个人资料</div>

                </div>
                <div class="block-content collapse in">
                    <div class="form-horizontal">
                        <fieldset>
                            <div class="control-group">
                                <label class="control-label" for="date01">姓名</label>
                                <div class="controls">
                                    <input type="text" class="input-xlarge datepicker" id="real_name" placeholder="真实姓名" value="<?php echo empty($data['real_name']) ? '':$data['real_name']; ?>">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="nick_name">昵称</label>
                                <div class="controls">
                                    <input type="text" class="input-xlarge datepicker" id="nick_name" placeholder="昵称" value="<?php echo empty($data['nick_name']) ? '':$data['nick_name']; ?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="job">职业</label>
                                <div class="controls">
                                    <input type="text" class="input-xlarge datepicker" id="job" placeholder="职业" value="<?php echo empty($data['job']) ? '':$data['job']; ?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="email">邮箱</label>
                                <div class="controls">
                                    <input type="text" class="input-xlarge datepicker" id="email"  placeholder="邮箱" value="<?php echo empty($data['email']) ? '':$data['email']; ?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="mobile">手机号</label>
                                <div class="controls">
                                    <input type="text" class="input-xlarge datepicker" id="mobile"  placeholder="手机号" value="<?php echo empty($data['mobile']) ? '':$data['mobile']; ?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="city">省</label>
                                <div class="controls">
                                    <input type="text" class="input-xlarge datepicker" id="city"  placeholder="省" value="<?php echo empty($data['city']) ? '':$data['city']; ?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="country">市</label>
                                <div class="controls">
                                    <input type="text" class="input-xlarge datepicker" id="country" placeholder="市" value="<?php echo empty($data['country']) ? '':$data['country']; ?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="motto">座右铭</label>
                                <div class="controls">
                                    <input type="text" class="input-xlarge datepicker" id="motto" placeholder="座右铭" value="<?php echo empty($data['motto']) ? '':$data['motto']; ?>" >
                                </div>
                            </div>


                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary btn-success" id="submit_blog_info">提交</button>

                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
            <!-- /block -->
        </div>
    </div>


    <script>
        $(function(){
            $("#real_name").focus();

            $("#submit_blog_info").click(function(){
                var real_name = $.trim($("#real_name").val());
                var nick_name = $.trim($("#nick_name").val());
                var job = $.trim($("#job").val());
                var email = $.trim($("#email").val());
                var mobile = $.trim($("#mobile").val());
                var city = $.trim($("#city").val());
                var country = $.trim($("#country").val());
                var motto = $.trim($("#motto").val());

                var data = {};

                data.real_name = real_name;
                data.nick_name = nick_name;
                data.job = job;
                data.email = email;
                data.mobile = mobile;
                data.city = city;
                data.country = country;
                data.motto = motto;

                var url = '/admin/Setting/do_set_blog_info';

                var curObj = $(this);
                curObj.attr('disabled',true);
                curObj.html("处理中");



                $.ajax({
                    type:"post",
                    url:url,
                    dataType:'json',
                    data:data,
                    success:function(res){
                        layer.msg(res.msg);
                        if(res.success == true){
                            setTimeout("location.reload()",2000);
                        } else {
                            curObj.removeAttr('disabled');
                            curObj.html("提交");
                        }
                    }
                })

            })
        })

    </script>




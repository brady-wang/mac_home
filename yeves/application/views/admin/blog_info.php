<script src="/themes/lib/jquery_pic_clipping/js/jquery.min.js" type="text/javascript"></script>
<!--主体内容开始-->
<div id="main" class="col-md-10">
    <div class="page-header">
        <h2>设置 <small>Site info </small></h2>
        <p class="text-primary">资料设置</p>
    </div>
    <div class="panel">
        <div class="panel-body">
            <div>
                <div class="form-group">
                    <label for="real_name">姓名</label>
                    <input type="text" class="form-control" value="<?php echo empty($data['real_name']) ? '':$data['real_name']; ?>" id="real_name" placeholder="real_name">
                </div>
                <div class="form-group">
                    <label for="nick_name">昵称</label>
                    <input type="text" class="form-control" value="<?php echo empty($data['nick_name']) ? '':$data['nick_name']; ?>" id="nick_name" placeholder="nick_name">
                </div>

                <div class="form-group">
                    <label for="job">职业</label>
                    <input type="text" class="form-control" value="<?php echo empty($data['job']) ? '':$data['job']; ?>" id="job" placeholder="job">
                </div>

                <div class="form-group">
                    <label for="email">邮箱</label>
                    <input type="text" class="form-control"  value="<?php echo empty($data['email']) ? '':$data['email']; ?>" id="email" placeholder="email">
                </div>

                <div class="form-group">
                    <label for="mobile">手机号</label>
                    <input type="text" class="form-control"  value="<?php echo empty($data['mobile']) ? '':$data['mobile']; ?>" id="mobile" placeholder="mobile">
                </div>

                <div class="form-group">
                    <label for="city">省</label>
                    <input type="text" class="form-control"  value="<?php echo empty($data['city']) ? '':$data['city']; ?>" id="city" placeholder="city">
                </div>

                <div class="form-group">
                    <label for="country">市</label>
                    <input type="text" class="form-control"  value="<?php echo empty($data['country']) ? '':$data['country']; ?>" id="country" placeholder="country">
                </div>

                <div class="form-group">
                    <label for="motto">座右铭</label>
                    <input type="text" class="form-control"  value="<?php echo empty($data['motto']) ? '':$data['motto']; ?>" id="motto" placeholder="motto">
                </div>


                <button type="submit" class="btn btn-default btn-info" id="submit_user_info">Submit</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        $("#real_name").focus();

        $("#submit_user_info").click(function(){
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

            var url = '/admin/Setting/do_set_blog_info'

            $.ajax({
                type:"post",
                url:url,
                dataType:'json',
                data:data,
                success:function(res){
                    layer.msg(res.msg);
                    if(res.success == true){
                        setTimeout("location.reload()",2000);
                    }
                }
            })

        })
    })

</script>
</body>
</html>


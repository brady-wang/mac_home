<!--主体内容开始-->
<div id="main" class="col-md-10">
    <div class="page-header">
        <h2>密码修改 <small>Change password</small></h2>
        <p class="text-primary">密码修改</p>
    </div>

    <div class="panel">
        <div class="panel-body">
            <div class="form-horizontal">
                <div class="form-group">
                    <div class="col-sm-12">
                        <p class="form-control-static">当前用户:brady</p>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <input type="password" class="form-control" id="old_pwd" placeholder="请输入原密码">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <input type="password" class="form-control" id="new_pwd" placeholder="请输入新密码">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <input type="password" class="form-control" id="new_pwd_re" placeholder="请再次输入新密码">
                    </div>
                </div>

                <div class="form-group">
                    <div class=" col-sm-12">
                        <button type="submit" class="btn  btn-block " id="change_pwd_btn">修改</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--主体内容结束-->

<script>
    $(document).keyup(function(event){
        if(event.keyCode ==13){
            $("#change_pwd_btn").trigger('click');
        }
    });
    $(function () {
        $("#old_pwd").focus();
        $("#change_pwd_btn").click(function () {
            var old_pwd = $.trim($("#old_pwd").val());
            var new_pwd = $.trim($("#new_pwd").val());
            var new_pwd_re = $.trim($("#new_pwd_re").val());

            var front = "<?php echo $front?>";


            if (front) {
                if (old_pwd.length <= 0) {
                    layer.msg("原密码不能为空");
                    return;
                }
                if (new_pwd.length <= 0) {
                    layer.msg('新密码不能为空');
                    return;
                }

                if (new_pwd_re.length <= 0) {
                    layer.msg('请再次输入新密码');
                    return;
                }

                if(new_pwd !== new_pwd_re){
                    layer.msg('两次输入不一致');
                    return;
                }
            }
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {'old_pwd': old_pwd, 'new_pwd': new_pwd,'new_pwd_re': new_pwd_re},
                url: "/admin/Setting/do_change_pwd",
                success: function (res) {
                    layer.msg(res.msg);
                    if (res.success == true) {
                        setTimeout('location.reload()', 2000);
                    }
                }
            })
        })
    })
</script>
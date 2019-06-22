<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title>登陆</title>

    <!-- Bootstrap -->
    <link href="/themes/lib/bootstrap-3.3.7/css/bootstrap.min.css" rel="stylesheet">

</head>

<style>
    .form-horizontal .form-group {
        margin-right: -11px;
        margin-left: -15px;
        margin-top: 36px;
    }
    .panel-body {
        padding: 263px;
    }

</style>
<body>
<div class="container-fluid">
    <div class="panel">
        <div class="panel-body text-center">
            <div class="form-horizontal">
                <div class="form-group">
                    <label for="username" class="col-sm-2 control-label">用户名</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control input-lg username" id="username" placeholder="请输入用户名">
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="col-sm-2 control-label ">密&nbsp;&nbsp;&nbsp;码</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control input-lg password" id="password" placeholder="请输入密码">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-default btn-block btn-info" id="login">Login</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="/themes/lib/jquery/jquery-3.3.1.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/themes/lib/bootstrap-3.3.7/js/bootstrap.min.js"></script>
<script src="/themes/lib/layer-3.1.1/layer.js"></script>

<script>
    $(document).keyup(function(event){
        if(event.keyCode ==13){
            $("#login").trigger('click');
        }
    });

    $(function () {
        $(".username").focus();
        $("#login").click(function () {
            var username = $.trim($(".username").val());
            var password = $.trim($(".password").val());

            front_flag = false;
            if (front_flag == true) {
                if (username.length <= 0) {
                    layer.msg("请输入用户名");
                    return;
                }
                if (password.length <= 0) {
                    layer.msg('请输入密码');
                    return;
                }

            }
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {'username': username, 'password': password},
                url: "/admin/login/do_login",
                success: function (res) {
                    layer.msg(res.msg);
                    if (res.success == true) {
                        setTimeout('location.href="/admin/index"', 2000);
                    }
                }
            })
        })
    })
</script>
</body>
</html>
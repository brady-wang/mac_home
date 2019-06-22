<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>管理后台</title>

    <link href="/themes/lib/bootstrap-3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="/themes/admin/css/style.css" rel="stylesheet">


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/themes/lib/jquery/jquery-3.3.1.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/themes/lib/bootstrap-3.3.7/js/bootstrap.min.js"></script>
    <script src="/themes/lib/layer-3.1.1/layer.js"></script>
    <script src="/themes/lib/bootstrap-paginator.js"></script>

    <!--[if lt IE 9]>
    <script src="/themes/lib/bootstrap-3.3.7/js/html5shiv.min.js"></script>
    <script src="/themes/lib/bootstrap-3.3.7/js/respond.min.js"></script>
    <![endif]-->

    <style>
        .face{
            width: 30px;
            height: 30px;
            border-radius: 15px;
        }
        .my-navbar li{
            margin-top:5px;
        }

        #pageLimit a{
            cursor:pointer;
        }

    </style>
</head>

<body>
<!--header开始-->
<div id="header">
    <!--导航开始-->
    <nav class="navbar navbar-default navbar-fixed-top navbar-inverse my-navbar ">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#top-bar" aria-expanded="true">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/admin/Articles">后台管理</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" >


                <ul class="nav navbar-nav navbar-right">
                    <li><a>欢迎您:<?php echo $user_info['username']?> <span><img class="face" src="<?php echo $user_info['face'] ?>" alt=""></span></a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">设置 <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="/login/logout">注销</a></li>
                            <li><a href="/admin/Setting/face">头像</a></li>

                        </ul>

                    </li>
                    <li><a href="/" target="_blank">网站前台</a></li>


                </ul>



            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
    <!--导航结束-->
</div>
<!--header结束-->
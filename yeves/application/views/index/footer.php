<footer class="footer">
    <div class="container">
        <p>&copy; 2018 <a href="">yeves.cn</a> &nbsp; <a href="http://www.miitbeian.gov.cn/" target="_blank" rel="nofollow">粤ICP备18010513号-2</a> &nbsp; &nbsp;</p>
    </div>
    <div id="gotop"><a class="gotop"></a></div>
</footer>



<!--登录注册模态框-->
<div class="modal fade " id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="/Admin/Index/login" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="loginModalLabel">登录</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="loginModalUserNmae">用户名</label>
                        <input type="text" class="form-control" id="loginModalUserNmae" placeholder="请输入用户名" autofocus maxlength="15" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label for="loginModalUserPwd">密码</label>
                        <input type="password" class="form-control" id="loginModalUserPwd" placeholder="请输入密码" maxlength="18" autocomplete="off" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">登录</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/themes/mall/js/bootstrap.min.js"></script>
<script src="/themes/mall/js/jquery.ias.js"></script>
<script src="/themes/mall/js/scripts.js"></script>
<script src="/themes/mall/lib/layer/layer.js"></script>
</body>
</html>
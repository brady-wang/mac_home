
// 按键回调
function keydownLogin(event) {
    // 回车键
    if (event.keyCode == 13) {
        submitLogin();
    }
}

// 登陆
function submitLogin() {

    var data = $("#loginForm").serializeObject();

    $.ajax({
        url: "/Auth/ajaxLogin",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            console.log(data);
            if (0 == data.code) {
                window.location = referer;
            } else {
                $.zmsg.error(data.msg);
            }
        },
        error: function(data) {
            console.log(data.responseText);
            $.zmsg.fatal(data.responseText);
        }
    });
}

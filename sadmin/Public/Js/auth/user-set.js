
// 回车提交
function keydownSubmit() {
    // 回车键
    if (event.keyCode == 13) {
        setSubmit();
    }
}

// 提交修改
function setSubmit() {

    var data = $('#setForm').serializeObject();

    $.loading.show('setUser');
    $.ajax({
        url: "/Auth/ajaxUserSet",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('setUser');
            if (0 == data.code) {
                $.zmsg.success(referer);
            } else {
                $.zmsg.error(data.msg);
            }
        },
        error: function(data) {
            $.loading.hide('setUser');
            $.zmsg.fatal(data.responseText);
        }
    });
}

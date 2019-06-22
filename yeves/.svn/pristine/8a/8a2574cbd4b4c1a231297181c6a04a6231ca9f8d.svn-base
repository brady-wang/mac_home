/**
 * Created by brady.wang on 2018/2/9.
 */

$(function () {
    $("#name").focus();
    $("#submit_blog_info").click(function () {
        var name = $.trim($("#name").val());
        var job = $.trim($("#job").val());
        var email = $.trim($("#email").val());
        var city = $.trim($("#city").val());
        var country = $.trim($("#country").val());

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'name': name, 'job': job,'email': email,'city': city,'country': country},
            url: "/admin/Others/do_set_blog_info",
            success: function (res) {
                layer.msg(res.msg);
                if (res.success == true) {
                    setTimeout('location.reload()', 2000);
                }
            }
        })
    })
})
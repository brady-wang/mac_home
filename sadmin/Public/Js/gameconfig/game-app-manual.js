
// 配置文件更新AppID
function submitUpdateConfAppid(id) {

    var data = {};

    // type 1 appid
    data.type = 1;

    data.id = id;

    $.ajax({
        url: "/Gameconf/ajaxUpdateManualShareConf",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('updateLoading');
            if (0 == data.code) {
                window.location.reload();
            } else {
                $.zmsg.error(data.msg);
            }
        },
        error: function(data) {
            $.loading.hide('updateLoading');
            $.zmsg.fatal(data.responseText);
        }
    });
}

// 配置文件更新域名
function submitUpdateConfDomain(id) {

    var data = {};

    // type 2 appid
    data.type = 2;

    data.id = id;

    $.ajax({
        url: "/Gameconf/ajaxUpdateManualShareConf",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('updateLoading');
            if (0 == data.code) {
                window.location.reload();
            } else {
                $.zmsg.error(data.msg);
            }
        },
        error: function(data) {
            $.loading.hide('updateLoading');
            $.zmsg.fatal(data.responseText);
        }
    });
}

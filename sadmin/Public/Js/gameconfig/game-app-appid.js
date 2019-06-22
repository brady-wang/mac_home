
$(function() {
    'use strict';

    // query bar init
    if (query.share_min) {
        $('#shareMin').val(query.share_min);
    }
    if (query.share_max) {
        $('#shareMax').val(query.share_max);
    }
    if (query.status) {
        $('#status').children('[value=' + query.status + ']').prop('selected', true);
    }

    // add modal 触发初始化
    $('#addMod').on('show.bs.modal', function (event) {
        var btn = $(event.relatedTarget);
        $('#addModGid').val(btn.data('gid'));
        $('#addModGname').text(btn.data('gname'));
    });
});

// 添加提交
function submitAddConf() {

    let data = $("#addModForm").serializeObject();
    let gameName = $('#addModGname').text();

    $('#addMod').modal('hide');

    data.game_id = $('#addModGid').val();

    // appID校验
    if ("" == data.appid) {
        $.zmsg.errorShowModal('请输入AppID', 'addMod');
        return false;
    }

    // app名称校验
    if ("" == data.app_name) {
        $.zmsg.errorShowModal('请输入App名称', 'addMod');
        return false;
    }

    var msg = '<table class="table" style="width: 80%; margin: auto;">';
    msg += '<tr><td class="text-success">id： </td><td class="text-danger">' + data.gid + '</td></tr>';
    msg += '<tr><td class="text-success">游戏： </td><td class="text-danger">' + gameName + '</td></tr>';
    msg += '<tr><td class="text-success">AppID： </td><td class="text-danger">' + data.appid + '</td></tr>';
    msg += '<tr><td class="text-success">App名称： </td><td class="text-danger">' + data.app_name + '</td></tr>';
    msg += '</table>';

    $.Zebra_Dialog(msg, {
        'title': '添加信息核对',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'width': 720,
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {

                // 开始 loading 遮盖
                $.loading.show("addConfLoading");

                $.ajax({
                    url: "/Gameconf/ajaxConfAddAppid",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('addConfLoading');
                        if (0 == data.code) {
                            $.zmsg.success();
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('addConfLoading');
                        $.zmsg.fatalShowModal(data.responseText, 'addMod');
                    }
                });
            }
        }
    });
}

// 关闭配置
function submitCloseConf(gid, key) {

    var row = list[gid][key];

    var msg = '<div class="row">';
    msg += '<div class="col-sm-9 col-sm-offset-1">';
    msg += '<table class="table">';
    msg += '<tr><td class="text-success">游戏： </td><td class="text-danger">' + gameMap[gid] + '</td></tr>';
    msg += '<tr><td class="text-success">AppID： </td><td class="text-danger">' + row.appid + '</td></tr>';
    msg += '<tr><td class="text-success">App名称： </td><td class="text-danger">' + row.app_name + '</td></tr>';
    msg += '<tr><td class="text-success">已分享次数： </td><td class="text-danger">' + row.share_count + '</td></tr>';
    msg += '</table>';
    msg += '</div>';
    msg += '</div>';
    msg += '<p class="text-center text-danger">是否确认关闭该配置？</p>';

    var screen_width = document.body.clientWidth;
    if (screen_width > 640) {
        screen_width = 640;
    }

    var screen_height = document.documentElement.clientHeight;
    if (screen_height > 650) {
        screen_height = 650;
    } else {
        screen_height -= 250;
    }

    $.Zebra_Dialog(msg, {
        'title': '关闭配置确认',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'width': screen_width,
        'max_height': screen_height,
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {
                var data = {};
                data.id = row.id;
                data.status = 9;

                // 开始 loading 遮盖
                $.loading.show("closeConfLoading");

                $.ajax({
                    url: "/Gameconf/ajaxConfEditAppid",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('closeConfLoading');
                        if (0 == data.code) {
                            $.zmsg.success();
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('closeConfLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
}

// 打开配置
function submitOpenConf(gid, key) {

    var row = list[gid][key];

    var msg = '<div class="row">';
    msg += '<div class="col-sm-9 col-sm-offset-1">';
    msg += '<table class="table">';
    msg += '<tr><td class="text-success">游戏： </td><td class="text-danger">' + gameMap[gid] + '</td></tr>';
    msg += '<tr><td class="text-success">AppID： </td><td class="text-danger">' + row.appid + '</td></tr>';
    msg += '<tr><td class="text-success">App名称： </td><td class="text-danger">' + row.app_name + '</td></tr>';
    msg += '<tr><td class="text-success">已分享次数： </td><td class="text-danger">' + row.share_count + '</td></tr>';
    msg += '</table>';
    msg += '</div>';
    msg += '</div>';
    msg += '<p class="text-center text-danger">是否确认打开该配置？</p>';

    var screen_width = document.body.clientWidth;
    if (screen_width > 640) {
        screen_width = 640;
    }

    var screen_height = document.documentElement.clientHeight;
    if (screen_height > 650) {
        screen_height = 650;
    } else {
        screen_height -= 250;
    }

    $.Zebra_Dialog(msg, {
        'title': '关闭配置确认',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'width': screen_width,
        'max_height': screen_height,
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {
                var data = {};
                data.id = row.id;
                data.status = 1;

                // 开始 loading 遮盖
                $.loading.show("openConfLoading");

                $.ajax({
                    url: "/Gameconf/ajaxConfEditAppid",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('openConfLoading');
                        if (0 == data.code) {
                            $.zmsg.success();
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('openConfLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
}

// 删除配置
function submitDeleteConf(gid, key) {

    var row = list[gid][key];

    var msg = '<div class="row">';
    msg += '<div class="col-sm-9 col-sm-offset-1">';
    msg += '<table class="table">';
    msg += '<tr><td class="text-success">游戏： </td><td class="text-danger">' + gameMap[gid] + '</td></tr>';
    msg += '<tr><td class="text-success">AppID： </td><td class="text-danger">' + row.appid + '</td></tr>';
    msg += '<tr><td class="text-success">App名称： </td><td class="text-danger">' + row.app_name + '</td></tr>';
    msg += '</table>';
    msg += '</div>';
    msg += '</div>';
    msg += '<p class="text-center text-danger">是否确认删除该配置？</p>';

    var screen_width = document.body.clientWidth;
    if (screen_width > 640) {
        screen_width = 640;
    }

    var screen_height = document.documentElement.clientHeight;
    if (screen_height > 650) {
        screen_height = 650;
    } else {
        screen_height -= 250;
    }

    $.Zebra_Dialog(msg, {
        'title': '删除配置确认',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'width': screen_width,
        'max_height': screen_height,
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {
                var data = {};
                data.id = row.id;
                data.status = 1;

                // 开始 loading 遮盖
                $.loading.show("deleteConfLoading");

                $.ajax({
                    url: "/Gameconf/ajaxConfDeleteAppid",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('deleteConfLoading');
                        if (0 == data.code) {
                            $.zmsg.success();
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('deleteConfLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
}

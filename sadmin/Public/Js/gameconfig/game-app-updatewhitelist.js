
$(function() {
    'use strict';

    // query bar init
    if (query.white_type) {
        $('#whiteType').children('[value=' + query.white_type + ']').prop('selected', true);
    }
    if (query.white_val) {
        $('#whiteVal').val(query.white_val);
    }

    // delete modal 触发初始化
    $('#delMod').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        $('#delModWhiteVal').text(button.data('whiteval'));
        $('#delModId').val(button.data('id'));
    });
});

function submitAddWhiteList() {

    var data = $("#addModForm").serializeObject();

    // 开始 loading 遮盖
    $.loading.show("addWhiteListLoading");

    $('#addMod').modal('hide');

    $.ajax({
        url: "/Gameconf/ajaxAddUpdateWhiteList",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('addWhiteListLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "addMod");
            }
        },
        error: function(data) {
            $.loading.hide('addWhiteListLoading');
            $.zmsg.fatalShowModal(data.responseText, "addMod");
        }
    });
}

function submitDelWhiteList() {

    var data = {};

    data.id = $('#delModId').val();

    // 开始 loading 遮盖
    $.loading.show("deleteWhiteListLoading");

    $('#delMod').modal('hide');
    $.ajax({
        url: "/Gameconf/ajaxDelUpdateWhiteList",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('deleteWhiteListLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.error(data.msg);
            }
        },
        error: function(data) {
            $.loading.hide('deleteWhiteListLoading');
            $.zmsg.fatal(data.responseText);
        }
    });
}

function whiteConfSetting() {

    var data = {
        'white_status' : $('#white_status').val(),
        'white_version' : $('#white_version').val()
    };
    $.ajax({
        url: "/Gameconf/ajaxChangeUpdateWhiteConf",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "addMod");
            }
        },
        error: function(data) {
            $.zmsg.fatalShowModal(data.responseText, "addMod");
        }
    });
}

function updatePlayVersion(obj) {

    var version = $('#version_' + obj.data('playid')).val();
    if ('' == version) {
        $.zmsg.error('请指定版本');
        return false;
    }

    var msg = '<div class="row">';
    msg += '<div class="col-sm-9 col-sm-offset-1">';
    msg += '<table class="table">';
    msg += '<tr><td class="text-success">子游戏： </td><td class="text-danger">' + obj.data('playname') + '</td></tr>';
    msg += '<tr><td class="text-success">旧指定版本号： </td><td class="text-danger">' + obj.data('version') + '</td></tr>';
    msg += '<tr><td class="text-success">新指定版本号： </td><td class="text-danger">' + version + '</td></tr>';
    msg += '</table>';
    msg += '</div>';
    msg += '</div>';
    msg += '<p class="text-center text-danger">是否确认？</p>';

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
        'title': '修改确认',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'width': screen_width,
        'max_height': screen_height,
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {
                var data = {
                    play_id: obj.data('playid'),
                    version: version
                };

                // 开始 loading 遮盖
                $.loading.show("selectVerLoading");

                $.ajax({
                    url: "/Gameconf/ajaxUpdatePlayVersion",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('selectVerLoading');
                        if (0 == data.code) {
                            $.zmsg.success();
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('selectVerLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
}

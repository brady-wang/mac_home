
$(function() {
    'use strict';

    // 地址 tip
    $(".tiptip").tipTip({
        maxWidth: "auto",
        delay: 0
    });

    // query bar
    if (query.game_id) {
        $('#gameId').children("[value=" + query.game_id + "]").prop("selected", true);
    }

    // edit modal 触发初始化
    $('#edtMod').on('show.bs.modal', function (event) {
        var btn = $(event.relatedTarget);
        $('#edtModId').text(btn.data('id'));
        $('#edtModGame').children('[value=' + btn.data('game_id') + ']').prop('selected', true);
        $('#edtModType').children('[value=' + btn.data('db_type') + ']').prop('selected', true);
        $('#edtModHost').val(btn.data('host'));
        $('#edtModPort').val(btn.data('port'));
        $('#edtModUser').val(btn.data('user'));
        $('#edtModPwd').val(btn.data('pwd'));
        $('#edtModDbName').val(btn.data('db_name'));
        $('#edtModCharset').val(btn.data('charset'));
        if (btn.data('is_master') == 1) {
            $('#edtModMaster').prop('checked', true);
        } else if (btn.data('is_master') == 0) {
            $('#edtModSlave').prop('checked', true);
        }
        $('#edtModRemark').val(btn.data('remark'));
    });

    // delete modal 触发初始化
    $('#delMod').on('show.bs.modal', function (event) {
        var btn = $(event.relatedTarget);
        $('#delModId').text(btn.data('id'));
        $('#delModGame').text(btn.data('name'));
        $('#delModType').text(btn.data('db_type'));
        $('#delModHost').text(btn.data('host'));
        $('#delModDbName').text(btn.data('db_name'));
    });
});

function submitAddConf() {

    var data = $("#addModForm").serializeObject();

    // 开始 loading 遮盖
    $.loading.show("addConfLoading");

    $('#addMod').modal('hide');
    $.ajax({
        url: "/System/ajaxAddDbconf",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('addConfLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "addMod");
            }
        },
        error: function(data) {
            $.loading.hide('addConfLoading');
            $.zmsg.fatalShowModal(data.responseText, "addMod");
        }
    });
}

function submitEdtConf() {

    var data = $("#edtModForm").serializeObject();

    data.id = $('#edtModId').text();

    // 开始 loading 遮盖
    $.loading.show("edtConfLoading");

    $('#edtMod').modal('hide');
    $.ajax({
        url: "/System/ajaxEdtDbconf",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('edtConfLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.error(data.msg);
            }
        },
        error: function(data) {
            $.loading.hide('edtConfLoading');
            $.zmsg.fatalShowModal(data.responseText, "edtMod");
        }
    });
}

function submitDelConf() {

    var data = {};
    data.id = $('#delModId').text();

    // 开始 loading 遮盖
    $.loading.show("deleteConfLoading");

    $('#delMod').modal('hide');
    $.ajax({
        url: "/System/ajaxDelDbconf",
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

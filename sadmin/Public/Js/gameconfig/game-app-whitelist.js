
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
        url: "/Gameconf/ajaxAddWhiteList",
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
        url: "/Gameconf/ajaxDelWhiteList",
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

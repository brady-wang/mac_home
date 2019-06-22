
$(function() {
    'use strict';

    // query bar init
    if (query.black_type) {
        $('#blackType').children('[value=' + query.black_type + ']').prop('selected', true);
    }
    if (query.black_val) {
        $('#blackVal').val(query.black_val);
    }

    // delete modal 触发初始化
    $('#delMod').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        $('#delModBlackVal').text(button.data('blackval'));
        $('#delModId').val(button.data('id'));
    });
});

function submitAddBlackList() {

    var data = $("#addModForm").serializeObject();

    // 开始 loading 遮盖
    $.loading.show("addBlackListLoading");

    $('#addMod').modal('hide');

    $.ajax({
        url: "/Gameconf/ajaxAddBlackList",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('addBlackListLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "addMod");
            }
        },
        error: function(data) {
            $.loading.hide('addBlackListLoading');
            $.zmsg.fatalShowModal(data.responseText, "addMod");
        }
    });
}

function submitDelBlackList() {

    var data = {};

    data.id = $('#delModId').val();

    // 开始 loading 遮盖
    $.loading.show("deleteBlackListLoading");

    $('#delMod').modal('hide');
    $.ajax({
        url: "/Gameconf/ajaxDelBlackList",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('deleteBlackListLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.error(data.msg);
            }
        },
        error: function(data) {
            $.loading.hide('deleteBlackListLoading');
            $.zmsg.fatal(data.responseText);
        }
    });
}

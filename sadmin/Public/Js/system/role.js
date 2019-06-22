
$(function() {
    'use strict';

    if (query.username) {
        $('#username').val(query.username);
    }

    $('#delMod').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        $('#delModRoleName').text(button.data('role'));
        $('#delModRoleId').val(button.data('id'));
    });
});

function submitDelRole() {

    var data = {};
    data.id = $('#delModRoleId').val();

    // 开始 loading 遮盖
    $.loading.show("deleteRoleLoading");

    $('#delMod').modal('hide');
    $.ajax({
        url: "/System/ajaxDelRole",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('deleteRoleLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.error(data.msg);
            }
        },
        error: function(data) {
            $.loading.hide('deleteRoleLoading');
            $.zmsg.fatal(data.responseText);
        }
    });
}


$(function() {
    'use strict';

    // query bar init
    if (query.uid) {
        $('#uid').val(query.uid);
    }
    if (query.username) {
        $('#username').val(query.username);
    }
    if (query.role_id) {
        $('#roleId').children('[value=' + query.role_id + ']').prop('selected', true);
    }
    if (query.status) {
        $('#status').children('[value=' + query.status + ']').prop('selected', true);
    }

    // delete modal 触发初始化
    $('#delMod').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        $('#delModRoleUser').text(button.data('uid') + " " + button.data('username'));
        $('#delModUid').val(button.data('uid'));
    });

    // recover modal 触发初始化
    $('#recvMod').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        $('#recvModUid').text(button.data('uid'));
        $('#recvModUsername').text(button.data('username'));
    });
});

function submitAddUser() {

    var data = $("#addModForm").serializeObject();

    // 开始 loading 遮盖
    $.loading.show("addUserLoading");

    $('#addMod').modal('hide');
    $.ajax({
        url: "/System/ajaxAddUser",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('addUserLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "addMod");
            }
        },
        error: function(data) {
            $.loading.hide('addUserLoading');
            $.zmsg.fatalShowModal(data.responseText, "addMod");
        }
    });
}

function clickEdtUser(id) {

    var user = userList[id];

    $('#edtModUid').text(user.uid);
    $('#edtModRoleId').children("[value=" + user.role_id + "]").prop('selected', true);
    $('#edtModUsername').val(user.username);
    $('#edtModRealname').val(user.realname);

    $('#edtMod').modal();
}

function submitEdtUser() {

    var data = $("#edtModForm").serializeObject();

    data.uid = $('#edtModUid').text();

    // 开始 loading 遮盖
    $.loading.show("edtUserLoading");

    $('#edtMod').modal('hide');
    $.ajax({
        url: "/System/ajaxEdtUser",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('edtUserLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "edtMod");
            }
        },
        error: function(data) {
            $.loading.hide('edtUserLoading');
            $.zmsg.fatalShowModal(data.responseText, "edtMod");
        }
    });
}

function submitDelUser() {

    var data = {};
    data.uid = $('#delModUid').val();

    // 开始 loading 遮盖
    $.loading.show("deleteUserLoading");

    $('#delMod').modal('hide');
    $.ajax({
        url: "/System/ajaxDelUser",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('deleteUserLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.error(data.msg);
            }
        },
        error: function(data) {
            $.loading.hide('deleteUserLoading');
            $.zmsg.fatal(data.responseText);
        }
    });
}

function submitRecoverUser() {

    var data = {};
    data.uid = $('#recvModUid').text();
    data.role_id = $('#recvModRoleId').val();

    // 开始 loading 遮盖
    $.loading.show("recoverUserLoading");

    $('#recvMod').modal('hide');
    $.ajax({
        url: "/System/ajaxRecoverUser",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('recoverUserLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, 'recvMod');
            }
        },
        error: function(data) {
            $.loading.hide('recoverUserLoading');
            $.zmsg.fatalShowModal(data.responseText, 'recvMod');
        }
    });
}

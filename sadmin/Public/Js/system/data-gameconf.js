
$(function() {
    'use strict';

    // 列表内容处理
    for (var i in glist) {
        // 缩略内容
        var abbrCont = "游戏web：" + glist[i].api_ip + ":" + glist[i].api_port + "...";

        // 完成地址内容
        var detailCont = "\n游戏web：" + glist[i].api_ip + ":" + glist[i].api_port + "\n";
        detailCont += "活动服：" + glist[i].activity_api + ":" + glist[i].activity_api_port + "\n";
        detailCont += "战绩文件：" + glist[i].resource_ip + ":" + glist[i].resource_port;

        $('#tdShowSvr' + glist[i].id).append(
            $('<pre/>').addClass("pre-dis-border").append(
                $('<a/>').attr('href', "javascript:void(0)").append(
                    $('<span/>').addClass("glyphicon glyphicon-menu-down").attr("aria-hidden", true).html("&nbsp;")
                ).append(
                    $('<span/>').text(abbrCont)
                )
            ).on('click', function() {
                $(this).hide();
                $(this).next().slideDown();
            })
        ).append(
            $('<pre/>').addClass("pre-dis-border").append(
                $('<a/>').attr('href', "javascript:void(0)").append(
                    $('<span/>').addClass("glyphicon glyphicon-menu-up").attr("aria-hidden", true).html("&nbsp;")
                ).on('click', function() {
                    $(this).parent().slideUp();
                    $(this).parent().prev().show();
                })
            ).append(
                $('<span/>').text(detailCont)
            ).hide()
        );
    }

    // edit modal 触发初始化
    $('#edtMod').on('show.bs.modal', function (event) {
        var btn = $(event.relatedTarget);
        $('#edtModId').text(btn.data('id'));
        $('#edtModGameId').text(btn.data('game_id'));
        $('#edtModName').val(btn.data('game_name'));
        $('#edtModIosPackageName').val(btn.data('ios_package_name'));
        $('#edtModAndroidPackageName').val(btn.data('android_package_name'));
        $('#edtModApiIp').val(btn.data('api_ip'));
        $('#edtModApiPort').val(btn.data('api_port'));
        $('#edtModActivityApiIp').val(btn.data('activity_api'));
        $('#edtModActivityApiPort').val(btn.data('activity_api_port'));
        $('#edtModStatus').children('[value=' + btn.data('game_status') + ']').prop('selected', true);
        $('#edtModResourceIp').val(btn.data('resource_ip'));
        $('#edtModResourcePort').val(btn.data('resource_port'));
    });

    // delete modal 触发初始化
    $('#delMod').on('show.bs.modal', function (event) {
        var btn = $(event.relatedTarget);
        $('#delModId').text(btn.data('id'));
        $('#delModGameId').text(btn.data('game_id'));
        $('#delModName').text(btn.data('game_name'));
    });
});

function submitAddConf() {

    var data = $("#addModForm").serializeObject();

    // 开始 loading 遮盖
    $.loading.show("addConfLoading");

    $('#addMod').modal('hide');
    $.ajax({
        url: "/System/ajaxAddGameconf",
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
        url: "/System/ajaxEdtGameconf",
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
    data.game_id = $('#delModGameId').text();

    // 开始 loading 遮盖
    $.loading.show("deleteConfLoading");

    $('#delMod').modal('hide');
    $.ajax({
        url: "/System/ajaxDelGameconf",
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


$(function() {
    'use strict';

    // tip初始化
    $(".tiptip").tipTip({
        maxWidth: "auto",
        delay: 0
    });

    // 初始化 add edit modal 游戏列表
    initModGameList('add');
    initModGameList('edt');

    // 列表相关代码内容处理
    for (var i in gameList) {
        var firstCarriage = gameList[i].del_desc.indexOf("\n");
        if (firstCarriage > 0) {
            var curtailCont = gameList[i].del_desc.substr(0, firstCarriage);

            $('#tdDesc' + gameList[i].id).append(
                $('<pre/>').addClass("pre-dis-border").append(
                    $('<a/>').attr('href', "javascript:void(0)").append(
                        $('<span/>').addClass("glyphicon glyphicon-menu-down").attr("aria-hidden", true).html("&nbsp;")
                    ).append(
                        $('<span/>').text(curtailCont + "...")
                    )
                ).on('click', function() {
                    $(this).hide();
                    $(this).next().slideDown();
                })
            ).append(
                $('<pre/>').addClass("pre-dis-border").append(
                    $('<a/>').attr('href', "javascript:void(0)").append(
                        $('<span/>').addClass("glyphicon glyphicon-menu-up").attr("aria-hidden", true)
                    ).on('click', function() {
                        $(this).parent().slideUp();
                        $(this).parent().prev().show();
                    }).append(
                        $('<br/>')
                    )
                ).append(
                    $('<span/>').text(gameList[i].del_desc)
                ).hide()
            );
        } else {
            // 没有换行，直接输入内容，不做缩展功能
            $('#tdDesc' + gameList[i].id).append(
                $('<pre/>').addClass("pre-dis-border").text(gameList[i].del_desc)
            );
        }
    }

    // delete modal 触发初始化
    $('#delMod').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        $('#delModId').text(button.data('id'));
        $('#delModEditionKey').text(button.data('edition-key'));
    });
});

// 初始化 add edit modal 游戏列表
function initModGameList(type) {

    // 行游标，一行只允许排 rowMaxNum 个游戏，超过游标回退行首
    var rowCursor = 1;
    var rowMaxNum = 5;

    var groupElm = $('<div/>').addClass('form-group').append(
        $('<label/>').addClass('col-sm-2 control-label request').text('游戏开关')
    );

    var mainElm = $('<div/>').addClass('col-sm-10');

    for (var i in gameMap) {
        mainElm.append(
            $('<div/>').addClass('checkbox3 checkbox-inline checkbox-success checkbox-check checkbox-light').append(
                $('<input/>').attr('type', 'checkbox').attr('id', type + 'ModGame' + i).attr('name', 'game').val(i)
            ).append(
                $('<label/>').attr('for', type + 'ModGame' + i).text(gameMap[i])
            )
        );
        rowCursor++;

        if (rowMaxNum < rowCursor) {
            $('#' + type + 'ModGameFS').append(groupElm.append(mainElm));
            rowCursor = 1;
            var groupElm = $('<div/>').addClass('form-group').append();
            var mainElm = $('<div/>').addClass('col-sm-10 col-sm-offset-2');
        }
    }
    if (1 != rowCursor) {
        $('#' + type + 'ModGameFS').append(groupElm.append(mainElm));
    }
}

// 添加兼容项 submit
function submitAddEdition() {

    var data = $("#addModForm").serializeObject();

    // 开始 loading 遮盖
    $.loading.show("addLoading");

    $('#addMod').modal('hide');
    $.ajax({
        url: "/System/ajaxAddEdition",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('addLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "addMod");
            }
        },
        error: function(data) {
            $.loading.hide('addLoading');
            $.zmsg.fatalShowModal(data.responseText, "addMod");
        }
    });
}

// 修改兼容项 click
function clickEdtEdition(id) {

    var info = gameList[id];

    $('#edtModId').text(info.id);
    $('#edtModEditionKey').text(info.edition_key);
    $('#edtModEditionName').val(info.edition_name);
    $('#edtModDelDesc').val(info.del_desc);
    $('#edtModForm').find(':checkbox[name=game]').prop('checked', false);
    for (var i in info.game_list) {
        $('#edtModForm').find(':checkbox[name=game][value=' + info.game_list[i] + ']').prop('checked', true);
    }

    $('#edtMod').modal();
}

// 修改兼容项 submit
function submitEdtEdition() {

    var data = $("#edtModForm").serializeObject();

    data.id = $('#edtModId').text();

    // 开始 loading 遮盖
    $.loading.show("edtLoading");

    $('#edtMod').modal('hide');
    $.ajax({
        url: "/System/ajaxEdtEdition",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('edtLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "edtMod");
            }
        },
        error: function(data) {
            $.loading.hide('edtLoading');
            $.zmsg.fatalShowModal(data.responseText, "edtMod");
        }
    });
}

// 删除兼容项 submit
function submitDelEdition() {

    var data = {
        id: $('#delModId').text()
    };

    // 开始 loading 遮盖
    $.loading.show("delLoading");

    $('#delMod').modal('hide');
    $.ajax({
        url: "/System/ajaxDelEdition",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('delLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "delMod");
            }
        },
        error: function(data) {
            $.loading.hide('delLoading');
            $.zmsg.fatalShowModal(data.responseText, "delMod");
        }
    });
}

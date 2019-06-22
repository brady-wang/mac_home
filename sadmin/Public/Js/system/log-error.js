
$(function() {
    'use strict';

    // query bar 参数初始化：状态、开始日期、结束日期
    if (query.handle_status) {
        $('#handleStatus').children("[value=" + query.handle_status + "]").prop("selected", true);
    }
    if (query.start_date) {
        $('#startDate').val(query.start_date);
        var year = query.start_date.substr(0, 4);
        var month = query.start_date.substr(5, 2) - 1;
        var day = query.start_date.substr(8, 2);
        var initStartDate = new Date(year, month, day);
    } else {
        var initStartDate = null;
    }
    if (query.end_date) {
        $('#endDate').val(query.end_date);
        var year = query.end_date.substr(0, 4);
        var month = query.end_date.substr(5, 2) - 1;
        var day = query.end_date.substr(8, 2);
        var initEndDate = new Date(year, month, day);
    } else {
        var initEndDate = new Date();
    }

    // 开始日期、结束日期插件初始化
    $('#startDate').datepicker({
        todayBtn: "linked",
        autoclose: true,
        language: 'zh-CN',
        format: 'yyyy-mm-dd',
        endDate: initEndDate
    }).on('changeDate', function(e) {
        var startTime = e.date;
        $('#endDate').datepicker('setStartDate', startTime);
    });
    $('#endDate').datepicker({
        todayBtn: "linked",
        autoclose: true,
        language: 'zh-CN',
        format: 'yyyy-mm-dd',
        startDate: initStartDate,
        endDate: new Date()
    }).on('changeDate', function(e) {
        var endTime = e.date;
        $('#startDate').datepicker('setEndDate', endTime);
    });
});

// 查看详情
function clickExceptionInfo(id) {

    var screen_width = document.body.clientWidth;
    if (screen_width > 1280) {
        screen_width = 1280;
    }

    var screen_height = document.documentElement.clientHeight;
    if (screen_height > 890) {
        screen_height = 640;
    } else {
        screen_height -= 250;
    }

    $.Zebra_Dialog("", {
        animation_speed_show: 500,
        buttons: ['确定'],
        center_buttons: true,
        type: 'information',
        width: screen_width,
        source: {'iframe': {
            src: '/System/iframeGetErrorInfo/id/' + id,
            height: screen_height,
        }}
    });
}

// 错误处理 click
function clickDispose(obj) {

    var trElm = obj.parent().parent();
    var id = trElm.data("id");
    var position = trElm.children(":eq(0)").text();
    var log = trElm.children(":eq(1)").children().text();
    var count = trElm.data("count");
    var time = trElm.data("time");

    $('#dspModId').val(id);
    $('#dpsModPostition').text(position);
    $('#dpsModLog').text(log);
    $('#dpsModCount').text(count);
    $('#dspModTime').text($.tool.getDate(time));
    $('#dspModRemark').val("");

    $('#disposeModal').modal();
}

// 已处理错误 submit
function submitExceptionFix() {

    var data = {};

    data.id = $('#dspModId').val(); // id
    data.handle_remark = $('#dspModRemark').val(); // 处理备注
    data.handle_status = 2; // 处理状态：2 已处理

    // 开始 loading 遮盖
    $.loading.show("exceptionFixLoading");

    $('#disposeModal').modal('hide');
    $.ajax({
        url: "/System/ajaxEditException",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('exceptionFixLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "disposeModal");
            }
        },
        error: function(data) {
            $.loading.hide('exceptionFixLoading');
            $.zmsg.fatalShowModal(data.responseText, "disposeModal");
        }
    });
}

// 不处理错误 submit
function submitExceptionIgnore() {

    var data = {};

    data.id = $('#dspModId').val(); // id
    data.handle_remark = $('#dspModRemark').val(); // 处理备注
    data.handle_status = 3; // 处理状态：3 不处理

    // 开始 loading 遮盖
    $.loading.show("exceptionIgnoreLoading");

    $('#disposeModal').modal('hide');
    $.ajax({
        url: "/System/ajaxEditException",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('exceptionIgnoreLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "disposeModal");
            }
        },
        error: function(data) {
            $.loading.hide('exceptionIgnoreLoading');
            $.zmsg.fatalShowModal(data.responseText, "disposeModal");
        }
    });
}

// 批量处理 click
function clickBatch(obj) {

    var id = obj.parent().parent().data("id");

    // 开始 loading 遮盖
    $.loading.show("getBatchLoading");

    var data = {};
    data.id = id;
    $.ajax({
        url: "/System/ajaxGetBatchData",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('getBatchLoading');
            if (0 == data.code) {

                var excp = data.data;
                var info = "";
                for (var i in excp.info) {
                    console.log(excp, i);
                    info += excp.info[i].exce_log + "\n**********\n";
                }
                // 过滤掉最后一行星号
                info = info.substring(0, info.length - 12);

                // id
                $('#batModId').val(id);

                // 错误位置
                $('#batModPostition').text(excp.file + " " + excp.line);

                // 错误信息
                $('#batModLog').text(info);

                $('#batModal').modal();
            } else {
                $.zmsg.error(data.msg);
            }
        },
        error: function(data) {
            $.loading.hide('getBatchLoading');
            $.zmsg.fatal(data.responseText);
        }
    });
}

// 已处理批量 submit
function submitBatchFix() {

    var data = {};

    data.id = $('#batModId').val(); // id
    data.handle_remark = $('#batModRemark').val(); // 处理备注
    data.handle_status = 2; // 处理状态：2 已处理

    // 开始 loading 遮盖
    $.loading.show("batchFixLoading");

    $('#batModal').modal('hide');
    $.ajax({
        url: "/System/ajaxEditExceptionBatch",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('batchFixLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "batModal");
            }
        },
        error: function(data) {
            $.loading.hide('batchFixLoading');
            $.zmsg.fatalShowModal(data.responseText, "batModal");
        }
    });
}

// 不处理批量 submit
function submitBatchIgnore() {

    var data = {};

    data.id = $('#batModId').val(); // id
    data.handle_remark = $('#batModRemark').val(); // 处理备注
    data.handle_status = 3; // 处理状态：3 不处理

    // 开始 loading 遮盖
    $.loading.show("batchIgnoreLoading");

    $('#batModal').modal('hide');
    $.ajax({
        url: "/System/ajaxEditExceptionBatch",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('batchIgnoreLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "batModal");
            }
        },
        error: function(data) {
            $.loading.hide('batchIgnoreLoading');
            $.zmsg.fatalShowModal(data.responseText, "batModal");
        }
    });
}

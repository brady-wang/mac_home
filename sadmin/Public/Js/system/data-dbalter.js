
$(function() {
    'use strict';

    // query bar 参数初始化：表名、开始日期、结束日期、状态
    if (query.table_name) {
        $('#tableName').children("[value=" + query.table_name + "]").prop("selected", true);
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
    if (query.status) {
        $('#status').children("[value=" + query.status + "]").prop("selected", true);
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

// 内容展示
function clickContSpread(obj) {
    obj.hide();
    obj.next().slideDown();
}

// 内容收缩
function clickContConcentrate(obj) {
    obj.parent().slideUp(function() {
        obj.parent().prev().show();
    });
}

// 获取信息
function clickSqlInfo(id) {

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
            src: '/System/iframeGetSqlInfo/id/' + id,
            height: screen_height,
        }}
    });
    return true;
}

// 申请 submit
function submitApplySql() {

    $('#applyMod').modal('hide');

    var data = {};

    data.sql_statement = $("#applyModSql").val();
    data.sql_describe = $("#applyModReason").val();

    // 开始 loading 遮盖
    $.loading.show("applyLoading");

    $.ajax({
        url: "/System/doApplySqlStatement",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('applyLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "applyMod");
            }
        },
        error: function(data) {
            $.loading.hide('applyLoading');
            $.zmsg.fatalShowModal(data.responseText, "applyMod");
        }
    });
}

// 语句修改 click
function clickEdtSql(sqlId, stmId, obj) {

    $("#edtModId").text(sqlId);
    $("#edtModStmId").val(stmId);
    $("#edtModCont").val(obj.text());

    $("#edtMod").modal();
}

// 语句修改 submit
function submitEdtSql() {

    $('#edtMod').modal('hide');

    var data = {};
    data.sql_id = $('#edtModId').text();
    data.statement_id = $('#edtModStmId').val();
    data.sql_statement = $('#edtModCont').val();

    // 开始 loading 遮盖
    $.loading.show("edtLoading");

    $.ajax({
        url: "/System/doUpdateSqlStatement",
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

// 语句取消 click
function clickCancelSql(sqlId) {

    $("#cancelModId").text(sqlId);

    $("#cancelMod").modal();
}

// 语句取消 submit
function submitCancelSql() {

    $('#cancelMod').modal('hide');

    var data = {};
    data.id = $('#cancelModId').text();
    data.remark = $('#cancelModRemark').val();

    // 开始 loading 遮盖
    $.loading.show("cancelLoading");

    $.ajax({
        url: "/System/doCancelSqlStatement",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('cancelLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "cancelMod");
            }
        },
        error: function(data) {
            $.loading.hide('cancelLoading');
            $.zmsg.fatalShowModal(data.responseText, "cancelMod");
        }
    });
}

// 语句审核 click
function clickCheckSql(id) {

    var statement = "";
    var desc = sqlList[id].describe;

    for (var i in sqlList[id].statement) {
        statement += sqlList[id].statement[i].sql_statement + "\n\n";
    }

    $('#checkModId').text(id);
    $('#checkModDesc').text(desc);
    $('#checkModStatement').text(statement.substr(0, statement.length - 2));

    $("#checkMod").modal();
}

// 执行 sql submit
function submitExecuteSql() {

    $('#checkMod').modal('hide');

    var data = {};

    data.id = $('#checkModId').text();
    data.remark = $('#checkModRemark').val();

    // 开始 loading 遮盖
    $.loading.show("executeLoading");

    $.ajax({
        url: "/Home/System/doExecuteSqlStatement",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('executeLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorReload(data.msg);
            }
        },
        error: function(data) {
            $.loading.hide('executeLoading');
            $.zmsg.fatalShowModal(data.responseText, "checkMod");
        }
    });
}

// 驳回 sql submit
function submitRejectSql() {

    $('#checkMod').modal('hide');

    var data = {};

    data.id = $('#checkModId').text();
    data.remark = $('#checkModRemark').val();

    // 开始 loading 遮盖
    $.loading.show("rejectLoading");

    $.ajax({
        url: "/Home/System/doRejectSqlStatement",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('rejectLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorReload(data.msg);
            }
        },
        error: function(data) {
            $.loading.hide('rejectLoading');
            $.zmsg.fatalShowModal(data.responseText, "checkMod");
        }
    });
}

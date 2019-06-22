
$(function() {
    'use strict';

    // 开始日期、结束日期插件初始化
    $('#startDate').datepicker({
        todayBtn: "linked",
        autoclose: true,
        language: 'zh-CN',
        format: 'yyyy-mm-dd',
        endDate: new Date()
    }).on('changeDate', function(e) {
        var startTime = e.date;
        $('#endDate').datepicker('setStartDate', startTime);
    });
    $('#endDate').datepicker({
        todayBtn: "linked",
        autoclose: true,
        language: 'zh-CN',
        format: 'yyyy-mm-dd',
        endDate: new Date()
    }).on('changeDate', function(e) {
        var endTime = e.date;
        $('#startDate').datepicker('setEndDate', endTime);
    });

    // query bar 参数初始化
    if (query.api_type) {
        // 类型 option selected
        $('#apiType').children("[value=" + query.api_type + "]").prop("selected", true);

        // 刷新接口 select
        refreshApiCodeSelect();

        // 接口
        if (query.api_code) {
            $('#apiCode').children("[value=" + query.api_code + "]").prop("selected", true);
        }
    }
    if (query.request_key) {
        $('#requestKey').val(query.request_key);
    }
    if (query.start_date) {
        $('#startDate').val(query.start_date);
    }
    if (query.end_date) {
        $('#endDate').val(query.end_date);
    }
    if (query.response_key) {
        $('#responseKey').val(query.response_key);
    }

    // 类型修改回调
    $('#apiType').change(refreshApiCodeSelect);
});

// 根据类型刷新接口 select 框
function refreshApiCodeSelect() {

    let apiType = $('#apiType').val();

    $('#apiCode').empty().append(
        $('<option/>').val(0).text('全部')
    );

    if (apiType != 0) {
        var codeMap = typeCodeMap[apiType].code;
        for (var i in codeMap) {
            $('#apiCode').append(
                $('<option/>').val(i).text(codeMap[i])
            );
        }
    }

    return true;
}


$(function() {
    'use strict';

    // tip初始化
    $(".tiptip").tipTip({
        maxWidth: "auto",
        delay: 0
    });
    // query bar 参数初始化：开始月份
    if (query.select_month) {
        $('#select_month').val(query.select_month);
    }

    // 开始日期、结束日期插件初始化
    $('#select_month').datepicker({language:  'zh-CN',
        format: 'yyyy-mm',
        autoclose: true,
        startView: 'year',
        minViewMode: 'year',
        todayBtn: "linked",
    });

    // 报表导出
    $('#exportBtn').on('click', function() {
        var data = {
            'select_month': $("#select_month").val(),
        }
        $.postDownFile('/Stat/iframeStaticsCountDownFile', data);
    });
});


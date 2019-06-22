
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
        $.postDownFile('/Stat/iframeUserDailyDownFile', data);
    });
});

// 查看地区数据详情
function clickRegionInfo(pid, type) {

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
            src: '/Stat/iframeGetRegionInfo/pid/' + pid + '/type/' + type,
            height: screen_height,
        }}
    });
}

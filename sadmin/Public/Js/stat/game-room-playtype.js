
$(function() {
    'use strict';

    // tip
    $(".tiptip").tipTip({
        maxWidth: "auto",
        delay: 0
    });

    // 玩法选择框
    $('#playId').selectpicker({
        // 菜单下拉时右对齐
        dropdownAlignRight: true,
        // 在菜单顶部添加两个按钮：全选、全不选
        actionsBox: true,
        // 内容显示格式：当勾选项超过一项，显示已勾选的数量
        selectedTextFormat: 'count'
    });

    // query bar 参数初始化
    // 房间类型
    if (query.room_type) {
        $('#roomType').children('[value=' + query.room_type + ']').prop('selected', true);
    }
    // 玩法
    if (query.play_id) {
        for (var i in query.play_id) {
            $('#playId').children('[value=' + query.play_id[i] + ']').prop('selected', true);
        }
        $('#playId').selectpicker('refresh');
    } else {
        // 默认全选
        $('#playId').selectpicker('selectAll');
    }
    // 子玩法数据类型
    if (query.data_type) {
        $('#dataType').children('[value=' + query.data_type + ']').prop('selected', true);
    }
    // 开始日期
    if (query.start_date) {
        $('#startDate').val(query.start_date);
        var year = query.start_date.substr(0, 4);
        var month = query.start_date.substr(5, 2) - 1;
        var day = query.start_date.substr(8, 2);
        var initStartDate = new Date(year, month, day);
    } else {
        var initStartDate = null;
    }
    // 结束日期
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

    // 报表导出
    $('#exportBtn').on('click', function() {
        var data = {
            'data_type': $('#dataType').val(),
            'room_type': $('#roomType').val(),
            'start_date': $("#startDate").val(),
            'end_date': $("#endDate").val(),
        }
        if (!data.start_date || !data.end_date) {
            $.zmsg.error("必须选择开始日期和结束日期");
            return false;
        }
        $.postDownFile('/Stat/iframeGameRoomPlayDownFile', data);
    });

    // 折线图
    showChart(0);
    $('.dataButton').click(function(){
        var item = $(this).data("items");
        for (var i = 0; i < chartData.length; i++) {
            if (chartData[i].key == item) {
                showChart(i);
            }
        }
    });
});

function showChart(idx) {
    // 基于准备好的dom，初始化echarts实例
    var myChart = echarts.init(document.getElementById('divChart'));

    // 指定图表的配置项和数据
    var dftOption = {
        tooltip: {
            trigger: 'axis'
        },
        grid: {
            top: 20,
            bottom: 20
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: chartData[idx].xAxis
        },
        yAxis: {
           type: 'value'
        },
        series: [
            {
                name:chartData[idx].name,
                type:'line',
                smooth: true,
                data:chartData[idx].data
            }
        ]
    };
    myChart.setOption(dftOption);
    $(".dataButton").removeClass("btn-query");
    $("[data-items=" + chartData[idx].key + "]").addClass("btn-query");
}

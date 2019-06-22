
$(function() {
    'use strict';

    // tip
    $(".tiptip").tipTip({
        maxWidth: "auto",
        delay: 0
    });

    // query bar 参数初始化：开始日期、结束日期
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

//    // 报表导出
//    $('#exportBtn').on('click', function() {
//        var data = {
//            'start_date': $("#startDate").val(),
//            'end_date': $("#endDate").val(),
//        }
//        if (!data.start_date || !data.end_date) {
//            $.zmsg.error("必须选择开始日期和结束日期");
//            return false;
//        }
//        $.postDownFile('/Stat/iframeGameRoundDownFile', data);
//    });

    // 折线图
    showChart('award');
    $('.dataButton').click(function(){
        var item = $(this).data("items");
        for (var i in chartMap) {
            if (i == item) {
                showChart(item);
            }
        }
    });
});

function showChart(idx) {
    // 基于准备好的dom，初始化echarts实例
    var myChart = echarts.init(document.getElementById('divChart'));

    var chartSeries = [];
    for (var i in chartMap[idx].series) {
        var ser = {
            name: chartMap[idx].series[i].name,
            type: 'bar',
            barWidth: 10,
            stack: chartMap[idx].series[i].stack,
            data: chartMap[idx].series[i].data
        };
        chartSeries.push(ser);
    }

    var dftOption = {
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        legend: {
            data: chartMap[idx].legend
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: [
            {
                type : 'category',
                data : chartXAxis
            }
        ],
        yAxis: [
            {
                type: 'value'
            }
        ],
        series: chartSeries
    };

    myChart.clear();
    myChart.setOption(dftOption);
    $(".dataButton").removeClass("btn-query");
    $("[data-items=" + idx + "]").addClass("btn-query");
}

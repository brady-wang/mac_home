
$(document).ready(function() {
    // tip初始化
    $(".tiptip").tipTip({
        maxWidth: "auto",
        delay: 0
    });
    if (os) {
        $('#os').children('[value=' + os + ']').prop('selected', true);
    }
    if (type) {
        $('#type').children('[value=' + type + ']').prop('selected', true);
    }
    $("input[name=stime]").datepicker({
        language: "zh-CN",
        autoclose: true,
        clearBtn: true,
        endDate: new Date(),//new Date(new Date().valueOf() - 24 * 3600 * 1000),
        //todayBtn: true,
        format: "yyyy-mm-dd"
    });
    $("input[name=etime]").datepicker({
        language: "zh-CN",
        autoclose: true,
        clearBtn: true,
        endDate: new Date(),//new Date(new Date().valueOf() - 24 * 3600 * 1000),
        format: "yyyy-mm-dd"
    });
    
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

function checkSelDay() {
    var stime = $("#stime").val();
    var etime = $("#etime").val();
    if (stime) {
        stime = Date.parse(new Date(stime));
        stime = stime / 1000;
    }
    if (etime) {
        etime = Date.parse(new Date(etime));
        etime = etime / 1000;
    }
    if (stime && etime && stime > etime) {
        $.zmsg.error("开始时间不能大于结束时间");
        return false;
    }
    return true;
}

function onExport() {
    var stime = $("#stime").val();
    var etime = $("#etime").val();
    if (!stime || !etime) {
        $.zmsg.error("必须选择开始日期和结束日期");
        return false;
    }
}


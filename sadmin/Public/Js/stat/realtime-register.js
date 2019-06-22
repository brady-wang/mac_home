
$(document).ready(function(){
    // 基于准备好的dom，初始化echarts实例
    var myChart = echarts.init(document.getElementById('stat-line-chart'));

    // 指定图表的配置项和数据
    var dftOption = {
         title : {
            text: '注册人数实时统计图',
            subtext: '',
            x: 'center'
        },
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            selected: legendStatus,
            data: legendData,
            y : 30
        },
        grid: {
            top: 70,
            bottom: 50
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: xVal
        },
        yAxis: {
                type: 'value'
        },
        series: onlineData
    };

    myChart.setOption(dftOption); 
    $("#viewType").val(selView);
    if (selView) {
        $("#divDiff").css("display", "");
    }

    //点击事件
    $('.dateButton').click(function(){
        var checkDate = $(this).attr('data-times'),tempSelect={};
        $.each(dftOption.legend.selected , function(i, val) {  
            if(i == checkDate){
                tempSelect[i] = true;
            }else{
                tempSelect[i] = false ;
            }
        });
        dftOption.legend.selected = tempSelect ;
        myChart.setOption(dftOption);
        $(".dateButton").removeClass("btn-query");
        $("[data-times=" + checkDate + "]").addClass("btn-query");
    });
    
    // 日期选择框
    $('#select_days').selectpicker({
        // 菜单下拉时右对齐
        dropdownAlignRight: true,
        // 限制选择项
        maxOptions: 7,
        // 内容显示格式：当勾选项超过一项，显示已勾选的数量
        selectedTextFormat: 'count'
    });
        
    $("#viewType").change(function() {
        var val = $(this).val();
        if (val == 0) {
            location.href = "/Stat/realtimeRegister";
        } else {
            $("#divDiff").css("display", "");
        }
    });
    
    $("input[name=start]").datepicker({
        language: "zh-CN",
        autoclose: true,
        clearBtn: true,
        endDate: new Date(),//new Date(new Date().valueOf() - 24 * 3600 * 1000),
        //todayBtn: true,
        format: "yyyy-mm-dd"
    }).on('changeDate', function(){
        var val = $(this).val();
        refreshDay(val);
    });   
    
    refreshDay(startDay);
    // 选择日期
    if (selDay) {
        $('#select_days').children().prop('selected', false);
        for (var i = 0; i < selDay.length; i++) {
            $('#select_days').children('[value=' + selDay[i] + ']').prop('selected', true);
        }
        $('#select_days').selectpicker('refresh');
    }
});

// 默认选择最近7天
function refreshDay(day) {
    var tm = Date.parse(day);
    tm = tm / 1000;
    var count = 7;
    var selected = "";
    $('#select_days').empty();
    for (var i = 0; i < 60; i++) {
        if (count > 0) {
            selected = "selected";
            count--;
        } else {
            selected = "";
        }
        var txt = "<option value='" + $.tool.getDateYmd(tm) + "' " + selected + " >" + $.tool.getDateYmd(tm) + "</option>";
        $('#select_days').append(txt);
        tm -= 24 * 3600;
    }
    $('#select_days').selectpicker('refresh');
}

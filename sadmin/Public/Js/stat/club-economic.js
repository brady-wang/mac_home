
$(function() {
    'use strict';

    // tip初始化
    $(".tiptip").tipTip({
        maxWidth: "auto",
        delay: 0
    });

    // query bar init 查看方式
    if (query.query_type) {
        $('#queryType').children('[value=' + query.query_type + ']').prop('selected', true);

        // query bar init 时间区间：开始日期、结束日期
        if (2 == query.query_type) {
            queryTypeChangeEV();
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
        }
    }

    // 查看方式切换事件
    $('#queryType').on('change', queryTypeChangeEV);

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

    /****************************** 钻石产出消耗 ******************************/
    // 查阅图表
    $('#dmdChartBtn').on('click', function() {
        $('#dmdChartBtn').addClass('active-bg');
        $('#dmdTableBtn').removeClass('active-bg');
        $('#dmdTableFS').slideUp();
        $('#dmdChartFS').slideDown();
    });
    // 查阅表格
    $('#dmdTableBtn').on('click', function() {
        $('#dmdChartBtn').removeClass('active-bg');
        $('#dmdTableBtn').addClass('active-bg');
        $('#dmdChartFS').slideUp();
        $('#dmdTableFS').slideDown();
    });
    // 新增代理
    $('#dmdDownBtn').on('click', function() {
        var data = $('#queryForm').serializeObject();
        data.source = 'dmd';
        $.postDownFile('/Stat/iframeClubEconomicDownFile', data);
    });

    /****************************** 钻石结余 ******************************/
    // 查阅图表
    $('#rmnChartBtn').on('click', function() {
        $('#rmnChartBtn').addClass('active-bg');
        $('#rmnTableBtn').removeClass('active-bg');
        $('#rmnTableFS').slideUp();
        $('#rmnChartFS').slideDown();
    });
    // 查阅表格
    $('#rmnTableBtn').on('click', function() {
        $('#rmnChartBtn').removeClass('active-bg');
        $('#rmnTableBtn').addClass('active-bg');
        $('#rmnChartFS').slideUp();
        $('#rmnTableFS').slideDown();
    });
    // 新增代理
    $('#rmnDownBtn').on('click', function() {
        var data = $('#queryForm').serializeObject();
        data.source = 'rmn';
        $.postDownFile('/Stat/iframeClubEconomicDownFile', data);
    });

    /****************************** 异常用户 ******************************/
    // 查阅图表
    $('#abnChartBtn').on('click', function() {
        $('#abnChartBtn').addClass('active-bg');
        $('#abnTableBtn').removeClass('active-bg');
        $('#abnTableFS').slideUp();
        $('#abnChartFS').slideDown();
    });
    // 查阅表格
    $('#abnTableBtn').on('click', function() {
        $('#abnChartBtn').removeClass('active-bg');
        $('#abnTableBtn').addClass('active-bg');
        $('#abnChartFS').slideUp();
        $('#abnTableFS').slideDown();
    });
    // 新增代理
    $('#abnDownBtn').on('click', function() {
        var data = $('#queryForm').serializeObject();
        data.source = 'abn';
        $.postDownFile('/Stat/iframeClubEconomicDownFile', data);
    });

    // 复合数据图表初始化
    initCompData();
});

// 查看方式切换事件
function queryTypeChangeEV() {

    let qType = $('#queryType').val();

    if (1 == qType) {
        $('.type-interval').hide();
    } else if (2 == qType) {
        $('.type-interval').show();
    }
}

// 复合数据图表初始化
function initCompData() {

    // 钻石产出消耗
    let dmdDom = document.getElementById("dmdChartCont");
    let dmdChart = echarts.init(dmdDom);
    let dmdOption = {
        // 提示框组件
        tooltip: {
            // 触发类型：axis 坐标轴触发，主要在柱状图，折线图等会使用类目轴的图表中使用。
            trigger: 'axis'
        },
        // 图例组件
        legend: {
            // 图例的数据数组。数组项通常为一个字符串，每一项代表一个系列的 name。
            data: ['当日钻石产出','当日钻石消耗','活跃代理钻石结余']
        },
        // 直角坐标系 grid 中的 x 轴
        xAxis: [
            {
                // 坐标轴类型。category 类目轴，适用于离散的类目数据，为该类型时必须通过 data 设置类目数据。
                type: 'category',
                // 类目数据，在类目轴（type: 'category'）中有效。
                data: comp.chart.category
            }
        ],
        // 直角坐标系 grid 中的 y 轴
        yAxis: [
            {
                // 坐标轴类型。value 数值轴，适用于连续数据。
                type: 'value'
            }
        ],
        // 系列列表。每个系列通过 type 决定自己的图表类
        series: [
            {
                // 系列名称，用于tooltip的显示，legend 的图例筛选，在 setOption 更新数据和配置项对用于指定对应的系列
                name: '当日钻石产出',
                // 线图
                type: 'line',
                // 系列中的数据内容数组
                data: comp.chart.diamondProduce
            },
            {
                name: '当日钻石消耗',
                type: 'line',
                data: comp.chart.diamondConsume
            },
            {
                name: '活跃代理钻石结余',
                type: 'line',
                data: comp.chart.agentRemain
            }
        ]
    };
    dmdChart.setOption(dmdOption, true);

    // 钻石结余
    let rmnDom = document.getElementById("rmnChartCont");
    let rmnChart = echarts.init(rmnDom);
    let rmnOption = {
        // 提示框组件
        tooltip: {
            // 触发类型：axis 坐标轴触发，主要在柱状图，折线图等会使用类目轴的图表中使用。
            trigger: 'axis'
        },
        // 图例组件
        legend: {
            // 图例的数据数组。数组项通常为一个字符串，每一项代表一个系列的 name。
            data: ['活跃代理发放','活跃代理钻石结余','活跃用户发放','用户结余']
        },
        // 直角坐标系 grid 中的 x 轴
        xAxis: [
            {
                // 坐标轴类型。category 类目轴，适用于离散的类目数据，为该类型时必须通过 data 设置类目数据。
                type: 'category',
                // 类目数据，在类目轴（type: 'category'）中有效。
                data: comp.chart.category
            }
        ],
        // 直角坐标系 grid 中的 y 轴
        yAxis: [
            {
                // 坐标轴类型。value 数值轴，适用于连续数据。
                type: 'value'
            }
        ],
        // 系列列表。每个系列通过 type 决定自己的图表类
        series: [
            {
                // 系列名称，用于tooltip的显示，legend 的图例筛选，在 setOption 更新数据和配置项对用于指定对应的系列
                name: '活跃代理发放',
                // 线图
                type: 'line',
                // 系列中的数据内容数组
                data: comp.chart.agentProduce
            },
            {
                name: '活跃代理钻石结余',
                type: 'line',
                data: comp.chart.agentRemain
            },
            {
                name: '活跃用户发放',
                type: 'line',
                data: comp.chart.gameProduce
            },
            {
                name: '用户结余',
                type: 'line',
                data: comp.chart.gameRemain
            }
        ]
    };
    rmnChart.setOption(rmnOption, true);

    // 异常用户
    let abnDom = document.getElementById("abnChartCont");
    let abnChart = echarts.init(abnDom);
    let abnOption = {
        // 提示框组件
        tooltip: {
            // 触发类型：axis 坐标轴触发，主要在柱状图，折线图等会使用类目轴的图表中使用。
            trigger: 'axis',
            // 坐标轴指示器，坐标轴触发有效
            axisPointer : {
                // 指示器类型。'shadow' 阴影指示器
                type : 'shadow'
            }
        },
        // 图例组件
        legend: {
            // 图例的数据数组。数组项通常为一个字符串，每一项代表一个系列的 name。
            data: ['一次性发放超过300','亲友圈转移钻石代理数']
        },
        // 直角坐标系内绘图网格，单个 grid 内最多可以放置上下两个 X 轴，左右两个 Y 轴。
        grid: {
            // grid 组件离容器左侧的距离
            left: '3%',
            // grid 组件离容器右侧的距离
            right: '4%',
            // grid 组件离容器底侧的距离
            bottom: '3%',
            // grid 区域是否包含坐标轴的刻度标签
            containLabel: true
        },
        // 直角坐标系 grid 中的 x 轴
        xAxis: [
            {
                // 坐标轴类型。category 类目轴，适用于离散的类目数据，为该类型时必须通过 data 设置类目数据。
                type: 'category',
                // 类目数据，在类目轴（type: 'category'）中有效。
                data: comp.chart.category
            }
        ],
        // 直角坐标系 grid 中的 y 轴
        yAxis: [
            {
                // 坐标轴类型。value 数值轴，适用于连续数据。
                type: 'value'
            }
        ],
        // 系列列表。每个系列通过 type 决定自己的图表类
        series: [
            {
                // 系列名称，用于tooltip的显示，legend 的图例筛选，在 setOption 更新数据和配置项对用于指定对应的系列
                name: '一次性发放超过300',
                // 柱状图
                type: 'bar',
                // 数据堆叠，同个类目轴上系列配置相同的stack值可以堆叠放置
                stack: '总量',
                // 图形上的文本标签
                label: {
                    normal: {
                        // 是否显示标签
                        show: true,
                        // 标签的位置
                        position: 'insideRight'
                    }
                },
                // 系列中的数据内容数组
                data: comp.chart.abnormalGet
            },
            {
                name: '亲友圈转移钻石代理数',
                type: 'bar',
                stack: '总量',
                label: {
                    normal: {
                        show: true,
                        position: 'insideRight'
                    }
                },
                data: comp.chart.abnormalTransfer
            }
        ]
    };
    abnChart.setOption(abnOption, true);
}


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

    /****************************** 收入趋势 ******************************/
    // 查阅图表
    $('#amtChartBtn').on('click', function() {
        $('#amtChartBtn').addClass('active-bg');
        $('#amtTableBtn').removeClass('active-bg');
        $('#amtTableFS').slideUp();
        $('#amtChartFS').slideDown();
    });
    // 查阅表格
    $('#amtTableBtn').on('click', function() {
        $('#amtChartBtn').removeClass('active-bg');
        $('#amtTableBtn').addClass('active-bg');
        $('#amtChartFS').slideUp();
        $('#amtTableFS').slideDown();
    });
    // 新增代理
    $('#amtDownBtn').on('click', function() {
        var data = $('#queryForm').serializeObject();
        data.source = 'amt';
        $.postDownFile('/Stat/iframeClubIncomeDownFile', data);
    });

    /****************************** 付费人数 ******************************/
    // 查阅图表
    $('#numChartBtn').on('click', function() {
        $('#numChartBtn').addClass('active-bg');
        $('#numTableBtn').removeClass('active-bg');
        $('#numTableFS').slideUp();
        $('#numChartFS').slideDown();
    });
    // 查阅表格
    $('#numTableBtn').on('click', function() {
        $('#numChartBtn').removeClass('active-bg');
        $('#numTableBtn').addClass('active-bg');
        $('#numChartFS').slideUp();
        $('#numTableFS').slideDown();
    });
    // 新增代理
    $('#numDownBtn').on('click', function() {
        var data = $('#queryForm').serializeObject();
        data.source = 'num';
        $.postDownFile('/Stat/iframeClubIncomeDownFile', data);
    });

    /****************************** 新增付费（付费额度） ******************************/
    // 查阅图表
    $('#namChartBtn').on('click', function() {
        $('#namChartBtn').addClass('active-bg');
        $('#namTableBtn').removeClass('active-bg');
        $('#namTableFS').slideUp();
        $('#namChartFS').slideDown();
    });
    // 查阅表格
    $('#namTableBtn').on('click', function() {
        $('#namChartBtn').removeClass('active-bg');
        $('#namTableBtn').addClass('active-bg');
        $('#namChartFS').slideUp();
        $('#namTableFS').slideDown();
    });
    // 新增代理
    $('#namDownBtn').on('click', function() {
        var data = $('#queryForm').serializeObject();
        data.source = 'nam';
        console.log(data);
        $.postDownFile('/Stat/iframeClubIncomeDownFile', data);
    });

    /****************************** 新增付费（付费人数） ******************************/
    // 查阅图表
    $('#nnmChartBtn').on('click', function() {
        $('#nnmChartBtn').addClass('active-bg');
        $('#nnmTableBtn').removeClass('active-bg');
        $('#nnmTableFS').slideUp();
        $('#nnmChartFS').slideDown();
    });
    // 查阅表格
    $('#nnmTableBtn').on('click', function() {
        $('#nnmChartBtn').removeClass('active-bg');
        $('#nnmTableBtn').addClass('active-bg');
        $('#nnmChartFS').slideUp();
        $('#nnmTableFS').slideDown();
    });
    // 新增代理
    $('#nnmDownBtn').on('click', function() {
        var data = $('#queryForm').serializeObject();
        data.source = 'nnm';
        console.log(data);
        $.postDownFile('/Stat/iframeClubIncomeDownFile', data);
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

    // 收入趋势
    let amtDom = document.getElementById("amtChartCont");
    let amtChart = echarts.init(amtDom);
    let amtOption = {
        // 提示框组件
        tooltip: {
            // 触发类型：axis 坐标轴触发，主要在柱状图，折线图等会使用类目轴的图表中使用。
            trigger: 'axis'
        },
        // 图例组件
        legend: {
            // 图例的数据数组。数组项通常为一个字符串，每一项代表一个系列的 name。
            data: ['付费金额']
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
                name: '付费金额',
                // 线图
                type: 'line',
                // 系列中的数据内容数组
                data: comp.chart.incomeAmount
            }
        ]
    };
    amtChart.setOption(amtOption, true);

    // 付费人数
    let numDom = document.getElementById("numChartCont");
    let numChart = echarts.init(numDom);
    let numOption = {
        // 提示框组件
        tooltip: {
            // 触发类型：axis 坐标轴触发，主要在柱状图，折线图等会使用类目轴的图表中使用。
            trigger: 'axis'
        },
        // 图例组件
        legend: {
            // 图例的数据数组。数组项通常为一个字符串，每一项代表一个系列的 name。
            data: ['付费人数']
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
                name: '付费人数',
                // 线图
                type: 'line',
                // 系列中的数据内容数组
                data: comp.chart.payNum
            }
        ]
    };
    numChart.setOption(numOption, true);

    // 新增付费（付费额度）
    let namDom = document.getElementById("namChartCont");
    let namChart = echarts.init(namDom);
    let namOption = {
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
            data: ['当日新增付费','1-3日新增付费','4-7日新增付费','7日以上新增付费']
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
                name: '当日新增付费',
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
                data: comp.chart.payTypeOneAmount
            },
            {
                name: '1-3日新增付费',
                type: 'bar',
                stack: '总量',
                label: {
                    normal: {
                        show: true,
                        position: 'insideRight'
                    }
                },
                data: comp.chart.payTypeTwoAmount
            },
            {
                name: '4-7日新增付费',
                type: 'bar',
                stack: '总量',
                label: {
                    normal: {
                        show: true,
                        position: 'insideRight'
                    }
                },
                data: comp.chart.payTypeThreeAmount
            },
            {
                name: '7日以上新增付费',
                type: 'bar',
                stack: '总量',
                label: {
                    normal: {
                        show: true,
                        position: 'insideRight'
                    }
                },
                data: comp.chart.payTypeFourAmount
            }
        ]
    };
    namChart.setOption(namOption, true);

    // 新增付费（付费人数）
    let nnmDom = document.getElementById("nnmChartCont");
    let nnmChart = echarts.init(nnmDom);
    let nnmOption = {
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
            data: ['当日新增付费','1-3日新增付费','4-7日新增付费','7日以上新增付费']
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
                name: '当日新增付费',
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
                data: comp.chart.payTypeOneNum
            },
            {
                name: '1-3日新增付费',
                type: 'bar',
                stack: '总量',
                label: {
                    normal: {
                        show: true,
                        position: 'insideRight'
                    }
                },
                data: comp.chart.payTypeTwoNum
            },
            {
                name: '4-7日新增付费',
                type: 'bar',
                stack: '总量',
                label: {
                    normal: {
                        show: true,
                        position: 'insideRight'
                    }
                },
                data: comp.chart.payTypeThreeNum
            },
            {
                name: '7日以上新增付费',
                type: 'bar',
                stack: '总量',
                label: {
                    normal: {
                        show: true,
                        position: 'insideRight'
                    }
                },
                data: comp.chart.payTypeFourNum
            }
        ]
    };
    nnmChart.setOption(nnmOption, true);
}

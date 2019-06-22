
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

    /****************************** 新增代理 ******************************/
    // 查阅图表
    $('#incChartBtn').on('click', function() {
        $('#incChartBtn').addClass('active-bg');
        $('#incTableBtn').removeClass('active-bg');
        $('#incTableFS').slideUp();
        $('#incChartFS').slideDown();
    });
    // 查阅表格
    $('#incTableBtn').on('click', function() {
        $('#incChartBtn').removeClass('active-bg');
        $('#incTableBtn').addClass('active-bg');
        $('#incChartFS').slideUp();
        $('#incTableFS').slideDown();
    });
    // 新增代理
    $('#incDownBtn').on('click', function() {
        var data = $('#queryForm').serializeObject();
        data.source = 'inc';
        $.postDownFile('/Stat/iframeClubPromoterDownFile', data);
    });

    /****************************** 有效代理数据 ******************************/
    // 查阅图表
    $('#effChartBtn').on('click', function() {
        $('#effChartBtn').addClass('active-bg');
        $('#effTableBtn').removeClass('active-bg');
        $('#effTableFS').slideUp();
        $('#effChartFS').slideDown();
    });
    // 查阅表格
    $('#effTableBtn').on('click', function() {
        $('#effChartBtn').removeClass('active-bg');
        $('#effTableBtn').addClass('active-bg');
        $('#effChartFS').slideUp();
        $('#effTableFS').slideDown();
    });
    // 新增代理
    $('#effDownBtn').on('click', function() {
        var data = $('#queryForm').serializeObject();
        data.source = 'eff';
        $.postDownFile('/Stat/iframeClubPromoterDownFile', data);
    });

    /****************************** 亲友圈代理分析 ******************************/
    // 查阅图表
    $('#clbChartBtn').on('click', function() {
        $('#clbChartBtn').addClass('active-bg');
        $('#clbTableBtn').removeClass('active-bg');
        $('#clbTableFS').slideUp();
        $('#clbChartFS').slideDown();
    });
    // 查阅表格
    $('#clbTableBtn').on('click', function() {
        $('#clbChartBtn').removeClass('active-bg');
        $('#clbTableBtn').addClass('active-bg');
        $('#clbChartFS').slideUp();
        $('#clbTableFS').slideDown();
    });
    // 新增代理
    $('#clbDownBtn').on('click', function() {
        var data = $('#queryForm').serializeObject();
        data.source = 'clb';
        $.postDownFile('/Stat/iframeClubPromoterDownFile', data);
    });

    /****************************** 散户代理分析 ******************************/
    // 查阅图表
    $('#rtlChartBtn').on('click', function() {
        $('#rtlChartBtn').addClass('active-bg');
        $('#rtlTableBtn').removeClass('active-bg');
        $('#rtlTableFS').slideUp();
        $('#rtlChartFS').slideDown();
    });
    // 查阅表格
    $('#rtlTableBtn').on('click', function() {
        $('#rtlChartBtn').removeClass('active-bg');
        $('#rtlTableBtn').addClass('active-bg');
        $('#rtlChartFS').slideUp();
        $('#rtlTableFS').slideDown();
    });
    // 新增代理
    $('#rtlDownBtn').on('click', function() {
        var data = $('#queryForm').serializeObject();
        data.source = 'rtl';
        $.postDownFile('/Stat/iframeClubPromoterDownFile', data);
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
    // 新增代理
    let incDom = document.getElementById("incChartCont");
    let incChart = echarts.init(incDom);
    let incOption = {
        // 提示框组件
        tooltip: {
            // 触发类型：axis 坐标轴触发，主要在柱状图，折线图等会使用类目轴的图表中使用。
            trigger: 'axis'
        },
        // 图例组件
        legend: {
            // 图例的数据数组。数组项通常为一个字符串，每一项代表一个系列的 name。
            data: ['新开通代理','新转正代理']
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
                name: '新开通代理',
                // 柱状图
                type: 'bar',
                // 系列中的数据内容数组
                data: comp.chart.promoterCount
            },
            {
                name: '新转正代理',
                type: 'bar',
                data: comp.chart.transferCount
            }
        ]
    };
    incChart.setOption(incOption, true);

    // 有效代理数据
    let effDom = document.getElementById("effChartCont");
    let effChart = echarts.init(effDom);
    let effOption = {
        // 提示框组件
        tooltip: {
            // 触发类型：axis 坐标轴触发，主要在柱状图，折线图等会使用类目轴的图表中使用。
            trigger: 'axis',
            // 坐标轴指示器配置项。
            axisPointer: {
                // cross 十字准星指示器。其实是种简写，表示启用两个正交的轴的 axisPointer。
                type: 'cross',
                // axisPointer.type 为 'cross' 时有效。
                crossStyle: {
                    // 线的颜色。
                    color: '#999'
                }
            }
        },
        // 图例组件
        legend: {
            // 图例的数据数组。数组项通常为一个字符串，每一项代表一个系列的 name。
            data: ['累计转正','当日活跃','当日充值总额']
        },
        // 直角坐标系 grid 中的 x 轴
        xAxis: [
            {
                // 坐标轴类型。category 类目轴，适用于离散的类目数据，为该类型时必须通过 data 设置类目数据。
                type: 'category',
                // 类目数据，在类目轴（type: 'category'）中有效。
                data: comp.chart.category,
                axisPointer: {
                    // shadow 阴影指示器
                    type: 'shadow'
                }
            }
        ],
        // 直角坐标系 grid 中的 y 轴
        yAxis: [
            // 双 y 轴
            {
                // value 数值轴，适用于连续数据。
                type: 'value',
                // 坐标轴名称。
                name: '人数'
            },
            {
                // value 数值轴，适用于连续数据。
                type: 'value',
                // 坐标轴名称。
                name: '金额',
                // 坐标轴在 grid 区域中的分隔线。
                splitLine: {
                    // 双 y 轴一起显示分隔线会导致页面杂乱，隐藏其中一条
                    show: false
                },
                // 坐标轴刻度标签的相关设置。
                axisLabel: {
                    // 刻度标签的内容格式器，支持字符串模板和回调函数两种形式。
                    formatter: '{value} 元'
                }
            }
        ],
        // 系列列表。每个系列通过 type 决定自己的图表类
        series: [
            {
                // 系列名称，用于tooltip的显示，legend 的图例筛选，在 setOption 更新数据和配置项对用于指定对应的系列
                name: '累计转正',
                // 柱状图
                type: 'bar',
                // 系列中的数据内容数组
                data: comp.chart.effectiveTransfer
            },
            {
                name: '当日活跃',
                type: 'bar',
                data: comp.chart.effectiveActive
            },
            {
                name: '当日充值总额',
                type: 'line',
                yAxisIndex: 1,
                data: comp.chart.effectiveRecharge
            }
        ]
    };
    effChart.setOption(effOption, true);

    // 亲友圈代理分析
    let clbDom = document.getElementById("clbChartCont");
    let clbChart = echarts.init(clbDom);
    let clbOption = {
        // 提示框组件
        tooltip: {
            // 触发类型：axis 坐标轴触发，主要在柱状图，折线图等会使用类目轴的图表中使用。
            trigger: 'axis',
            // 坐标轴指示器配置项。
            axisPointer: {
                // cross 十字准星指示器。其实是种简写，表示启用两个正交的轴的 axisPointer。
                type: 'cross',
                // axisPointer.type 为 'cross' 时有效。
                crossStyle: {
                    // 线的颜色。
                    color: '#999'
                }
            }
        },
        // 图例组件
        legend: {
            // 图例的数据数组。数组项通常为一个字符串，每一项代表一个系列的 name。
            data: ['累计转正','当日活跃','当日充值总额']
        },
        // 直角坐标系 grid 中的 x 轴
        xAxis: [
            {
                // 坐标轴类型。category 类目轴，适用于离散的类目数据，为该类型时必须通过 data 设置类目数据。
                type: 'category',
                // 类目数据，在类目轴（type: 'category'）中有效。
                data: comp.chart.category,
                axisPointer: {
                    // shadow 阴影指示器
                    type: 'shadow'
                }
            }
        ],
        // 直角坐标系 grid 中的 y 轴
        yAxis: [
            // 双 y 轴
            {
                // value 数值轴，适用于连续数据。
                type: 'value',
                // 坐标轴名称。
                name: '人数'
            },
            {
                // value 数值轴，适用于连续数据。
                type: 'value',
                // 坐标轴名称。
                name: '金额',
                // 坐标轴在 grid 区域中的分隔线。
                splitLine: {
                    // 双 y 轴一起显示分隔线会导致页面杂乱，隐藏其中一条
                    show: false
                },
                // 坐标轴刻度标签的相关设置。
                axisLabel: {
                    // 刻度标签的内容格式器，支持字符串模板和回调函数两种形式。
                    formatter: '{value} 元'
                }
            }
        ],
        // 系列列表。每个系列通过 type 决定自己的图表类
        series: [
            {
                // 系列名称，用于tooltip的显示，legend 的图例筛选，在 setOption 更新数据和配置项对用于指定对应的系列
                name: '累计转正',
                // 柱状图
                type: 'bar',
                // 系列中的数据内容数组
                data: comp.chart.clubTransfer
            },
            {
                name: '当日活跃',
                type: 'bar',
                data: comp.chart.clubActive
            },
            {
                name: '当日充值总额',
                type: 'line',
                yAxisIndex: 1,
                data: comp.chart.clubRecharge
            }
        ]
    };
    clbChart.setOption(clbOption, true);

    // 散户代理分析
    let rtlDom = document.getElementById("rtlChartCont");
    let rtlChart = echarts.init(rtlDom);
    let rtlOption = {
        // 提示框组件
        tooltip: {
            // 触发类型：axis 坐标轴触发，主要在柱状图，折线图等会使用类目轴的图表中使用。
            trigger: 'axis',
            // 坐标轴指示器配置项。
            axisPointer: {
                // cross 十字准星指示器。其实是种简写，表示启用两个正交的轴的 axisPointer。
                type: 'cross',
                // axisPointer.type 为 'cross' 时有效。
                crossStyle: {
                    // 线的颜色。
                    color: '#999'
                }
            }
        },
        // 图例组件
        legend: {
            // 图例的数据数组。数组项通常为一个字符串，每一项代表一个系列的 name。
            data: ['累计转正','当日活跃','当日充值总额']
        },
        // 直角坐标系 grid 中的 x 轴
        xAxis: [
            {
                // 坐标轴类型。category 类目轴，适用于离散的类目数据，为该类型时必须通过 data 设置类目数据。
                type: 'category',
                // 类目数据，在类目轴（type: 'category'）中有效。
                data: comp.chart.category,
                axisPointer: {
                    // shadow 阴影指示器
                    type: 'shadow'
                }
            }
        ],
        // 直角坐标系 grid 中的 y 轴
        yAxis: [
            // 双 y 轴
            {
                // value 数值轴，适用于连续数据。
                type: 'value',
                // 坐标轴名称。
                name: '人数'
            },
            {
                // value 数值轴，适用于连续数据。
                type: 'value',
                // 坐标轴名称。
                name: '金额',
                // 坐标轴在 grid 区域中的分隔线。
                splitLine: {
                    // 双 y 轴一起显示分隔线会导致页面杂乱，隐藏其中一条
                    show: false
                },
                // 坐标轴刻度标签的相关设置。
                axisLabel: {
                    // 刻度标签的内容格式器，支持字符串模板和回调函数两种形式。
                    formatter: '{value} 元'
                }
            }
        ],
        // 系列列表。每个系列通过 type 决定自己的图表类
        series: [
            {
                // 系列名称，用于tooltip的显示，legend 的图例筛选，在 setOption 更新数据和配置项对用于指定对应的系列
                name: '累计转正',
                // 柱状图
                type: 'bar',
                // 系列中的数据内容数组
                data: comp.chart.retailTransfer
            },
            {
                name: '当日活跃',
                type: 'bar',
                data: comp.chart.retailActive
            },
            {
                name: '当日充值总额',
                type: 'line',
                yAxisIndex: 1,
                data: comp.chart.retailRecharge
            }
        ]
    };
    rtlChart.setOption(rtlOption, true);
}

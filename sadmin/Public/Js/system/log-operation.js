
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

    // query bar 参数初始化：用户 id、姓名、开始时间、结束时间
    if (query.uid) {
        $('#uid').val(query.uid);
    }
    if (query.username) {
        $('#username').val(query.username);
    }
    if (query.start_date) {
        $('#startDate').val(query.start_date);
    }
    if (query.end_date) {
        $('#endDate').val(query.end_date);
    }
    if (query.cont_key) {
        $('#contKey').val(query.cont_key);
    }

    // 游戏
    if (query.game_id) {
        $('#gameId').children("[value=" + query.game_id + "]").prop("selected", true);
    }

    // query bar 参数初始化：一级、二级、三级权限
    if (query.main_code) {
        // 一级权限 option selected
        $('#mainCode').children("[value=" + query.main_code + "]").prop("selected", true);

        // 刷新二级权限 select
        refreshSublevelSelect();

        // 只有拥有一级权限参数才去初始化二级权限
        if (query.sublevel_code) {
            // 二级权限 option selected
            $('#sublevelCode').children("[value=" + query.sublevel_code + "]").prop("selected", true);

            // 刷新三级权限 select
            refreshThirdSelect();

            // 只有拥有二级权限参数才去初始化三级权限
            if (query.third_code) {
                $('#thirdCode').children("[value=" + query.third_code + "]").prop("selected", true);
            }
        }
    }

    // 一级、二级权限修改回调
    $('#mainCode').change(refreshSublevelSelect);
    $('#sublevelCode').change(refreshThirdSelect);

    // 列表内容处理
    for (var i in loglist) {
        var firstCarriage = loglist[i].show_cont.indexOf("\n");
        if (firstCarriage > 0) {
            var curtailCont = loglist[i].show_cont.substr(0, firstCarriage);

            $('#tdShowCont' + loglist[i].id).append(
                $('<pre/>').addClass("pre-dis-border").append(
                    $('<a/>').attr('href', "javascript:void(0)").append(
                        $('<span/>').addClass("glyphicon glyphicon-menu-down").attr("aria-hidden", true).html("&nbsp;")
                    ).append(
                        $('<span/>').text(curtailCont + "...")
                    )
                ).on('click', function() {
                    $(this).hide();
                    $(this).next().slideDown();
                })
            ).append(
                $('<pre/>').addClass("pre-dis-border").append(
                    $('<a/>').attr('href', "javascript:void(0)").append(
                        $('<span/>').addClass("glyphicon glyphicon-menu-up").attr("aria-hidden", true).html("&nbsp;")
                    ).on('click', function() {
                        $(this).parent().slideUp();
                        $(this).parent().prev().show();
                    })
                ).append(
                    $('<span/>').text(loglist[i].show_cont)
                ).hide()
            );
        } else {
            // 没有换行，直接输入内容，不做缩展功能
            $('#tdShowCont' + loglist[i].id).append(
                $('<pre/>').addClass("pre-dis-border").text(loglist[i].show_cont)
            );
        }
    }
});

// 根据 main code 刷新 sublevel code select 框
function refreshSublevelSelect() {

    var mainCode = $('#mainCode').val();

    $('#sublevelDiv').hide();
    $('#sublevelCode').children().remove();
    $('#thirdDiv').hide();
    $('#thirdCode').children().remove();

    var sublevel = authMap[mainCode].sublevel;
    if (sublevel != null) {
        for (var i in sublevel) {
            $('#sublevelCode').append(
                $('<option/>').val(i).text(sublevel[i].name)
            );
        }
        $('#sublevelDiv').show();
    }

    return true;
}

// 根据 sublevel code 刷新 third code select 框
function refreshThirdSelect() {

    var mainCode = $('#mainCode').val();
    var sublevelCode = $('#sublevelCode').val();

    $('#thirdDiv').hide();
    $('#thirdCode').children().remove();

    var third = authMap[mainCode].sublevel[sublevelCode].third;
    if (third != null) {
        for (var i in third) {
            $('#thirdCode').append(
                $('<option/>').val(i).text(third[i])
            );
        }
        $('#thirdDiv').show();
    }

    return true;
}

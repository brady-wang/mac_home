
$(document).ready(function() {
    // tip初始化
    $(".tiptip").tipTip({
        maxWidth: "auto",
        delay: 0
    });
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
    showRankDay();
});

function onExport() {
    var stime = $("#stime").val();
    var etime = $("#etime").val();
    if (!stime || !etime) {
        $.zmsg.error("必须选择开始日期和结束日期");
        return false;
    }
}

function onWinResize() {
    if (!resizeFlag) {
        resizeFlag = true;
        setTimeout(subResize, 100);
    }
}
function subResize() {
    resizeFlag = false;
}
function checkInterDay() {
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
    if (stime == "" || etime == "" || stime > etime) {
        $.zmsg.error("开始时间和结束时间都必须选择，且开始时间不能大于结束时间");
        return false;
    }
    return true;
}

function getHtml(gameData) {
    var html = "";
    html += "<div class='table-responsive'>";
    html += '<table class="table table-striped table-bordered table-hover dataTable no-footer" aria-describedby="dataTables-example_info">';
    html += "<thead><tr role='row'><th rowspan='2'>排行</th>";
    for (var i = 0; i < gameData["data_time"].length; i++) {
        html += "<th colspan='12'>" + gameData["data_time"][i] + "</th>";//15
    }
    html += "</tr><tr>";
    for (var i = 0; i < gameData["data_time"].length; i++) {
        html += "<th colspan='3'>消耗钻石</th>";
        html += "<th colspan='3'>最大赢家</th>";
        html += "<th colspan='3'>参加牌局</th>";
        html += "<th colspan='3'>四人牌局</th>";
        //html += "<th colspan='3'>邀请人</th>";
    }
    html += "</tr></thead><tbody>";
    for (j = 0; j < gameData["data"].length; j++) {
        var d = gameData["data"][j];
        html += "<tr><td>" + (j + 1) + "</td>";
        if (gameData["data_time"].length > 1) {
            for (var i = 0; i < gameData["data_time"].length; i++) {
                html += "<td>" + d['prop_user_id'][i] + "</td>" + "<td>" + d['prop_user_name'][i] + "</td>" + "<td>" + d['prop_nums'][i] + "颗</td>";
                html += "<td>" + d['win_user_id'][i] + "</td>" + "<td>" + d['win_user_name'][i] + "</td>" + "<td>" + d['win_nums'][i] + "次</td>";
                html += "<td>" + d['record_user_id'][i] + "</td>" + "<td>" + d['record_user_name'][i] + "</td>" + "<td>" + d['record_nums'][i] + "局</td>";
                html += "<td>" + d['record4_user_id'][i] + "</td>" + "<td>" + d['record4_user_name'][i] + "</td>" + "<td>" + d['record4_nums'][i] + "局</td>";
                //html += "<td>" + d['invite_user_id'][i] + "</td>" + "<td>" + d['invite_user_name'][i] + "</td>" + "<td>" + d['invite_nums'][i] + "</td>";
            }
        } else {
            html += "<td>" + d['prop_user_id'] + "</td>" + "<td>" + d['prop_user_name'] + "</td>" + "<td>" + d['prop_nums'] + "颗</td>";
            html += "<td>" + d['win_user_id'] + "</td>" + "<td>" + d['win_user_name'] + "</td>" + "<td>" + d['win_nums'] + "次</td>";
            html += "<td>" + d['record_user_id'] + "</td>" + "<td>" + d['record_user_name'] + "</td>" + "<td>" + d['record_nums'] + "局</td>";
            html += "<td>" + d['record4_user_id'] + "</td>" + "<td>" + d['record4_user_name'] + "</td>" + "<td>" + d['record4_nums'] + "局</td>";
            //html += "<td>" + d['invite_user_id'] + "</td>" + "<td>" + d['invite_user_name'] + "</td>" + "<td>" + d['invite_nums'] + "</td>";
        }
        html += "</tr>";
    }
    html += '</tbody></table>';
    html += "</div>";
    return html;
}
function showRankDay() {
    if (gameData && gameData.data) {
        html = getHtml(gameData);
        $("#html_day").html(html);
    }
}

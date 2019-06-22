$(document).ready(function() {
    $(".tiptip").tipTip({
        maxWidth: "auto",
        delay: 0
    });
});

$('#give-gold-num').on('keyup', function () {
    var currentNum = parseInt($('#current-gold').html());
    var giveNum = parseInt($('#give-gold-num').val());
    giveNum = isNaN(giveNum) ? 0 : giveNum;
    // 判断整型
    if (!isNaN(giveNum) && giveNum != 0 && (!(typeof giveNum === 'number' && giveNum % 1 === 0) || (giveNum != $('#give-gold-num').val()))) {
        $.zmsg.fatal('请填写正整数');
        return false;
    }
    $('#total-gold').html(currentNum + giveNum);
})

$('#change-gold').on('click', function () {
    var changeGold = parseInt($('#give-gold-num').val());
    var userId = parseInt($('#userid').val());
    $.ajax({
        url: "/Gamemgr/ajaxGiveGold",
        type: "POST",
        data: {'gold':changeGold, 'userId':userId},
        dataType: "json",
        success: function(data) {
            if (0 == data.code) {
                $.Zebra_Dialog('<p class="text-left">赠送元宝成功</p>', {
                    'animation_speed_show': 500,
                    'buttons': ["确定"],
                    'center_buttons': true,
                    'type': 'confirmation',
                    'onClose': function() {
                        $('#giveMod').modal('hide');
                    }
                });
                onQuery();
            } else {
                $.zmsg.error(data.msg);
            }
        },
        error: function(data) {
            $.zmsg.fatal(data.responseText);
        }
    });
})

// 显示用户信息
function showInfo(userdata) {
    var mapdata = [{name: "玩家昵称", field: "nick", tip: "玩家游戏中的名称"}, 
        {name: "玩家ID", field: "userid", tip: "玩家游戏中的ID"},
        {name: "是否代理商", field: "isproxy", tip: "用户是否为代理商"}, 
        {name: "认证实名", field: "truename", tip: "玩家认证的真实姓名"}, 
        {name: "认证证件号", field: "idcard", tip: "玩家认证的证件号码"},
        {name: "认证手机号", field: "phone", tip: "玩家认证的手机号码"}, 
        {name: "创建时间", field: "createtime", tip: "玩家注册时间"},
        {name: "最后登录", field: "loginrecenttime", tip: "玩家最后登录的时间"}, 
        {name: "最后登录IP", field: "loginrecentip", tip: "玩家最后登录的IP"}, 
        {name: "当前钻石", field: "leftdiamod", tip: "玩家剩余的钻石数量"}, 
        {name: "当前元宝", field: "leftgold", tip: "玩家剩余的元宝数量"},
        {name: "累计消耗钻石", field: "totaldiamod", tip: "玩家累计消耗的钻石数量"}, 
        {name: "累计消耗元宝", field: "totalgold", tip: "玩家累计消耗的元宝数量"},
        {name: "游戏次数", field: "playcounts", tip: "玩家打牌的牌局次数"}, 
        {name: "活跃天数", field: "playdays", tip: "玩家打过游戏的天数"},
        {name: "所选地区", field: "region", tip: "玩家打牌选择的地区"}, 
        {name: "加入亲友圈", field: "clubNames", tip: "玩家加入的所有俱乐部名称"}
    ];
    var html = "";
    html += "<div class='table-responsive'>";
    html += "<div class='tab-caption'>用户信息</div>";
    html += '<table class="table table-striped table-bordered table-hover dataTable no-footer table-nowrap" aria-describedby="dataTables-example_info">';
    html += "<thead><tr role='row'><th>信息类型</th><th>信息明细</th><th>信息类型</th><th>信息明细</th></tr></thead><tbody>";
    for (var j = 0; j < mapdata.length; j++) {
        html += "<tr><td class='text-info'><span class='tiptip' title='"+mapdata[j].tip+"'>" + mapdata[j].name + "</span></td>";
        leftTag = j + 1 == mapdata.length ? "<td colspan='3'>" : "<td>";
        var content = '';
        content += userdata.hasOwnProperty(mapdata[j].field) ? userdata[mapdata[j].field] : '';
        content += mapdata[j].field == 'leftgold' && userdata['isgivegold'] ?
            '<button class="btn btn-danger pull-right" data-toggle="modal" data-target="#giveMod">赠送</button>' : '';
        html += leftTag + content + "</td>";
        j++;
        if (j < mapdata.length) {
            html += "<td class='text-info'><span class='tiptip' title='"+mapdata[j].tip+"'>" + mapdata[j].name + "</span></td>";
            if (userdata.hasOwnProperty(mapdata[j].field)) {
                html += "<td>"+userdata[mapdata[j].field]+"</td>";
            } else {
                html += "<td></td>";
            }
        }
        html += "</tr>";
    }
    html += '</tbody></table>';
    html += "</div>";
    $("#userInfoPanel").show();
    $("#userInfo").html(html);
    var leftgold = userdata['leftgold'] == 'undefined' ? 0 : parseInt(userdata['leftgold']);
    $('#current-gold').html(leftgold);
    $('#total-gold').html(leftgold);
    $('#give-gold-num').val('');
    console.log($('tbody tr:eq(5) td:eq(1):not(:button)').html());
}

// 查询用户
function onQuery() {
    var data = {};
    data.userid = $("#userid").val();
    if (data.userid == '') {
        $.zmsg.error("必须输入玩家ID");
        return false; 
    }
    $.ajax({
        url: "/Gamemgr/ajaxQueryUser",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            if (0 == data.code) {
                showInfo(data.data);
                //$.zmsg.success('/Gamemgr/notify/third/info');
            } else {
                $.zmsg.error(data.msg);
            }
        },
        error: function(data) {
            $.zmsg.fatal(data.responseText);
        }
    });
}

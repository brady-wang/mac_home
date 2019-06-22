$(document).ready(function() {
    if (dbList && dbList.length > 0) {
        showDbInfo(dbList);
    }
    if (memList && memList.length > 0) {
        showMemInfo(memList);
    }
    if (roomInfo && roomInfo.hasOwnProperty("roomId")) {
        showRoomInfo(roomInfo);
    }
    
    // tip初始化
    $(".tiptip").tipTip({
        maxWidth: "auto",
        delay: 0
    });
});

// 数据库中的房间信息
function showDbInfo(list) {
    var html = "";
    html += "<div class='table-responsive'>";
    html += "<div class='tab-caption'>数据库中的房间信息</div>";
    for (var i = 0; i < list.length; i++) {
        var data = list[i];
        html += '<table class="table table-striped table-bordered table-hover dataTable no-footer table-nowrap">';
        html += "<tbody>";
        html += "<tr><td class='text-info'><span class='tiptip' title='房间号'>房间号</span></td><td>"+data.roomId+"</td>";
        html += "<td class='text-info'><span class='tiptip' title='创建房间的时间'>创建时间</span></td>";
        html += "<td>"+data.createTime+"</td></tr>";
        html += "<tr><td class='text-info'><span class='tiptip' title='房主ID'>房主</span></td><td>"+data.ownerId+"</td>";
        //html += "<td class='text-info'><span class='tiptip' title='重置玩家房间信息'>重置玩家房间信息</span></td>";
        //html += "<td><button class='btn btn-danger' type='button' onclick='resetUser("+data.ownerId+");'>重置</button></td></tr>";
        html += "<td class='text-info'><span class='tiptip' title='进入房间的时间'>进入时间</span></td>";
        html += "<td>"+data.ownerEnterTime+"</td></tr>";
        for (var j = 0; j < data.userInfo.length; j++) {
            html += "<tr><td class='text-info'><span class='tiptip' title='玩家ID'>玩家</span></td>";
            html += "<td>"+data.userInfo[j].userId+"</td>";
            //html += "<td class='text-info'><span class='tiptip' title='重置玩家房间信息'>重置玩家房间信息</span></td>";
            //html += "<td><button class='btn btn-danger' type='button' onclick='resetUser("+data.userInfo[j].userId+");'>重置</button></td></tr>";
            html += "<td class='text-info'><span class='tiptip' title='进入房间的时间'>进入时间</span></td>";
            html += "<td>"+data.userInfo[j].enterTime+"</td></tr>";
        }
        if (disbandPower) {
            html += '<tr><td colspan="4" style="text-align:center;"><button class="btn btn-danger" type="button" onclick="removeRoom(' + data.roomId + ');">解除房间</button></td></tr>'
        }
        html += '</tbody></table>';
    }
    html += "</div>";
    $("#roomInfo").html(html);
}

// 内存中的房间信息
function showMemInfo(list) {
    var html = "";
    html += "<div class='table-responsive'>";
    html += "<div class='tab-caption'>内存中的房间信息</div>";
    for (var i = 0; i < list.length; i++) {
        var data = list[i];
        html += '<table class="table table-striped table-bordered table-hover dataTable no-footer table-nowrap">';
        html += "<tbody>";
        html += "<tr><td class='text-info'><span class='tiptip' title='房间号'>房间号</span></td><td>"+data.roomId+"</td>";
        html += "<td class='text-info'><span class='tiptip' title='房间总局数'>总局数</span></td><td>"+data.roundSum+"</td></tr>";
        html += "<tr><td class='text-info'><span class='tiptip' title='正在进行的局数'>当前局数</span></td><td>"+data.nowRound+"</td>";
        html += "<td class='text-info'><span class='tiptip' title='创建房间的时间'>创建时间</span></td>";
        html += "<td>"+data.createTime+"</td></tr>";
        html += "<tr><td class='text-info'><span class='tiptip' title='房主ID'>房主</span></td><td>"+data.ownerId+"</td>";
        //html += "<td class='text-info'><span class='tiptip' title='重置玩家房间信息'>重置玩家房间信息</span></td>";
        //html += "<td><button class='btn btn-danger' type='button' onclick='resetUser("+data.ownerId+");'>重置</button></td></tr>";
        html += "<td class='text-info'><span class='tiptip' title='进入房间的时间'>进入时间</span></td>";
        html += "<td>"+data.ownerEnterTime+"</td></tr>";
        for (var j = 0; j < data.userInfo.length; j++) {
            html += "<tr><td class='text-info'><span class='tiptip' title='玩家ID'>玩家</span></td>";
            html += "<td>"+data.userInfo[j].userId+"</td>";
            //html += "<td class='text-info'><span class='tiptip' title='重置玩家房间信息'>重置玩家房间信息</span></td>";
            //html += "<td><button class='btn btn-danger' type='button' onclick='resetUser("+data.userInfo[j].userId+");'>重置</button></td></tr>";
            html += "<td class='text-info'><span class='tiptip' title='进入房间的时间'>进入时间</span></td>";
            html += "<td>"+data.userInfo[j].enterTime+"</td></tr>";
        }
        //html += "<tr><td class='text-info'><span class='tiptip' title='房间的玩法'>玩法</span></td>";
        //html += "<td colspan='3'>"+data.wanfa+"</td></tr>";
        if (disbandPower) {
            html += '<tr><td colspan="4" style="text-align:center;"><button class="btn btn-danger" type="button" onclick="removeRoom(' + data.roomId + ');">解除房间</button></td></tr>'
        }
        html += '</tbody></table>';
    }
    html += "</div>";
    $("#roomInfo").append(html);
}

// 房间信息
function showRoomInfo(data) {
    var html = "";
    html += "<div class='table-responsive'>";
    html += "<div class='tab-caption'>房间信息</div>";
    html += '<table class="table table-striped table-bordered table-hover dataTable no-footer table-nowrap">';
    html += "<tbody>";
    html += "<tr><td class='text-info'><span class='tiptip' title='房间号'>房间号</span></td><td>"+data.roomId+"</td>";
    html += "<td class='text-info'><span class='tiptip' title='房间总局数'>总局数</span></td><td>"+data.roundSum+"</td></tr>";
    html += "<tr><td class='text-info'><span class='tiptip' title='正在进行的局数'>当前局数</span></td><td>"+data.nowRound+"</td>";
    html += "<td class='text-info'><span class='tiptip' title='创建房间的时间'>创建时间</span></td><td>"+data.createTime+"</td></tr>";
    html += "<tr><td class='text-info'><span class='tiptip' title='房主ID'>房主</span></td><td>"+data.ownerId+"</td>";
    html += "<td class='text-info'><span class='tiptip' title='房主昵称'>昵称</span></td><td>"+data.owner+"</td></tr>";
    for (var j = 0; j < data.userInfo.length; j++) {
        html += "<tr><td class='text-info'><span class='tiptip' title='玩家ID'>玩家</span></td>";
        html += "<td>"+data.userInfo[j].userId+"</td>";
        html += "<td class='text-info'><span class='tiptip' title='玩家昵称'>昵称</span></td><td>"+data.userInfo[j].nick+"</td></tr>";
    }
    if (disbandPower) {
        html += '<tr><td colspan="4" style="text-align:center;"><button class="btn btn-danger" type="button" onclick="removeRoom(' + data.roomId + ');">解除房间</button></td></tr>'
    }
    html += '</tbody></table>';
    html += "</div>";
    $("#roomInfo").html(html);
}

// 解除房间
function removeRoom(roomId) {
    $.Zebra_Dialog("确认要解散房间号为“"+roomId+"”的房间吗？", {
        'title': '解散房间',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'width': "300px",
        'max_height': "200",
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {
                var data = {};
                data.roomid = roomId;

                // 开始 loading 遮盖
                $.loading.show("removeLoading");

                $.ajax({
                    url: "/Gamemgr/ajaxRemoveRoom",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('removeLoading');
                        if (0 == data.code) {
                            $.zmsg.success('/Gamemgr/user/third/terminal');
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('removeLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
}

// 重置玩家房间信息
function resetUser(userId) {
    if (!userId) {
        userId = $("#userId").val();
    }
    $.Zebra_Dialog("确认要将玩家ID为“"+userId+"”的房间重置吗？", {
        'title': '玩家重置',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'width': "300px",
        'max_height': "200",
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {
                var data = {};
                data.userId = userId;

                // 开始 loading 遮盖
                $.loading.show("resetLoading");

                $.ajax({
                    url: "/Gamemgr/ajaxResetUser",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('resetLoading');
                        if (0 == data.code) {
                            $.zmsg.success('/Gamemgr/user/third/terminal');
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('resetLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
    return false;
}

// 验证房间号
function checkRoomId() {
    let val = $("#roomId").val();
    if (val) {
        return true;
    } else {
        $.zmsg.error("请输入房间号");
        return false;
    }
}

// 验证玩家ID
function checkUserId() {
    let val = $("#userId").val();
    if (val) {
        return true;
    } else {
        $.zmsg.error("请输入玩家ID");
        return false;
    }
}
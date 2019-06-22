
$(function() {
    'use strict';

    // tip
    $(".tiptip").tipTip({
        maxWidth: "auto",
        delay: 0
    });

    // 系统维护时间，时间插件
    $("#upgradeTime").datetimepicker({
        language: "zh-CN",
        autoclose: true,
        clearBtn: true,
        startDate: new Date(),
        format: "yyyy-mm-dd hh:ii:ss"
    });

    // 页面数据初始化
    initConfData(gameConf);

    // 游戏状态切换事件
    $(':radio[name=game_status]').on('change', gameStatusChangeEV);

    // 维护公告面板开关切换事件
    $(':radio[name=upgrade_notify_status]').on('change', notifyStatusChangeEV);
});

// 字符串长度显示，超过 maxLen 部分会自动截断，一个中文为 2 个字符
function widthCheck(str, maxLen) {
    var w = 0;
    var tempCount = 0;
    // length 获取字数，不区分汉字或英文
    for (var i = 0; i < str.value.length; i++) {
        // charCodeAt()获取字符串中某一个字符的编码
        var c = str.value.charCodeAt(i);
        // 单字节加1
        if ((c >= 0x0001 && c <= 0x007e) || (0xff60 <= c && c <= 0xff9f)) {
            w++;
        } else {
            w += 2;
        }
        if (w > maxLen) {
            str.value = str.value.substr(0, i);
            break;
        }
    }
}

// 游戏状态切换事件
function gameStatusChangeEV() {

    var status = $(':radio[name=game_status]:checked').val();

    // 正常运行
    if (status == '1') {
        $('#configFS').slideUp();
    }
    // 维护中、准备维护
    else {
        $('#configFS').slideDown();
    }
}

// 维护公告面板开关切换事件
function notifyStatusChangeEV() {

    var status = $(':radio[name=upgrade_notify_status]:checked').val();

    // 开启
    if (status == '1') {
        $('#notifyFS').slideDown();
    }
    // 关闭
    else {
        $('#notifyFS').slideUp();
    }
}

// 页面数据初始化
function initConfData(conf) {

    // 未有配置，游戏状态默认为正常运行
    if (conf == null) {
        $(':radio[name=game_status][value=1]').prop('checked', true);
        $(':radio[name=upgrade_notify_status][value=9]').prop('checked', true);
        return true;
    }

    // 游戏状态
    $(':radio[name=game_status][value=' + conf.game_status + ']').prop('checked', true);
    gameStatusChangeEV();

    // 系统维护时间
    $('#upgradeTime').val(conf.upgrade_time);

    // 提醒时间点
    if (conf.upgrade_notify_rule) {
        var r = conf.upgrade_notify_rule;
        for (var i in r) {
            $(':checkbox[name=upgrade_notify_rule][value=' + r[i] + ']').prop('checked', true);
        }
    }

    // 维护提示
    $('#upgradeMsg').val(conf.upgrade_msg);

    // 维护公告面板开关
    $(':radio[name=upgrade_notify_status][value=' + conf.upgrade_notify_status + ']').prop('checked', true);
    notifyStatusChangeEV();

    // 开始展示时间
    $('#upgradeNotifyLaunch').val(conf.upgrade_notify_launch);

    // 公告标题
    $('#upgradeNotifyTitle').val(conf.upgrade_notify_title);

    // 公告内容
    $('#upgradeNotifyContent').val(conf.upgrade_notify_content);
}

// submit 配置添加
function submitAddConf() {

    var data = $('#confForm').serializeObject();

    // 操作类型为添加配置
    data.action_type = 'add';

    data.game_id = $('#gameId').text();

    // 核对对话框表格内容
    var msg = '';
    msg += '<table class="table" style="width: 80%; margin: auto;">';
    msg += '<thead><tr><th style="width: 30%;">属性</th><th style="width: 70%;">配置</th></tr></thead>';
    msg += '<tbody>';

    // 游戏状态
    msg += '<tr class="text-danger">';
    msg += '<td class="text-success">游戏状态：</td>';
    msg += '<td>' + gameStatusMap[data.game_status].name + '</td>';
    msg += '</tr>';

    if ('1' != data.game_status) {
        // 系统维护时间
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">系统维护时间：</td>';
        msg += '<td>' + data.upgrade_time + '</td>';
        msg += '</tr>';

        // 提醒时间点
        if (typeof(data.upgrade_notify_rule) == 'undefined') {
            data.upgrade_notify_rule = [];
        }
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">提醒时间点：</td>';
        msg += '<td>' + data.upgrade_notify_rule.toString() + '</td>';
        msg += '</tr>';

        // 维护提示
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">维护提示：</td>';
        msg += '<td>' + data.upgrade_msg + '</td>';
        msg += '</tr>';

        // 维护公告面板开关
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">维护公告面板：</td>';
        msg += '<td>' + notifyStatusMap[data.upgrade_notify_status].name + '</td>';
        msg += '</tr>';

        if ('1' == data.upgrade_notify_status) {
            // 开始展示时间
            msg += '<tr class="text-danger">';
            msg += '<td class="text-success">开始展示时间：</td>';
            msg += '<td>' + data.upgrade_notify_launch  + '分钟</td>';
            msg += '</tr>';

            // 公告标题
            msg += '<tr class="text-danger">';
            msg += '<td class="text-success">公告标题：</td>';
            msg += '<td>' + data.upgrade_notify_title + '</td>';
            msg += '</tr>';

            // 公告内容
            msg += '<tr class="text-danger">';
            msg += '<td class="text-success">公告内容：</td>';
            msg += '<td>' + data.upgrade_notify_content + '</td>';
            msg += '</tr>';
        }
    }

    msg += '</tbody></table>';

    var screen_width = document.body.clientWidth;
    if (screen_width > 1280) {
        screen_width = 1280;
    }

    var screen_height = document.documentElement.clientHeight;
    if (screen_height > 940) {
        screen_height = 640;
    } else {
        screen_height -= 300;
    }

    $.Zebra_Dialog(msg, {
        'title': '添加信息核对',
        'animation_speed_show': 700,
        'center_buttons': true,
        'type': '',
        'width': screen_width ,
        'max_height': screen_height,
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {

                // 开始 loading 遮盖
                $.loading.show("saveLoding");

                $.ajax({
                    url: "/Gameconf/ajaxGameAppSaveConf",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('saveLoding');
                        if (0 == data.code) {
                            $.zmsg.success();
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('saveLoding');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
}

// submit 配置修改
function submitEditConf() {

    var data = $('#confForm').serializeObject();

    // 操作类型为修改配置
    data.action_type = 'edt';

    data.game_id = $('#gameId').text();

    var isChange = 0 ;

    // 核对对话框表格内容
    var msg = '';
    msg += '<table class="table" style="width: 80%; margin: auto;">';
    msg += '<thead><tr><th style="width: 16%;">属性</th><th style="width: 42%;">原配置</th><th style="width: 42%;">现配置</th></tr></thead>';
    msg += '<tbody>';

    // 游戏状态
    if (data.game_status != gameConf.game_status) {
        isChange = 1;
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">游戏状态：</td>';
        msg += '<td>' + gameStatusMap[gameConf.game_status].name + '</td>';
        msg += '<td>' + gameStatusMap[data.game_status].name + '</td>';
        msg += '</tr>';
    }

    // 系统维护时间
    if (data.upgrade_time != gameConf.upgrade_time) {
        isChange = 1;
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">系统维护时间：</td>';
        msg += '<td>' + gameConf.upgrade_time + '</td>';
        msg += '<td>' + data.upgrade_time + '</td>';
        msg += '</tr>';
    }

    // 提醒时间点
    if (typeof(data.upgrade_notify_rule) == 'undefined') {
        data.upgrade_notify_rule = [];
    }
    if (false == gameConf.upgrade_notify_rule) {
        gameConf.upgrade_notify_rule = [];
    }
    if (data.upgrade_notify_rule.toString() != gameConf.upgrade_notify_rule.toString()) {
        isChange = 1;
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">提醒时间点：</td>';
        msg += '<td>' + gameConf.upgrade_notify_rule.toString() + '</td>';
        msg += '<td>' + data.upgrade_notify_rule.toString() + '</td>';
        msg += '</tr>';
    }

    // 维护提示
    if (data.upgrade_msg != gameConf.upgrade_msg) {
        isChange = 1;
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">维护提示：</td>';
        msg += '<td>' + gameConf.upgrade_msg + '</td>';
        msg += '<td>' + data.upgrade_msg + '</td>';
        msg += '</tr>';
    }

    // 维护公告面板开关
    if (data.upgrade_notify_status != gameConf.upgrade_notify_status) {
        isChange = 1;
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">维护公告面板：</td>';
        msg += '<td>' + notifyStatusMap[gameConf.upgrade_notify_status].name + '</td>';
        msg += '<td>' + notifyStatusMap[data.upgrade_notify_status].name + '</td>';
        msg += '</tr>';
    }

    // 开始展示时间
    if (data.upgrade_notify_launch != gameConf.upgrade_notify_launch) {
        isChange = 1;
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">开始展示时间：</td>';
        msg += '<td>' + gameConf.upgrade_notify_launch + '分钟</td>';
        msg += '<td>' + data.upgrade_notify_launch  + '分钟</td>';
        msg += '</tr>';
    }

    // 公告标题
    if (data.upgrade_notify_title != gameConf.upgrade_notify_title) {
        isChange = 1;
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">公告标题：</td>';
        msg += '<td>' + gameConf.upgrade_notify_title + '</td>';
        msg += '<td>' + data.upgrade_notify_title + '</td>';
        msg += '</tr>';
    }

    // 公告内容
    if (data.upgrade_notify_content != gameConf.upgrade_notify_content) {
        isChange = 1;
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">公告内容：</td>';
        msg += '<td>' + gameConf.upgrade_notify_content + '</td>';
        msg += '<td>' + data.upgrade_notify_content + '</td>';
        msg += '</tr>';
    }

    msg += '</tbody></table>';

    if (isChange == 0) {
        $.zmsg.error("没有任何修改");
        return false ;
    }

    var screen_width = document.body.clientWidth;
    if (screen_width > 1280) {
        screen_width = 1280;
    }

    var screen_height = document.documentElement.clientHeight;
    if (screen_height > 940) {
        screen_height = 640;
    } else {
        screen_height -= 300;
    }

    $.Zebra_Dialog(msg, {
        'title': '修改信息核对',
        'animation_speed_show': 700,
        'center_buttons': true,
        'type': '',
        'width': screen_width ,
        'max_height': screen_height,
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {

                // 开始 loading 遮盖
                $.loading.show("saveLoding");

                $.ajax({
                    url: "/Gameconf/ajaxGameAppSaveConf",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('saveLoding');
                        if (0 == data.code) {
                            $.zmsg.success();
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('saveLoding');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
}

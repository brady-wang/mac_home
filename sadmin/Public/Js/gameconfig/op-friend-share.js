
$(function() {
    'use strict';

    // 刷新出地区树
    var placeTree = [];
    // 第一级
    for (var i in tree) {
        placeTree[i] = {
            text: tree[i].placeName,
            href: '/Gameconf/opFriendShare?firstId=' + tree[i].firstID + '&placeId=' + tree[i].placeID,
        }
        // 当前编辑项加一个icon
        if (curPlaceId == tree[i].placeID) {
            placeTree[i].icon = 'glyphicon glyphicon-edit text-danger';
        }
        // 如果该地区没有任何配置，显示为灰色
        if (!$.tool.inArray(tree[i].placeID, validPlace)) {
            placeTree[i].color = '#777';
        }
        // 第二级
        if ($.tool.arrayCount(tree[i].node) > 0) {
            var t1 = tree[i].node;
            placeTree[i].nodes = [];
            for (var j in t1) {
                placeTree[i].nodes[j] = {
                    text: t1[j].placeName,
                    href: '/Gameconf/opFriendShare?firstId=' + t1[j].firstID + '&placeId=' + t1[j].placeID,
                }
                // 当前编辑项加一个icon
                if (curPlaceId == t1[j].placeID) {
                    placeTree[i].nodes[j].icon = 'glyphicon glyphicon-edit text-danger';
                }
                // 如果该地区没有任何配置，显示为灰色
                if (!$.tool.inArray(t1[j].placeID, validPlace)) {
                    placeTree[i].nodes[j].color = '#777';
                }
                // 第三级
                if ($.tool.arrayCount(t1[j].node) > 0) {
                    var t2 = t1[j].node;
                    placeTree[i].nodes[j].nodes = [];
                    for (var k in t2) {
                        placeTree[i].nodes[j].nodes[k] = {
                            text: t2[k].placeName,
                            href: '/Gameconf/opFriendShare?firstId=' + t2[k].firstID + '&placeId=' + t2[k].placeID,
                        }
                        // 当前编辑项加一个icon
                        if (curPlaceId == t2[k].placeID) {
                            placeTree[i].nodes[j].nodes[k].icon = 'glyphicon glyphicon-edit text-danger';
                            // 地区树默认只展示两级，若第三级地区即是当前地区，则将其二级展开来
                            placeTree[i].nodes[j].state = {expanded: true}
                        }
                        // 如果该地区没有任何配置，显示为灰色
                        if (!$.tool.inArray(t2[k].placeID, validPlace)) {
                            placeTree[i].nodes[j].nodes[k].color = '#777';
                        }
                    }
                }
            }
        }
    }

    // 生成地区树
    $('#placeTreeDiv').treeview({
        data: placeTree,
        levels: 3, // 默认全部展开，如果第三级收起来总是对操作员造成疏漏
        enableLinks: true,
        color: '#31708F',
        showBorder: false,
        selectedBackColor: '#FFFFFF',
        selectedColor: '#337AB7'
    });

    // 复制事件绑定
    $('#copyBtn').on('click', clickCopyBtn);

    /**************************************** 大厅分享给好友（无奖励） ****************************************/

    // init
    if (conf[6]) {
        confParamInit('hall', conf[6]);
    }

    // 添加提交
    $('#hallAddBtn').on('click', function() {
        submitAddConf(6, 'hall');
    });

    // 修改提交
    $('#hallEditBtn').on('click', function() {
        submitEditConf(6, 'hall');
    });

    /**************************************** 领取钻石-分享给好友 ****************************************/

    // init
    if (conf[7]) {
        confParamInit('diamond', conf[7]);
    }

    // 添加提交
    $('#diamondAddBtn').on('click', function() {
        submitAddConf(7, 'diamond');
    });

    // 修改提交
    $('#diamondEditBtn').on('click', function() {
        submitEditConf(7, 'diamond');
    });

    /**************************************** 俱乐部分享给好友 ****************************************/

    // init
    if (conf[8]) {
        confParamInit('club', conf[8]);
    }

    // 添加提交
    $('#clubAddBtn').on('click', function() {
        submitAddConf(8, 'club');
    });

    // 修改提交
    $('#clubEditBtn').on('click', function() {
        submitEditConf(8, 'club');
    });
});

// 配置参数初始化
function confParamInit(ab, obj) {
    // 标题
    $('#' + ab + 'Title').val(obj.title);
    // 描述
    $('#' + ab + 'Desc').val(obj.desc);
}

// 点击复制事件
function clickCopyBtn() {

    $('#copyBtn').removeClass("btn-operate").addClass("btn-success").blur();
    $('#pasteBtn').hide();

    var data = {
        place_id: curPlaceId,
        page_head: pageHead
    };

    $.ajax({
        url: "/Gameconf/ajaxFriendShareSetCopyCookie",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            if (0 != data.code) {
                $.zmsg.error(data.msg);
            }
        },
        error: function(data) {
            $.zmsg.fatal(data.responseText);
        }
    });
}

// 分享配置粘贴提交
function submitPasteConf(copyId) {

    var replacementCont = $('#pasteBtn').data('replacement');

    // 核对对话框表格内容
    var msg = '<p class="text-center text-danger" style=\'padding: 0;\'>是否确定粘贴';
    msg += replacementCont;
    msg += '的配置内容？该操作会先清除当前所有配置。</p>'

    var data = {
        copy_id: copyId,
        place_id: curPlaceId
    };

    $.Zebra_Dialog(msg, {
        'title': '粘贴核对',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'width': 640,
        'max_height': 200,
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {
                // 开始 loading 遮盖
                $.loading.show("pasteLoading");

                $.ajax({
                    url: "/Gameconf/ajaxPasteFriendShare",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('pasteLoading');
                        if (0 == data.code) {
                            $.zmsg.success();
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('pasteLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
}

// 大厅分享提交前参数校验
function paramValidatorForHall(data)
{
    // 标题校验
    if ("" == data.title) {
        $.zmsg.error('请输入标题');
        return false;
    }
    // 描述校验
    if ("" == data.desc) {
        $.zmsg.error('请输入描述');
        return false;
    }

    return true
}

// 领取钻石提交前参数校验
function paramValidatorForDiamond(data)
{
    // 标题校验
    if ("" == data.title) {
        $.zmsg.error('请输入标题');
        return false;
    }
    if (1 != (data.title.split('%d')).length - 1) {
        $.zmsg.error('标题必须包含且最多只能有一个参数 "%d"');
        return false;
    }

    // 描述校验
    if ("" == data.desc) {
        $.zmsg.error('请输入描述');
        return false;
    }
    if (1 != (data.desc.split('%d')).length - 1) {
        $.zmsg.error('描述必须包含且最多只能有一个参数 "%d"');
        return false;
    }

    return true
}

// 俱乐部提交前参数校验
function paramValidatorForClub(data)
{
    // 标题校验
    if ("" == data.title) {
        $.zmsg.error('请输入标题');
        return false;
    }
    // 描述校验
    if ("" == data.desc) {
        $.zmsg.error('请输入描述');
        return false;
    }
    var reg = /\%s\S*\%s/g;
    if (true != reg.test(data.desc)) {
        $.zmsg.error('描述必须包含两个"%s"，且"s"为小写');
        return false;
    }

    return true
}

// 分享配置添加提交
function submitAddConf(src, ab) {

    var data = $('#' + ab + 'Form').serializeObject();

    data.first_id = curFirstId;
    data.place_id = curPlaceId;

    // 分享功能
    data.source = src;

    if ('6' == src) {
        if (true !== paramValidatorForHall(data)) {
            return false;
        }
    } else if ('7' == src) {
        if (true !== paramValidatorForDiamond(data)) {
            return false;
        }
    } else if ('8' == src) {
        if (true !== paramValidatorForClub(data)) {
            return false;
        }
    }

    var msg = '<table class="table" style="width: 80%; margin: auto;">';
    msg += '<tr><td class="text-success">分享功能： </td><td class="text-danger">' + sourceMap[src] + '</td></tr>';
    msg += '<tr><td class="text-success">标题： </td><td class="text-danger">' + data.title + '</td></tr>';
    msg += '<tr><td class="text-success">描述： </td><td class="text-danger">' + data.desc + '</td></tr>';
    msg += '</table>';

    var screen_width = document.body.clientWidth;
    if (screen_width > 800) {
        screen_width = 800;
    }

    var screen_height = document.documentElement.clientHeight;
    if (screen_height > 800) {
        screen_height = 650;
    } else {
        screen_height -= 250;
    }

    $.Zebra_Dialog(msg, {
        'title': '添加信息核对',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'width': screen_width,
        'max_height': screen_height,
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {
                // 开始 loading 遮盖
                $.loading.show("addLoading");

                $.ajax({
                    url: "/Gameconf/ajaxAddFriendShare",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('addLoading');
                        if (0 == data.code) {
                            $.zmsg.success();
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('addLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
}

// 修改提交
function submitEditConf(src, ab) {

    var cfObj = conf[src];
    if (undefined == cfObj) {
        $.zmsg.error('链接类型的原配置不存在，无法进行修改');
        return false;
    }

    var data = $('#' + ab + 'Form').serializeObject();

    // id
    data.id = cfObj.id;

    // 分享功能
    data.source = src;

    if ('6' == src) {
        if (true !== paramValidatorForHall(data)) {
            return false;
        }
    } else if ('7' == src) {
        if (true !== paramValidatorForDiamond(data)) {
            return false;
        }
    } else if ('8' == src) {
        if (true !== paramValidatorForClub(data)) {
            return false;
        }
    }

    var noChange = 1;

    // 核对对话框表格内容
    var msg = '<p class="text-center text-danger" style=\'padding: 0;\'>' + sourceMap[src] + '</p>';
    msg += '<table class="table" style="width: 80%; margin: auto;word-break: break-all;">';
    msg += '<thead><tr><th>属性</th><th>原配置</th><th>现配置</th></tr></thead>';
    msg += '<tbody>';
    // 标题
    if (data.title != cfObj.title) {
        noChange = 0;
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">标题：</td>';
        msg += '<td>' + cfObj.title + '</td>';
        msg += '<td>' + data.title + '</td>';
        msg += '</tr>';
    }
    // 描述
    if (data.desc != cfObj.desc) {
        noChange = 0;
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">描述：</td>';
        msg += '<td>' + cfObj.desc + '</td>';
        msg += '<td>' + data.desc + '</td>';
        msg += '</tr>';
    }
    msg += '</tbody>';
    msg += '</table>';
    msg += '<p class="text-center text-danger" style=\'padding: 20px 0px 0px;\'>是否确认修改？</p>';

    if (1 == noChange) {
        $.zmsg.error('未进行任何修改');
        return false;
    }

    var screen_width = document.body.clientWidth;
    if (screen_width > 800) {
        screen_width = 800;
    }

    var screen_height = document.documentElement.clientHeight;
    if (screen_height > 800) {
        screen_height = 650;
    } else {
        screen_height -= 250;
    }

    $.Zebra_Dialog(msg, {
        'title': '修改信息核对',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'width': screen_width,
        'max_height': screen_height,
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {
                // 开始 loading 遮盖
                $.loading.show("edtLoading");

                $.ajax({
                    url: "/Gameconf/ajaxEditFriendShare",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('edtLoading');
                        if (0 == data.code) {
                            $.zmsg.success();
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('edtLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
}

// 删除提交
function submitDelConf() {

    var data = {place_id: curPlaceId};

    // 核对对话框表格内容
    var msg = '<p class="text-center text-danger" style=\'padding: 0;\'>是否确定删除';
    msg += pageHead;
    msg += '下的所有配置？</p>'

    $.Zebra_Dialog(msg, {
        'title': '删除核对',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'width': 640,
        'max_height': 200,
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {
                // 开始 loading 遮盖
                $.loading.show("delLoading");

                $.ajax({
                    url: "/Gameconf/ajaxDelFriendShare",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('delLoading');
                        if (0 == data.code) {
                            $.zmsg.success();
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('delLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
}

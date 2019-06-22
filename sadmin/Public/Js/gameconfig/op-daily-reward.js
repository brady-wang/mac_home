$(function() {
    'use strict';
    var placeTree = [];
    // 第一级
    for (var i in topTree) {
        placeTree[i] = {
            text: topTree[i].placeName,
            href: '/Gameconf/opDailyReward?firstId=' + topTree[i].firstID + '&placeId=' + topTree[i].placeID,
        }
        // 添加第一级icon
        if (curPlaceId == topTree[i].placeID){
            placeTree[i].icon = 'glyphicon glyphicon-edit text-danger';
        }
        // 如果该地区没有任何配置，显示为灰色
        if (!$.tool.inArray(topTree[i].placeID, validPlace)) {
            placeTree[i].color = '#777';
        }
        // 第二级
        if ($.tool.arrayCount(topTree[i].node) > 0) {
            var tree = topTree[i].node;
            placeTree[i].nodes = [];
            for (var j in tree) {
                placeTree[i].nodes[j] = {
                    text: tree[j].placeName,
                    href: '/Gameconf/opDailyReward?firstId=' + tree[j].firstID + '&placeId=' + tree[j].placeID,
                }
                // 添加第二级icon
                if (curPlaceId == tree[j].placeID) {
                    placeTree[i].nodes[j].icon = 'glyphicon glyphicon-edit text-danger';
                }
                // 如果该地区没有任何配置，显示为灰色
                if (!$.tool.inArray(tree[j].placeID, validPlace)) {
                    placeTree[i].nodes[j].color = '#777';
                }
                // 第三级
                if ($.tool.arrayCount(tree[j].node) > 0) {
                    var subTree = tree[j].node;
                    placeTree[i].nodes[j].nodes = [];
                    for (var k in subTree) {
                        placeTree[i].nodes[j].nodes[k] = {
                            text: subTree[k].placeName,
                            href: '/Gameconf/opDailyReward?firstId=' + subTree[k].firstID + '&placeId=' + subTree[k].placeID,
                        }
                        // 当前编辑项加一个icon
                        if (curPlaceId == subTree[k].placeID) {
                            placeTree[i].nodes[j].nodes[k].icon = 'glyphicon glyphicon-edit text-danger';
                            // 地区树默认只展示两级，若第三级地区即是当前地区，则将其二级展开来
                            placeTree[i].nodes[j].state = {expanded: true};
                        }
                        // 如果该地区没有任何配置，显示为灰色
                        if (!$.tool.inArray(subTree[k].placeID, validPlace)) {
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
    })
    /************************************开启配置***********************************/
    $(".open-check").click(function () {
        openStatusSwitch(this);
    })

    /************************************保存***********************************/
    $("#edtModSubmitBtn").click(function () {
        saveDailyRewardConf();
    })

    /*************************************删除配置***************************************************/
    $('#deleteBtn').click(function() {
        deleteDailyRewardConf();
    });
});

// 切换开启状态
function openStatusSwitch(node) {
    var ableStatus = $(node).parents('.form-group').find('.num').attr('disabled');

    if (!ableStatus) {
        $(node).parents('.form-group').find('.num').attr('disabled', true);
    } else {
        $(node).parents('.form-group').find('.num').attr('disabled', false);
    }
}

function checkNum(id) {
    // 不适用parsentInt 因为无法判断文本框中是空字符串还是0
    var value = $('#' + id).val();
    // 判断是否为空或0
    if(value === undefined || value <= 0 || value == '') {
        return false;
    }
    // 判断是否为正整数 String(value).indexOf(".")+1判断例如'11.0'形式
    if (value % 1 !== 0) {
        return false;
    }
    // 上限1000
    if (parseInt(value) > 1000) {
        return false;
    }
    return true;
}

// 保存每日奖励配置
function saveDailyRewardConf() {
    var data = {
        club_diamond: 0,
        club_yuanbao: 0,
        no_club_diamond: 0,
        no_club_yuanbao: 0,
    };
    data.confid = curPlaceId;
    var message = '参数配置错误<br><br>请关闭开启状态或输入不为0的正整数,且上限为1000';
    if ($('.club-diamond-check').prop('checked')) {
        if (!checkNum('club-diamond-num')) {
            $.zmsg.error('<b>亲友圈玩家·钻石</b>' + message);
            return false;
        }
        data.club_diamond = parseInt($('#club-diamond-num').val());
    }
    if ($('.club-yuanbao-check').prop('checked')) {
        if (!checkNum('club-yuanbao-num')) {
            $.zmsg.error('<b>亲友圈玩家·元宝</b>' + message);
            return false;
        }
        data.club_yuanbao = parseInt($('#club-yuanbao-num').val());
    }
    if ($('.no-club-diamond-check').prop('checked')) {
        if (!checkNum('no-club-diamond-num')) {
            $.zmsg.error('<b>非亲友圈玩家·钻石</b>' + message);
            return false;
        }
        data.no_club_diamond = parseInt($('#no-club-diamond-num').val());
    }
    if ($('.no-club-yuanbao-check').prop('checked')) {
        if (!checkNum('no-club-yuanbao-num')) {
            $.zmsg.error('<b>非亲友圈玩家·元宝</b>' + message);
            return false;
        }
        data.no_club_yuanbao = parseInt($('#no-club-yuanbao-num').val());
    }

    $.ajax({
        url: "/Gameconf/ajaxSubmitDailyReward",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                if (data.error == 'edition') {
                    $.zmsg.warning(data.msg);
                } else {
                    $.zmsg.error(data.msg);
                }
            }
        },
        error: function(data) {
            $.zmsg.fatal(data.responseText);
        }
    })
}

// 删除每日奖励配置
function deleteDailyRewardConf() {
    var data = {place_id: curPlaceId};
    //核对对话框表格内容
    var msg = "<p class='text-center text-danger' style='padding: 0'>是否确定删除";
    msg += pageHead;
    msg += "下的所有配置?</p>";
    msg += "<p class='text-center text-danger'>(若整条运营配置都为空，则将删除整条运营配置)</p>";

    $.Zebra_Dialog(msg, {
        'title': '删除核对',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'width': 640,
        'max_height': 200,
        'buttons': ['取消', '确定'],
        'onClose': function (caption) {
            if ('确定' == caption) {
                $.loading.show("delLoading");
                $.ajax({
                    url: "/Gameconf/ajaxDelDailyReward",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function (data) {
                        $.loading.hide('delLoading');
                        if (0 == data.code) {
                            $.zmsg.success();
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function (data) {
                        $.loading.hide('delLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
}
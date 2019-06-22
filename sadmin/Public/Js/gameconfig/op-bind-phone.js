$('#prize_type').click(function () {
    let type = $('#prize_type').val();
    if (type === '0') {
        $('#prize_num').val('');
        $('#prize_num').attr('disabled', true);
    } else {
        $('#prize_num').attr('disabled', false);
    }
})


$('#saveBindPrize').click(function () {
    let prize_type = $('#prize_type').val();
    let prize_num = $('#prize_num').val();
    if (prize_type === '0') {
        msg = '确认关闭绑定手机奖励配置吗';
    } else {
        msg = '确认修改绑定手机奖励配置为:'+$('#prize_type').find("option:selected").text()+prize_num+'个吗?';
    }
    $.Zebra_Dialog(msg, {
        'title': '修改确认框',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' === caption) {
            } else if ('确定' === caption) {
                if (parseInt(prize_num) === 0) {
                    $.zmsg.warning('奖励数量不能为空');
                    return false;
                } else if (parseInt(prize_num) < 0) {
                    $.zmsg.warning('奖励数量不能为负数');
                    return false;
                } else if (parseInt(prize_num) != prize_num) {
                    $.zmsg.warning('奖励数量只能为整数');
                    return false;
                }
                $.ajax({
                    url: "/Gameconf/ajaxEditBindPhone",
                    type: "POST",
                    data: {'type' : prize_type, 'num' : prize_num},
                    dataType: "json",
                    success: function(data) {
                        if (0 === data.code) {
                            $.zmsg.success();
                        } else {
                            $.zmsg.warning(data.msg);
                        }
                    },
                    error: function(data) {
                        $.zmsg.fatal('修改失败');
                    }
                })
            }
        }
    });
});
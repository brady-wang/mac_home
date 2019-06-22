function verifyMail(obj, flag) {
    var data = {};
    var id = $(obj).attr('data-id');
    data.id = id;
    data.flag = flag;
    var tip = "";
    if (flag == 1)
        tip = '确认要通过id为"'+id+'"的邮件吗？';
    else
        tip = '确认要拒绝id为"'+id+'"的邮件吗？';
    $.Zebra_Dialog(tip, {
        'title': '审核邮件确认',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {
                $.loading.show("verifyTimerLoading");
                $.ajax({
                    url: "/Gamemgr/ajaxVerifyMail",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('verifyTimerLoading');
                        if (0 == data.code) {
                            $.zmsg.success('/Gamemgr/notify/third/mail/tab/log');
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('verifyTimerLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
    return false;        
}
function sendMail() {
    var intreg = /^[0-9]{1,}$/;
    var data = {};
    data.subj = $("#mail_subj").val();
    if ("" == data.subj) {
        $.zmsg.error("必须输入邮件标题");
        return false; 
    }
    if (data.subj.length > 32) {
        $.zmsg.error("邮件标题不能超过32个字");
        return false; 
    }
    data.cont = $("#mail_cont").val();
    if ("" == data.cont) {
        $.zmsg.error("必须输入邮件内容");
        return false; 
    }
    if (data.cont.length > 4096) {
        $.zmsg.error("邮件内容不能超过4096个字");
        return false; 
    }
    data.user_type = parseInt($("input[name=mail_player]:checked").val());
    if (data.user_type == 0) { // 指定玩家
        data.users = $("#mail_users").val();
        if ("" == data.users) {
            $.zmsg.error("指定玩家时必须输入玩家ID");
            return false; 
        }
        if (data.users.length > 4096) {
            $.zmsg.error("玩家ID的内容不能超过4096个字");
            return false;
        }
        var useridreg = /^[0-9\,\r\n]+$/;
        if (!useridreg.test(data.users)) {
            $.zmsg.error("玩家id只能输入数字和半角逗号");
            return false; 
        }
    }
    if (data.user_type == 2) { // 渠道
        data.channel_user = $("#channel_users").is(':checked');
        if (!data.channel_user) {
            data.os = $("#os_type").val();
            data.code = $("#code_type").val();
        }
    }
    if (data.user_type == 3) { // 批量玩家
        data.users = $("#hdnBatchUsers").val();
        if ("" == data.users) {
            $.zmsg.error("必须上传玩家数据文件");
            return false; 
        }
    }
    var dt = new Date();
    var now = dt.getTime()/1000;
    var timerflag = parseInt($("input[name=sender_time]:checked").val());
    if (timerflag) {
        data.starttime = $("#starttime").val();
        if ("" == data.starttime) {
            $.zmsg.error("定时邮件必须指定发送时间");
            return false; 
        }
        var stime = Date.parse(new Date(data.starttime));
        stime = stime / 1000;
        if (now >= stime) {
            $.zmsg.error("邮件定时发送时间必须大于当前时间");
            return false;
        }
    }
    data.endtime = $("#endtime").val();
    if ("" == data.endtime) {
        $.zmsg.error("邮件必须指定失效时间");
        return false; 
    }
    var etime = Date.parse(new Date(data.endtime));
    etime = etime / 1000;
    if (now >= etime) {
        $.zmsg.error("邮件失效时间必须大于当前时间");
        return false;
    }
    if (timerflag) {
        if (stime >= etime) {
            $.zmsg.error("邮件失效时间必须大于邮件开始时间");
            return false;            
        }
    }
    data.reward = '';
    if ($("#pay_type").length > 0 && $("#pay_type").val() != 0) {
        if ("" == $("#pay_numbers").val()) {
            $.zmsg.error("选择赔偿物品类型后必须输入赔偿物品数量");
            return false;
        } else if (!intreg.test($("#pay_numbers").val())) {
            $.zmsg.error("赔偿物品数量只能为整数");
            return false;
        }
        data.reward = $("#pay_type").val()+":"+$("#pay_numbers").val();
    }
    var tipinfo = [];
    tipinfo.push({title: '邮件标题', value: data.subj});
    tipinfo.push({title: '邮件内容', value: data.cont});
    if (data.user_type == 1) {
        tipinfo.push({title: '发送玩家', value: '全服玩家'});
    } else if (data.user_type == 2) {
        if (data.channel_user)
            tipinfo.push({title: '发送玩家', value: '无渠道玩家'});
        else
            tipinfo.push({title: '发送玩家', value: $("#code_type").find("option:selected").text()});
    } else if (data.user_type == 3) {
        tipinfo.push({title: '发送玩家', value: '批量玩家'})
    } else {
        tipinfo.push({title: '发送玩家', value: data.users});
    }
    tipinfo.push({title: '发送时间', value: (timerflag?data.starttime:'立即发送')});
    tipinfo.push({title: '失效时间', value: (data.endtime)});
    if ($("#pay_type").length > 0) {
        tipinfo.push({title: '补偿物品', value: ($("#pay_type").val()!=0?($("#pay_type").find("option:selected").text()+'*'+$("#pay_numbers").val()):'无')});
    }
    var tip = '<table style="table-layout:fixed; width:100%;">';
    for (var idx = 0; idx < tipinfo.length; idx++) {
        tip += '<tr class="text-danger"><td class="text-success" width="80px">'+tipinfo[idx].title+'</td><td style="overflow:hidden;">'+tipinfo[idx].value+'</td></tr>';
    }
    data.subj = data.subj;
    data.cont = data.cont;
    $.Zebra_Dialog(tip, {
        'title': '发送邮件确认',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {
                $.loading.show("sendTimerLoading");
                $.ajax({
                    url: "/Gamemgr/ajaxSendTimerMail",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('sendTimerLoading');
                        if (0 == data.code) {
                            $.zmsg.success('/Gamemgr/notify/third/mail');//$.zmsg.info('保存成功');
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('sendTimerLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
    return false;
}
function delMailTimer(obj) {
    var data = {};
    var id = $(obj).attr('data-id');
    data.id = id;
    $.Zebra_Dialog('确认要取消id为"'+id+'"的邮件吗？', {
        'title': '邮件确认',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {
                $.loading.show("delTimerLoading");
                $.ajax({
                    url: "/Gamemgr/ajaxDelTimerMail",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('delTimerLoading');
                        if (0 == data.code) {
                            $.zmsg.success('/Gamemgr/notify/third/mail/tab/log');
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('delTimerLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
    return false;
}
function showUserDetail(obj) {
    var data;
    var id = $(obj).data('id');
    data = senderlist;
    var info = [];
    for (var i = 0; i < data.length; i++) {
        if (data[i].id == id) {
            info = data[i].users;
            break;
        }
    }
    var tip = '<div class="table-responsive" style="overflow-y:auto;max-height:500px;"><table class="table table-striped table-bordered table-hover dataTable no-footer table-list">' + 
        '<tr><th>编号</th><th class="text-success">玩家ID</th><th class="text-success">钻石数量</th><th>编号</th><th class="text-success">玩家ID</th><th class="text-success">钻石数量</th>' + 
        '<th>编号</th><th class="text-success">玩家ID</th><th class="text-success">钻石数量</th></tr>';
    var trR = false;
    for (var idx = 0; idx < info.length; idx++) {
        if ((idx % 3) == 0) {
            trR = true;
            tip += '<tr>';
        }
        tip += '<td>'+ (idx + 1) + '</td><td>'+info[idx].uid+'</td><td>'+info[idx].num+'</td>';
        if ((idx % 3) == 2) {
            trR = false;
            tip += '</tr>';
        }
    }
    if (trR) {
        var lft = 3 - (info.length % 3);
        for (var i = 0; i < lft; i++) {
            tip += '<td></td><td></td><td></td>';
        }
        tip += '</tr>';
    }
    tip += '</table></div>';
    $.Zebra_Dialog(tip, {
        'title': '玩家详情',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'width': 1000,
        'buttons': ['确定']
    });
}
function mail_user_click(opt) {
    if (opt == 3) {
        $('#batch_player').prop('checked', true);
        $('#mail_users').closest('.form-group').addClass('x-hide');
        $('#page_channel').addClass('x-hide');
        $('#pageBatchUser').removeClass('x-hide');
        $('#divPay').addClass('x-hide');
    } else if (opt == 2) {
        $('#channel_player').prop('checked', true);
        $('#mail_users').closest('.form-group').addClass('x-hide');
        $('#page_channel').removeClass('x-hide');
        $('#pageBatchUser').addClass('x-hide');
        $('#divPay').removeClass('x-hide');
    } else if (opt == 1) {
        $('#all_player').prop('checked', true);
        $('#mail_users').closest('.form-group').addClass('x-hide');
        $('#page_channel').addClass('x-hide');
        $('#pageBatchUser').addClass('x-hide');
        $('#divPay').removeClass('x-hide');
    } else {
        $('#custom_player').prop('checked', true);
        $('#mail_users').closest('.form-group').removeClass('x-hide');
        $('#page_channel').addClass('x-hide');
        $('#pageBatchUser').addClass('x-hide');
        $('#divPay').removeClass('x-hide');
    }
}
function mail_channel_click() {
    var chk = $("#channel_users").is(':checked');
    if (chk) {
        $('#os_type').closest('.form-group').addClass('x-hide');            
    } else {
        $('#os_type').closest('.form-group').removeClass('x-hide'); 
    }
}
function mail_timer_click(timer) {
    if (timer) {
        $('#timer_send').prop('checked', true);
        $('#starttime').closest('.form-group').removeClass('x-hide');
    } else {
        $('#imme_send').prop('checked', true);
        $('#starttime').closest('.form-group').addClass('x-hide');
    }
}
function osChange(obj) {
    var val = $("#os_type").val();
    $("#code_type").empty();

    if (val == 1) {
        var oscode = codeType1;
    } else if (val == 2) {
        var oscode = codeType2;
    } else if (val == 3) {
        var oscode = codeType3;
    } else {
        var oscode = codeType;
    }
    for (var i = 0; i < oscode.length; i++) {
        $("#code_type").append("<option value='"+oscode[i].code+"'>"+oscode[i].name+"</option>");
    }
}
function tabpage(page) {
    $("#li_send").removeClass("active");
    $("#mail_send").removeClass("active in");
    $("#li_log").removeClass("active");
    $("#mail_log").removeClass("active in");
    if (page == 'log') {
        $("#li_log").addClass("active");
        $("#mail_log").addClass("active in");
    } else {
        $("#li_send").addClass("active");
        $("#mail_send").addClass("active in");            
    }
}

// 点击显示邮件详细信息
function clickMailInfo(id) {
    var screen_width = document.body.clientWidth;
    if (screen_width > 1280) {
        screen_width = 1280;
    }

    var screen_height = document.documentElement.clientHeight;
    if (screen_height > 890) {
        screen_height = 640;
    } else {
        screen_height -= 250;
    }

    $.Zebra_Dialog("", {
        animation_speed_show: 500,
        buttons: ['确定'],
        center_buttons: true,
        type: 'information',
        width: screen_width,
        source: {'iframe': {
            src: '/Gamemgr/iframeGetMailInfo/id/' + id,
            height: screen_height,
        }}
    });
    return true;
}

function batchUserUploadSetup() {
    $('#iptBatchUsers').fileupload({
        url: "/Gamemgr/ajaxBatchUserUpload"
    }).on('fileuploadstart', function(e, data) {
        // 上传 input,提交input disabled
        $('#iptBatchUsers').prop('disabled', true);
        $('#btnSendMail').prop('disabled', true);
        // 图片上传按钮切换为进度样式
        $('#badgeBatchUsers').empty().append($('<span/>').text("0%"));
    }).on('fileuploadprogress', function(e, data) {
        // 更新进度值
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#badgeBatchUsers').children().text(progress + "%");
    }).on('fileuploadalways', function(e, data) {
        // 上传 input enabled
        $('#iptBatchUsers').prop('disabled', false);
        $('#btnSendMail').prop('disabled', false);

        if (!data.result) {
            $.zmsg.fatal('上传失败');
        } else if (0 == data.result.code) {
            $('#hdnBatchUsers').val(data.result.data.fileUrl);
            $.zmsg.info('文件上传成功');

            // 添加后将图片上传按钮的样式改为修改样式
            setTimeout(function() {
                $('#btnBatchUsers').fadeOut('slow', function() {
                    if ("add" == $('#btnBatchUsers').data('tag')) {
                        $('#btnBatchUsers').removeClass('btn-success').addClass('btn-primary').data('tag', 'edit');
                    }
                    $('#badgeBatchUsers').empty().append(
                            $('<span/>').addClass("glyphicon glyphicon-edit")
                            );
                    $('#btnBatchUsers').fadeIn('slow');
                });
            }, 1000);
        } else {
            // 弹出错误信息
            if (data.result.msg) {
                $.zmsg.error(data.result.msg);
            } else {
                $.zmsg.fatal(data.result);
            }
        }
    });
}

$(document).ready(function() {
    $(".tiptip").tipTip({
        maxWidth: "auto",
        delay: 0
    });
    mail_user_click(0);
    mail_timer_click(0);
    tabpage(tab);
    osChange();
    batchUserUploadSetup();
    if (query.attQuery)
        $('#attQuery').children('[value=' + query.attQuery + ']').prop('selected', true);
    if (query.mailStatus)
        $('#mailStatus').children('[value=' + query.mailStatus + ']').prop('selected', true);
    if (query.userQuery) {
        $('#userQuery').children('[value=' + query.userQuery + ']').prop('selected', true);
    }
    $("input[name=starttime]").datetimepicker({
        language: "zh-CN",
        autoclose: true,
        clearBtn: true,
        //todayBtn: true,
        format: "yyyy-mm-dd hh:ii:00"
    });
    $("input[name=endtime]").datetimepicker({
        language: "zh-CN",
        autoclose: true,
        clearBtn: true,
        //todayBtn: true,
        format: "yyyy-mm-dd hh:ii:00"
    });
    $("input[name=stime]").datetimepicker({
        language: "zh-CN",
        autoclose: true,
        clearBtn: true,
        //todayBtn: true,
        format: "yyyy-mm-dd hh:ii:00"
    });
    $("input[name=etime]").datetimepicker({
        language: "zh-CN",
        autoclose: true,
        clearBtn: true,
        format: "yyyy-mm-dd hh:ii:00"
    });
    
    // 列表内容处理
    for (var i in senderlist) {
        var firstCarriage = senderlist[i].cont.indexOf("\n");
        if (firstCarriage <= 0 && senderlist[i].cont.length > 10) {
            firstCarriage = 10;
        }
        if (firstCarriage > 10) {
            firstCarriage = 10;
        }
        if (firstCarriage > 0) {
            //只有当"邮件内容"大于50个字才收起
            if (senderlist[i].cont.length > 50) {
                var curtailCont = senderlist[i].cont.substr(0, firstCarriage);

                $('#tdShowCont' + senderlist[i].id).append(
                    $('<pre/>').addClass("pre-dis-border").append(
                        $('<a/>').attr('href', "javascript:void(0)").append(
                            $('<span/>').addClass("glyphicon glyphicon-menu-down").attr("aria-hidden", true).html("&nbsp;")
                        ).append(
                            $('<span/>').text(curtailCont + "...")
                        )
                    ).on('click', function () {
                        $(this).hide();
                        $(this).next().slideDown();
                    })
                ).append(
                    $('<pre/>').addClass("pre-dis-border").append(
                        $('<a/>').attr('href', "javascript:void(0)").append(
                            $('<span/>').addClass("glyphicon glyphicon-menu-up").attr("aria-hidden", true).html("&nbsp;")
                        ).on('click', function () {
                            $(this).parent().slideUp();
                            $(this).parent().prev().show();
                        })
                    ).append(
                        $('<span/>').text(senderlist[i].cont)
                    ).hide()
                );
            } else {
                $('#tdShowCont' + senderlist[i].id).append(
                    $('<pre/>').addClass("pre-dis-border").text(senderlist[i].cont)
                );
            }
        } else {
            // 没有换行，直接输入内容，不做缩展功能
            $('#tdShowCont' + senderlist[i].id).append(
                $('<pre/>').addClass("pre-dis-border").text(senderlist[i].cont)
            );
        }
        
        // 指定玩家
        if (senderlist[i].user_type == 0) {
            var arrUsers = senderlist[i].users.split(",");
            if (arrUsers.length > 3) {
                var curtailCont = arrUsers.slice(0, 3).join(",");
                var batchUser = "";
                for (var k = 0; k < arrUsers.length; k += 3) {
                    var ar = arrUsers.slice(k, k + 3);
                    batchUser += ar.join(",") + "\n";
                }

                $('#tdShowUser' + senderlist[i].id).append(
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
                        $('<span/>').text(batchUser)
                    ).hide()
                );
            } else {
                // 没有换行，直接输入内容，不做缩展功能
                $('#tdShowUser' + senderlist[i].id).append(
                    $('<pre/>').addClass("pre-dis-border").text(senderlist[i].users)
                );
            }
        }
    }
});

// 导出csv时必须选择起止时间
function onExportBtn() {
    let stime = $("#stime").val();
    let etime = $("#etime").val();
    if (!stime || !etime) {
        $.zmsg.error("必须选择开始日期和结束日期");
        return false;
    }
    // 获取表单数据
    let formArr = $('#mailForm').serializeArray();
    let postData = [];
    $.each(formArr, function (key, input) {
        if(input.value != '' && input.value != 0 && input.value != undefined) {
            let name = input.name;
            postData[name] = input.value;
        }
    });
    let url = '/Gamemgr/iframeExportMail';
    $.postDownFile(url, postData);
    return false;
}

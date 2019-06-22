
// 热更资源文件
var gPacFileSavename = null;

$(function() {
    'use strict';

    $(".tiptip").tipTip({
        maxWidth: "auto",
        delay: 0
    });

    // query bar init
    if (query.channel_code) {
        $('#channelCode').val(query.channel_code);
    }
    if (query.channel_name_like) {
        $('#channelName').val(query.channel_name_like);
    }

    // 添加 modal 更新方式 change 事件绑定
    $('#addModUpdateMode').on('change', updateModeChangeEV);

    // 添加、修改 modal 文件上传插件安装
    resFileUploadSetup('addMod');
    resFileUploadSetup('edtMod');
});

// 添加框更新方式改变事件
function updateModeChangeEV() {
    let upMode = $('#addModUpdateMode').val();

    // 数据格式清理
    $('#addModFourceUrl').val('');
    gPacFileSavename = null;
    if ("edit" == $('#addModUpResBtn').data('tag')) {
        $('#addModUpResBtn').removeClass('btn-primary').addClass('btn-success').data('tag', 'add');
    }
    $('#addModUpResBadge').empty().append(
        $('<span/>').addClass("glyphicon glyphicon-arrow-up")
    );
    $('#addModUpFileView').text('');

    // 强更
    if (1 == upMode) {
        $('#addModRepFS').show();
        $('#addModPacFS').hide();
    }
    // 热更
    else if (2 == upMode) {
        $('#addModPacFS').show();
        $('#addModRepFS').hide();
    }
}

// 切换版本列表面板
function clickTogglePlane(chCode, obj) {
    // 通过判断面板是否处于隐藏状态而进行不同操作
    if ($('#plane' + chCode).is(':hidden')) {
        // 刷新面板内容，刷第一页
        reflashPlaneList(chCode, 1);

        // 先显示 tr 再下滑 div
        $('#plane' + chCode).show();
        $('#planeDiv' + chCode).slideDown('fast', function() {
            // 向下 icon 切换成向上
            obj.children().toggleClass('glyphicon-menu-down glyphicon-menu-up');
        });
    } else {
        // 先收起 div 再隐藏 tr
        $('#planeDiv' + chCode).slideUp('fast', function() {
            $('#plane' + chCode).hide();
            // 向上 icon 切换成向下
            obj.children().toggleClass('glyphicon-menu-down glyphicon-menu-up');
        });
    }
}

// 刷新面板内容
function reflashPlaneList(chCode, page) {

    var data = {
        channel_code: chCode,
        page: page
    }

    // 清除面板内容，包括分页栏
    $('#planeBody' + chCode).empty();
    $('#planePage' + chCode).empty();

    // 开始 loading 遮盖
    $.loading.show("reflashPlaneLoading");

    $.ajax({
        url: "/Gameconf/ajaxGetVersionList",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('reflashPlaneLoading');
            if (0 == data.code) {
                var list = data.data.list;
                var totalPage = data.data.totalPage;

                // append 版本记录
                for (var i in list) {
                    var status = statusMap[list[i].status];
                    var ver = list[i];

                    // 根据当前状态判断能进行哪些操作
                    if ('10' == list[i].status) {
                        // 待上线，可进行发版、修改、取消操作，要根据操作权限来加按钮
                        var statusElm = $('<td/>').data('ver', list[i]);
                        if (1 == pubFlag) {
                            // 发版
                            statusElm.append(
                                $('<button/>').on('click', function() {
                                    submitPublishVersion($(this).parent().data('ver'));
                                }).addClass('btn btn-sm btn-success hori-gap').attr('type', 'button').text('发版')
                            );
                        }
                        if (1 == mgrFlag) {
                            // 修改
                            statusElm.append(
                                $('<button/>').on('click', function() {
                                    clickEdtVersion($(this).parent().data('ver'), page);
                                }).addClass('btn btn-sm btn-primary hori-gap').attr('type', 'button').text('修改')
                            );
                            // 取消
                            statusElm.append(
                                $('<button/>').on('click', function() {
                                    submitCancelVersion($(this).parent().data('ver'));
                                }).addClass('btn btn-sm btn-danger hori-gap').attr('type', 'button').text('取消')
                            );
                        }
                    } else {
                        // 其他状态不可进行任何操作
                        var statusElm = $('<td/>');
                    }

                    $('#planeBody' + chCode).append(
                        $('<tr/>').addClass(status.text).append(
                            // 序号
                            $('<td/>').text(list[i].id)
                        ).append(
                            // 版本号
                            $('<td/>').append(
                                $('<a/>').on('click', {id: list[i].id}, function(e) {
                                    clickGetVersionInfo(e.data.id);
                                }).attr('href', 'javascript:void(0);').text(list[i].update_version)
                            )
                        ).append(
                            // 上线状态
                            $('<td/>').append(
                                $('<span/>').addClass("label " + status.label).text(status.name)
                            )
                        ).append(
                            // 更新时间
                            $('<td/>').text(list[i].update_time)
                        ).append(
                            // 版本备注
                            $('<td/>').text(list[i].remark)
                        ).append(
                            // 操作
                            statusElm
                        )
                    );
                }

                // 只有一页就无需分页了
                if (totalPage > 1) {
                    // 分页 elm
                    var pagiantion = $('<ul/>').addClass('pagination pagination-sm ver-pagination-top');

                    // 计算分页临时变量
                    var rowLen = 20;
                    var nowCoolPage = rowLen / 2;
                    var nowCoolPageCeil = Math.ceil(nowCoolPage);

                    // 上一页
                    var upRow = page - 1;
                    if (upRow > 0) {
                        pagiantion.append(
                            $('<li/>').append(
                                $('<a/>').on('click', function() {
                                    reflashPlaneList(chCode, upRow);
                                }).attr('href', 'javascript:void(0)').attr('aria-label', 'Previous').append(
                                    $('<span/>').attr('aria-hidden', 'true').html('&laquo;')
                                )
                            )
                        );
                    } else {
                        pagiantion.append(
                            $('<li/>').addClass('disabled').append(
                                $('<a/>').attr('aria-label', 'Previous').append(
                                    $('<span/>').attr('aria-hidden', 'true').html('&laquo;')
                                )
                            )
                        );
                    }

                    // 数字连接
                    for (var i = 1; i <= totalPage; i++) {
                        if (page - nowCoolPage <= 0) {
                            var p = i;
                        } else if (page + nowCoolPage - 1 >= totalPage) {
                            var p = totalPage - page + i;
                        } else {
                            var p = page - nowCoolPageCeil + i;
                        }
                        if (p > 0 && p != page) {
                            if (p <= totalPage) {
                                pagiantion.append(
                                    $('<li/>').append(
                                        $('<a/>').on('click', function() {
                                            reflashPlaneList(chCode, $(this).text());
                                        }).attr('href', 'javascript:void(0)').text(p)
                                    )
                                );
                            } else {
                                break;
                            }
                        } else {
                            if (p > 0 && totalPage != 1) {
                                pagiantion.append(
                                    $('<li/>').addClass('active').append(
                                        $('<a/>').html(p + '<span class="sr-only">(current)</span>')
                                    )
                                );
                            }
                        }
                    }

                    // 下一页
                    var downRow = page + 1;
                    if (downRow <= totalPage) {
                        pagiantion.append(
                            $('<li/>').append(
                                $('<a/>').on('click', function() {
                                    reflashPlaneList(chCode, downRow);
                                }).attr('href', 'javascript:void(0)').attr('aria-label', 'Next').append(
                                    $('<span/>').attr('aria-hidden', 'true').html('&raquo;')
                                )
                            )
                        );
                    } else {
                        pagiantion.append(
                            $('<li/>').addClass('disabled').append(
                                $('<a/>').attr('aria-label', 'Next').append(
                                    $('<span/>').attr('aria-hidden', 'true').html('&raquo;')
                                )
                            )
                        );
                    }

                    // 总页数
                    pagiantion.append(
                        $('<li/>').addClass('active').append(
                            $('<a/>').html('总共' + totalPage + '页<span class="sr-only">(total page)</span>')
                        )
                    );

                    // append nav
                    $('#planePage' + chCode).append(
                        $('<nav/>').attr('aria-label', 'Page navigation').append(pagiantion)
                    );
                }
            } else {
                $.zmsg.error(data.msg);
            }
        },
        error: function(data) {
            $.loading.hide('reflashPlaneLoading');
            $.zmsg.fatal(data.responseText);
        }
    });
}

// 获取版本详细信息
function clickGetVersionInfo(id) {

    var screen_width = document.body.clientWidth;
    if (screen_width > 1280) {
        screen_width = 1280;
    }

    var screen_height = document.documentElement.clientHeight;
    if (screen_height > 570) {
        screen_height = 320;
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
            src: '/Gameconf/iframeGetVersionInfo/id/' + id,
            height: screen_height,
        }}
    });
}

// 压缩文件上传插件安装
function resFileUploadSetup(ab) {

    $('#' + ab + 'UpResInput').fileupload({
        url: "/Gameconf/ajaxVersionUploadResource"
    }).on('fileuploadstart', function (e, data) {
        // 上传 input,提交input disabled
        $('#' + ab + 'UpResInput').prop('disabled', true);
        $('#' + ab + 'UpResBtn').prop('disabled', true);
        // 图片上传按钮切换为进度样式
        $('#' + ab + 'UpResBadge').empty().append(
            $('<span/>').text("0%")
        );
    }).on('fileuploadprogress', function (e, data) {
        // 更新进度值
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#' + ab + 'UpResBadge').children().text(progress + "%");
    }).on('fileuploadalways', function(e, data) {
        // 上传 input enabled
        $('#' + ab + 'UpResInput').prop('disabled', false);
        $('#' + ab + 'UpResBtn').prop('disabled', false);

        if (0 == data.result.code) {
            gPacFileSavename = data.result.data.saveName;
            $('#' + ab + 'UpFileView').text(data.result.data.fileName);

            // 添加后将上传按钮的样式改为修改样式
            setTimeout(function() {
                $('#' + ab + 'UpResBtn').fadeOut('fast', function() {
                    if ("add" == $('#' + ab + 'UpResBtn').data('tag')) {
                        $('#' + ab + 'UpResBtn').removeClass('btn-success').addClass('btn-primary').data('tag', 'edit');
                    }
                    $('#' + ab + 'UpResBadge').empty().append(
                        $('<span/>').addClass("glyphicon glyphicon-edit")
                    );
                    $('#' + ab + 'UpResBtn').fadeIn('fast');
                });
            }, 200);
        } else {
            // 上传出错，将按钮Badge还原
            if ("add" == $('#' + ab + 'UpResBtn').data('tag')) {
                $('#' + ab + 'UpResBadge').empty().append(
                    $('<span/>').addClass("glyphicon glyphicon-arrow-up")
                );
            } else {
                $('#' + ab + 'UpResBadge').empty().append(
                    $('<span/>').addClass("glyphicon glyphicon-edit")
                );
            }
            // 弹出错误信息
            if (data.result.msg) {
                $.zmsg.error(data.result.msg);
            } else {
                $.zmsg.fatal(data.result);
            }
        }
    });
}

// 提交版本 click
function clickAddVersion(obj) {

    // 渠道号、渠道名
    $('#addModChnCode').val(obj.data('chncode'));
    $('#addModChnName').text(obj.data('chnname'));
    // 最新版本号
    $('#addModLatestVersion').text(obj.data('lver'));
    // 游戏版本号
    $('#addModUpdateVersion').val('');
    // 更新方式，同时刷新强更地址或热更资源
    gPacFileSavename = null;
    $('#addModUpdateMode').children('[value=1]').prop('selected', true);
    updateModeChangeEV('addMod');
    // 备注
    $('#addModRemark').val('');

    $('#addMod').modal();
}

// 提交版本 submit
function submitAddVersion() {

    var data = $("#addModForm").serializeObject();

    if (2 == data.update_mode) {
        data.file_savename = gPacFileSavename;
    }

    // 开始 loading 遮盖
    $.loading.show("addVerLoading");

    $('#addMod').modal('hide');

    $.ajax({
        url: "/Gameconf/ajaxAddVersion",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('addVerLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "addMod");
            }
        },
        error: function(data) {
            $.loading.hide('addVerLoading');
            $.zmsg.fatalShowModal(data.responseText, "addMod");
        }
    });
}

// 修改 click
function clickEdtVersion(ver, page) {

    // 序号
    $('#edtModId').text(ver.id);
    // 渠道名
    $('#edtModChnName').text($('#channelTr' + ver.channel_code).children(':eq(1)').text());
    // 最新版本号
    $('#edtModLatestVersion').text($('#channelTr' + ver.channel_code).children(':eq(3)').text());
    // 游戏版本号
    $('#edtModUpdateVersion').text(ver.update_version);
    // 更新方式，强更
    if (1 == ver.update_mode) {
        $('#edtModUpdateMode').text('强更');
        $('#edtModFourceUrl').val(ver.update_url);
        $('#edtModRepFS').show();
        $('#edtModPacFS').hide();
    }
    // 热更
    else {
        gPacFileSavename = null;
        $('#edtModUpdateMode').text('热更');
        $('#edtModUpFileView').text(ver.update_md5);
        $('#edtModRepFS').hide();
        $('#edtModPacFS').show();
    }
    // 备注
    $('#edtModRemark').val(ver.remark);

    // 用于修改成功后刷新版本面板内容
    $('#edtModId').data('chnCode', ver.channel_code);
    $('#edtModId').data('page', page);

    $('#edtMod').modal();
}

// 修改 submit
function submitEdtVersion() {

    var data = $("#edtModForm").serializeObject();
    var chnCode = $('#edtModId').data('chnCode');
    var page = $('#edtModId').data('page');

    data.id = $('#edtModId').text();

    if (null != gPacFileSavename) {
        data.file_savename = gPacFileSavename;
    }

    // 开始 loading 遮盖
    $.loading.show("edtVerLoading");

    $('#edtMod').modal('hide');

    $.ajax({
        url: "/Gameconf/ajaxEdtVersion",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('edtVerLoading');
            if (0 == data.code) {
                $.Zebra_Dialog('<p style="padding-left: 80px;">成功</p>', {
                    'animation_speed_show': 500,
                    'auto_close': 2000,
                    'buttons': ["确定"],
                    'center_buttons': true,
                    'type': 'confirmation'
                });
                reflashPlaneList(chnCode, page);
            } else {
                $.zmsg.errorShowModal(data.msg, "edtMod");
            }
        },
        error: function(data) {
            $.loading.hide('edtVerLoading');
            $.zmsg.fatalShowModal(data.responseText, "edtMod");
        }
    });
}

// 发版 submit
function submitPublishVersion(ver) {

    var msg = '<div class="row">';
    msg += '<div class="col-sm-9 col-sm-offset-1">';
    msg += '<table class="table">';
    msg += '<tr><td class="text-success">序号： </td><td class="text-danger">' + ver.id + '</td></tr>';
    msg += '<tr><td class="text-success">版本号： </td><td class="text-danger">' + ver.update_version + '</td></tr>';
    msg += '<tr><td class="text-success">更新方式： </td><td class="text-danger">' + modeMap[ver.update_mode].name + '</td></tr>';
    msg += '<tr><td class="text-success">地址： </td><td class="text-danger">' + ver.update_url + '</td></tr>';
    msg += '<tr><td class="text-success">备注： </td><td class="text-danger">' + ver.remark + '</td></tr>';
    msg += '</table>';
    msg += '</div>';
    msg += '</div>';
    msg += '<p class="text-center text-danger">是否确认发布版本？发布后不可撤回！</p>';

    var screen_width = document.body.clientWidth;
    if (screen_width > 640) {
        screen_width = 640;
    }

    var screen_height = document.documentElement.clientHeight;
    if (screen_height > 650) {
        screen_height = 650;
    } else {
        screen_height -= 250;
    }

    $.Zebra_Dialog(msg, {
        'title': '发版确认',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'width': screen_width,
        'max_height': screen_height,
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {
                var data = {
                    id: ver.id
                };

                // 开始 loading 遮盖
                $.loading.show("pubVerLoading");

                $.ajax({
                    url: "/Gameconf/ajaxPublishVersion",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('pubVerLoading');
                        if (0 == data.code) {
                            $.zmsg.success();
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('pubVerLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
}

// 取消 submit
function submitCancelVersion(ver) {

    var msg = '<div class="row">';
    msg += '<div class="col-sm-6 col-sm-offset-3">';
    msg += '<table class="table">';
    msg += '<tr><td class="text-success">序号： </td><td class="text-danger">' + ver.id + '</td></tr>';
    msg += '<tr><td class="text-success">版本号： </td><td class="text-danger">' + ver.update_version + '</td></tr>';
    msg += '</table>';
    msg += '</div>';
    msg += '</div>';
    msg += '<p class="text-center text-danger">是否确认取消本版本发布？</p>';

    var screen_width = document.body.clientWidth;
    if (screen_width > 640) {
        screen_width = 640;
    }

    var screen_height = document.documentElement.clientHeight;
    if (screen_height > 650) {
        screen_height = 650;
    } else {
        screen_height -= 250;
    }

    $.Zebra_Dialog(msg, {
        'title': '取消确认',
        'animation_speed_show': 500,
        'center_buttons': true,
        'type': '',
        'width': screen_width,
        'max_height': screen_height,
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {
                var data = {
                    id: ver.id
                };

                // 开始 loading 遮盖
                $.loading.show("delVerLoading");

                $.ajax({
                    url: "/Gameconf/ajaxCancelVersion",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('delVerLoading');
                        if (0 == data.code) {
                            $.zmsg.success();
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('delVerLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
}

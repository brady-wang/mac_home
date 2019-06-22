
$(function() {
    'use strict';

    // 缩略图 tip
    $(".tiptip").tipTip({
        maxWidth: "auto",
        delay: 0
    });

    // init
    confParamInit();

    // 缩略图上传插件安装
    thumbImgUploadSetup();

    // 缩略图移除事件
    $('#uploadImgRemoveIcon').on('click', removeThumbImgEV);

    // 保存提交
    $('#saveBtn').on('click', function() {
        submitSaveConf();
    });
});

// 配置参数初始化
function confParamInit() {
    // 初始分数
    $('#initScore').val(gameInfo.initScore);

    // 解散时间
    $('#expiredTime').val(gameInfo.expiredTime);

    if ($.tool.arrayCount(shareInfo) > 0) {
        // 标题
        $('#shareTitle').val(shareInfo.title);
        // 描述
        $('#shareDesc').val(shareInfo.desc);
        // 缩略图
        if ('' != shareInfo.image) {
            $('#uploadImgBtn').removeClass('btn-success').addClass('btn-primary').data('tag', 'edit');
            $('#uploadImgBadge').empty().append(
                $('<span/>').addClass("glyphicon glyphicon-edit")
            );
            $('#uploadImgPreviewDiv').children().append(
                $('<img/>').css("box-shadow", "0px 0px 3px #565656").css('max-width', '100%').attr({
                    id: 'uploadImg',
                    src: imgUrlPrefix + shareInfo.image
                })
            );
            $('#uploadImgPreviewDiv').show();

            // 显示移除 icon
            $('#uploadImgRemoveIcon').show();
        }
    }
}

// 安装缩略图上传插件
function thumbImgUploadSetup() {

    $('#uploadImgInput').fileupload({
        url: "/Gameconf/ajaxRoomUploadThumbImg"
    }).on('fileuploadstart', function (e, data) {
        // 上传 input,提交input disabled
        $('#uploadImgInput').prop('disabled', true);
        $('#uploadImgBtn').prop('disabled', true);
        // 图片上传按钮切换为进度样式
        $('#uploadImgBadge').empty().append(
            $('<span/>').text("0%")
        );
    }).on('fileuploadprogress', function (e, data) {
        // 更新进度值
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#uploadImgBadge').children().text(progress + "%");
    }).on('fileuploadalways', function(e, data) {
        // 上传 input enabled
        $('#uploadImgInput').prop('disabled', false);
        $('#uploadImgBtn').prop('disabled', false);

        if (0 == data.result.code) {
            // 先隐藏预览框
            $('#uploadImgPreviewDiv').fadeOut("fast", function() {
                // 先清掉旧图片，再添加新图片并显示出来
                $('#uploadImgPreviewDiv').children().empty().append(
                    $('<img/>').css("box-shadow", "0px 0px 3px #565656").css('max-width', '100%').attr({
                        id: 'uploadImg',
                        src: data.result.data.imgUrl
                    }).data('savename', data.result.data.saveName)
                );
                $('#uploadImgPreviewDiv').fadeIn("slow");
            });

            // 添加后将图片上传按钮的样式改为修改样式
            setTimeout(function() {
                $('#uploadImgBtn').fadeOut('slow', function() {
                    if ("add" == $('#uploadImgBtn').data('tag')) {
                        $('#uploadImgBtn').removeClass('btn-success').addClass('btn-primary').data('tag', 'edit');
                    }
                    $('#uploadImgBadge').empty().append(
                        $('<span/>').addClass("glyphicon glyphicon-edit")
                    );
                    $('#uploadImgBtn').fadeIn('slow');
                });
            }, 1000);

            // 显示移除 icon
            $('#uploadImgRemoveIcon').show();
        } else {
            // 上传出错，将按钮Badge还原
            if ("add" == $('#uploadImgBtn').data('tag')) {
                $('#uploadImgBadge').empty().append(
                    $('<span/>').addClass("glyphicon glyphicon-arrow-up")
                );
            } else {
                $('#uploadImgBadge').empty().append(
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

// 缩略图移除事件
function removeThumbImgEV() {

    $('#uploadImgPreviewDiv').fadeOut("fast", function() {
        // 清掉旧图片
        $('#uploadImgPreviewDiv').children().empty();
    });

    $('#uploadImgBadge').empty().append(
        $('<span/>').addClass("glyphicon glyphicon-arrow-up")
    );

    $('#uploadImgBtn').removeClass('btn-primary').addClass('btn-success').data('tag', 'add');

    $('#uploadImgRemoveIcon').hide();
}

// 保存提交
function submitSaveConf() {

    var data = $('#roomForm').serializeObject();

    // id
    data.place_id = curPlaceId;
    data.play_id = $('#playId').text();

    // 输入校验，初始分数
    if ('' == data.init_score) {
        $.zmsg.error('请输入初始分数');
        return false;
    }
    // 解散时间
    if ('' == data.expired_time) {
        $.zmsg.error('请输入解散时间');
        return false;
    }
    // 标题校验
    if ("" == data.title) {
        $.zmsg.error('请输入标题');
        return false;
    }
    if (data.title.indexOf('d') < 0){
        $.zmsg.error('标题必须包含一个参数 "d"');
        return false;
    }
    // 描述校验
    if ("" == data.desc) {
        $.zmsg.error('请输入描述');
        return false;
    }
    if (data.desc.indexOf('dd') < 0){
        $.zmsg.error('描述至少包含一个参数"dd"');
        return false;
    }
    // 缩略图
    data.savename = $('#uploadImg').data('savename');
    if (undefined == data.savename) {
        data.savename = '';
    }

    var noChange = 1;

    // 核对对话框表格内容
    var msg = '<table class="table" style="width: 80%; margin: auto;word-break: break-all;">';
    msg += '<thead><tr><th>属性</th><th>原配置</th><th>现配置</th></tr></thead>';
    msg += '<tbody>';
    // 初始分数
    if (data.init_score != gameInfo.initScore) {
        noChange = 0;
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">初始分数：</td>';
        msg += '<td>' + gameInfo.initScore + '</td>';
        msg += '<td>' + data.init_score + '</td>';
        msg += '</tr>';
    }
    // 解散时间
    if (data.expired_time != gameInfo.expiredTime) {
        noChange = 0;
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">解散时间：</td>';
        msg += '<td>' + gameInfo.expiredTime + '</td>';
        msg += '<td>' + data.expired_time + '</td>';
        msg += '</tr>';
    }
    // 标题
    if (!shareInfo.title || data.title != shareInfo.title) {
        noChange = 0;
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">标题：</td>';
        msg += '<td>';
        if (shareInfo.title) {
            msg += shareInfo.title;
        }
        msg += '</td>';
        msg += '<td>' + data.title + '</td>';
        msg += '</tr>';
    }
    // 描述
    if (!shareInfo.desc || data.desc != shareInfo.desc) {
        noChange = 0;
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">描述：</td>';
        msg += '<td>';
        if (shareInfo.desc) {
            msg += shareInfo.desc;
        }
        msg += '</td>';
        msg += '<td>' + data.desc + '</td>';
        msg += '</tr>';
    }
    // 缩略图
    var showImgFlag = 0;
    if (undefined == shareInfo.image && '' != data.savename) {
        showImgFlag = 1;
    } else if (undefined != shareInfo.image && shareInfo.image != data.savename) {
        showImgFlag = 1;
    }
    if (showImgFlag) {
        if ('' == shareInfo.image || undefined == shareInfo.image) {
            var imgHtml = '';
        } else {
            var imgHtml = '<img src="' + imgUrlPrefix + shareInfo.image + '" style="box-shadow: 0px 0px 3px #565656; max-width: 100%;" />';
        }
        if ('' == data.savename) {
            var thumbImg = '';
        } else {
            var thumbImg = $('#uploadImgPreviewDiv').children().html();
        }
        noChange = 0;
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">缩略图：</td>';
        msg += '<td>' + imgHtml + '</td>';
        msg += '<td>' + thumbImg + '</td>';
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
                $.loading.show("saveLoading");

                $.ajax({
                    url: "/Gameconf/ajaxSaveRoomConf",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('saveLoading');
                        if (0 == data.code) {
                            $.zmsg.success();
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('saveLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
}

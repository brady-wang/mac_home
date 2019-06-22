
$(function() {
    'use strict';

    // 刷新出地区树
    var placeTree = [];
    // 第一级
    for (var i in tree) {
        placeTree[i] = {
            text: tree[i].placeName,
            href: '/Gameconf/opShareCont?firstId=' + tree[i].firstID + '&placeId=' + tree[i].placeID,
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
                    href: '/Gameconf/opShareCont?firstId=' + t1[j].firstID + '&placeId=' + t1[j].placeID,
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
                            href: '/Gameconf/opShareCont?firstId=' + t2[k].firstID + '&placeId=' + t2[k].placeID,
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

    // 缩略图 tip
    $(".tiptip").tipTip({
        maxWidth: "auto",
        delay: 0
    });

    /**************************************** 大厅分享（无奖励） ****************************************/

    // init
    if (conf[1]) {
        confParamInit('fhall', conf[1]);
    }

    $('#fhallForm').find(':radio[name=share_type]').on('change', function() {
        shareTypeChangeEV('fhall');
    });

    // 缩略图上传插件安装
    thumbImgUploadSetup('fhall');

    // 背景图上传插件安装
    bgImgUploadSetup('fhall');

    // 添加提交
    $('#fhallAddBtn').on('click', function() {
        submitAddConf(1, 'fhall');
    });

    // 修改提交
    $('#fhallEditBtn').on('click', function() {
        submitEditConf(1, 'fhall');
    });

    /**************************************** 大厅分享（有奖励） ****************************************/

    // init
    if (conf[2]) {
        confParamInit('ahall', conf[2]);
    }

    $('#ahallForm').find(':radio[name=share_type]').on('change', function() {
        shareTypeChangeEV('ahall');
    });

    // 缩略图上传插件安装
    thumbImgUploadSetup('ahall');

    // 背景图上传插件安装
    bgImgUploadSetup('ahall');

    // 添加提交
    $('#ahallAddBtn').on('click', function() {
        submitAddConf(2, 'ahall');
    });

    // 修改提交
    $('#ahallEditBtn').on('click', function() {
        submitEditConf(2, 'ahall');
    });

    /**************************************** 领取钻石 ****************************************/

    // init
    if (conf[3]) {
        confParamInit('diamond', conf[3]);
    }

    $('#diamondForm').find(':radio[name=share_type]').on('change', function() {
        shareTypeChangeEV('diamond');
    });

    // 缩略图上传插件安装
    thumbImgUploadSetup('diamond');

    // 背景图上传插件安装
    bgImgUploadSetup('diamond');

    // 添加提交
    $('#diamondAddBtn').on('click', function() {
        submitAddConf(3, 'diamond');
    });

    // 修改提交
    $('#diamondEditBtn').on('click', function() {
        submitEditConf(3, 'diamond');
    });

    /**************************************** 俱乐部分享 ****************************************/

    // init
    if (conf[4]) {
        confParamInit('club', conf[4]);
    }

    // 添加提交
    $('#clubAddBtn').on('click', function() {
        submitAddConf(4, 'club');
    });

    // 修改提交
    $('#clubEditBtn').on('click', function() {
        submitEditConf(4, 'club');
    });
});

// 配置参数初始化
function confParamInit(ab, obj) {

    if (1 == obj.share_type) {
        // 标题
        $('#' + ab + 'Title').val(obj.title);
        // 描述
        $('#' + ab + 'Desc').val(obj.desc);
        // 缩略图
        $('#' + ab + 'ImgBtn').removeClass('btn-success').addClass('btn-primary').data('tag', 'edit');
        $('#' + ab + 'ImgBadge').empty().append(
            $('<span/>').addClass("glyphicon glyphicon-edit")
        );
        $('#' + ab + 'ImgPreviewDiv').children().append(
            $('<img/>').css("box-shadow", "0px 0px 3px #565656").css('max-width', '100%').attr({
                id: ab + 'Img',
                src: imgUrlPrefix + obj.image
            }).data('savename', '')
        );
        $('#' + ab + 'ImgPreviewDiv').show();
    } else if (2 == obj.share_type) {
        // 分享方式切换成系统分享
        $('#' + ab + 'Form').find(':radio[name=share_type][value=2]').prop('checked', true);
        shareTypeChangeEV(ab);

        // 分享语句
        $('#' + ab + 'Cont').val(obj.desc);
        // 二维码地址
        $('#' + ab + 'Address').val(obj.address);
        // x锚点
        $('#' + ab + 'QrcodeX').val(obj.qrcode_x / 1000);
        // y锚点
        $('#' + ab + 'QrcodeY').val(obj.qrcode_y / 1000);
        // 背景图
        $('#' + ab + 'BgBtn').removeClass('btn-success').addClass('btn-primary').data('tag', 'edit');
        $('#' + ab + 'BgBadge').empty().append(
            $('<span/>').addClass("glyphicon glyphicon-edit")
        );
        $('#' + ab + 'BgPreviewDiv').children().append(
            $('<img/>').css("box-shadow", "0px 0px 3px #565656").css('max-width', '100%').attr({
                id: ab + 'Bg',
                src: imgUrlPrefix + obj.image
            }).data('savename', '')
        );
        $('#' + ab + 'BgPreviewDiv').show();
    }
}

// 分享方式切换 event
function shareTypeChangeEV(ab) {
    var type = $('#' + ab + 'Form').find(':radio[name=share_type]:checked').val();

    $('#' + ab + 'DymcFS').hide();
    $('#' + ab + 'SysFS').hide();

    if (1 == type) {
        $('#' + ab + 'DymcFS').show();
    } else if (2 == type) {
        $('#' + ab + 'SysFS').show();
    }
}

// 安装缩略图上传插件
function thumbImgUploadSetup(ab) {

    $('#' + ab + 'ImgInput').fileupload({
        url: "/Gameconf/ajaxShareUploadThumbImg"
    }).on('fileuploadstart', function (e, data) {
        // 上传 input,提交input disabled
        $('#' + ab + 'ImgInput').prop('disabled', true);
        $('#' + ab + 'ImgBtn').prop('disabled', true);
        // 图片上传按钮切换为进度样式
        $('#' + ab + 'ImgBadge').empty().append(
            $('<span/>').text("0%")
        );
    }).on('fileuploadprogress', function (e, data) {
        // 更新进度值
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#' + ab + 'ImgBadge').children().text(progress + "%");
    }).on('fileuploadalways', function(e, data) {
        // 上传 input enabled
        $('#' + ab + 'ImgInput').prop('disabled', false);
        $('#' + ab + 'ImgBtn').prop('disabled', false);

        if (0 == data.result.code) {
            // 先隐藏预览框
            $('#' + ab + 'ImgPreviewDiv').fadeOut("fast", function() {
                // 先清掉旧图片，再添加新图片并显示出来
                $('#' + ab + 'ImgPreviewDiv').children().empty().append(
                    $('<img/>').css("box-shadow", "0px 0px 3px #565656").css('max-width', '100%').attr({
                        id: ab + 'Img',
                        src: data.result.data.imgUrl
                    }).data('savename', data.result.data.saveName)
                );
                $('#' + ab + 'ImgPreviewDiv').fadeIn("slow");
            });

            // 添加后将图片上传按钮的样式改为修改样式
            setTimeout(function() {
                $('#' + ab + 'ImgBtn').fadeOut('slow', function() {
                    if ("add" == $('#' + ab + 'ImgBtn').data('tag')) {
                        $('#' + ab + 'ImgBtn').removeClass('btn-success').addClass('btn-primary').data('tag', 'edit');
                    }
                    $('#' + ab + 'ImgBadge').empty().append(
                        $('<span/>').addClass("glyphicon glyphicon-edit")
                    );
                    $('#' + ab + 'ImgBtn').fadeIn('slow');
                });
            }, 1000);
        } else {
            // 上传出错，将按钮Badge还原
            if ("add" == $('#' + ab + 'ImgBtn').data('tag')) {
                $('#' + ab + 'ImgBadge').empty().append(
                    $('<span/>').addClass("glyphicon glyphicon-arrow-up")
                );
            } else {
                $('#' + ab + 'ImgBadge').empty().append(
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

// 安装背景图上传插件
function bgImgUploadSetup(ab) {

    $('#' + ab + 'BgInput').fileupload({
        url: "/Gameconf/ajaxShareUploadBgImg"
    }).on('fileuploadstart', function (e, data) {
        // 上传 input,提交input disabled
        $('#' + ab + 'BgInput').prop('disabled', true);
        $('#' + ab + 'BgBtn').prop('disabled', true);
        // 图片上传按钮切换为进度样式
        $('#' + ab + 'BgBadge').empty().append(
            $('<span/>').text("0%")
        );
    }).on('fileuploadprogress', function (e, data) {
        // 更新进度值
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#' + ab + 'BgBadge').children().text(progress + "%");
    }).on('fileuploadalways', function(e, data) {
        // 上传 input enabled
        $('#' + ab + 'BgInput').prop('disabled', false);
        $('#' + ab + 'BgBtn').prop('disabled', false);

        if (0 == data.result.code) {
            // 先隐藏预览框
            $('#' + ab + 'BgPreviewDiv').fadeOut("fast", function() {
                // 先清掉旧图片，再添加新图片并显示出来
                $('#' + ab + 'BgPreviewDiv').children().empty().append(
                    $('<img/>').css("box-shadow", "0px 0px 3px #565656").css('max-width', '100%').attr({
                        id: ab + 'Bg',
                        src: data.result.data.imgUrl
                    }).data('savename', data.result.data.saveName)
                );
                $('#' + ab + 'BgPreviewDiv').fadeIn("slow");
            });

            // 添加后将图片上传按钮的样式改为修改样式
            setTimeout(function() {
                $('#' + ab + 'BgBtn').fadeOut('slow', function() {
                    if ("add" == $('#' + ab + 'BgBtn').data('tag')) {
                        $('#' + ab + 'BgBtn').removeClass('btn-success').addClass('btn-primary').data('tag', 'edit');
                    }
                    $('#' + ab + 'BgBadge').empty().append(
                        $('<span/>').addClass("glyphicon glyphicon-edit")
                    );
                    $('#' + ab + 'BgBtn').fadeIn('slow');
                });
            }, 1000);
        } else {
            // 上传出错，将按钮Badge还原
            if ("add" == $('#' + ab + 'BgBtn').data('tag')) {
                $('#' + ab + 'BgBadge').empty().append(
                    $('<span/>').addClass("glyphicon glyphicon-arrow-up")
                );
            } else {
                $('#' + ab + 'BgBadge').empty().append(
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

// 点击复制事件
function clickCopyBtn() {

    $('#copyBtn').removeClass("btn-operate").addClass("btn-success").blur();
    $('#pasteBtn').hide();

    var data = {
        place_id: curPlaceId,
        page_head: pageHead
    };

    $.ajax({
        url: "/Gameconf/ajaxShareContSetCopyCookie",
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
                    url: "/Gameconf/ajaxPasteShareCont",
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
function shareValidatorForHallDiamond(data)
{
    if (1 == data.share_type) {
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
        // 缩略图校验
        if (undefined === data.savename) {
            $.zmsg.error('请上传缩略图');
            return false;
        }
    } else if (2 == data.share_type) {
        // 分享语句校验
        if ("" == data.cont) {
            $.zmsg.error('请输入分享语句');
            return false;
        }
        // x锚点校验
        if ("" == data.qrcode_x) {
            $.zmsg.error('请输入x锚点');
            return false;
        } else if (!$.tool.isRealNum(data.qrcode_x) || data.qrcode_x < 0 || data.qrcode_x > 1) {
            $.zmsg.error('x锚点必须是0至1之间的数字');
            return false;
        }
        // y锚点校验
        if ("" == data.qrcode_y) {
            $.zmsg.error('请输入y锚点');
            return false;
        } else if (!$.tool.isRealNum(data.qrcode_y) || data.qrcode_y < 0 || data.qrcode_y > 1) {
            $.zmsg.error('y锚点必须是0至1之间的数字');
            return false;
        }
        // 背景图校验
        if (undefined === data.savename) {
            $.zmsg.error('请上传背景图');
            return false;
        }
    }

    return true
}

// 俱乐部提交前参数校验
function shareValidatorForClub(data)
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

    if (1 == data.share_type) {
        // 缩略图
        data.savename = $('#' + ab + 'Img').data('savename');
    } else if (2 == data.share_type) {
        // 背景图
        data.savename = $('#' + ab + 'Bg').data('savename');
    }

    if ('1' == src || '2' == src || '3' == src) {
        if (true !== shareValidatorForHallDiamond(data)) {
            return false;
        }
    } else if ('4' == src) {
        if (true !== shareValidatorForClub(data)) {
            return false;
        }
    }

    var msg = '<table class="table" style="width: 80%; margin: auto;">';
    msg += '<tr><td class="text-success">分享功能： </td><td class="text-danger">' + sourceMap[src] + '</td></tr>';
    msg += '<tr><td class="text-success">分享方式： </td><td class="text-danger">' + shareTypeMap[data.share_type] + '</td></tr>';
    if (1 == data.share_type) {
        msg += '<tr><td class="text-success">标题： </td><td class="text-danger">' + data.title + '</td></tr>';
        msg += '<tr><td class="text-success">描述： </td><td class="text-danger">' + data.desc + '</td></tr>';
        if (undefined != data.savename) {
            msg += '<tr><td class="text-success">缩略图： </td><td>';
            msg += $('#' + ab + 'ImgPreviewDiv').children().html();
        }
        msg += '</td></tr>';
    } else if (2 == data.share_type) {
        msg += '<tr><td class="text-success">分享语句： </td><td class="text-danger">' + data.cont + '</td></tr>';
        msg += '<tr><td class="text-success">二维码地址： </td><td class="text-danger">' + data.address + '</td></tr>';
        msg += '<tr><td class="text-success">二维码x锚点： </td><td class="text-danger">' + data.qrcode_x + '</td></tr>';
        msg += '<tr><td class="text-success">二维码y锚点： </td><td class="text-danger">' + data.qrcode_y + '</td></tr>';
        msg += '<tr><td class="text-success">背景图： </td><td class="fixed-width-60p">';
        msg += $('#' + ab + 'BgPreviewDiv').children().html();
        msg += '</td></tr>';
    }
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
                    url: "/Gameconf/ajaxAddShareCont",
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

    if (1 == data.share_type) {
        // 缩略图
        data.savename = $('#' + ab + 'Img').data('savename');
    } else if (2 == data.share_type) {
        // 背景图
        data.savename = $('#' + ab + 'Bg').data('savename');
    }

    if ('1' == src || '2' == src || '3' == src) {
        if (true !== shareValidatorForHallDiamond(data)) {
            return false;
        }
    } else if ('4' == src) {
        if (true !== shareValidatorForClub(data)) {
            return false;
        }
    }

    // 如果分享方式有更改，图片要重新传
    if (data.share_type != cfObj.share_type) {
        if (undefined === data.savename) {
            $.zmsg.error('请上传图片');
            return false;
        }
    }

    var noChange = 1;

    // 核对对话框表格内容
    var msg = '<p class="text-center text-danger" style=\'padding: 0;\'>' + sourceMap[src] + '</p>';
    msg += '<table class="table" style="width: 80%; margin: auto;word-break: break-all;">';
    msg += '<thead><tr><th>属性</th><th>原配置</th><th>现配置</th></tr></thead>';
    msg += '<tbody>';
    // 分享方式
    if (data.share_type != cfObj.share_type) {
        noChange = 0;
        msg += '<tr class="text-danger">';
        msg += '<td class="text-success">分享方式：</td>';
        msg += '<td>' + shareTypeMap[cfObj.share_type] + '</td>';
        msg += '<td>' + shareTypeMap[data.share_type] + '</td>';
        msg += '</tr>';
    }
    if (1 == data.share_type) {
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
        // 缩略图
        if (undefined != data.savename && '' != data.savename) {
            if (data.share_type != cfObj.share_type) {
                var imgHtml = '';
            } else {
                var imgHtml = '<img src="' + imgUrlPrefix + cfObj.image + '" style="box-shadow: 0px 0px 3px #565656; max-width: 100%;" />';
            }
            noChange = 0;
            msg += '<tr class="text-danger">';
            msg += '<td class="text-success">缩略图：</td>';
            msg += '<td>' + imgHtml + '</td>';
            msg += '<td>' + $('#' + ab + 'ImgPreviewDiv').children().html() + '</td>';
            msg += '</tr>';
        }
    } else if (2 == data.share_type) {
        // 分享语句
        if (data.cont != cfObj.desc) {
            noChange = 0;
            msg += '<tr class="text-danger">';
            msg += '<td class="text-success">分享语句：</td>';
            msg += '<td>' + cfObj.desc + '</td>';
            msg += '<td>' + data.cont + '</td>';
            msg += '</tr>';
        }
        // 二维码地址
        if (data.address != cfObj.address) {
            noChange = 0;
            msg += '<tr class="text-danger">';
            msg += '<td class="text-success">二维码地址：</td>';
            msg += '<td>' + cfObj.address + '</td>';
            msg += '<td>' + data.address + '</td>';
            msg += '</tr>';
        }
        // 二维码x锚点
        if (data.qrcode_x != (cfObj.qrcode_x / 1000)) {
            noChange = 0;
            msg += '<tr class="text-danger">';
            msg += '<td class="text-success">二维码x锚点：</td>';
            msg += '<td>' + (cfObj.qrcode_x / 1000) + '</td>';
            msg += '<td>' + data.qrcode_x + '</td>';
            msg += '</tr>';
        }
        // 二维码y锚点
        if (data.qrcode_y != (cfObj.qrcode_y / 1000)) {
            noChange = 0;
            msg += '<tr class="text-danger">';
            msg += '<td class="text-success">二维码y锚点：</td>';
            msg += '<td>' + (cfObj.qrcode_y / 1000) + '</td>';
            msg += '<td>' + data.qrcode_y + '</td>';
            msg += '</tr>';
        }
        // 背景图
        if ('' != data.savename) {
            if (data.share_type != cfObj.share_type) {
                var imgHtml = '';
            } else {
                var imgHtml = '<img src="' + imgUrlPrefix + cfObj.image + '" style="box-shadow: 0px 0px 3px #565656; max-width: 100%;" />';
            }
            noChange = 0;
            msg += '<tr class="text-danger">';
            msg += '<td class="text-success">背景图：</td>';
            msg += '<td class="fixed-width-200">' + imgHtml + '</td>';
            msg += '<td class="fixed-width-200">' + $('#' + ab + 'BgPreviewDiv').children().html() + '</td>';
            msg += '</tr>';
        }
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
                    url: "/Gameconf/ajaxEditShareCont",
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
                    url: "/Gameconf/ajaxDelShareCont",
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

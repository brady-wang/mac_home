$(function() {
    // 生成地址树
    'use strict';
    var placeTree = [];
    // 第一级
    for (var i in topTree) {
        placeTree[i] = {
            text: topTree[i].placeName,
            href: '/Gameconf/opLoginAd?firstId=' + topTree[i].firstID + '&placeId=' + topTree[i].placeID,
        }
        // 添加第一级icon
        if (curPlaceId == topTree[i].placeID){
            placeTree[i].icon = 'glyphicon glyphicon-edit text-danger';
        }//console.log(validPlace[topTree[i].placeID].logadv);return false;
        // 如果该地区没有任何配置，显示为灰色
        if (validPlace[topTree[i].placeID] === undefined || validPlace[topTree[i].placeID].logadv == '') {
            placeTree[i].color = '#777';
        }
        // 第二级
        if ($.tool.arrayCount(topTree[i].node) > 0) {
            var tree = topTree[i].node;
            placeTree[i].nodes = [];
            for (var j in tree) {
                placeTree[i].nodes[j] = {
                    text: tree[j].placeName,
                    href: '/Gameconf/opLoginAd?firstId=' + tree[j].firstID + '&placeId=' + tree[j].placeID,
                }
                // 添加第二级icon
                if (curPlaceId == tree[j].placeID) {
                    placeTree[i].nodes[j].icon = 'glyphicon glyphicon-edit text-danger';
                }
                // 如果该地区没有任何配置，显示为灰色
                if (validPlace[tree[j].placeID] === undefined || validPlace[tree[j].placeID].logadv == '') {
                    placeTree[i].nodes[j].color = '#777';
                }
                // 第三级
                if ($.tool.arrayCount(tree[j].node) > 0) {
                    var subTree = tree[j].node;
                    placeTree[i].nodes[j].nodes = [];
                    for (var k in subTree) {
                        placeTree[i].nodes[j].nodes[k] = {
                            text: subTree[k].placeName,
                            href: '/Gameconf/opLoginAd?firstId=' + subTree[k].firstID + '&placeId=' + subTree[k].placeID,
                        }
                        // 当前编辑项加一个icon
                        if (curPlaceId == subTree[k].placeID) {
                            placeTree[i].nodes[j].nodes[k].icon = 'glyphicon glyphicon-edit text-danger';
                            // 地区树默认只展示两级，若第三级地区即是当前地区，则将其二级展开来
                            placeTree[i].nodes[j].state = {expanded: true};
                        }
                        // 如果该地区没有任何配置，显示为灰色
                        if (validPlace[subTree[k].placeID] === undefined || validPlace[subTree[k].placeID].logadv == '') {
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

    if (conflist.hasOwnProperty(curPlaceId)) {
        var conf = conflist[curPlaceId];
        $("#tbLogAdv").empty();
        if (conf.logadv) {
            for (var i = 0; i < conf.logadv.length; i++) {
                addLogAdvLine(conf.logadv[i]);
            }
        }
    }

    // 当关闭面板开关时才显示是否读取上级选项
    $("#logadv_open").click(function() {
        $('#parentSwitch').hide();
    })
    $("#logadv_close").click(function() {
        $('#parentSwitch').show();
    })
});

var g_logadv_idx = 1;
function addLogAdvLine(cont) {
    var td = $("#tbLogAdv");
    var lines = td.children('tr').size();
    if (lines >= 3) {
        $('#alertLogAdvCont').text('广告不能多于3个');
        return;
    }
    if (!cont) {
        cont = {};
        cont.title = '';
        cont.wx = '';
        cont.pic = '';
    }
    if (td) {
        var html = '<tr><td><input class="form-control" type="text" name="logadvtitle" value="'+texttohtml(cont.title)+'" /></td>'+
            '<td><input class="form-control" type="text" name="logadvwx" value="'+cont.wx+'" /></td>'+
            '<td><div style="display:inline-block;">'+
            '<span class="btn btn-success fileinput-button" id="edtModImgBtnLogAdv'+g_logadv_idx+'" data-tag="add">'+
            '<span>上传图片</span>'+
            '<span class="badge" id="edtModImgBadgeLogAdv'+g_logadv_idx+'">'+
            '<span class="glyphicon glyphicon-arrow-up"></span>'+
            '</span>'+
            '<input type="file" name="image" id="edtModImgInputLogAdv'+g_logadv_idx+'" />'+
            '</span>'+
            '<input name="logadvpic" id="edtModImagesLogAdv'+g_logadv_idx+'" type="hidden"  />'+
            '</div><div style="display:inline-block;" id="edtModImgPreviewDivLogAdv'+g_logadv_idx+'" hidden><div style="display:inline-block;"></div></div></td>'+
            '<td><button class="btn btn-danger" type="button" onclick="delLogAdvLine(this);">删除</button></div></td></tr>';

        td.append(html);
        if (cont.pic) {
            var imgHtml = '<span class="imgListItem">' +//<i class="fa fa-times imgListItem-close"></i>
                '<img class="addGoodsImg" src="' +  cont.pic + '?t=' + new Date() + '" style="box-shadow: rgb(86, 86, 86) 0px 0px 3px; width: 100px; max-width: 100%;">' +
                '</span>';
            $('#edtModImgPreviewDivLogAdv'+g_logadv_idx).children("div").html(imgHtml);
            $('#edtModImagesLogAdv'+g_logadv_idx).val(cont.pic);
            $('#edtModImgPreviewDivLogAdv'+g_logadv_idx).fadeIn("slow");
        }
        imgUploadSetup('edtMod', 'LogAdv'+g_logadv_idx);
        g_logadv_idx++;
    }
}
//字符转换
function texttohtml(text) {
    if (text)
        return text.replace(/&/gi, '&amp;').replace(/\s/gi, "&nbsp;").replace(/>/gi, '&gt;').replace(/</gi, '&lt;').replace(/\"/gi, '&quot;');
    return text;
}

function imgUploadSetup(mod, suff) {
    $('#' + mod + 'ImgInput' + suff).fileupload({
        url: "/Gameconf/ajaxAdvUploadImages"
    }).on('fileuploadstart', function(e, data) {
        // 上传 input,提交input disabled
        $('#' + mod + 'ImgInput' + suff).prop('disabled', true);
        $('#' + mod + 'UpimgBtn').prop('disabled', true);
        // 图片上传按钮切换为进度样式
        $('#' + mod + 'ImgBadge' + suff).empty().append($('<span/>').text("0%"));
    }).on('fileuploadprogress', function(e, data) {
        // 更新进度值
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#' + mod + 'ImgBadge' + suff).children().text(progress + "%");
    }).on('fileuploadalways', function(e, data) {
        // 上传 input enabled
        $('#' + mod + 'ImgInput' + suff).prop('disabled', false);
        $('#' + mod + 'UpimgBtn').prop('disabled', false);

        if (0 == data.result.code) {
            // 先隐藏预览框
            $('#' + mod + 'ImgPreviewDiv' + suff).fadeOut("fast", function() {
                var imgHtml = '<span class="imgListItem">' +
                    //'<i class="fa fa-times imgListItem-close"></i>' +
                    '<img class="addGoodsImg" src="' + data.result.data.imgUrl + '?t=' + new Date() + '" style="box-shadow: rgb(86, 86, 86) 0px 0px 3px; width: 100px; max-width: 100%;">' +
                    '</span>';
                $('#' + mod + 'ImgPreviewDiv' + suff).children("div").html(imgHtml);
                $('#' + mod + 'Images' + suff).val(data.result.data.imgUrl);

                $('#' + mod + 'ImgPreviewDiv' + suff).fadeIn("slow");
            });

            // 添加后将图片上传按钮的样式改为修改样式
            setTimeout(function() {
                $('#' + mod + 'ImgBtn' + suff).fadeOut('slow', function() {
                    if ("add" == $('#' + mod + 'ImgBtn' + suff).data('tag')) {
                        $('#' + mod + 'ImgBtn' + suff).removeClass('btn-success').addClass('btn-primary').data('tag', 'edit');
                    }
                    $('#' + mod + 'ImgBadge' + suff).empty().append(
                        $('<span/>').addClass("glyphicon glyphicon-edit")
                    );
                    $('#' + mod + 'ImgBtn' + suff).fadeIn('slow');
                });
            }, 1000);
        } else {
            $('#' + mod).modal('hide');
            // 弹出错误信息
            if (data.result.msg) {
                $.zmsg.errorShowModal(data.result.msg, mod);
            } else {
                $.zmsg.fatalShowModal(data.result, mod);
            }
        }
    });
}

// 保存登录广告配置
function submitLogAdv() {
    $('#edtMod').modal('hide');
    var data = {};
    data.confid = curPlaceId;
    data.switch = $("input[name=switch_logadv]:checked").val();
    data.logadv = [];

    if (data.switch == 0) {
        data.parentSwitch = $("input[name=switch_read_parent]:checked").val();
    } else {
        data.parentSwitch = 0;
    }
    if ($('#parentSwitch').length == 0) {
        data.parentSwitch = 0;
    }

    var count = $('input[name=logadvpic]').size();
    for (var i = 0; i < count; i++) {
        var obj = {};
        obj.title = $('input[name=logadvtitle]').eq(i).val();
        if (obj.title == '') {
            $.zmsg.errorShowModal('必须输入广告标题', 'edtMod');
            return false;
        }
        obj.wx = $('input[name=logadvwx]').eq(i).val();
        if (obj.wx == '') {
            $.zmsg.errorShowModal('广告微信号不能为空', 'edtMod');
            return false;
        }
        if (!wxreg.test(obj.wx)) {
            $.zmsg.errorShowModal('必须输入正确的微信号', 'edtMod');
            return false;
        }
        obj.pic = $('input[name=logadvpic]').eq(i).val();
        if (obj.pic == '' || typeof (obj.pic) == 'undefined') {
            $.zmsg.errorShowModal('有图片未上传，请上传', 'edtMod');
            return false;
        }
        data.logadv.push(obj);
    }
    $.ajax({
        url: "/Gameconf/ajaxSubmitLogAdv",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('delMsgLoading');
            var curUrl = window.location.href;
            if (0 == data.code) {
                $.zmsg.success(curUrl);
            } else {
                $.zmsg.error(data.msg);
            }
        },
        error: function(data) {
            $.loading.hide('delMsgLoading');
            $.zmsg.fatal(data.responseText);
        }
    });
}


// 删除一条配置
function delLogAdvLine(obj) {
    var target = $(obj).closest('tr');
    if (target)
        target.remove();
}

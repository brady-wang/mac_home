$(document).ready(function() {
    $('#shade').hide();
    imgUploadSetup('LandpagePic');
    if (imageUrl) {
        var imgHtml = '<span class="imgListItem">' +
                        '<img class="addGoodsImg" src="' +  imageUrl + '?t=' + new Date() + '" style="box-shadow: rgb(86, 86, 86) 0px 0px 3px; width: 100px; max-width: 100%;height: 250px;">' +
                        '</span>';
        $('#ImgPreviewDivLandpagePic').children("div").html(imgHtml);
        $('#hiddenLandpagePic').val(imageUrl);
        $('#ImgPreviewDivLandpagePic').fadeIn("slow");
    } else {
        $('#ImgPreviewDivLandpagePic').children("div").html('');
        $('#hiddenLandpagePic').val('');
    }

    $('.addGoodsImg').click(function () {
        let src = $('.addGoodsImg').prop('src');
        if (src != '' && src != undefined && src != null) {
            $('#shade').fadeIn('fast');
        }
    })

    $('img').mouseover(function () {
        $(this).css("cursor", "pointer");
    })

    $('#shade').click(function () {
        $('#shade').fadeOut('fast');
    })

    // 生成地址树
    let placeTree = [];
    // 第一级
    for (var i in tree) {
        placeTree[i] = {
            text: tree[i].placeName,
            href: '/Gameconf/gameAppLandpage?placeId=' + tree[i].placeID,
        }
        // 当前编辑地区level添加编辑icon
        if (curPlaceId == tree[i].placeId) {
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
                    href: '/Gameconf/gameAppLandpage?placeId=' + t1[j].placeID,
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
                            href: '/Gameconf/gameAppLandpage?placeId=' + t2[k].placeID,
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
    $('#placeTreeDiv').treeview({
        data: placeTree,
        levels: 2,
        enableLinks: true,
        color: '#31708F',
        showBorder: false,
        selectedBackColor: '#FFF',
        selectedColor: '#337AB7'
    });
});
function saveLandpage() {
    var tipinfo = [];
    var data = $("#formLandpage").serializeObject();
    data.image_path = $("#hiddenLandpagePic").val();
    if (data.title == '') {
        $.zmsg.error('必须填写标题');
        return false;
    }
    tipinfo.push({title: '标题', value: data.title});
    if (data.image_path == '' || typeof (data.image_path) == 'undefined') {
        $.zmsg.error('图片必须上传');
        return false;
    }
    tipinfo.push({title: '图片', value: $('#ImgPreviewDivLandpagePic').children().html()});
    if (opeAndroid) {
        if (data.and_downlink == '') {
            $.zmsg.error('安卓下载地址必须填写');
            return false;
        }
        if (data.and_downlink.indexOf('http://') != 0 && data.and_downlink.indexOf('https://') != 0) {
            $.zmsg.error('安卓下载地址必须以http://或https://开头');
            return false;
        }
        tipinfo.push({title: '安卓下载地址', value: data.and_downlink});
    }
    if (opeIos) {
        if (data.ios_downlink == '') {
            $.zmsg.error('IOS下载地址必须填写');
            return false;
        }
        if (data.ios_downlink.indexOf('http://') != 0 && data.ios_downlink.indexOf('https://') != 0) {
            $.zmsg.error('IOS下载地址必须以http://或https://开头');
            return false;
        } 
        tipinfo.push({title: 'IOS下载地址', value: data.ios_downlink});
    }
    data.placeId = curPlaceId;
    tipinfo.push({title: '更新备注', value: '线上环境更新配置后,需5分钟左右生效'});
    var tip = '<table style="width: 80%; margin: auto;">';
    for (var idx = 0; idx < tipinfo.length; idx++) {
        tip += '<tr class="text-danger"><td class="text-success">'+tipinfo[idx].title+'</td><td>'+tipinfo[idx].value+'</td></tr>';
    }
    tip += '</table>';
    // 兼容长网址，否则会折行
    $.Zebra_Dialog(tip, {
        'title': '落地页配置',
        'animation_speed_show': 500,
        'center_buttons': true,
        'width': '1050',
        'type': '',
        'buttons': ['取消', '确定'],
        'onClose': function(caption) {
            if ('取消' == caption) {
            } else if ('确定' == caption) {
                $.loading.show("saveConfLoading");
                $.ajax({
                    url: "/Gameconf/ajaxSaveLandpage",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $.loading.hide('saveConfLoading');
                        if (0 == data.code) {
                            $.zmsg.success('/Gameconf/gameAppLandpage?placeId='+curPlaceId);
                        } else {
                            $.zmsg.error(data.msg);
                        }
                    },
                    error: function(data) {
                        $.loading.hide('saveConfLoading');
                        $.zmsg.fatal(data.responseText);
                    }
                });
            }
        }
    });
    return false;
}
function imgUploadSetup(suff) {
    $('#ImgInput' + suff).fileupload({
        url: "/Gameconf/ajaxLandpageUploadImages"
    }).on('fileuploadstart', function(e, data) {
        // 上传 input,提交input disabled
        $('#ImgInput' + suff).prop('disabled', true);
        $('#UpimgBtn').prop('disabled', true);
        // 图片上传按钮切换为进度样式
        $('#ImgBadge' + suff).empty().append($('<span/>').text("0%"));
    }).on('fileuploadprogress', function(e, data) {
        // 更新进度值
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#ImgBadge' + suff).children().text(progress + "%");
    }).on('fileuploadalways', function(e, data) {
        // 上传 input enabled
        $('#ImgInput' + suff).prop('disabled', false);
        $('#UpimgBtn').prop('disabled', false);

        if (0 == data.result.code) {
            // 先隐藏预览框
            $('#ImgPreviewDiv' + suff).fadeOut("fast", function() {
                var imgHtml = '<span class="imgListItem">' +
                        '<img class="addGoodsImg" src="' + data.result.data.imgUrl + '?t=' + new Date() + '" style="box-shadow: rgb(86, 86, 86) 0px 0px 3px; width: 100px; max-width: 100%;height: 250px;">' +
                        '</span>';
                $('#ImgPreviewDiv' + suff).children("div").html(imgHtml);
                $('#hidden' + suff).val(data.result.data.imgUrl);
                $('#ImgPreviewDiv' + suff).fadeIn("slow");
            });

            // 添加后将图片上传按钮的样式改为修改样式
            setTimeout(function() {
                $('#ImgBtn' + suff).fadeOut('slow', function() {
                    if ("add" == $('#ImgBtn' + suff).data('tag')) {
                        $('#ImgBtn' + suff).removeClass('btn-success').addClass('btn-primary').data('tag', 'edit');
                    }
                    $('#ImgBadge' + suff).empty().append(
                            $('<span/>').addClass("glyphicon glyphicon-edit")
                            );
                    $('#ImgBtn' + suff).fadeIn('slow');
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
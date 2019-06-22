$(function() {
    'use strict';

    // 增加子礼包
    $(".bagOptPlus").on("click", function(){

        var num = $(this).index();
        var html = '<a href="#" class="list-group-item optionDom" onclick="optionToggle(this)"><span class="glyphicon glyphicon-minus bagOptMinus">&nbsp;&nbsp;</span>子礼包<span>'+ (Number(num) + 1) +'</span></a>';
        $(this).before(html);

        var content = $(this).parents(".optionUl").parent();
        var cloneDom = content.find(".optionContent").first().clone();

        cloneDom.find(":input").val("");
        content.find(".optionContent").hide();
        content.append(cloneDom);

        $(this).prev().click();
        return false;
    });

    // 删除子礼包
    $(".optionUl").on("click", ".bagOptMinus", function(){

        var parent = $(this).parent();
        var siblings = $(this).parent().siblings();
        var parentUl = $(this).parents(".optionUl");

        // 移除当前DOM
        parentUl.siblings().eq(parent.index()).remove();
        parent.remove();

        // 重置子礼包数字
        siblings.each(function(k, v){
            $(v).find("span").last().text(Number($(v).index()) + 1);
        });

        // 重置焦点
        parentUl.find("a").first().click();
        return false;
    });
});

function optionToggle(obj) {

    $(obj).siblings().removeClass("active");
    $(obj).addClass("active");

    var content = $(obj).parent().siblings();
    content.hide();
    content.eq($(obj).index()).show();

}

function submitBagSave(add) {
    objName = add ? "#addModForm" : "#editModForm";
    var data = $(objName).serializeObject();

    // 开始 loading 遮盖
    $.loading.show("addUserLoading");

    $('#addMod').modal('hide');
    $.ajax({
        url: "/Activity/bagSave",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('addUserLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.error(data.msg);
                //$.zmsg.errorShowModal(data.msg, "addMod");
            }
        },
        error: function(data) {
            $.loading.hide('addUserLoading');
            $.zmsg.fatalShowModal(data.responseText, "addMod");
        }
    });
}

function setInfo(bagId) {
    $.ajax({
        url: "/Activity/getBagInfoById",
        type: "POST",
        data: {bagId: bagId},
        dataType: "json",
        success: function(data) {
            $.loading.hide('addUserLoading');

            var editDom = $('#editMod');
            var data = data.data;
            editDom.find('input[name=name]').val(data.bagInfo.name);
            editDom.find('input[name=limit0]').val(data.bagInfo.limit0 !== '0' ? data.bagInfo.limit0 : '');
            editDom.find('input[name=limit1]').val(data.bagInfo.limit1 !== '0' ? data.bagInfo.limit1 : '');
            editDom.find('input[name=limit2]').val(data.bagInfo.limit2 !== '0' ? data.bagInfo.limit2 : '');
            editDom.find('input[name=limit3]').val(data.bagInfo.limit3 !== '0' ? data.bagInfo.limit3 : '');
            editDom.find('select[name=luck]').val(data.bagInfo.luck);

            var html = '';
            var html1 = '';
            for(var i in data.bagInfo.data) {
                if (i == 0) {
                    html += '<a href="#" class="list-group-item optionDom" onclick="optionToggle(this)"><input name="id" type="hidden" class="optionDom" value="'+ data.bagInfo.id +'"><span class="glyphicon">&nbsp;&nbsp;&nbsp;</span>子礼包<span>1</span></a>';
                } else {
                    html += '<a href="#" class="list-group-item optionDom" onclick="optionToggle(this)"><span class="glyphicon glyphicon-minus bagOptMinus">&nbsp;&nbsp;</span>子礼包<span>'+ (Number(i)+1) +'</span></a>';
                }

                html1 += '<div class="col-sm-8 optionContent optionDom">\
                            <div class="form-group">\
                                <label class="col-sm-3 request">名称</label>\
                                <div class="col-sm-6 input-group">\
                                    <input class="form-control" type="text" name="data[name][]" value="'+ data.bagInfo.data[i]['name'] +'"/>\
                                </div>\
                            </div>\
                            <div class="form-group">\
                                <label class="col-sm-3 request">类型</label>\
                                <div class="col-sm-6 input-group">\
                                    <select class="form-control" name="data[type][]">\
                                        <option value="real" '+ (data.bagInfo.data[i]['type'] === 'real' ? 'selected' : '') +'>实体奖励</option>\
                                        <option value="yuanbao" '+ (data.bagInfo.data[i]['type'] === 'yuanbao' ? 'selected' : '') +'>元宝</option>\
                                        <option value="redPacket" '+ (data.bagInfo.data[i]['type'] === 'redPacket' ? 'selected' : '') +'>红包券</option>\
                                        <option value="thanks" '+ (data.bagInfo.data[i]['type'] === 'thanks' ? 'selected' : '') +'>谢谢参与</option>\
                                    </select>\
                                </div>\
                            </div>\
                            <div class="form-group">\
                                <label class="col-sm-3 request">数量</label>\
                                <div class="col-sm-6 input-group">\
                                    <input class="form-control" type="text" name="data[val][]" value="'+ data.bagInfo.data[i]['val'] +'"/>\
                                </div>\
                            </div>\
                            <div class="form-group">\
                                <label class="col-sm-3 request">概率</label>\
                                <div class="col-sm-6 input-group">\
                                    <input class="form-control" type="text" name="data[percent][]" value="'+ data.bagInfo.data[i]['percent'] +'" oninput = "value=value.replace(/\D/gi,"")"/>\
                                    <div class="input-group-addon">%</div>\
                                </div>\
                            </div>\
                            <div class="form-group">\
                                <label class="col-sm-3">页面显示</label>\
                                <div class="col-sm-6 input-group">\
                                    <input class="form-control" type="text" name="data[showPanel][]" value="'+ (typeof(data.bagInfo.data[i]['showPanel']) !== 'undefined' ? data.bagInfo.data[i]['showPanel'] : '')  +'"/>\
                                </div>\
                            </div>\
                        </div>';
            }

            editDom.find(".optionDom").remove();
            editDom.find(".optionUl").prepend(html);
            editDom.find(".optionUl").parent().append(html1);
            editDom.find(".optionUl a").first().click();
            editDom.modal();
        }
    });
}

function delInfo(bagId) {
    $.ajax({
        url: "/Activity/delBagById",
        type: "POST",
        data: {bagId: bagId},
        dataType: "json",
        success: function(data) {
            $.loading.hide('addUserLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.error('删除失败');
            }
        }
    });
}
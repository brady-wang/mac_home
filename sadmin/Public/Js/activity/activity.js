
$(function() {
    'use strict';

    // 玩法选择框
    var choose = $('#gameId').ui_choose();
    $.fn.bootstrapSwitch.defaults.size = 'small';
    $.fn.bootstrapSwitch.defaults.onText = '开启';
    $.fn.bootstrapSwitch.defaults.offText = '关闭';
    $(".actSwitch").bootstrapSwitch('state', parseInt($(".actSwitch").val()));

    $("input[name=etime], input[name=stime]").datetimepicker({
        language: 'zh-CN',
        autoclose: true,
        showSecond: true,
        format: 'yyyy-mm-dd hh:ii'
    });

    /*$('.confAct').on('click', function(e) {
        e.preventDefault();
        $.Zebra_Dialog('', {
            source: {'ajax': '/Activity/actDetail'},
            buttons: false,
            width: 1200,
            vcenter_short_message: false,
            title:  '活动数据配置',
            buttons:
                [
                    {caption: '保存', callback: function(rs) {
                            var index = $('.act-conf .active').index();
                            var data = $(".tab-content").eq(index).find("form").serializeObject();
                            data.savename = $('#fhallBg').data('savename');
                            console.log(data);
                        }
                    },
                    {caption: '取消'}
                ]

        });
    });

    $('.analysis').on('click', function(e) {
        e.preventDefault();
        $.Zebra_Dialog('', {
            source: {'ajax': '/Activity/actAnalysis'},
            buttons: false,
            width: 1200,
            vcenter_short_message: false,
            title:  '活动数据分析'
        });
    });

    $('.testConf').on('click', function(e) {
        e.preventDefault();
        $.Zebra_Dialog('', {
            source: {'ajax': '/Activity/setTestParams'},
            buttons: false,
            width: 1200,
            vcenter_short_message: false,
            title:  '活动数据分析'
        });
    });*/

});

function submitAddAct() {

    var data = $("#addModForm").serializeObject();

    // 开始 loading 遮盖
    $.loading.show("addUserLoading");

    $('#addMod').modal('hide');
    $.ajax({
        url: "/Activity/actSave",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('addUserLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "addMod");
            }
        },
        error: function(data) {
            $.loading.hide('addUserLoading');
            $.zmsg.fatalShowModal(data.responseText, "addMod");
        }
    });
}

function submitSetAct() {
    var data = $("#setModForm").serializeObject();
    data.act_switch = typeof(data.act_switch) != 'undefined' ? 1 : 0;

    // 开始 loading 遮盖
    $.loading.show("addUserLoading");

    $('#setMod').modal('hide');
    $.ajax({
        url: "/Activity/actSet",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('addUserLoading');
            if (0 == data.code) {
                $.zmsg.success();
            } else {
                $.zmsg.errorShowModal(data.msg, "setMod");
            }
        },
        error: function(data) {
            $.loading.hide('addUserLoading');
            $.zmsg.fatalShowModal(data.responseText, "setMod");
        }
    });
}

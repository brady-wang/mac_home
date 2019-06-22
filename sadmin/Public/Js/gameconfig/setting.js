var changeItem = {} ;//被修改的对象
changeItem.common = {} ;
changeItem.AA = {} ;

$(function() {
    'use strict';
    //大赢家数据同步显示
    $('.commonInput').keyup(function(){
        var code = $(this).attr('data-item') ;
        $('#ownerInput'+code).val(  $(this).val()  );
    });

    //点击删除游戏配置
    $('.setting-delete').click(function(){
        var setting = $(this).parent().parent();

        $.Zebra_Dialog("确定删除该配置吗？", {
            animation_speed_show: 500,
            center_buttons: true,
            type: 'question',
            buttons :[
                    {caption: '确定', callback: function() {
                            $(setting).remove();
                        }},
                    {caption: '取消', callback: function() { }}
                ]
        });
    });
    //检测文本框是否修改
    $('.settingInput').blur(function(){
        console.log($(this).val());
        var oldValue = $(this).attr('data-oldvalue'); 
        var itemKey =  $(this).attr('name'); 
        var itemtype = $(this).attr('data-itemtype'); 
        var itemId = $(this).attr('id'); 
        var itemUserNum = $(this).attr('data-gameuser'); 
        var itemGameNum = $(this).attr('data-item'); 

        if( oldValue !=  $(this).val() ){
            if(itemtype == 'common'){
                changeItem.common[itemId] = {oldValue:oldValue,newValue:$(this).val(),itemGameNum :itemGameNum ,itemUserNum:itemUserNum} ;
            }else if(itemtype == 'AA'){
                changeItem.AA[itemId] = {oldValue:oldValue,newValue:$(this).val(),itemGameNum :itemGameNum ,itemUserNum:itemUserNum} ;
            }
        }
    });

    //保存房费修改
    $('#saveSetting').click(function(){
        var setting = {}, item = [],code ='' ;

        if(PLACEID == '' || typeof(PLACEID) == 'undefined' || GAMEIDS == '' || typeof(GAMEIDS) == 'undefined'){
            $.zmsg.errorShowModal('页面placeId或Game玩法参数不正确');
            return false;
        }

        $('.settingInput').each(function(){
            var value = $(this).val();
            if(value == '' || typeof(value) == 'undefined' ){
                $.zmsg.errorShowModal('设置数值不能为空');
                return false;
            }

            if(isNaN(value)){
                $.zmsg.errorShowModal('设置数值只能填写数字');
                return false;
            }

            code = $(this).attr('data-item');
            if( !isNaN(code) ){
                item.push(code);
            }
        });
        $.each(item,function(index,value){
            var common = [], AA = [],item = {};
            common[2] = $('#commonInput'+value+'-2').val();
            common[3] = $('#commonInput'+value+'-3').val();
            common[4] = $('#commonInput'+value+'-4').val();
            AA[2] = $('#aaInput'+value+'-2').val();
            AA[3] = $('#aaInput'+value+'-3').val();
            AA[4] = $('#aaInput'+value+'-4').val();

            item.common = common;
            item.AA = AA;
            setting[value] = item ;
        });

        if(JSON.stringify(changeItem.common) == '{}' && JSON.stringify(changeItem.AA) =='{}'  ) {
            $.zmsg.errorShowModal('没有任何修改');
                return false;
        }

        var url = '/Gameconf/game/third/savegamefeed';
        // 开始 loading 遮盖
        $.loading.show("setting");
        $.post(url,{setting : setting, placeid :PLACEID,gid:GAMEIDS},function( result ){
            $.loading.hide('setting');
            if (0 == result.code) {
                $.zmsg.success();
            } else {
                if (result.data != '' && typeof (result.data) != 'undefined') {
                    $.zmsg.error(result.msg + ',' + result.data);
                } else {
                    $.zmsg.error(result.msg);
                }
            }
        });
    });

    //点击添加房费配置
    $('#setting-add').click(function(){
        var setting = [], item = [],code ='' ;
        var status = false ;

        if(PLACEID == '' || typeof(PLACEID) == 'undefined' || GAMEIDS == '' || typeof(GAMEIDS) == 'undefined'){
            $.zmsg.errorShowModal('页面placeId或Game玩法参数不正确');
            return false;
        }

        $('.settingInputNew').each(function(){
            var value = $(this).val();
            if(value == '' || typeof(value) == 'undefined' ){
                $.zmsg.errorShowModal('设置数值不能为空');
                status = true ;
                return false;
            }

            if(isNaN(value)){
                $.zmsg.errorShowModal('设置数值只能填写数字');
                status = true ;
                return false;
            }
        });
        if(status ){
            return false;
        }
        code = $('#codeNew').val();

        var common = [], AA = [],item = {};
            common[2] = $('#commonInputNew-2').val();
            common[3] = $('#commonInputNew-3').val();
            common[4] = $('#commonInputNew-4').val();
            AA[2] = $('#aaInputNew-2').val();
            AA[3] = $('#aaInputNew-3').val();
            AA[4] = $('#aaInputNew-4').val();

            item.common = common;
            item.AA = AA;
            setting[ code ] = item;
        var url = '/Gameconf/game/third/addgamefeed';
        // 开始 loading 遮盖
        $.loading.show("setting");
        $.post(url,{setting :setting ,placeid :PLACEID,gid :GAMEIDS },function(result){
            $.loading.hide('setting');
            if (0 == result.code) {
                $.zmsg.success();
            } else {
                if (result.data != '' && typeof (result.data) != 'undefined') {
                    $.zmsg.error(result.msg + ',' + result.data);
                } else {
                    $.zmsg.error(result.msg);
                }
            }
        })
    });

    //角色配置，用户初始化
    $("#saveGameConfig").click(function(){
        var data = $("#gameConfigForm").serializeObject();
        var isChange = 0 ;
        console.log(data.initdiamond);
        if(gameConfig.initdiamond === false && gameConfig.initgold === false ){
            $.zmsg.error("后端未查询到相关配置，不能修改");
            return false ;
        }

        // 核对对话框表格内容
        var msg = '';
        msg += '<table class="table" style="width: 80%; margin: auto;">';
        msg += '<thead><tr><th>属性</th><th>原配置</th><th>现配置</th></tr></thead>';
        msg += '<tbody>';

        // 初始化钻石
        if(data.initdiamond != gameConfig.initdiamond){
            isChange = 1;
            msg += '<tr class="text-danger">';
            msg += '<td class="text-success">初始化钻石：</td>';
            msg += '<td>' + gameConfig.initdiamond + '</td>';
            msg += '<td>' + data.initdiamond + '</td>';
            msg += '</tr>';
        }

        data.initgold = typeof(data.initgold ) =='undefined' ? false : data.initgold ;
        // 初始化元宝
        if(data.initgold != gameConfig.initgold){
            isChange = 1;
            msg += '<tr class="text-danger">';
            msg += '<td class="text-success">初始化元宝：</td>';
            msg += '<td>' + gameConfig.initgold + '</td>';
            msg += '<td>' + data.initgold + '</td>';
            msg += '</tr>';
        }

        if(isChange == 0){
            $.zmsg.error("没有任何修改");
            return false ;
        }
        $.Zebra_Dialog(msg, {
            'title': '修改信息核对',
            'animation_speed_show': 700,
            'center_buttons': true,
            'type': '',
            'buttons': ['取消', '确定'],
            'onClose': function(caption) {
                if ('取消' == caption) {
                } else if ('确定' == caption) {

                    // 开始 loading 遮盖
                    $.loading.show("gameConfigLoding");

                    $.ajax({
                        url: "/Gameconf/game/third/savegameuser",
                        type: "POST",
                        data: data,
                        dataType: "json",
                        success: function(data) {
                            $.loading.hide('gameConfigLoding');
                            if (0 == data.code) {
                                $.zmsg.success();
                            } else {
                                $.zmsg.errorShowModal(data.msg);
                            }
                        },
                        error: function(data) {
                            $.loading.hide('gameConfigLoding');
                            $.zmsg.errorShowModal(data.responseText);
                        }
                    });
                }
            }
        });
    });
});

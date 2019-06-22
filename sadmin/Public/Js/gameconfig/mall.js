var MALL_GOODS_INFO ;
var IS_CHANGE = false ; 
$(function() {
    'use strict';

    // 初始化添加框时间插件
    $('#addGoodsstartTime').datetimepicker({
        autoclose: true,
        format: 'yyyy-mm-dd hh:ii',
        language: 'zh-CN'
    }).on('change', function(ev) {
        var startTime = $('#addGoodsstartTime').val();
        $('#addGoodsendTime').datetimepicker('setStartDate', startTime);
    });
    $('#addGoodsendTime').datetimepicker({
        autoclose: true,
        format: 'yyyy-mm-dd hh:ii',
        language: 'zh-CN'
    }).on('change', function(ev) {
        var endTime = $('#addGoodsendTime').val();
        $('#addGoodsstartTime').datetimepicker('setEndDate', endTime);
    });

    // 修改商品图片上传插件安装
    imgUploadSetup('addMod');
    // 修改商品图片上传插件安装
    imgUploadSetup('editMod');

    //商品图删除按钮
    $('.mallImgPreviewDiv').on('click','.imgListItem-close',function(){
        $(this).parent().remove();

        $('#addModImages').val(''); //清空图片地址
        $('#editModImages').val(''); //清空图片地址
    });

    $('#addBtn').click(function(){
        var html = '<div class="form-group" style="border-bottom: 1px solid #eee;margin-left: 20px;">';
            html += '            <div class="col-sm-2"> <span style="padding:20px; font-size: 26px; display: block;text-align: center;" class="listNumber">1</span> </div>';
            html += '            <div class="col-sm-4">';
            html += '                <div  style="clear:both;height:45px;">';
            html += '                    <label class="control-label request pull-left"  >红包下限 ：</label> ';
            html += '                    <input class="form-control proba-price-down proba-input-check" data-oldvalue="" type="text" name="proba-price-down" value="" style="width:50%;float:left" /> ';
            html += '                    <label class="control-label request" >元</label>';
            html += '                </div>';
            html += '                <div style="height:45px;">';
            html += '                    <label class="control-label request pull-left"  >红包上限 ：</label> ';
            html += '                    <input class="form-control proba-price-up proba-input-check" data-oldvalue="" type="text" name="proba-price-up" value="" style="width:50%;float:left" /> ';
            html += '                    <label class="control-label request" >元</label>';
            html += '                </div>        ';
            html += '            </div>';

            html += '            <div class="col-sm-4">';
            html += '                <div style="height:45px; margin: 20px 0 0 0;">';
            html += '                    <label class="control-label request pull-left">获得概率 ：</label> ';
            html += '                    <input class="form-control proba-percent proba-input-check" data-oldvalue="" type="text" name="proba-percent" value="" style="width:50%;float:left" /> ';
            html += '                    <label class="control-label request" >%</label>';
            html += '                </div>';
            html += '            </div>';
            html += '            <div class="col-sm-2">';
            html += '                <button class="btn btn-warning proba-delete-btn" type="button"  style=" margin: 20px 0 0 0;" >删除</button>';
            html += '            </div>';
            html += '        </div>';

            $('#probaBox').html(html);
    });

    //点击保存按钮时触发表单校验
    $('#addMallGoodsBtn').click(function() {

        $('#addMod').modal('hide');

        var params = {};

        params.type = $('#addModType').val();
        params.name = $('#addModName').val();
        params.ishot = $("input[name=isHot]:checked").val();
        params.image = $('#addModImages').val();
        params.price = $('#addModPrice').val();
        params.priceType = $('#addModPriceType').val(); //价格类型

        if (params.name == '' || typeof (params.name) == 'undefined') {
            $.zmsg.errorShowModal('请输入物品名称', 'addMod');
            return false;
        }

//        if (params.image == '' || typeof (params.image) == 'undefined') {
//            $.zmsg.errorShowModal('请上传物品图标', 'addMod');
//            return false;
//        }

        if (params.price == '' || typeof (params.price) == 'undefined') {
            $.zmsg.errorShowModal('请填写物品价格', 'addMod');
            return false;
        }
//        if (params.amount == '' || typeof (params.amount) == 'undefined') {
//            $.zmsg.errorShowModal('请填写物品数量', 'addMod');
//            return false;
//        }

        //检查概率数据
        var modelType = $(this).attr('data-model') ;
        var msgStatus = checkProbaData(modelType);

        //参数设置错误，不予提交
        if(msgStatus === false ){
            return false;
        }

        probaData = getProBaData();
        params.probadata = probaData ;
        
        var url = "/Gameconf/mall/third/addGoods";

        // 开始 loading 遮盖
        $.loading.show("addMallLoading");
        $.post(url, params, function(data) {

            $.loading.hide('addMallLoading');
            if (0 == data.code) {
                $.zmsg.success();
                window.location.href = '/Gameconf/mall/third/mallconf'
            } else {
                if (data.data != '' && typeof (data.data) != 'undefined') {
                    $.zmsg.error(data.msg + ',' + data.data);
                } else {
                    $.zmsg.error(data.msg);
                }

            }
        });

    });

    //点击编辑按钮时异步获取数据
    $('.editBtn').click(function() {
        var id = $(this).attr('data-id');

        var url = '/Gameconf/mallGetGoodsInfo';

        $.post(url,{id : id}, function( result ){

            if(result.code != 0 ){
                $.zmsg.error(result.msg);
                return false;
            }
            MALL_GOODS_INFO = result.data;
            $('#editModId').val(result.data.id);
            $('#id').val(result.data.id);
            $('#editModType').find("option[value = '"+result.data.type+"']").attr("selected",true);

            $('#editModName').val(result.data.name);
            if(result.data.isHot == 1){
                $('#editcheckbox').find("input[name=isHot]").prop("checked",true);
            }
            if(result.data.image){
                var imgHtml = '<span class="imgListItem">' +
                        '<i class="fa fa-times imgListItem-close"></i>' +
                        '<img class="addGoodsImg" src="' +  result.data.image + '" style="box-shadow: rgb(86, 86, 86) 0px 0px 3px; width: 100px; max-width: 100%;">' +
                        '</span>';
                $('#editModImgPreviewDiv').children("div").html(imgHtml);
                $('#editModImages').val(result.data.image);

                $('#editModImgPreviewDiv').fadeIn("slow");
            }

            var html = '' ,number ;

            if( typeof(result.data.rewardData) == 'object'   ){

                $(result.data.rewardData).each(function(index,item){

                    number = index + 1 ;
                    html += '<div class="form-group" style="border-bottom: 1px solid #eee;margin-left: 20px;">';
                    html += '            <div class="col-sm-2"> <span style="padding:20px; font-size: 26px; display: block;text-align: center;" class="listNumber">'+number+'</span> </div>';
                    html += '            <div class="col-sm-4">';
                    html += '                <div  style="clear:both;height:45px;">';
                    html += '                    <label class="control-label request pull-left"  >红包下限 ：</label> ';
                    html += '                    <input class="form-control proba-price-down proba-input-check" data-oldvalue="'+ item[1] +'" type="text" name="proba-price-down" value="'+ item[1] +'" style="width:50%;float:left" /> ';
                    html += '                    <label class="control-label request" >元</label>';
                    html += '                </div>';
                    html += '                <div style="height:45px;">';
                    html += '                    <label class="control-label request pull-left"  >红包上限 ：</label> ';
                    html += '                    <input class="form-control proba-price-up proba-input-check" data-oldvalue="'+ item[2] +'" type="text" name="proba-price-up" value="'+ item[2] +'" style="width:50%;float:left" /> ';
                    html += '                    <label class="control-label request" >元</label>';
                    html += '                </div>        ';
                    html += '            </div>';

                    html += '            <div class="col-sm-4">';
                    html += '                <div style="height:45px; margin: 20px 0 0 0;">';
                    html += '                    <label class="control-label request pull-left">获得概率 ：</label> ';
                    html += '                    <input class="form-control proba-percent proba-input-check" data-oldvalue="'+ item[0] +'" type="text" name="proba-percent" value="'+ item[0] +'" style="width:50%;float:left" /> ';
                    html += '                    <label class="control-label request" >%</label>';
                    html += '                </div>';
                    html += '            </div>';
                    html += '            <div class="col-sm-2">';
                    html += '                <button class="btn btn-warning proba-delete-btn" type="button"  style=" margin: 20px 0 0 0;" >删除</button>';
                    html += '            </div>';
                    html += '        </div>';
                });

                $('#probaBox').html(html);
            }

            $('#rewardid').val(result.data.rewardId);
            $('#editModPriceType option[value="'+result.data.priceType+'"]').attr("selected", true);
            $('#editPrice').val(result.data.price);
        });
    });
    
    $("#probaBox").on('blur','.proba-input-check',function(){
        var oldValue = $(this).attr('data-oldvalue');
        
        if(oldValue != $(this).val() ){
            IS_CHANGE = true ;
        }
    });
    
    //点击保存按钮时触发表单校验
    $('#editModMallGoodsBtn').click(function() {

        $('#editMod').modal('hide');

        var params = {},probaData = {};
        params.id = $('#id').val();
        params.rewardid = $('#rewardid').val();
        params.type = $('#editModType').val();
        params.name = $('#editModName').val();
        params.ishot = $("input[name=isHot]:checked").val();
        params.image = $('#editModImages').val();
        params.price = $('#editPrice').val();
//        params.amount = $('#editModAmount').val();
        params.priceType = $('#editModPriceType').find("option:selected").val(); //价格类型

        if (params.name == '' || typeof (params.name) == 'undefined') {
            $.zmsg.errorShowModal('请输入物品名称', 'editMod');
            return false;
        }

//        if (params.image == '' || typeof (params.image) == 'undefined') {
//            $.zmsg.errorShowModal('请上传物品图标', 'editMod');
//            return false;
//        }

        if (params.price == '' || typeof (params.price) == 'undefined') {
            $.zmsg.errorShowModal('请填写物品价格', 'editMod');
            return false;
        }
//        if (params.amount == '' || typeof (params.amount) == 'undefined') {
//            $.zmsg.errorShowModal('请填写物品数量', 'editMod');
//            return false;
//        }

        //检查概率数据
        var modelType = $(this).attr('data-model') ;
        var msgStatus = checkProbaData(modelType);

        //参数设置错误，不予提交
        if(msgStatus === false ){
            return false;
        }

        probaData = getProBaData();
        params.probadata = probaData ;

        //检测基础表单有无修改
        for( var key in params){
            if(key == 'ishot' ){
                if( typeof(params[key]) != 'undefined' && MALL_GOODS_INFO.isHot != params[key] ){
                    IS_CHANGE = true ;
                }else if(MALL_GOODS_INFO.isHot == 1){
                    IS_CHANGE = true ;
                }
            }else if(key == 'rewardid'  ){
                if(MALL_GOODS_INFO.rewardId != params[key]){
                    IS_CHANGE = true ;
                }
            }else if( key == 'probadata' || key == 'type' ){
                continue;
            }else if( MALL_GOODS_INFO[key] != params[key] ){
                IS_CHANGE = true ;
            }
        }
        
        if(IS_CHANGE === false ){
            
            $.zmsg.errorShowModal('没有任何修改！');
            return false;
        }
        


        var url = "/Gameconf/mall/third/editGoods";

        // 开始 loading 遮盖
        $.loading.show("addMallLoading");
        $.post(url, params, function(data) {

            $.loading.hide('addMallLoading');
            if (0 == data.code) {
                $.zmsg.success();
                window.location.href = '/Gameconf/mall/third/mallconf'
            } else {
                if (data.data != '' && typeof (data.data) != 'undefined') {
                    $.zmsg.error(data.msg + ',' + data.data);
                } else {
                    $.zmsg.error(data.msg);
                }

            }
        });

    });

     //点击下架商品
    $('.offGoodsBtn').click(function(){
        var id = $(this).attr('data-id');

        $.Zebra_Dialog("确定下架该商品吗？", {
            animation_speed_show: 500,
            center_buttons: true,
            type: 'question',
            buttons :[
                {caption: '确定', callback: function() {
                    var url = "/Gameconf/mall/third/editGoods";

                    var action = "offGoods";

                    // 开始 loading 遮盖
                    $.loading.show("downGoodsLoading");
                    $.post(url, {id:id, action:action}, function(data) {
                        $.loading.hide('downGoodsLoading');
                        if (0 == data.code) {
                            $.zmsg.success();
                            window.location.reload();
                        } else {
                            if (data.data != '' && typeof (data.data) != 'undefined') {
                                $.zmsg.error(data.msg + ',' + data.data);
                            } else {
                                $.zmsg.error(data.msg);
                            }

                        }
                    });

                }},
                {caption: '取消', callback: function() { }}
            ]
        });
    });

     //点击删除商品
    $('.deleteBtn').click(function(){
        var id = $(this).attr('data-id');

        $.Zebra_Dialog("确定删除该商品吗？", {
            animation_speed_show: 500,
            center_buttons: true,
            type: 'question',
            buttons :[
                {caption: '确定', callback: function() {
                    var url = "/Gameconf/mall/third/editGoods";
                    var action = "deleteGoods";

                    // 开始 loading 遮盖
                    $.loading.show("deleteGoodsLoading");
                    $.post(url, {id:id, action:action}, function(data) {

                        $.loading.hide('deleteGoodsLoading');
                        if (0 == data.code) {
                            $.zmsg.success();
                            window.location.reload();
                        } else {
                            if (data.data != '' && typeof (data.data) != 'undefined') {
                                $.zmsg.error(data.msg + ',' + data.data);
                            } else {
                                $.zmsg.error(data.msg);
                            }

                        }
                    });
                }},
                {caption: '取消', callback: function() { }}
            ]
        });
    });

    /*
     * 编辑概率
     */
    var modelType, probaData;
    $('.editGoodsProbaBtn').click(function(){
        var modelType = $(this).attr('data-model') ;
        if(modelType == 'addGoodsProba'){
            $('#addMod').modal('hide');
        }else{
            $('#editMod').modal('hide');
        }
        $('#cancelProbaBtn').attr('data-model',modelType);
        $('#saveProbaBtn').attr('data-model',modelType);
        $('#editGoodsProba').modal('show');
    });

    //保存编辑概率框
    $('#saveProbaBtn').click(function(){
        //检查概率数据
        var modelType = $(this).attr('data-model') ;
        checkProbaData(modelType);
    });

    //取消编辑概率框
    $('#cancelProbaBtn').click(function(){
        if(modelType == 'addGoodsProba'){
            $('#addMod').modal('show');
        }else{
            $('#editMod').modal('show');
        }
        $('#editGoodsProba').modal('hide');
    });

    //概率删除
    $('#probaBox').on("click",".proba-delete-btn",function(){
        if($('#probaBox').find('.form-group').length < 2){
            $.zmsg.error("至少要保留一条规则");
            return false;
        }

        $(this).parent().parent().remove();
        $('#probaBox').find(".form-group").each(function(index,obj){
            $(obj).find('.listNumber').html( index + 1 );
        });
    });

    //概率新增
    $('#proba-add-btn').click(function(){
        var number ;

        number = $('#probaBox').find('.form-group').length + 1 ;

        var html = '<div class="form-group" style="border-bottom: 1px solid #eee;margin-left: 20px;">';
            html += '            <div class="col-sm-2"> <span style="padding:20px; font-size: 26px; display: block;text-align: center;" class="listNumber">'+number+'</span> </div>';
            html += '            <div class="col-sm-4">';
            html += '                <div  style="clear:both;height:45px;">';
            html += '                    <label class="control-label request pull-left"  >红包下限 ：</label> ';
            html += '                    <input class="form-control proba-price-down proba-input-check" data-oldvalue=""  type="text" name="proba-price-down" value="" style="width:50%;float:left" /> ';
            html += '                    <label class="control-label request" >元</label>';
            html += '                </div>';
            html += '                <div style="height:45px;">';
            html += '                    <label class="control-label request pull-left"  >红包上限 ：</label> ';
            html += '                    <input class="form-control proba-price-up proba-input-check" data-oldvalue=""  type="text" name="proba-price-up" value="" style="width:50%;float:left" /> ';
            html += '                    <label class="control-label request" >元</label>';
            html += '                </div>        ';
            html += '            </div>';

            html += '            <div class="col-sm-4">';
            html += '                <div style="height:45px; margin: 20px 0 0 0;">';
            html += '                    <label class="control-label request pull-left">获得概率 ：</label> ';
            html += '                    <input class="form-control proba-percent proba-input-check" data-oldvalue=""  type="text" name="proba-percent" value="" style="width:50%;float:left" /> ';
            html += '                    <label class="control-label request" >%</label>';
            html += '                </div>';
            html += '            </div>';
            html += '            <div class="col-sm-2">';
            html += '                <button class="btn btn-warning proba-delete-btn" type="button"  style=" margin: 20px 0 0 0;" >删除</button>';
            html += '            </div>';
            html += '        </div>';

            $('#probaBox').append(html);
    });
});

// 安装商品图片上传插件
function imgUploadSetup(mod, isList) {

    $('#' + mod + 'ImgInput').fileupload({
        url: "/Gameconf/mallUploadImages"
    }).on('fileuploadstart', function(e, data) {
        // 上传 input,提交input disabled
        $('#' + mod + 'ImgInput').prop('disabled', true);
        $('#' + mod + 'SubmitBtn').prop('disabled', true);
        // 图片上传按钮切换为进度样式
        $('#' + mod + 'ImgBadge').empty().append(
                $('<span/>').text("0%")
                );
    }).on('fileuploadprogress', function(e, data) {
        // 更新进度值
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#' + mod + 'ImgBadge').children().text(progress + "%");
    }).on('fileuploadalways', function(e, data) {
        // 上传 input enabled
        $('#' + mod + 'ImgInput').prop('disabled', false);
        $('#' + mod + 'SubmitBtn').prop('disabled', false);

        if (0 == data.result.code) {
            // 先隐藏预览框
            $('#' + mod + 'ImgPreviewDiv').fadeOut("fast", function() {
                var Bixid = $(this).attr('id');
                var imgHtml = '<span class="imgListItem">' +
                        '<i class="fa fa-times imgListItem-close"></i>' +
                        '<img class="addGoodsImg" src="' + data.result.data.imgUrl + '" style="box-shadow: rgb(86, 86, 86) 0px 0px 3px; width: 100px; max-width: 100%;">' +
                        '</span>';
                $('#' + mod + 'ImgPreviewDiv').children("div").html(imgHtml);
                $('#' + mod + 'Images').val(data.result.data.imgUrl);

                $('#' + mod + 'ImgPreviewDiv').fadeIn("slow");
            });

            // 添加后将图片上传按钮的样式改为修改样式
            setTimeout(function() {
                $('#' + mod + 'ImgBtn').fadeOut('slow', function() {
                    if ("add" == $('#' + mod + 'ImgBtn').data('tag')) {
                        $('#' + mod + 'ImgBtn').removeClass('btn-success').addClass('btn-primary').data('tag', 'edit');
                    }
                    $('#' + mod + 'ImgBadge').empty().append(
                            $('<span/>').addClass("glyphicon glyphicon-edit")
                            );
                    $('#' + mod + 'ImgBtn').fadeIn('slow');
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

function checkProbaData(modelType) {
        //检查文本框有没有空白
        var msgStatus = false ,inputValue;

        $('#probaBox').find("input").each(function(index,input){
            inputValue = $(input).val() ;
           if( inputValue == '' || typeof(inputValue) == 'undefined' ){
               msgStatus = true ;
           }
        });
        //为空提示
        if(msgStatus){
            $.zmsg.error("请输入正确的概率值");
            return false;
        }

        //判断概率的值
        var inputPercent = 0 ;
        $('#probaBox').find(".proba-percent").each(function(index,input){
            inputPercent +=  parseInt( $(input).val() );
        });

        //为空提示
        if(inputPercent != 100 ){
            $.zmsg.error("获得概率百分比不等于100%");
            return false;
        }

        msgStatus = false;
        $('#probaBox').find(".form-group").each(function(index,item){
            var upVlue = parseInt( $(item).find('.proba-price-up').val() );
            var downVlue = parseInt( $(item).find('.proba-price-down').val() );

            if(upVlue < downVlue){
                msgStatus = true;
                return false;
            }
        });
        //为空提示
        if(msgStatus ){
            $.zmsg.error("上线值不能小于下限值");
            return false;
        }

        if(modelType == 'addGoodsProba'){
            $('#addMod').modal('show');
            $('#editGoodsProba').modal('hide');
        }else if(modelType == 'editGoodsProba'){
            $('#editMod').modal('show');
            $('#editGoodsProba').modal('hide');
        }
}

//获取概率数据
function getProBaData() {
    var probadata = [],tempdata={};

    var upVlue,downVlue,percentVlue;
    $('#probaBox').find(".form-group").each(function(index,item){
        upVlue =  $(item).find('.proba-price-up').val() ;
        downVlue =  $(item).find('.proba-price-down').val() ;
        percentVlue =  $(item).find('.proba-percent').val() ;
        tempdata={};
        tempdata.upVlue = upVlue ;
        tempdata.downVlue = downVlue ;
        tempdata.percentVlue = percentVlue ;

        probadata.push( tempdata );
    });

    return probadata ;
}

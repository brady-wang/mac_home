
$(function() {
    'use strict';

    // 初始化权限值状态
    initAuthClass(acss, oper);

    //游戏授权点击事件
    $('.checkgame').click(function(){
        if($(this).hasClass("tree-leaf-unselected")){
            $(this).removeClass("tree-leaf-unselected");
            $(this).addClass("tree-leaf-selected");
        }else{
            $(this).removeClass("tree-leaf-selected");
            $(this).addClass("tree-leaf-unselected");
        }
    });
});

// 初始化权限值状态
function initAuthClass(acss, oper) {

    // 遍历访问权限，若拥有该权限则将选择状态置为已选择(selected)
    $(".access-auth").each(function() {
        var code = $(this).data("code");
        if (true == $.tool.inArray(code, acss)) {
            $(this).removeClass("tree-leaf-unselected");
            $(this).addClass("tree-leaf-selected");

            var operTd = $(this).parent().next("td.oper-auth-td");
            operTd.children(".oper-auth").each(function() {
                $(this).removeClass("tree-leaf-disabled");
                $(this).addClass("tree-leaf-unselected");
            });

            var referScode = $(this).data("refer-scode");
            if (referScode) {
                var sOperTd = $(".oper-sl-" + referScode);
                if (sOperTd.length > 0) {
                    sOperTd.children().each(function() {
                        $(this).removeClass("tree-leaf-disabled");
                        $(this).addClass("tree-leaf-unselected");
                    });
                }
            }
        }
    });

    // 遍历操作权限，若拥有该权限则选择状态置为已选择
    $(".oper-auth.tree-leaf-unselected").each(function() {
        var code = $(this).data("code");
        if (true == $.tool.inArray(code, oper)) {
            $(this).removeClass("tree-leaf-unselected");
            $(this).addClass("tree-leaf-selected");
        }
    });
}

// 切换权限值状态
function toggleAuthClass(obj) {
    // 如果该权限不可选，直接返回
    if (obj.hasClass("tree-leaf-disabled")) {
        return false;
    }

    obj.toggleClass("tree-leaf-unselected tree-leaf-selected");

    // 如果权限是访问权限，要切换其对应的操作权限的可选状态，否则只切换操作权限的选择状态
    if (obj.hasClass("access-auth")) {
        // 切换后一级操作权限的选择状态
        var operTd = obj.parent().next("td.oper-auth-td");
        // 从未选择切换为已选择，操作权限全部改为未选择
        if (obj.hasClass("tree-leaf-selected")) {
            operTd.children(".oper-auth").each(function() {
                $(this).removeClass("tree-leaf-disabled");
                $(this).addClass("tree-leaf-unselected");
            });
        }
        // 从已选择切换为未选择，操作权限全部改为不可选
        else {
            operTd.children(".oper-auth").each(function() {
                $(this).removeClass("tree-leaf-unselected tree-leaf-selected");
                $(this).addClass("tree-leaf-disabled");
            });
        }

        // 如果存在 referScode 那这个 obj 是三级访问权限，需要处理二级操作权限
        var referScode = obj.data("refer-scode");
        if (referScode) {
            var sOperTd = $(".oper-sl-" + referScode);
            if (sOperTd.length > 0) {
                // 只要对应二级访问权限下面的所有三级访问权限为未选择，那么其二级操作权限设为不能选
                var disableFlag = 1;
                $(".third-sl-" + referScode).each(function() {
                    if ($(this).hasClass("tree-leaf-selected")) {
                        disableFlag = 0;
                    }
                });
                if (disableFlag == 0) {
                    sOperTd.children(".oper-auth").each(function() {
                        if ($(this).hasClass("tree-leaf-disabled")) {
                            $(this).removeClass("tree-leaf-disabled");
                            $(this).addClass("tree-leaf-unselected");
                        }
                    });
                } else {
                    sOperTd.children(".oper-auth").each(function() {
                        $(this).removeClass("tree-leaf-unselected tree-leaf-selected");
                        $(this).addClass("tree-leaf-disabled");
                    });
                }
            }
        }
    }

    return true;
}

// 添加角色 submit
function submitAddRole() {

    var data = {};

    // 角色名称
    data.role_name = $('#roleName').val();

    // 访问权限
    var accessCode = [];
    $(".access-auth").each(function() {
        if ($(this).hasClass("tree-leaf-selected")) {
            accessCode.push($(this).data('code'));
        }
    });
    data.access = accessCode;

    // 操作权限
    var operCode = [];
    $(".oper-auth").each(function() {
        if ($(this).hasClass("tree-leaf-selected")) {
            operCode.push($(this).data('code'));
        }
    });
    data.oper = operCode;

    //游戏授权
    var gameids  = [];
    $('.gameAuthList  .tree-leaf-selected').each(function(item){
        gameids.push( $(this).attr('data-id') ); 
    });
    data.gameids = gameids;

    // 开始 loading 遮盖
    $.loading.show("addRoleLoading");

    $.ajax({
        url: "/System/ajaxAddRole",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('addRoleLoading');
            if (0 == data.code) {
                $.zmsg.success(referer);
            } else {
                $.zmsg.error(data.msg);
            }
        },
        error: function(data) {
            $.loading.hide('addRoleLoading');
            $.zmsg.fatal(data.responseText);
        }
    });
}

// 修改角色 submit
function submitEditRole() {

    var data = {};

    data.id = $('#roleId').text();

    // 角色名称
    data.role_name = $('#roleName').val();

    // 访问权限
    var accessCode = [];
    $(".access-auth").each(function() {
        if ($(this).hasClass("tree-leaf-selected")) {
            accessCode.push($(this).data('code'));
        }
    });
    data.access = accessCode;

    // 操作权限
    var operCode = [];
    $(".oper-auth").each(function() {
        if ($(this).hasClass("tree-leaf-selected")) {
            operCode.push($(this).data('code'));
        }
    });
    data.oper = operCode;

    //游戏授权
    var gameids  = [];
    $('.gameAuthList  .tree-leaf-selected').each(function(item){
        gameids.push( $(this).attr('data-id') ); 
    });
    data.gameids = gameids;

    // 开始 loading 遮盖
    $.loading.show("editRoleLoading");

    $.ajax({
        url: "/System/ajaxEditRole",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
            $.loading.hide('editRoleLoading');
            if (0 == data.code) {
                $.zmsg.success(referer);
            } else {
                $.zmsg.error(data.msg);
            }
        },
        error: function(data) {
            $.loading.hide('editRoleLoading');
            $.zmsg.fatal(data.responseText);
        }
    });
}

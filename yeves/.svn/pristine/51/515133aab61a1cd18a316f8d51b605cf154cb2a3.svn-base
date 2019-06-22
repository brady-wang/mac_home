$(function () {
    $(".edit_cate").click(function () {
        $("input[name='cate_name']").val($(this).attr('data-cate_name'))
        $("input[name='cate_id']").val($(this).attr('data-cate_id'))

    })

    $(".del_category").click(function () {
        var number = $(this).attr('data-number');
        var id = $(this).attr('data-cate_id');
        if (false ) {
            layer.msg("该分类下有文章,不能删除!");
            return;
        } else {
            layer.confirm('确定删除该分类?', {icon: 3, title: '提示'}, function (index) {
                var data = {};
                data.id = id;
                $.ajax({
                    type: 'post',
                    url: '/admin/Category/cate_del',
                    data: data,
                    dataType: 'json',
                    success: function (res) {
                        layer.msg(res.msg);
                        if (res.success == true) {
                            layer.close(index);
                            setTimeout("location.reload();", 2000);
                        }
                    }
                })


            });


        }

    })

    $(".submit_edit_cate").click(function () {
        var cate_id = $("input[name='cate_id']").val();
        var cate_name = $.trim($("input[name='cate_name']").val());
        var front = "<?php echo $front;?>";
        if (front) {
            if (cate_id.length <= 0) {
                layer.msg("参数错误 分类id不存在");
                return;
            }

            if (cate_name.length <= 0) {
                layer.msg("请输入分类名称");
                return;
            }
        }

        var data = {
            'id': cate_id,
            'category': cate_name
        };

        $(".submit_edit_cate").attr('disabled', true);
        $(".submit_edit_cate").html('处理中');
        $.ajax({
            type: 'post',
            url: '/admin/Category/cate_edit',
            data: data,
            dataType: 'json',
            success: function (res) {
                console.log(res)
                layer.msg(res.msg);
                if (res.success == true) {
                    setTimeout("location.reload();", 2000);
                } else {
                    $(".submit_edit_cate").removeAttr('disabled');
                    $(".submit_edit_cate").html("提交");
                }
            }
        })


    })


    $("#add_category").click(function () {
        var category = $.trim($("#category").val());

        if (category.length > 0) {
            $("#add_category").attr('disabled', true);
            $("#add_category").html('处理中');
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {'category': category},
                url: "/admin/Category/add",
                success: function (res) {
                    layer.msg(res.msg);
                    console.log(res)
                    if (res.success == true) {
                        setTimeout('location.reload()', 1500);
                    } else {
                        $("#add_category").removeAttr('disabled');
                        $("#add_category").html("提交");
                    }
                }
            })
        } else {
            layer.msg("分类名称不能为空");
            return;
        }
    })
})
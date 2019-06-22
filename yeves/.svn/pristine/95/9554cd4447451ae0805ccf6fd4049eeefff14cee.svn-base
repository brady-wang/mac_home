<style>
    .red{
        color:red;
        font-size:1.2em;
    }
    #content{
        margin-left: 12px;
    }
    #headPortraitImgShow{
        width:200px;
        max-height:120px;
        margin-top: 5px;
        border-radius: 8px;
        /*border: 1px solid #aaa;*/
        padding: 4px;
    }
</style>

<link rel="stylesheet" href="/themes/lib/editor.md/css/editormd.css" />
<script src="/themes/lib/editor.md/editormd.js"></script>

<!--主体内容开始-->
<div id="main" class="col-md-10">
    <div class="page-header">
        <?php
        if (empty($data['id'])){ ?>
            <h2>新建文章 <small>New Article</small></h2>
        <?php } else {?>
            <h2>编辑文章 <small>Edit Article</small></h2>
        <?php }?>

        <p class="text-primary">发布,编辑文章</p>

    </div>
    <div class="panel">
        <div class="panel-body text-center ">
            <a type="button" href="/admin/Articles" class="btn btn-info " > 返回列表</a>
        </div>
    </div>
    <div class="panel">
        <div class="panel-body">
            <div class="form-horizontal ">

                    <?php
                    if (!empty($data['id'])){ ?>
                        <div class="form-group">
                            <label for="id" class="col-md-2 control-label">ID <span
                                    class="tpl-form-line-small-title">id</span></label>
                            <div class="col-md-10">
                                <input type="text" disabled  class="form-control" value="<?php  echo !empty($data['id']) ? $data['id'] : '';  ?>" id="id" >
                            </div>

                        </div>
                        <?php

                    }else {
                        ?>
                        <input type="hidden" id="id" value="">
                        <?php
                    }
                    ?>

                <div class="form-group">
                    <label for="title" class="col-md-2 control-label"><span class="red">*</span> 标题 <small>title</small> </label>
                    <div class="col-md-10">
                        <input type="text" class="form-control"  value="<?php  echo !empty($data['title']) ? $data['title'] : '';  ?>" id="title" placeholder="请输入标题文字">
                    </div>
                </div>

                <div class="form-group">
                    <label for="title" class="col-md-2 control-label"><span class="red">*</span> 上下架状态 <small>Is_del</small> </label>
                    <div class="col-md-10">
                        <select class="form-control" id="is_del">
                            <?php
                            if(isset($data['is_del'])){
                                if($data['is_del'] == 1){
                                    echo '<option value="1" selected>下架</option><option  value="0">上架</option>';
                                }else {
                                    echo '<option value="0" selected>上架</option><option  value="1">下架</option>';
                                }
                            } else {
                                echo '<option value="1" selected>下架</option><option  value="0">上架</option>';
                            }
                            ?>

                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="title" class="col-md-2 control-label"><span class="red">*</span> 类别选择 <small>Category</small> </label>
                    <div class="col-md-10">
                        <select class="form-control" id="category">
                            <?php foreach ($all_category as $key => $value):  ?>

                                <?php if(isset($data['category']) && $value['id'] == $data['category']){
                                    echo '<option
                                            value="'.$value['id'].'" selected>'.$value['category'].'</option>';
                                }else {
                                    echo '<option
                                            value="'.$value['id'].'" >'.$value['category'].'</option>';
                                }?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="title" class="col-md-2 control-label"><span class="red">*</span> 发布时间 <small>create_time</small> </label>
                    <div class="col-md-10">
                        <input type="text" class="form-control"  value="<?php  echo !empty($data['create_time']) ? $data['create_time'] : $now;  ?>" id="create_time" placeholder="发布时间">
                    </div>
                </div>


                <div class="form-group">
                    <label for="title" class="col-md-2 control-label"><span class="red">*</span> 标 签 <small>Tag</small> </label>
                    <div class="col-md-10">
                        <input type="text" class="form-control"  id="tag" value="<?php  echo !empty($data['tag']) ? $data['tag'] : '';  ?>" placeholder="多个标签请用英文" ,"分离">
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-md-2 control-label"> SEO关键词 <small>SEO keyword</small> </label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" id="seo_keyword" value="<?php  echo !empty($data['seo_keyword']) ? $data['seo_keyword'] : '';  ?>"  placeholder="多个关键词用,分割">
                    </div>
                </div>

                <div class="form-group">
                    <label for="title" class="col-md-2 control-label"> SEO描述 <small>SEO description</small> </label>
                    <div class="col-md-10">
                        <textarea  class="form-control" id="seo_description"  rows="3" placeholder="请输入seo描述"><?php  echo !empty($data['seo_description']) ? $data['seo_description'] : '';  ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="title" class="col-md-2 control-label"> 列表图 <small>image</small> </label>
                    <div class="col-md-10">
                        <input type="file" name="image" class="form-control" id="image" >
                        <div>
                            <img id="headPortraitImgShow" src="<?php echo (!empty($data['image'])) ? $data['image'] : ''; ?>" alt=""  />
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="content" class="col-md-2 control-label"><span class="red">*</span> 文章内容 <small>Content</small> </label>
                    <div class="col-sm-offset-2 col-sm-10 " id="content">
                    </div>
                </div>

                <textarea style="display:none;" id="bak_content" class="form-control" rows="20" placeholder="请输入markdown语法的内容"><?php  echo !empty($data['content']) ? $data['content'] : '';  ?></textarea>


                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit"  id="article_add_sub" class="btn btn-success btn-block btn-lg">
                            发布
                        </button>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $("#image").on("change",headPortraitListener);

    /*定义全局变量存贮图片信息*/
    var base64head="";
    /*头像上传监听*/
    function headPortraitListener(e) {

        var img = document.getElementById('headPortraitImgShow');
        if(window.FileReader) {
            var file  = e.target.files[0];
            var reader = new FileReader();
            if (file && file.type.match('image.*')) {
                reader.readAsDataURL(file);
            } else {
                img.css('display', 'none');
                img.attr('src', '');
            }
            reader.onloadend = function (e) {
                img.setAttribute('src', reader.result);
                base64head = reader.result;
            }
        }
    }

    var content_editor;

    $(function() {

        content_editor = editormd({
            id      : "content",
            width   : "82%",
            height  : 750,
            toolbarIcons : function() {
                // Or return editormd.toolbarModes[name]; // full, simple, mini
                // Using "||" set icons align right.
                return editormd.toolbarModes["full"];
            },
            path    : "/themes/lib/editor.md/lib/",
            emoji : true,
           // theme : "dark",
            //previewTheme : "dark",
            //editorTheme : "pastel-on-dark",
            //codeFold : true,
            //syncScrolling : false,
            saveHTMLToTextarea : true,    // 保存 HTML 到 Textarea
            searchReplace : true,
            watch : true,                // 关闭实时预览
            //htmlDecode : "style,script,iframe|on*",            // 开启 HTML 标签解析，为了安全性，默认不开启
            //toolbar  : false,             //关闭工具栏
            //previewCodeHighlight : false, // 关闭预览 HTML 的代码块高亮，默认开启
            emoji : true,
          //  markdown : content_editor,
            taskList : true,
            tocm            : false,         // Using [TOCM]
            tex : false,                   // 开启科学公式TeX语言支持，默认关闭
            flowChart : false,             // 开启流程图支持，默认关闭
            sequenceDiagram : false,       // 开启时序/序列图支持，默认关闭,
            //dialogLockScreen : false,   // 设置弹出层对话框不锁屏，全局通用，默认为true
            //dialogShowMask : false,     // 设置弹出层对话框显示透明遮罩层，全局通用，默认为true
            //dialogDraggable : false,    // 设置弹出层对话框不可拖动，全局通用，默认为true
            //dialogMaskOpacity : 0.4,    // 设置透明遮罩层的透明度，全局通用，默认值为0.1
            //dialogMaskBgColor : "#000", // 设置透明遮罩层的背景颜色，全局通用，默认为#fff

            onload : function() {
                var bak_content = $("#bak_content").val();
                content_editor.setMarkdown(bak_content);

            }


        });


    });
</script>

<script>
    $("#title").focus();
    $(function(){
        $("#article_add_sub").click(function(){
            var is_del = $.trim($("#is_del").val());
            var id = $.trim($("#id").val());
            var title = $.trim($("#title").val());
            var seo_keyword = $.trim($("#seo_keyword").val());
            var seo_description = $.trim($("#seo_description").val());
            var content =  content_editor.getMarkdown();
            var image = $("#headPortraitImgShow").attr('src');
            var create_time = $.trim($("#create_time").val());
            var category = $.trim($("#category").val());
            var tag = $.trim($("#tag").val());
            var front = "<?php echo $front;?>";

            if(front){
                if(title.length <= 0) {
                    layer.msg("请输入标题");
                    $("#title").focus();
                    return ;
                }

                if(category.length <= 0) {
                    layer.msg("请选择分类");
                    $("#category").focus();
                    return ;
                }
                if(content.length <= 0) {
                    layer.msg("请输入文章内容");
                    $("#content").focus();
                    return ;
                }
            }

            var data = {};
            data = {
                'id':id,
                'title':title,
                'is_del':is_del,
                'seo_keyword':seo_keyword,
                'seo_description':seo_description,
                'content':content,
                'create_time':create_time,
                'category':category,
                'tag':tag,
                'image':image
            }
            console.log(data);
            $.ajax({
                url:'/admin/Articles/update',
                data:data,
                dataType:"json",
                type:"post",
                success:function(res){
                    layer.msg(res.msg);
                    if(res.success == true){
                        setTimeout("location.href='/admin/Articles/index'",2000);
                    }
                }
            })
        })
    })
</script>
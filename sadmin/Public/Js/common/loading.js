
// github  https://github.com/IFmiss/loading

// 显示 Loading
(function($){
    "use strict";

    $.loading = {
        // 默认 loading
        show: function(loadingName) {
            $('body').loading({
                name:           loadingName,        // loading 名字
                loadingBg:      'transparent',      // 隐藏中间层
                loadingMaskBg:  'rgba(0,0,0,0.6)',  // 遮盖层阴影显示
                originWidth:    6,                  // 小圆点宽度
                originHeight:   6,                  // 小圆点高度
            });

        },
        // 隐藏 loading
        hide: function(loadingName) {
            removeLoading(loadingName);
        }
    }

    $.fn.loading = function(options) {
        var $this = $(this);
        var _this = this;
        return this.each(function(){
            var loadingPosition ='';
            var defaultProp = {
                name:           'loadingName',              // loading 的 data-name 的属性值，用于删除 loading 需要的参数
                direction:      'column',                   // 方向：column 纵向，   row 横向
                animateIn:      'fadeInNoTransform',        // 进入类型
                type:           'origin',                   // loading 类型：origin html 元素； pic 图片
                title:          '',                         // 标题内容，空字符串则不显示
                titleColor:     'rgba(255,255,255,0.7)',    // 标题内容文本颜色
                discription:    '',                         // 描述内容，空字符串则不显示
                discColor:      'rgba(255,255,255,0.7)',    // 描述内容文本颜色
                loadingWidth:   260,                        // 中间的背景宽度
                loadingBg:      'rgba(0, 0, 0, 0.6)',       // 中间的背景色
                borderRadius:   12,                         // 中间的背景边框圆角（border-radius）
                loadingMaskBg:  'transparent',              // 背景遮罩层颜色：transparent 透明；rgba(0, 0, 0, 0.6) 阴影
                zIndex:         1000001,                    // 层级

                // 这是圆形旋转的loading样式
                originDivWidth:     60,         // loadingDiv 的 width
                originDivHeight:    60,         // loadingDiv 的 Height
                originWidth:        8,          // 小圆点 width
                originHeight:       8,          // 小圆点 Height
                originBg:           '#fefefe',  // 小圆点背景色
                smallLoading:       false,      // 显示小的 loading

                // 这是图片的样式   (pic)
                imgSrc:         '',     // 默认的图片地址
                imgDivWidth:    80,     // imgDiv 的 width
                imgDivHeight:   80,     // imgDiv 的 Height

                flexCenter:     false,  // 是否用 flex 布局让 loading-div 垂直水平居中
                flexDirection:  'row',  // flex 的方向：row 横向；column 纵向
                mustRelative:   false,  // $this 是否规定 relative
            };

            var opt = $.extend(defaultProp,options || {});

            //根据用户是针对body还是元素  设置对应的定位方式
            if ($this.selector == 'body') {
                // 不要改 overflow，会隐藏掉页面滚动条
                //$('body,html').css({
                //    overflow:'hidden',
                //});

                loadingPosition = 'fixed';
            } else if(opt.mustRelative) {
                $this.css({
                    position:'relative',
                });
                loadingPosition = 'absolute';
            } else {
                loadingPosition = 'absolute';
            }

            defaultProp._showOriginLoading = function(){
                var smallLoadingMargin = opt.smallLoading ? 0 : '-10px';
                if(opt.direction == 'row'){smallLoadingMargin='-6px'}

                //悬浮层
                  _this.cpt_loading_mask = $('<div class="cpt-loading-mask animated '+opt.animateIn+' '+opt.direction+'" data-name="'+opt.name+'"></div>').css({
                    'background':opt.loadingMaskBg,
                    'z-index':opt.zIndex,
                    'position':loadingPosition,
                }).appendTo($this);

                  //中间的显示层
                _this.div_loading = $('<div class="div-loading"></div>').css({
                    'background':opt.loadingBg,
                    'width':opt.loadingWidth,
                    'height':opt.loadingHeight,
                    '-webkit-border-radius':opt.borderRadius,
                    '-moz-border-radius':opt.borderRadius,
                    'border-radius':opt.borderRadius,
                  }).appendTo(_this.cpt_loading_mask);

                if (opt.flexCenter) {
                    _this.div_loading.css({
                        "display": "-webkit-flex",
                        "display": "flex",
                        "-webkit-flex-direction":opt.flexDirection,
                        "flex-direction":opt.flexDirection,
                        "-webkit-align-items": "center",
                        "align-items": "center",
                        "-webkit-justify-content": "center",
                        "justify-content":"center",
                    });
                }

                //loading标题
                _this.loading_title = $('<p class="loading-title txt-textOneRow"></p>').css({
                    color:opt.titleColor,
                }).html(opt.title).appendTo(_this.div_loading);

                //loading中间的内容  可以是图片或者转动的小圆球
                 _this.loading = $('<div class="loading '+opt.type+'"></div>').css({
                    'width':opt.originDivWidth,
                    'height':opt.originDivHeight,
                  }).appendTo(_this.div_loading);

                 //描述
                _this.loading_discription = $('<p class="loading-discription txt-textOneRow"></p>').css({
                    color:opt.discColor,
                }).html(opt.discription).appendTo(_this.div_loading);

                if (opt.type == 'origin') {
                    _this.loadingOrigin = $('<div class="div-loadingOrigin"><span></span></div><div class="div-loadingOrigin"><span></span></div><div class="div_loadingOrigin"><span></span></div><div class="div_loadingOrigin"><span></span></div><div class="div_loadingOrigin"><span></span></div>').appendTo(_this.loading);
                      _this.loadingOrigin.children().css({
                        "margin-top":smallLoadingMargin,
                        "margin-left":smallLoadingMargin,
                        "width":opt.originWidth,
                        "height":opt.originHeight,
                        "background":opt.originBg,
                      });
                }

                if (opt.type == 'pic') {
                    _this.loadingPic = $('<img src="'+opt.imgSrc+'" alt="loading" />').appendTo(_this.loading);
                }


                  //关闭事件冒泡  和默认的事件
                _this.cpt_loading_mask.on('touchstart touchend touchmove click', function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                });
            };
            defaultProp._createLoading = function() {
                // 不能生成两个loading data-name 一样的 loading
                if ($(".cpt-loading-mask[data-name="+opt.name+"]").length > 0) {
                    // console.error('loading mask cant has same date-name('+opt.name+'), you cant set "date-name" prop when you create it');
                    return
                }

                defaultProp._showOriginLoading();
            };
            defaultProp._createLoading();
        });
    }
})(jQuery)

// 关闭 Loading
function removeLoading(loadingName) {
    var loadingName = loadingName || '';

    // 不要改 overflow，可能会导致页面多一条滚动条
    //$('body,html').css({
    //    overflow:'auto',
    //});

    if (loadingName == '') {
        $(".cpt-loading-mask").remove();
    } else {
        var name = loadingName || 'loadingName';
        $(".cpt-loading-mask[data-name="+name+"]").remove();
    }
}

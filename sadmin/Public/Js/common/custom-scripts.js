
(function($) {
    "use strict";

    // 把表单转成  json，并且 name 为 key, value 为值
    $.fn.serializeObject = function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    }

    // 通过表单 post 方式下载文件
    $.postDownFile = function(url, data) {

        var $iframe = $('<iframe/>').attr('id', 'downFileIframe');
        var $form = $('<form/>').attr({
            'target': 'downFileIframe',
            'method': 'post',
            'action': url
        });

        for (var i in data) {
            $form.append(
                $('<input/>').attr({
                    'type': 'hidden',
                    'name': i,
                    'value': data[i]
                })
            );
        }
        $iframe.append($form);

        $(document.body).append($iframe);

        $form[0].submit();

        $iframe.remove();
    }

    // 常用工具
    $.tool = {
        // 判断一个变量是否为数字
        isRealNum: function(obj) {
            if ($.tool.trim(obj) === "" || obj === null) {
                return false;
            }
            if (isNaN(obj)) {
                return false;
            }
            return true;
        },
        // 判断一个变量是否为整数
        isInteger: function(obj) {
            if (typeof obj !== 'number' && typeof obj !== 'string') {
                return false;
            }
            if (typeof obj === 'string' && trim(obj) === "") {
                return false;
            }
            return obj % 1 === 0;
        },
        // 判断一个变量是否为数组
        isArrayVal: function(value) {
            if (typeof Array.isArray === "function") {
                return Array.isArray(value);
            } else {
                return Object.prototype.toString.call(value) === "[object Array]";
            }
        },
        // js 判断一个值是否存在数组中
        inArray: function(val, arr) {
            // 不是数组
            if (!$.tool.isArrayVal(arr)) {
                return false;
            }
            // 遍历是否在数组中
            for (var i = 0, k = arr.length; i < k; i++) {
                if (val == arr[i]) {
                    return true;
                }
            }
            // 不在数组中
            return false;
        },
        // 计算数组长度或类数组对象成员数
        arrayCount: function(arr) {
            var count = 0;
            for (var i in arr) {
                count++;
            }
            return count;
        },
        // 删除左右两端的空格
        trim: function(str) {
            return str.replace(/(^\s*)|(\s*$)/g, "");
        },
        // 输入时戳秒，返回日期时间，格式： YYYY-mm-dd HH:ii:ss
        getDate: function(stamp) {
            // 秒要转成毫秒
            var date = new Date(stamp * 1000);
            var year = date.getFullYear();
            var month = date.getMonth() + 1;
            var day = date.getDate();
            var hour = date.getHours();
            var minute = date.getMinutes();
            var second = date.getSeconds();

            month = month < 10 ? '0' + month : month;
            day = day < 10 ? '0' + day : day;
            hour = hour < 10 ? '0' + hour : hour;
            minute = minute < 10 ? '0' + minute : minute;
            second = second < 10 ? '0' + second : second;

            return year + "-" + month + "-" + day + " " + hour + ":" + minute + ":" + second;
        },
        // 输入时戳秒，返回日期时间，格式： YYYY-mm-dd HH:ii:ss
        getDateYmd: function(stamp) {
            // 秒要转成毫秒
            var date = new Date(stamp * 1000);
            var year = date.getFullYear();
            var month = date.getMonth() + 1;
            var day = date.getDate();
            var hour = date.getHours();
            var minute = date.getMinutes();
            var second = date.getSeconds();

            month = month < 10 ? '0' + month : month;
            day = day < 10 ? '0' + day : day;
            hour = hour < 10 ? '0' + hour : hour;
            minute = minute < 10 ? '0' + minute : minute;
            second = second < 10 ? '0' + second : second;

            return year + "-" + month + "-" + day;
        }
    }

    // 统一使用 zebra 对话框
    $.zmsg = {
        // 页面信息对话框，不进行页面跳转
        info: function(msg) {
            $.Zebra_Dialog(msg, {
                'animation_speed_show': 500,
                'auto_close': 2000,
                'buttons': ["确定"],
                'center_buttons': true,
                'type': 'confirmation'
            });
        },
        //提示消息
        msg: function(msg) {
            $.Zebra_Dialog(msg, {
                'animation_speed_show': 500,
                'auto_close': 2000,
                'buttons': false,
                'center_buttons': true,
                'type': 'confirmation'
            });
        },
        // 警示消息
        warning: function(msg) {
            $.Zebra_Dialog(msg, {
                'animation_speed_show': 500,
                'auto_close': 5000,
                'buttons': ["确定"],
                'center_buttons': true,
                'type': 'warning'
            });
        },
        // 页面成功信息对话框
        success: function(referer = null) {
            $.Zebra_Dialog('<p style="padding-left: 80px;">成功</p>', {
                'animation_speed_show': 500,
                'auto_close': 2000,
                'buttons': ["确定"],
                'center_buttons': true,
                'type': 'confirmation',
                'onClose': function() {
                    if (referer) {
                        window.location = referer;
                    } else {
                        window.location.reload();
                    }
                }
            });
        },
        // 页面错误信息对话框
        error: function(msg) {
            $.Zebra_Dialog(msg, {
                'animation_speed_show': 500,
                'buttons': ["确定"],
                'center_buttons': true,
                'type': 'error'
            });
        },
        // 页面错误信息对话框，对话框关闭时显示指定模拟框
        errorShowModal: function(msg, modId) {
            $.Zebra_Dialog(msg, {
                'animation_speed_show': 500,
                'buttons': ["确定"],
                'center_buttons': true,
                'type': 'error',
                'onClose': function() {
                    $('#' + modId).modal();
                }
            });
        },
        // 页面错误信息对话框，对话框关闭时刷新页面
        errorReload: function(msg, referer = null) {
            $.Zebra_Dialog(msg, {
                'animation_speed_show': 500,
                'buttons': ["确定"],
                'center_buttons': true,
                'type': 'error',
                'onClose': function() {
                    if (referer) {
                        window.location = referer;
                    } else {
                        window.location.reload();
                    }
                }
            });
        },
        // 系统错误信息对话框
        fatal: function(msg) {
            var screen_width = document.body.clientWidth - 50;
            if (screen_width > 1330) {
                screen_width = 1280;
            }

            var screen_height = document.documentElement.clientHeight;
            if (screen_height > 576) {
                screen_height = 576;
            } else {
                screen_height -= 300;
            }

            $.Zebra_Dialog(msg, {
                'animation_speed_show': 500,
                'buttons': ["确定"],
                'center_buttons': true,
                'title': "System Fatal Error",
                'type': 'error',
                'width': screen_width,
                'max_height': screen_height
            });
        },
        // 系统错误信息对话框
        fatalShowModal: function(msg, modId) {
            var screen_width = document.body.clientWidth - 50;
            if (screen_width > 1330) {
                screen_width = 1280;
            }

            var screen_height = document.documentElement.clientHeight;
            if (screen_height > 576) {
                screen_height = 576;
            } else {
                screen_height -= 300;
            }

            $.Zebra_Dialog(msg, {
                'animation_speed_show': 500,
                'buttons': ["确定"],
                'center_buttons': true,
                'title': "System Fatal Error",
                'type': 'error',
                'width': screen_width,
                'max_height': screen_height,
                'onClose': function() {
                    $('#' + modId).modal();
                }
            });
        }
    }

    var mainApp = {
        initFunction: function() {
            /*
             * --------------------------------------------------------------
             * MENU
             * --------------------------------------------------------------
             */
            $('#main-menu').metisMenu();

            $(window).bind("load resize", function() {
                if ($(this).width() < 768) {
                    $('div.sidebar-collapse').addClass('collapse')
                } else {
                    $('div.sidebar-collapse').removeClass('collapse')
                }
            });

            /*
             * --------------------------------------------------------------
             * 用户注销弹出确认框
             * --------------------------------------------------------------
             */
            $('#userLogoutBtn').on('click', function() {
                console.log('user logout');

                $.Zebra_Dialog('确定要注销当前用户吗？', {
                    'title': '注销确认框',
                    'animation_speed_show': 500,
                    'center_buttons': true,
                    'type': '',
                    'buttons': ['取消', '确定'],
                    'onClose': function(caption) {
                        if ('取消' == caption) {
                        } else if ('确定' == caption) {
                            window.location = '/Auth/logout';
                        }
                    }
                });
            });
        }
    }

    /*
     * --------------------------------------------------------------
     * Initializing
     * --------------------------------------------------------------
     */
    $(document).ready(function() {
        // 程序初始化
        mainApp.initFunction();

        // 侧边栏隐藏行为
        $("#sideNav").click(function() {
            if ($(this).hasClass('closed')) {
                $('.navbar-side').animate({left: '0px'});
                $(this).removeClass('closed');
                $('#page-wrapper').animate({'margin-left': '230px'});
            } else {
                $(this).addClass('closed');
                $('.navbar-side').animate({left: '-230px'});
                $('#page-wrapper').animate({'margin-left': '0px'});
            }
        });
    });
}(jQuery));

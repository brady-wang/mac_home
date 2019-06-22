/*
Navicat MySQL Data Transfer

Source Server         : yves
Source Server Version : 50556
Source Host           : 120.79.172.45:3306
Source Database       : blog

Target Server Type    : MYSQL
Target Server Version : 50556
File Encoding         : 65001

Date: 2018-03-26 18:16:09
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for about
-- ----------------------------
DROP TABLE IF EXISTS `about`;
CREATE TABLE `about` (
  `id` int(11) NOT NULL,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tag` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of about
-- ----------------------------
INSERT INTO `about` VALUES ('1', '关于w', 'ee', 'ee');

-- ----------------------------
-- Table structure for article_tag
-- ----------------------------
DROP TABLE IF EXISTS `article_tag`;
CREATE TABLE `article_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(50) unsigned NOT NULL,
  `tag_id` int(50) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `INDEX_article_tag` (`article_id`,`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=507 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of article_tag
-- ----------------------------
INSERT INTO `article_tag` VALUES ('471', '27', '174');
INSERT INTO `article_tag` VALUES ('469', '27', '175');
INSERT INTO `article_tag` VALUES ('470', '27', '176');
INSERT INTO `article_tag` VALUES ('505', '29', '174');
INSERT INTO `article_tag` VALUES ('506', '29', '209');
INSERT INTO `article_tag` VALUES ('483', '38', '173');
INSERT INTO `article_tag` VALUES ('345', '101', '174');
INSERT INTO `article_tag` VALUES ('343', '101', '177');
INSERT INTO `article_tag` VALUES ('344', '101', '178');
INSERT INTO `article_tag` VALUES ('346', '101', '179');
INSERT INTO `article_tag` VALUES ('347', '101', '180');
INSERT INTO `article_tag` VALUES ('350', '102', '174');
INSERT INTO `article_tag` VALUES ('348', '102', '177');
INSERT INTO `article_tag` VALUES ('349', '102', '178');
INSERT INTO `article_tag` VALUES ('351', '102', '179');
INSERT INTO `article_tag` VALUES ('352', '102', '180');
INSERT INTO `article_tag` VALUES ('355', '103', '174');
INSERT INTO `article_tag` VALUES ('353', '103', '177');
INSERT INTO `article_tag` VALUES ('354', '103', '178');
INSERT INTO `article_tag` VALUES ('356', '103', '179');
INSERT INTO `article_tag` VALUES ('357', '103', '180');
INSERT INTO `article_tag` VALUES ('360', '104', '174');
INSERT INTO `article_tag` VALUES ('358', '104', '177');
INSERT INTO `article_tag` VALUES ('359', '104', '178');
INSERT INTO `article_tag` VALUES ('361', '104', '179');
INSERT INTO `article_tag` VALUES ('362', '104', '180');
INSERT INTO `article_tag` VALUES ('365', '105', '174');
INSERT INTO `article_tag` VALUES ('363', '105', '177');
INSERT INTO `article_tag` VALUES ('364', '105', '178');
INSERT INTO `article_tag` VALUES ('366', '105', '179');
INSERT INTO `article_tag` VALUES ('367', '105', '180');
INSERT INTO `article_tag` VALUES ('488', '106', '174');
INSERT INTO `article_tag` VALUES ('486', '106', '177');
INSERT INTO `article_tag` VALUES ('487', '106', '178');
INSERT INTO `article_tag` VALUES ('489', '106', '179');
INSERT INTO `article_tag` VALUES ('490', '106', '180');
INSERT INTO `article_tag` VALUES ('425', '107', '173');
INSERT INTO `article_tag` VALUES ('426', '108', '181');
INSERT INTO `article_tag` VALUES ('427', '108', '182');
INSERT INTO `article_tag` VALUES ('430', '109', '181');
INSERT INTO `article_tag` VALUES ('431', '109', '182');
INSERT INTO `article_tag` VALUES ('432', '110', '183');
INSERT INTO `article_tag` VALUES ('433', '111', '183');
INSERT INTO `article_tag` VALUES ('493', '112', '179');
INSERT INTO `article_tag` VALUES ('494', '112', '184');
INSERT INTO `article_tag` VALUES ('455', '113', '183');
INSERT INTO `article_tag` VALUES ('491', '124', '207');
INSERT INTO `article_tag` VALUES ('492', '124', '208');

-- ----------------------------
-- Table structure for articles
-- ----------------------------
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'admin',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '文章描述',
  `seo_keyword` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'seo关键词',
  `seo_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'seo关键词',
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '封面图片',
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `category` varchar(50) NOT NULL,
  `create_time` datetime NOT NULL COMMENT '发布时间',
  `pv` int(50) unsigned NOT NULL DEFAULT '1',
  `is_hot` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否热门推荐 0 否 1 是',
  `is_del` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除 0上架   1 下架',
  `update_time` datetime DEFAULT NULL,
  `article_month` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '1990-01' COMMENT '发表月份',
  `tag` varchar(255) NOT NULL DEFAULT '',
  `line-num` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of articles
-- ----------------------------
INSERT INTO `articles` VALUES ('26', 'admin', 'hexo-next主题添加桃心效果', '效果如下:第一步:打开目录themes\\next\\source\\js\\src新建love.js复制代码保存代码如下:!function(e,t,a){functionn(){c(\".heart{width:10px;height:10px;position:fixed;background:#f00;transform:rotate(45deg);-webkit-transform:rotate(', 'hexo,web', '', null, '## 效果如下:\n![](http://blog-img.yeves.cn/1.png)\n\n第一步 : 打开目录 themes\\next\\source\\js\\src 新建love.js 复制<a href=\'http://7u2ss1.com1.z0.glb.clouddn.com/love.js\'>代码</a> 保存\n\n代码如下:\n```\n!function(e,t,a){function n(){c(\".heart{width: 10px;height: 10px;position: fixed;background: #f00;transform: rotate(45deg);-webkit-transform: rotate(45deg);-moz-transform: rotate(45deg);}.heart:after,.heart:before{content: \'\';width: inherit;height: inherit;background: inherit;border-radius: 50%;-webkit-border-radius: 50%;-moz-border-radius: 50%;position: fixed;}.heart:after{top: -5px;}.heart:before{left: -5px;}\"),o(),r()}function r(){for(var e=0;e&lt;d.length;e++)d[e].alpha<=0?(t.body.removeChild(d[e].el),d.splice(e,1)):(d[e].y--,d[e].scale+=.004,d[e].alpha-=.013,d[e].el.style.cssText=\"left:\"+d[e].x+\"px;top:\"+d[e].y+\"px;opacity:\"+d[e].alpha+\";transform:scale(\"+d[e].scale+\",\"+d[e].scale+\") rotate(45deg);background:\"+d[e].color+\";z-index:99999\");requestAnimationFrame(r)}function o(){var t=\"function\"==typeof e.onclick&&e.onclick;e.onclick=function(e){t&&t(),i(e)}}function i(e){var a=t.createElement(\"div\");a.className=\"heart\",d.push({el:a,x:e.clientX-5,y:e.clientY-5,scale:1,alpha:1,color:s()}),t.body.appendChild(a)}function c(e){var a=t.createElement(\"style\");a.type=\"text/css\";try{a.appendChild(t.createTextNode(e))}catch(t){a.styleSheet.cssText=e}t.getElementsByTagName(\"head\")[0].appendChild(a)}function s(){return\"rgb(\"+~~(255*Math.random())+\",\"+~~(255*Math.random())+\",\"+~~(255*Math.random())+\")\"}var d=[];e.requestAnimationFrame=function(){return e.requestAnimationFrame||e.webkitRequestAnimationFrame||e.mozRequestAnimationFrame||e.oRequestAnimationFrame||e.msRequestAnimationFrame||function(e){setTimeout(e,1e3/60)}}(),n()}(window,document);\n```\n第二步: 修改布局文件 引入js themes\\next\\layout\\_layout.swing 在最后面引入\n\n```\n&lt;!-- 页面点击小红心 --&gt;\n[removed][removed]\n```\n\n第三步: 重新生成静态页面\n\nhexo clean\n\nhexo generate\n\nhexo server\n', '34', '2018-03-05 10:00:00', '15', '1', '0', null, '2018-03', '', '1');
INSERT INTO `articles` VALUES ('27', 'admin', 'centos下搭建svn', '一SVN简介SVN是Subversion的简称，是一个开放源代码的版本控制系统，相较于RCS、CVS，它采用了分支管理系统，它的设计目标就是取代CVS。互联网上很多版本控制服务已从CVS迁移到Subversion。说得简单一点SVN就是用于多个人共同开发同一个项目，共用资源的目的。SVN服务器有2种运行方式：独立服务器和借助apache运行。两种方式各有利弊，用户可以自行选择。下载网址：https', 'linux', '', null, '# 一 SVN简介\n\n* SVN是Subversion的简称，是一个开放源代码的版本控制系统，相较于RCS、CVS，它采用了分支管理系统，它的设计目标就是取代CVS。互联网上很多版本控制服务已从CVS迁移到Subversion。说得简单一点SVN就是用于多个人共同开发同一个项目，共用资源的目的。\n\n* SVN服务器有2种运行方式：独立服务器和借助apache运行。两种方式各有利弊，用户可以自行选择。\n\n下载网址：https://subversion.apache.org/packages.html\n\n本文主要对Centos进行SVN服务器搭建，持续集成Jenkins常需要SVN命令方式执行一些操作。\n\n&lt;!-- more --&gt;\n# 二 安装SVN（Linux）\n\n1. 安装SVN\n    yum -y install subversion\n    注：想快速安装就用Root用户\n2. 查看安装内容与位置\n    rpm -ql subversion\n3. 建立SVN根目录\n    mkdir /data/svn\n4. 新建版本库:TestCode\n    svnadmin create /data/svn/TestCode\n        注: 执行完后，/data/svn/TestCode目录下文件说明\n        README.txt   版本库的说明文件\n        conf              配置文件件夹（后续操作最多的文件夹）\n        db                SVN数据文件文件夹\n        format          当前版本库的版本号\n        hooks           SVN的钩子脚本文件夹\n        locks            SVN的文件锁相关的文件夹\n5. blog库添加用户、分配权限\n\n    A. 配置SVN\n        vim /data/svn/blog/conf/svnserve.conf\n        anon-access=none     #去除文件前的注释，将read改为none,防止匿名访问#\n        auth-access=write    #去除文件前的注释\n        password-db=passwd   #去除文件前的注释\n        authz-db = authz\n    B. 添加访问TestCode用户\n        vim /data/svn/blog/conf/passwd\n            [users]\n            test=123456\n    注:添加了一个测试用户  登陆密码为 123456\n    C. 设置添加的用户权限\n\n        vim /data/svn/TestCode/conf/authz\n        [groups]\n        me=test\n        [blog:/]\n        @test=rw\n    注：test为我新建的账号可读可写\n    D. 防火墙开放SVN端口通行\n        iptables -A INPUT -p tcp --dport 3690 -j ACCEPT  如果是阿里云的服务器,还要再安全策略里面开放3690端口\n    E.开启SVN服务\n        svnserve -d -r /data/svn\n        注：命令写入脚本，开机执行，如需要停止SVN，用 “ps -ef|grep svn”  查找到进程;再 \" kill -9 进程号 \" 即可。\n    F. SVN客户端访问\n    svn://IP/blog\n6. 启动服务器\n    svnserve -d -r /home/svn\n7. 测试服务器\n    svn co svn://192.168.33.30/<repo>\n    以blog为例:\n    svn co svn://192.168.33.30/blog\n8. 注意\n    我们的svn是以独立服务器形式运行的,没有和apache做整合,\n    因为地址svn://xxx/xxx,不是http或https\n9. 重启\n    如果修改了svn配置,需要重启svn服务,步骤如下:\n    ps -aux|grep svnserve\n    kill -9 ID号\n    svnserve -d -r /home/svn', '13', '2018-03-15 14:44:53', '3', '1', '0', '2018-03-15 14:44:53', '2018-03', 'centos,svn,linux', '1');
INSERT INTO `articles` VALUES ('29', 'admin', 'linux 压缩命令', 'tar命令首先要弄清两个概念：打包和压缩。打包是指将一大堆文件或目录变成一个总的文件；压缩则是将一个大的文件通过一些压缩算法变成一个小文件为什么要区分这两个概念呢？这源于Linux中很多压缩程序只能针对一个文件进行压缩，这样当你想要压缩一大堆文件时，你得先将这一大堆文件先打成一个包（tar命令），然后再用压缩程序进行压缩（gzipbzip2命令）。语法tar(选项)(参数)详细说明选项-A或--c', '', '', null, '### tar命令 \n* 首先要弄清两个概念：打包和压缩。打包是指将一大堆文件或目录变成一个总的文件；压缩则是将一个大的文件通过一些压缩算法变成一个小文件\n\n* 为什么要区分这两个概念呢？这源于Linux中很多压缩程序只能针对一个文件进行压缩，这样当你想要压缩一大堆文件时，你得先将这一大堆文件先打成一个包（tar命令），然后再用压缩程序进行压缩（gzip bzip2命令）。\n\n### 语法\n\n    tar(选项)(参数)\n    \n\n### 详细说明\n1. 选项 \n\n    ```\n    -A或--catenate：新增文件到以存在的备份文件；\n    -B：设置区块大小；\n    -c或--create：建立新的备份文件；\n    -C <目录>：这个选项用在解压缩，若要在特定目录解压缩，可以使用这个选项。\n    -d：记录文件的差别；\n    -x或--extract或--get：从备份文件中还原文件；\n    -t或--list：列出备份文件的内容；\n    -z或--gzip或--ungzip：通过gzip指令处理备份文件；\n    -Z或--compress或--uncompress：通过compress指令处理备份文件；\n    -f<备份文件>或--file=<备份文件>：指定备份文件；\n    -v或--verbose：显示指令执行过程；\n    -r：添加文件到已经压缩的文件；\n    -u：添加改变了和现有的文件到已经存在的压缩文件；\n    -j：支持bzip2解压文件；\n    -v：显示操作过程；\n    -l：文件系统边界设置；\n    -k：保留原有文件不覆盖；\n    -m：保留文件不被覆盖；\n    -w：确认压缩文件的正确性；\n    -p或--same-permissions：用原来的文件权限还原文件；\n    -P或--absolute-names：文件名使用绝对名称，不移除文件名称前的“/”号；\n    -N <日期格式> 或 --newer=<日期时间>：只将较指定日期更新的文件保存到备份文件里；\n    --exclude=<范本样式>：排除符合范本样式的文件。\n    ```\n\n\n\n2. 参数\n\n    文件或目录：指定要打包的文件或目录列表。\n\n3. 实例\n\n    将文件全部打包成tar包：\n    \n    ```\n    tar -cvf log.tar log2012.log    仅打包，不压缩！ \n    tar -zcvf log.tar.gz log2012.log   打包后，以 gzip 压缩 \n    tar -jcvf log.tar.bz2 log2012.log  打包后，以 bzip2 压缩 \n    ```\n    \n    在选项f之后的文件档名是自己取的，我们习惯上都用 .tar 来作为辨识。 如果加z选项，则以.tar.gz或.tgz来代表gzip压缩过的tar包；如果加j选项，则以.tar.bz2来作为tar包名。\n\n4.  查阅上述tar包内有哪些文件：\n\n    ```\n    tar -ztvf log.tar.gz\n    ```\n\n    由于我们使用 gzip 压缩的log.tar.gz，所以要查阅log.tar.gz包内的文件时，就得要加上z这个选项了。\n\n5. 将tar包解压缩：\n\n    ```\n    tar -zxvf /opt/soft/test/log.tar.gz\n    ```\n\n    在预设的情况下，我们可以将压缩档在任何地方解开的\n\n6. 只将tar内的部分文件解压出来：\n\n    ```\n    tar -zxvf /opt/soft/test/log30.tar.gz log2013.log\n    ```\n    我可以透过tar -ztvf来查阅 tar 包内的文件名称，如果单只要一个文件，就可以透过这个方式来解压部分文件！\n\n7.  文件备份下来，并且保存其权限：\n\n    ```\n    tar -zcvpf log31.tar.gz log2014.log log2015.log log2016.log\n    ```\n\n    这个-p的属性是很重要的，尤其是当您要保留原本文件的属性时。\n\n8. 在文件夹当中，比某个日期新的文件才备份：\n\n    ```\n    tar -N \"2012/11/13\" -zcvf log17.tar.gz test\n    ```\n\n9.  备份文件夹内容是排除部分文件：\n\n    ```\n    tar --exclude scf/service -zcvf scf.tar.gz scf/*\n    ```\n\n10.  其实最简单的使用 tar 就只要记忆底下的方式即可：\n\n    ```\n    压　缩：tar -jcv -f filename.tar.bz2 要被压缩的文件或目录名称\n    查　询：tar -jtv -f filename.tar.bz2\n    解压缩：tar -jxv -f filename.tar.bz2 -C 欲解压缩的目录\n', '34', '2018-03-21 15:28:31', '10', '1', '0', '2018-03-20 15:29:34', '2018-03', 'linux,tar', '1');
INSERT INTO `articles` VALUES ('38', 'admin', 'markdown 语法', 'markdown语法说明markdown中换行不是使用回车,而是在上一行后面输入两个空格或者两次enter用反斜线\\可以使很多语法失效例如\\##我是标题标题语法分为六级#号越多标题越小#H1标题##H2标题###H3标题####H4标题#####H5标题######H6标题H1标题H2标题H3标题H4标题H5标题H6标题文本样式__粗体文本__**粗体文本**粗体文本粗体文本_斜体文本_*斜体文本', '', '', '/upload/article_list/20180315185649_4835_300.jpeg', '# markdown 语法  \n## 说明  \n1. markdown中换行不是使用回车,而是在上一行后面输入两个空格 或者两次enter\n2. 用反斜线 \\可以使很多语法失效 例如 \\\\## 我是标题 \n## 标题\n 语法 分为六级 #号越多 标题越小\n ```\n    # H1标题\n    ## H2标题\n    ### H3标题\n    #### H4标题\n    ##### H5标题\n    ###### H6标题\n    ```\n# H1标题\n## H2标题\n### H3标题\n#### H4标题\n##### H5标题\n###### H6标题  \n\n## 文本样式\n  ```\n __粗体文本__\n    **粗体文本**\n  ```\n  __粗体文本__\n  **粗体文本**  \n  \n  ```\n   _斜体文本_\n    *斜体文本*\n  ```\n  _斜体文本_\n  *斜体文本*  \n  ```\n   ___斜粗体文本___\n   ***斜粗体文本***\n  ```\n  ___斜粗体文本___\n  ***斜粗体文本***\n  \n## 代码块  \n  ```\n  ` ``  \n  我是代码块  \n\n  `` `\n  ```\n## 删除线  两个波浪号  esc键下 \n```\n~~删除线~~\n```\n~~删除效果~~  \n## 添加背景色  \n```\n<code>\n 天地不仁。\n</code>\n```  \n<code>\n 天地不仁。\n</code>\n## 无序列表  \n无序列表有三种表示方法：* 、 + 和 -。\n下级在上级基础上前面多加两个空格，符号与内容直接有一个空格。\n```\n* 一级条目1\n* 一级条目2\n  * 二级条目1\n  * 二级条目2\n    * 三级条目1\n    * 三级条目2\n    * 三级条目3\n  * 二级条目3\n* 一级条目3\n```\n* 一级条目1\n* 一级条目2\n  * 二级条目1\n  * 二级条目2\n    * 三级条目1\n    * 三级条目2\n    * 三级条目3\n  * 二级条目3\n* 一级条目3\n\n## 有序列表  \n```\n1. 一级条目1\n2. 一级条目2\n  1. 二级条目1\n  * 二级条目2\n    * 三级条目1\n    + 三级条目2\n    - 三级条目3\n  - 二级条目3\n2. 一级条目3\n```\n\n1. 一级条目1\n2. 一级条目2\n  *  二级条目1\n  * 二级条目2\n    * 三级条目1\n    + 三级条目2\n    - 三级条目3\n  - 二级条目3\n2. 一级条目3\n\n## 引用\n```\n> 这个是引用\n```\n> 这个是引用\n>> 二层引用\n>>> 三层引用', '35', '2018-03-15 18:56:49', '7', '1', '0', '2018-03-15 18:56:49', '2018-03', 'markdown', '1');
INSERT INTO `articles` VALUES ('106', 'admin', '使用vagrant快速搭建lnmp开发环境', 'Vagrant是一款用来构建虚拟开发环境的工具，非常适合php/python/ruby/java这类语言开发web应用，“代码在我机子上运行没有问题”这种说辞将成为历史。我们可以通过Vagrant封装一个Linux的开发环境，分发给团队成员。成员可以在自己喜欢的桌面系统（Mac/Windows/Linux）上开发程序，代码却能统一在封装好的环境里运行，非常霸气。安装步骤安装VirtualBox刚用', 'vagrant,lnmp,使用vagrant快速搭建lnmp开发环境', '', '/upload/article_list/20180320142237_7111_300.png', '>> Vagrant 是一款用来构建虚拟开发环境的工具，非常适合 php/python/ruby/java 这类语言开发 web 应用，“代码在我机子上运行没有问题”这种说辞将成为历史。\n\n>> 我们可以通过 Vagrant 封装一个 Linux 的开发环境，分发给团队成员。成员可以在自己喜欢的桌面系统（Mac/Windows/Linux）上开发程序，代码却能统一在封装好的环境里运行，非常霸气。  \n\n## 安装步骤  \n\n### 安装 VirtualBox  \n* 刚用虚拟机的时候用的是vmware,but 太大了,太占内存了,vbox可以同时开几个虚拟机都没问题,不会卡机子,自从使用了vbox后,再没用过vmvare了.  \n* 在win7上和win10上都搭建过,最终发现win10无论下载什么版本的vbox启动都会报错,所以开发还是win7稳定 .虚拟机版本一直用的 4.2.12  [百度网盘下载地址](https://pan.baidu.com/s/1IL-B4X6GyUL7uZsQWhiYZA) 下载完成后按照步骤进行安装\n* 安装完成后,打开虚拟机,依次点击菜单 管理-全局设定-常规 进行选择默认虚拟机存放的位置,因为以后的虚拟机里面装的东西,都是放到这个目录,默认是放到系统盘的,我一般会选择磁盘空间比较大的D盘之类.如果c盘够大也可以不用管\n###  安装vagrant   \n 到 [官网地址](https://www.vagrantup.com/downloads.html )进行下载,或者到 [我的网盘](https://pan.baidu.com/s/1W_RUjnA2FA161BMOe1IKMA) 进行下载 下载后按照步骤进行安装\n### 下载vbox的镜像  \n 镜像下载地址[http://www.vagrantbox.es/](http://www.vagrantbox.es/) 熟悉什么系统的就下载什么系统 我网盘也保存有  ubuntu precise 32 VirtualBox [网盘地址](https://pan.baidu.com/s/177tx6jeuB-Cjlp9qzBPh4Q) 我自己比较熟悉的是centos [网盘地址](https://pan.baidu.com/s/1rqq1D8Y37R8ef7dCHnkUwQ)  \n\n### 添加镜像到vagrant\n ```\n     # win+r \n     cmd 回车\n     d:\n     mkdir vbox_list\n     cd vbox_list\n     ##复制下载好的centos-6.6-x86_64.box到该目录下\n     vagrant box add lnmp centos-6.6-x86_64.box ##lnmp为该box的名字,可以自己取,\n     vagrant init lnmp #初始化\n     vagrant up  \n```\n\n### 初始化后 会在当前目录生成一个 Vagrantfile文件和一个.vagrant目录  ,目录不用管  ,打开Vagrantfile文件 修改下面的两处\n\nconfig.vm.network \"private_network\", ip: \"192.168.33.90\"\nconfig.vm.synced_folder \"d:/www/my\", \"/www\"\n* 第一处是为了让本机电脑和虚拟机能网段ping通,到时候可以通过ip访问到虚拟机里面的代码\n* 第二处是为了把windows的开发目录文件共享到虚拟机上   \n   \n### 使用xshell连接工具连接到虚拟机  \n* ip为之前设置的,端口不是默认的22 而是启动的时候提示的 2222 或者2200 启动多个虚拟机占用了2222 下一个就会是2200了 \n* 默认账号和密码 vagrant vagrant  \n* 可以修改root密码  sudo passwd root  \n### 常用命令\n```\n  $ vagrant init  # 初始化\n  $ vagrant up  # 启动虚拟机\n  $ vagrant halt  # 关闭虚拟机\n  $ vagrant reload  # 重启虚拟机\n  $ vagrant ssh  # SSH 至虚拟机\n  $ vagrant status  # 查看虚拟机运行状态\n  $ vagrant destroy  # 销毁当前虚拟机\n```\n### 小tips  \n每次都要cmd 切换目录,启动 太麻烦了 ,可以使用快捷方式\n   桌面新建一个lnmp_start.bat  用编辑工具打开 写入\n    ```\n    d:\n    cd vbox_list\n    cd lnmp \n    vagrant reload\n    ```  \n   如此每次上班开机只要点击下即可,还可以直接放入windows开机启动里面 \n    \n### 安装lnmp环境  \n*  之前刚做开发的时候,用过wamp,phpstudy,后来要求用linux环境,自己照着教程一步步安装nginx  mysql  php  总之各种步骤, 解压,./configure , make, make install 还要自己建用户,给权限 安装依赖 ,总之一番折腾下来,运气好,不报错,运气不好,最后php就编译错误了.  \n*  后来发现个好东西 lnmp一键安装 官网地址 [https://lnmp.org/](https://lnmp.org/) 此后开发就一直用了,很少出问题,安装也特别简单\n    ```\n      cd /usr/local/src #我一般安装到该目录\n        yum -y update #安装前西关先更新下\n        wget -c http://soft.vpser.net/lnmp/lnmp1.4.tar.gz && tar zxf lnmp1.4.tar.gz && cd lnmp1.4 && ./install.sh lnmp\n      ```\n* 之后就按照提示选择mysql版本 ,php版本 设置mysql密码,然后就是漫长的等待,机子快的半小时左右就装好了\n* 安装好后 执行 lnmp start 即可启动php环境 \n* 添加虚拟机域名  lnmp vhost add 就可以了, 具体使用请看官网\n### 打包  \n  环境也有出问题的时候,现在我安装好了,全新的,我担心什么时候出问题,或者我想家里用.那么可以打包,打包后,以后 vagrant box add lnmp my.box 之后就是一件搭建好环境的了  \n  打包命令  vagrant package --output my.box \n  \n### 访问虚拟机 \n   使用switchhost工具,配置一个刚刚使用lnmp vhost add的域名 就可以访问我的虚拟机了 此时就可以实现在windows下进行linux环境的php开发\n ', '35', '2018-03-20 14:22:37', '7', '1', '0', '2018-03-20 14:22:37', '2018-03', 'lnmp,virtualbox,linux,php,vagrant', '1');
INSERT INTO `articles` VALUES ('107', 'admin', 'markdown超链接新窗口', 'markdown语法如果要给文字加上超链接语法如下[下载](http://www.baidu.com)然而这个超链接是在本页面打开的,如果我们想使其在新页面打开需要给超链接a标签增加_blank属性markdown本身语法没发现可以实现,通过jquery实现代码如下$(function(){$(\'a[href^=\"http\"]\').each(function(){$(this).attr(\'tar', 'markdown', '', null, '## markdown语法如果要给文字加上超链接 语法如下\n```\n[下载](http://www.baidu.com)\n```\n\n然而 这个超链接是在本页面打开的,如果我们想使其在新页面打开 需要给超链接 a标签增加_blank属性 markdown本身语法没发现可以实现,通过jquery实现\n 代码如下\n\n``` \n    $(function(){\n        $(\'a[href^=\"http\"]\').each(function() {\n            $(this).attr(\'target\', \'_blank\');\n        });\n        \n    })\n```\n', '35', '2018-03-11 14:59:11', '9', '0', '0', '2018-03-11 14:59:11', '2018-03', 'markdown', '1');
INSERT INTO `articles` VALUES ('109', 'admin', '修改图片浏览器不生效', '搭建网站的时候,会经常修改logo图片等,但是有时候会发现,修改了,无论刷新,还是强制刷新,还是关了浏览器重来,都无法生效,最后只好改文件名字最终发现是nginx一个配置问题修改nginx.confhttp模块下sendfileoff;然后再修改logo执行ctrl+F5生效了', '修改图片浏览器不生效', '', null, ' 搭建网站的时候,会经常修改logo图片等,但是有时候会发现,修改了,无论刷新,还是强制刷新,还是关了浏览器重来,都无法生效,最后只好改文件名字\n\n最终发现是nginx一个配置问题 修改nginx.conf http模块下\n```\n sendfile   off;\n\n```\n然后再修改logo 执行ctrl+F5 生效了', '34', '2018-03-11 15:23:18', '10', '0', '0', '2018-03-11 15:23:18', '2018-03', 'web,缓存', '1');
INSERT INTO `articles` VALUES ('111', 'admin', 'git refusing to merge unrelated histories', 'github新建了个项目,本地非空文件gitinit后,拉代码发现报错refusingtomergeunrelatedhistories解决办法:gitpulloriginmaster--allow-unrelated-histories然后gitstatusUnmergedpaths:(use\"gitadd&lt;file&gt;...\"tomarkresolution)bothadded:RE', 'git refusing to merge unrelated histories', '', null, 'github新建了个项目,本地非空文件git init 后,拉代码发现报错\n```\nrefusing to merge unrelated histories\n\n```\n\n解决办法 :\ngit pull origin master --allow-unrelated-histories\n\n然后 git status \n```\nUnmerged paths:\n  (use \"git add <file>...\" to mark resolution)\n\n        both added:      README.md\n\n```\n\n打开代码如下\n```\n<<<<<<&lt; HEAD\nmy website\n=======\n# my_web\nmyselft website\n&gt;>>>>>> 49b150126ca9cc53028bb6b90c4a91f3c012a706\n\n```\n\n修改掉冲突后重新提交 git add README.md\n', '34', '2018-03-11 15:48:24', '10', '0', '0', '2018-03-11 15:48:33', '2018-03', 'git', '1');
INSERT INTO `articles` VALUES ('112', 'admin', 'PHP file_put_contents: failed to open stream: Protocol error', '进行网站文章备份报错PHPfile_put_contents:failedtoopenstream:Protocolerror查看代码$file=iconv(\"utf-8\",\"gb2312\",$file);file_put_contents($file,$str);打印出$file发现转换为gb2312后乱码,导致无法写入注释掉编码转换的就可以了', 'PHP file_put_contents: failed to open stream: Protocol error', '', null, '>> 进行网站文章备份报错\n```\nPHP file_put_contents: failed to open stream: Protocol error\n```\n\n查看代码 \n```\n$file=iconv(\"utf-8\",\"gb2312\",$file);\nfile_put_contents($file,$str);\n```\n\n打印出$file 发现转换为gb2312后乱码,导致无法写入 注释掉编码转换的就可以了', '34', '2018-03-20 15:21:03', '6', '0', '0', '2018-03-20 15:21:03', '2018-03', 'php,file_put_contents', '1');
INSERT INTO `articles` VALUES ('113', 'admin', 'git modified content, untracked content', 'sfsdfsfds&quot;sdf&quot;sdfssfsdfsdfssdf\'&quot;&quot;&quot;&quot;&quot;&quot;\'sdf\'&quot;&quot;&quot;&quot;&quot;&quot;', 'git modified content, untracked content', '', null, 'sfsdfsfds\"sdf \"sdfs sf\nsdf\nsdf\ns\nsd\nf\'\"\"\"\"\"\"\'sdf\'\"\"\"\"\"\"', '35', '2018-03-14 21:10:45', '4', '0', '0', '2018-03-15 09:07:52', '2018-03', 'git', '1');
INSERT INTO `articles` VALUES ('124', 'admin', '基于bootstrap动态分页', 'bootstrap本身的分页有分页组件但是却是静态的,无法满足要求,分页必须根据当前的总页数来展示使用插件bootstrap-paginatorgithub下载地址https://github.com/lyonlai/bootstrap-paginator.git下载下来后解压,打开发现是一堆文件,不要急,有用的就几个:src目录的bootstrap-paginator.js拷贝到自己的项目里面去', 'bootstrap,分页', 'bootstrap,分页', '', '>> bootstrap本身的分页有分页组件 但是却是静态的,无法满足要求,分页必须根据当前的总页数来展示\n\n## 使用插件bootstrap-paginator\n\ngithub下载地址 https://github.com/lyonlai/bootstrap-paginator.git\n\n下载下来后解压,打开发现是一堆文件,不要急,有用的就几个:  \n1. src目录的bootstrap-paginator.js 拷贝到自己的项目里面去\n2. 打开documentation里面的index.html 这个是说明文档, 里面有很多例子 照着来就行了\n\n开始需要引入依赖文件\n```\n    <link href=\"//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css\" rel=\"stylesheet\">\n    <script src=\"//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js\"></script>\n    <script src=\"//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js\"></script>\n    <script src=\"/js/bootstrap-paginator.min.js\"></script>\n    \n    <div id=\"example\"></div> \n    <script type=\'text/javascript\'>\n        var options = {\n            currentPage: 3,\n            totalPages: 10\n        }\n\n        $(\'#example\').bootstrapPaginator(options);\n    </script>\n```\n\n\n下面放一个我自己用过的 其中总页数 总套数 当前页数都是从后端php传过来的\n```\n    <link href=\"/themes/lib/bootstrap-3.3.7/css/bootstrap.min.css\" rel=\"stylesheet\">\n    <!-- jQuery (necessary for Bootstrap\'s JavaScript plugins) -->\n    <script src=\"/themes/lib/jquery/jquery-3.3.1.min.js\"></script>\n    <!-- Include all compiled plugins (below), or include individual files as needed -->\n    <script src=\"/themes/lib/bootstrap-3.3.7/js/bootstrap.min.js\"></script>\n    \n<div  style=\"text-align: right\"> <ul id=\"pageLimit\"></ul> </div>\n<script>\n        //分页\n        $(\'#pageLimit\').bootstrapPaginator({\n            currentPage: <?php echo $data[\'page\']; ?>,//当前的请求页面。\n            totalPages: <?php echo $data[\'total_rows\']; ?>,//一共多少页。\n            size:\"normal\",//应该是页眉的大小。\n            bootstrapMajorVersion: 3,//bootstrap的版本要求。\n            alignment:\"right\",\n            totalPages:<?php echo $data[\'total_page\']; ?>,\n            useBootstrapTooltip:false,\n            numberOfPages:5,//一页列出多少数据。\n            tooltipTitles: function (type, page, current) {\n                switch (type) {\n                    case \"first\":\n                        return \"\";\n                    case \"prev\":\n                        return \"\";\n                    case \"next\":\n                        return \"\";\n                    case \"last\":\n                        return \"\";\n                    case \"page\":\n                        return  \'\';\n                }\n            },\n            itemTexts: function (type, page, current) {//如下的代码是将页眉显示的中文显示我们自定义的中文。\n                switch (type) {\n                    case \"first\": return \"首页\";\n                    case \"prev\": return \"上一页\";\n                    case \"next\": return \"下一页\";\n                    case \"last\": return \"末页\";\n                    case \"page\": return page;\n                }\n            },\n            pageUrl: function(type, page, current){\n                return \"/admin/articles/index?page=\"+page;\n    \n            }\n        });\n    </script>\n```\n', '35', '2018-03-20 15:20:30', '8', '0', '0', null, '2018-03', 'bootstrap,分页', '1');

-- ----------------------------
-- Table structure for blog_info
-- ----------------------------
DROP TABLE IF EXISTS `blog_info`;
CREATE TABLE `blog_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `real_name` varchar(50) NOT NULL DEFAULT '0' COMMENT '博主姓名',
  `job` varchar(50) NOT NULL DEFAULT '' COMMENT '职业',
  `email` varchar(50) NOT NULL DEFAULT '0' COMMENT '邮箱',
  `city` varchar(20) NOT NULL COMMENT '定位的省',
  `country` varchar(20) NOT NULL DEFAULT '0' COMMENT '定位 市',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `nick_name` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `motto` varchar(255) NOT NULL DEFAULT '' COMMENT '座右铭',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='博主信息';

-- ----------------------------
-- Records of blog_info
-- ----------------------------
INSERT INTO `blog_info` VALUES ('1', 'wys', 'php开发', 'hxl2219@163.com', '广东', '深圳', '2018-03-20 16:20:31', 'brady', '你的就是我的,我的还是我的', '13040798630', null);

-- ----------------------------
-- Table structure for category
-- ----------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  `category_order` int(50) NOT NULL DEFAULT '1',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of category
-- ----------------------------
INSERT INTO `category` VALUES ('13', '碎言碎语', '1', null);
INSERT INTO `category` VALUES ('34', '杂谈', '1', '2018-03-06 20:22:42');
INSERT INTO `category` VALUES ('35', '学无止境', '1', '2018-03-06 20:27:33');

-- ----------------------------
-- Table structure for daily_sentence
-- ----------------------------
DROP TABLE IF EXISTS `daily_sentence`;
CREATE TABLE `daily_sentence` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '英文内容',
  `note` varchar(255) NOT NULL DEFAULT '' COMMENT '中文内容',
  `translation` varchar(255) NOT NULL DEFAULT '' COMMENT '词霸小编',
  `picture` varchar(100) NOT NULL DEFAULT '' COMMENT '小图片地址',
  `picture2` varchar(100) DEFAULT '' COMMENT '大图片地址',
  `dateline` varchar(50) DEFAULT '' COMMENT '时间',
  PRIMARY KEY (`id`),
  KEY `INDEX_dateline` (`dateline`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='每日一句';

-- ----------------------------
-- Records of daily_sentence
-- ----------------------------
INSERT INTO `daily_sentence` VALUES ('1', 'Making a million friends is not a miracle. The miracle is to make  a friend who will stand by you when millions are against you.', '交许许多多的朋友不是什么奇迹，真正的奇迹是当所有人都弃你而去的时候，还有一个朋友坚定的站在你这一边。', '词霸小编：多个朋友多条路，可朋友多了也会迷路。', 'http://cdn.iciba.com/news/word/20180226.jpg', 'http://cdn.iciba.com/news/word/big_20180226b.jpg', '2018-02-26');
INSERT INTO `daily_sentence` VALUES ('2', 'Promises are often like the butterfly, which disappear after beautiful hover.', '承诺常常很像蝴蝶，美丽的盘旋后就不见了。', '词霸小编：陪伴是最长情的告白， 相守是最温暖的承诺。 ', 'http://cdn.iciba.com/news/word/20180227.jpg', 'http://cdn.iciba.com/news/word/big_20180227b.jpg', '2018-02-27');
INSERT INTO `daily_sentence` VALUES ('3', 'Fading is true while flowering is past.', '凋谢是真实的，盛开只是一种过去。', '词霸小编：风月若凋零繁花，华胥梦断，劫灰散尽，唯余暖香依旧。', 'http://cdn.iciba.com/news/word/20180228.jpg', 'http://cdn.iciba.com/news/word/big_20180228b.jpg', '2018-02-28');
INSERT INTO `daily_sentence` VALUES ('4', 'Some things are not see to insist, but insisted the will see hope.', '有些事情不是看到希望才去坚持，而是坚持了才会看到希望。', '词霸小编：人都是为希望而活，因为有了希望，人才有生活的勇气。你好，三月！', 'http://cdn.iciba.com/news/word/20180301.jpg', 'http://cdn.iciba.com/news/word/big_big_20180301b.jpg', '2018-03-01');
INSERT INTO `daily_sentence` VALUES ('5', 'Home is not where you live but where they understand you.', '栖身之所不一定是家，有人懂你之处才是家。', '词霸小编：正月十五，元宵节快乐! 因为元宵灯会，所以这一天也称得上是中国的情人节。当然也别忘了和家人团聚，一起赏花灯，猜灯谜。还有些霸粉明天要开学了，你们快乐寒假写完了吗？', 'http://cdn.iciba.com/news/word/2018-0302.jpg', 'http://cdn.iciba.com/news/word/big_2018-0302b.jpg', '2018-03-02');
INSERT INTO `daily_sentence` VALUES ('6', 'True friends will always find a way to help you. Fake friends will always find an excuse. ', '真心朋友总会找机会帮你，假意朋友总会找借口不帮。 ​​​​', '词霸小编：真朋友，淡中如水；假朋友，蜜里调油。因为在困难面前，前者生死相依，后者各奔东西，能跑多远跑多远。', 'http://cdn.iciba.com/news/word/20180302.jpg', 'http://cdn.iciba.com/news/word/big_big_20180302b.jpg', '2018-03-03');
INSERT INTO `daily_sentence` VALUES ('7', 'I don\'t let myself down, because I have met you in my most gorgeous age.', '能够在最美的时光和你相遇，才算没有辜负自己。', '词霸小编：因为有了人海，所以我们的相遇才显得那么意外。', 'http://cdn.iciba.com/news/word/20180304.jpg', 'http://cdn.iciba.com/news/word/big_20180304b.jpg', '2018-03-04');
INSERT INTO `daily_sentence` VALUES ('8', 'Don\'t be afraid of the darkness. Don\'t be afraid to chase your dreams. Don\'t be afraid to be yourself. Follow your own path.', '不要害怕黑暗，不要害怕追寻自己的梦想，不要害怕做与众不同的自己，走你自己的路。', '词霸小编：不要把全世界都放在自己的肩上，去做自己喜欢的事吧！做自己的梦，走自己的路，爱自己所爱的人。', 'http://cdn.iciba.com/news/word/20180305.jpg', 'http://cdn.iciba.com/news/word/big_20180305b.jpg', '2018-03-05');
INSERT INTO `daily_sentence` VALUES ('9', 'True love is not the temporary likeness, and I know it\'s the feeling—meeting you is hard and it will be a pity if I miss you.', '真正的爱情不是一时好感，而是我知道遇到你不容易，错过了会很可惜。 ', '词霸小编：当错过了失去了，忏悔的你，是否还能换回那颗，善良的心？这个拿青春和你赌的人，好好珍惜，有个爱你的人不容易。', 'http://cdn.iciba.com/news/word/20180306.jpg', 'http://cdn.iciba.com/news/word/big_20180306b.jpg', '2018-03-06');
INSERT INTO `daily_sentence` VALUES ('10', 'It takes a great deal of bravery to stand up to your enemies, but a great deal more to stand up to your friends.', '面对敌人需要勇气，但敢于直面朋友，需要更大的勇气。', '词霸小编：我们都知道与敌人对抗需要勇气，但很少有人承认与朋友对抗也需要勇气。特别是在学生时代，如果朋友之间的调侃，玩笑，没有把握好尺度，随时可能变成校园霸凌。但如果你勇敢对抗，就可能变得不受欢迎，甚至失去朋友，所以很多人选择默默承受。', 'http://cdn.iciba.com/news/word/20180307.jpg', 'http://cdn.iciba.com/news/word/big_20180307b.jpg', '2018-03-07');
INSERT INTO `daily_sentence` VALUES ('11', 'Falling in love with yourself first doesn’t make you vain or selfish, it makes you indestructible. ', '先爱自己不会让你变得无用或自私，它会让你无坚不摧。', '词霸小编：有人曾说：“女人在婚姻中渐渐明白，最重要的或许不是嫁谁，而是无论嫁了谁，都要有让自己幸福的能力。”今天是妇女节，别忘了给自己放个假，也别忘了给妈妈打个电话。', 'http://cdn.iciba.com/news/word/20180309.jpg', 'http://cdn.iciba.com/news/word/big_20180309b.jpg', '2018-03-08');
INSERT INTO `daily_sentence` VALUES ('12', 'Love is putting someone else\'s needs before yours.', '爱，就是把那个人的需要，看得比自己还重要。', '词霸小编：无论是亲情还是爱情，不都是把对方的需要放置于自己之上？不管是何种性质的爱，亘久不变的便是无私，打心底的为对方着想，始终将其摆在第一位。', 'http://cdn.iciba.com/news/word/20180308.jpg', 'http://cdn.iciba.com/news/word/big_20180308b.jpg', '2018-03-09');
INSERT INTO `daily_sentence` VALUES ('13', 'Water can carve its way, even through stone. And when trapped, water makes a new path.', '水滴能穿石，即便困于一隅，也能另辟蹊径。', '词霸小编：虽然常说条条大路通罗马，但实在去不了，可以去土耳其啊！', 'http://cdn.iciba.com/news/word/20180310.jpg', 'http://cdn.iciba.com/news/word/big_20180310b.jpg', '2018-03-10');
INSERT INTO `daily_sentence` VALUES ('14', 'A real friend is one who walks in when the rest of the world walks out.', '真正的朋友是‘全世界与我为敌，他与全世界为敌’。', '词霸小编：真正的朋友，不会刻意装给对方看，也不会通过装来收获友谊。能与你沉沉浮浮，共患难，便是真朋友。', 'http://cdn.iciba.com/news/word/20180311.jpg', 'http://cdn.iciba.com/news/word/big_20180311b.jpg', '2018-03-11');
INSERT INTO `daily_sentence` VALUES ('15', 'Life is not always what we want it to be. We fight. We cry. And sometimes, we give up. But there is always hope in our heart.', ' 生活有时不尽如人意。我们挣扎、哭泣，有时甚至放弃。但内心始终充满希望。', '词霸小编：世事不能说死，有些事情总值得尝试。永不轻言放弃，前方总有希望在等待。愿你永远年轻，永远热泪盈眶！然后今天是植树节，要给自己种下一颗新希望哦~', 'http://cdn.iciba.com/news/word/20180312.jpg', 'http://cdn.iciba.com/news/word/big_20180312b.jpg', '2018-03-12');
INSERT INTO `daily_sentence` VALUES ('16', 'I know someone in the world is waiting for me, although I\'ve no idea of who he is. But I feel happy every day for this.', '我知道这世上有人在等我，尽管我不知道我在等谁。但是因为这样，我每天都非常快乐。', '词霸小编：世上最幸福的事就是找个喜欢你真是面目的人，不管你是坏脾气还是好脾气，丑的，帅的，哪怕你放个屁都他都觉得香。然而在对的时间遇到对的人，恰恰需要耐心等待，在你的世界中总会有个人比想象中爱你。', 'http://cdn.iciba.com/news/word/20180313.jpg', 'http://cdn.iciba.com/news/word/big_20180313b.jpg', '2018-03-13');
INSERT INTO `daily_sentence` VALUES ('17', 'Every soil,where he is well,is to a valiant man his natural country.', '勇敢的人随遇而安, 所到之处都是故乡。', '词霸小编：人本过客来无处，休说故里在何方，随遇而安无不可，人间到处有花香。', 'http://cdn.iciba.com/news/word/20180314.jpg', 'http://cdn.iciba.com/news/word/big_20180314b.jpg', '2018-03-14');
INSERT INTO `daily_sentence` VALUES ('18', 'look up at the stars, not down at your feet.', '请仰望星空，而不要俯视脚下。', '词霸小编：不要让身体的不便限制了你的灵魂，无论生活如何艰难，要保持一颗好奇心。', 'http://cdn.iciba.com/news/word/201803-15.jpg', 'http://cdn.iciba.com/news/word/big_201803-15b.jpg', '2018-03-15');
INSERT INTO `daily_sentence` VALUES ('19', 'Sometimes people don\'t need advice, they just need someone to listen and care.', '有时候人们需要的不是建议，而是能有一个人来倾听和关心自己。', '词霸小编：一定要有几个这样的朋友，当你在他们面前可以很自然的相处，你们可以彼此倾听对方的心声，让你不用处事圆滑的面对每一个人，让你不用觉得每时每刻都那么累。 ​​​​', 'http://cdn.iciba.com/news/word/20180316.jpg', 'http://cdn.iciba.com/news/word/big_20180316b.jpg', '2018-03-16');
INSERT INTO `daily_sentence` VALUES ('20', 'Don\'t push your friends into your stuff, let them find it and like it if they want to.', '不要强迫你的朋友尝试去爱你所爱的，除非他们自已愿意。', '词霸小编：人和人成长环境天差地别，每个人也各有各的三观和苦衷。人一生做好三件事就够了，知道什么是对的，去做，然而不强迫别人也去做。', 'http://cdn.iciba.com/news/word/20180317.jpg', 'http://cdn.iciba.com/news/word/big_20180317b.jpg', '2018-03-17');
INSERT INTO `daily_sentence` VALUES ('21', ' If you truly take care of your heart, you will be amazed at how much girls start lining up at your front door.', '如果你真的照顾好自己的心，你会惊讶的发现，不知有多少那样的姑娘在你门前排长队。', '词霸小编：这个世界上，最让人畏惧的恰恰是通向自己内心的路，唯有你的专一与坚守，决定了你有多少人会在未来等你，在每个路口拥抱你。', 'http://cdn.iciba.com/news/word/20180318.jpg', 'http://cdn.iciba.com/news/word/big_20180318b.jpg', '2018-03-18');
INSERT INTO `daily_sentence` VALUES ('22', 'We used to look up at the sky and wonder at our place in the stars, now we just look down and worry about our place in the dirt.', '我们曾经仰望星空，思考我们在宇宙中的位置，而现在我们只会低着头，担心如何在这片土地上活下去。', '词霸小编：每一个人都在各自的城为了生活而拼命努力，美丽的梦想，与现实相遇，也许只能够暂时搁置一旁，毕竟为了生活，你不得不做一些退让。', 'http://cdn.iciba.com/news/word/20180319.jpg', 'http://cdn.iciba.com/news/word/big_20180319b.jpg', '2018-03-19');
INSERT INTO `daily_sentence` VALUES ('23', 'We all live in the past. We take a minute to have a crush on someone, an hour to like someone, and a day to love someone, but a lifetime to forget someone.', '我们都生活在过往。我们会用一分钟的时间去迷恋一个人，用一小时的时间去喜欢一个人，再用一天的时间去爱上一个人，最终用一辈子的时间去忘记一个人。', '词霸小编：当你试图忘记一个人时，其实你心里已经想他好几百次了，既然忘不掉，不如在心里放好。', 'http://cdn.iciba.com/news/word/20180320.jpg', 'http://cdn.iciba.com/news/word/big_20180320b.jpg', '2018-03-20');
INSERT INTO `daily_sentence` VALUES ('24', 'Destiny is something we\'ve invented because we can\'t stand the fact that everything that happens is accidental.', '所谓命运不过是托辞，因为我们无法忍受所有事情发生都是偶然的事实。', '词霸小编：命运让你独自面对一些事情一定有它的道理，只有脆弱的人才会四处游说自己的不幸，坚强的人只会不动声色地愈渐强大 。', 'http://cdn.iciba.com/news/word/20180321.jpg', 'http://cdn.iciba.com/news/word/big_20180321b.jpg', '2018-03-21');
INSERT INTO `daily_sentence` VALUES ('25', 'Don\'t walk in front of me, I may not follow. Don\'t walk behind me, I may not lead.  Walk beside me, just be my friend.', '不要走在我前面，我可能追不上你 ；不要走在我后面，我可能不会引路；走在我旁边，做我的朋友就好。', '词霸小编：这个世界就是这样的，有些人陪我永远，我陪有些人一段。有些人陪我一段，我陪有些人永远。至少我们曾一起同行，就很足够了。', 'http://cdn.iciba.com/news/word/20180322.jpg', 'http://cdn.iciba.com/news/word/big_20180322b.jpg', '2018-03-22');
INSERT INTO `daily_sentence` VALUES ('26', 'It is possible for ordinary people to choose to be extraordinary. ', '平凡人也能够选择做到不平凡。', '词霸小编：梦想可以把汽车送往太空，可以让火箭回收利用，可以让不可能成为可能。很多人羡慕那种生下来就知道自己人生目标的人，但别忘了，他们也是平凡人中的一员。', 'http://cdn.iciba.com/news/word/201803-23.jpg', 'http://cdn.iciba.com/news/word/big_201803-23b.jpg', '2018-03-23');
INSERT INTO `daily_sentence` VALUES ('27', 'It is only with the heart that one can see rightly. What\'s essential is invisible to the eyes. ', '一个人只有用心去看，才能看到真实。事情的真相只用眼睛是看不见的。', '词霸小编：生活就是这么现实，真真假假总是对立存在。对于一个事物，你不要急于用批判的眼光看待，因为那不一定就是你认为的真相。', 'http://cdn.iciba.com/news/word/20180315.jpg', 'http://cdn.iciba.com/news/word/big_20180315b.jpg', '2018-03-24');
INSERT INTO `daily_sentence` VALUES ('28', 'There will always be prettier and uglier people than you. Accept it and move on.', '在这个世界上，多得是比你丑和比你美的人。所以，不必纠结太多，勇往前行。', '词霸小编：既能够因当前的风景而动容，也能够冲着远方微微一笑，然后勇敢前行。只要活下去，我们都是孤独且骄傲的英雄。', 'http://cdn.iciba.com/news/word/20180325.jpg', 'http://cdn.iciba.com/news/word/big_20180325b.jpg', '2018-03-25');
INSERT INTO `daily_sentence` VALUES ('29', 'I don\'t know if we each have a destiny, or if we\'re all just floating around accident alike on a breeze.', '我不懂，是我们有着各自不同的命运，还是，我们只不过都是在风中，茫然飘荡。', '词霸小编：这个世界没有那么多理所当然，所谓命运，还是得自己一步步走出来，即便是与你相遇，也是命运把我们撮合。', 'http://cdn.iciba.com/news/word/201803-26.jpg', 'http://cdn.iciba.com/news/word/big_201803-26b.jpg', '2018-03-26');

-- ----------------------------
-- Table structure for friend_ship
-- ----------------------------
DROP TABLE IF EXISTS `friend_ship`;
CREATE TABLE `friend_ship` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `link_name` varchar(200) DEFAULT NULL,
  `link` varchar(200) DEFAULT NULL,
  `link_order` int(50) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of friend_ship
-- ----------------------------
INSERT INTO `friend_ship` VALUES ('4', 'brady', 'http://yeve.cn', '1');
INSERT INTO `friend_ship` VALUES ('5', 'q\'we\'q', 'qeqw', '1');
INSERT INTO `friend_ship` VALUES ('6', '34234', '234234', '1');
INSERT INTO `friend_ship` VALUES ('7', '23243', '234234', '1');

-- ----------------------------
-- Table structure for logs_cron
-- ----------------------------
DROP TABLE IF EXISTS `logs_cron`;
CREATE TABLE `logs_cron` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` text,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=967 DEFAULT CHARSET=utf8 COMMENT='计划任务日志表';

-- ----------------------------
-- Records of logs_cron
-- ----------------------------
INSERT INTO `logs_cron` VALUES ('1', '\'获取每日一句成功\'', '2018-03-10 16:10:35');
INSERT INTO `logs_cron` VALUES ('2', '\'获取每日一句成功\'', '2018-03-10 16:10:56');
INSERT INTO `logs_cron` VALUES ('3', '\'获取每日一句成功\'', '2018-03-10 16:11:35');
INSERT INTO `logs_cron` VALUES ('4', '\'获取每日一句成功\'', '2018-03-10 16:12:51');
INSERT INTO `logs_cron` VALUES ('5', '\'获取每日一句成功\'', '2018-03-10 16:16:09');
INSERT INTO `logs_cron` VALUES ('6', '\'获取每日一句成功\'', '2018-03-11 01:00:01');
INSERT INTO `logs_cron` VALUES ('7', '\'获取每日一句成功\'', '2018-03-11 01:01:01');
INSERT INTO `logs_cron` VALUES ('8', '\'获取每日一句成功\'', '2018-03-11 01:02:01');
INSERT INTO `logs_cron` VALUES ('9', '\'获取每日一句成功\'', '2018-03-11 01:03:01');
INSERT INTO `logs_cron` VALUES ('10', '\'获取每日一句成功\'', '2018-03-11 01:04:01');
INSERT INTO `logs_cron` VALUES ('11', '\'获取每日一句成功\'', '2018-03-11 01:05:01');
INSERT INTO `logs_cron` VALUES ('12', '\'获取每日一句成功\'', '2018-03-11 01:06:01');
INSERT INTO `logs_cron` VALUES ('13', '\'获取每日一句成功\'', '2018-03-11 01:07:01');
INSERT INTO `logs_cron` VALUES ('14', '\'获取每日一句成功\'', '2018-03-11 01:08:01');
INSERT INTO `logs_cron` VALUES ('15', '\'获取每日一句成功\'', '2018-03-11 01:09:01');
INSERT INTO `logs_cron` VALUES ('16', '\'获取每日一句成功\'', '2018-03-11 01:10:01');
INSERT INTO `logs_cron` VALUES ('17', '\'获取每日一句成功\'', '2018-03-11 01:11:01');
INSERT INTO `logs_cron` VALUES ('18', '\'获取每日一句成功\'', '2018-03-11 01:12:01');
INSERT INTO `logs_cron` VALUES ('19', '\'获取每日一句成功\'', '2018-03-11 01:13:02');
INSERT INTO `logs_cron` VALUES ('20', '\'获取每日一句成功\'', '2018-03-11 01:14:01');
INSERT INTO `logs_cron` VALUES ('21', '\'获取每日一句成功\'', '2018-03-11 01:15:01');
INSERT INTO `logs_cron` VALUES ('22', '\'获取每日一句成功\'', '2018-03-11 01:16:01');
INSERT INTO `logs_cron` VALUES ('23', '\'获取每日一句成功\'', '2018-03-11 01:17:01');
INSERT INTO `logs_cron` VALUES ('24', '\'获取每日一句成功\'', '2018-03-11 01:18:01');
INSERT INTO `logs_cron` VALUES ('25', '\'获取每日一句成功\'', '2018-03-11 01:19:01');
INSERT INTO `logs_cron` VALUES ('26', '\'获取每日一句成功\'', '2018-03-11 01:20:01');
INSERT INTO `logs_cron` VALUES ('27', '\'获取每日一句成功\'', '2018-03-11 01:21:01');
INSERT INTO `logs_cron` VALUES ('28', '\'获取每日一句成功\'', '2018-03-11 01:22:01');
INSERT INTO `logs_cron` VALUES ('29', '\'获取每日一句成功\'', '2018-03-11 01:23:01');
INSERT INTO `logs_cron` VALUES ('30', '\'获取每日一句成功\'', '2018-03-11 01:24:01');
INSERT INTO `logs_cron` VALUES ('31', '\'获取每日一句成功\'', '2018-03-11 01:25:01');
INSERT INTO `logs_cron` VALUES ('32', '\'获取每日一句成功\'', '2018-03-11 01:26:01');
INSERT INTO `logs_cron` VALUES ('33', '\'获取每日一句成功\'', '2018-03-11 01:27:02');
INSERT INTO `logs_cron` VALUES ('34', '\'获取每日一句成功\'', '2018-03-11 01:28:01');
INSERT INTO `logs_cron` VALUES ('35', '\'获取每日一句成功\'', '2018-03-11 01:29:01');
INSERT INTO `logs_cron` VALUES ('36', '\'获取每日一句成功\'', '2018-03-11 01:30:01');
INSERT INTO `logs_cron` VALUES ('37', '\'获取每日一句成功\'', '2018-03-11 01:31:01');
INSERT INTO `logs_cron` VALUES ('38', '\'获取每日一句成功\'', '2018-03-11 01:32:01');
INSERT INTO `logs_cron` VALUES ('39', '\'获取每日一句成功\'', '2018-03-11 01:33:01');
INSERT INTO `logs_cron` VALUES ('40', '\'获取每日一句成功\'', '2018-03-11 01:34:01');
INSERT INTO `logs_cron` VALUES ('41', '\'获取每日一句成功\'', '2018-03-11 01:35:01');
INSERT INTO `logs_cron` VALUES ('42', '\'获取每日一句成功\'', '2018-03-11 01:36:01');
INSERT INTO `logs_cron` VALUES ('43', '\'获取每日一句成功\'', '2018-03-11 01:37:01');
INSERT INTO `logs_cron` VALUES ('44', '\'获取每日一句成功\'', '2018-03-11 01:38:01');
INSERT INTO `logs_cron` VALUES ('45', '\'获取每日一句成功\'', '2018-03-11 01:39:01');
INSERT INTO `logs_cron` VALUES ('46', '\'获取每日一句成功\'', '2018-03-11 01:40:02');
INSERT INTO `logs_cron` VALUES ('47', '\'获取每日一句成功\'', '2018-03-11 01:41:01');
INSERT INTO `logs_cron` VALUES ('48', '\'获取每日一句成功\'', '2018-03-11 01:42:01');
INSERT INTO `logs_cron` VALUES ('49', '\'获取每日一句成功\'', '2018-03-11 01:43:01');
INSERT INTO `logs_cron` VALUES ('50', '\'获取每日一句成功\'', '2018-03-11 01:44:01');
INSERT INTO `logs_cron` VALUES ('51', '\'获取每日一句成功\'', '2018-03-11 01:45:01');
INSERT INTO `logs_cron` VALUES ('52', '\'获取每日一句成功\'', '2018-03-11 01:46:01');
INSERT INTO `logs_cron` VALUES ('53', '\'获取每日一句成功\'', '2018-03-11 01:47:01');
INSERT INTO `logs_cron` VALUES ('54', '\'获取每日一句成功\'', '2018-03-11 01:48:01');
INSERT INTO `logs_cron` VALUES ('55', '\'获取每日一句成功\'', '2018-03-11 01:49:01');
INSERT INTO `logs_cron` VALUES ('56', '\'获取每日一句成功\'', '2018-03-11 01:50:01');
INSERT INTO `logs_cron` VALUES ('57', '\'获取每日一句成功\'', '2018-03-11 01:51:01');
INSERT INTO `logs_cron` VALUES ('58', '\'获取每日一句成功\'', '2018-03-11 01:52:01');
INSERT INTO `logs_cron` VALUES ('59', '\'获取每日一句成功\'', '2018-03-11 01:53:01');
INSERT INTO `logs_cron` VALUES ('60', '\'获取每日一句成功\'', '2018-03-11 01:54:01');
INSERT INTO `logs_cron` VALUES ('61', '\'获取每日一句成功\'', '2018-03-11 01:55:01');
INSERT INTO `logs_cron` VALUES ('62', '\'获取每日一句成功\'', '2018-03-11 01:56:01');
INSERT INTO `logs_cron` VALUES ('63', '\'获取每日一句成功\'', '2018-03-11 01:57:01');
INSERT INTO `logs_cron` VALUES ('64', '\'获取每日一句成功\'', '2018-03-11 01:58:01');
INSERT INTO `logs_cron` VALUES ('65', '\'获取每日一句成功\'', '2018-03-11 01:59:01');
INSERT INTO `logs_cron` VALUES ('66', '\'获取每日一句成功\'', '2018-03-11 12:38:23');
INSERT INTO `logs_cron` VALUES ('67', '\'获取每日一句成功\'', '2018-03-12 01:00:01');
INSERT INTO `logs_cron` VALUES ('68', '\'获取每日一句成功\'', '2018-03-12 01:01:01');
INSERT INTO `logs_cron` VALUES ('69', '\'获取每日一句成功\'', '2018-03-12 01:02:01');
INSERT INTO `logs_cron` VALUES ('70', '\'获取每日一句成功\'', '2018-03-12 01:03:01');
INSERT INTO `logs_cron` VALUES ('71', '\'获取每日一句成功\'', '2018-03-12 01:04:01');
INSERT INTO `logs_cron` VALUES ('72', '\'获取每日一句成功\'', '2018-03-12 01:05:01');
INSERT INTO `logs_cron` VALUES ('73', '\'获取每日一句成功\'', '2018-03-12 01:06:01');
INSERT INTO `logs_cron` VALUES ('74', '\'获取每日一句成功\'', '2018-03-12 01:07:01');
INSERT INTO `logs_cron` VALUES ('75', '\'获取每日一句成功\'', '2018-03-12 01:08:01');
INSERT INTO `logs_cron` VALUES ('76', '\'获取每日一句成功\'', '2018-03-12 01:09:01');
INSERT INTO `logs_cron` VALUES ('77', '\'获取每日一句成功\'', '2018-03-12 01:10:01');
INSERT INTO `logs_cron` VALUES ('78', '\'获取每日一句成功\'', '2018-03-12 01:11:01');
INSERT INTO `logs_cron` VALUES ('79', '\'获取每日一句成功\'', '2018-03-12 01:12:01');
INSERT INTO `logs_cron` VALUES ('80', '\'获取每日一句成功\'', '2018-03-12 01:13:01');
INSERT INTO `logs_cron` VALUES ('81', '\'获取每日一句成功\'', '2018-03-12 01:14:01');
INSERT INTO `logs_cron` VALUES ('82', '\'获取每日一句成功\'', '2018-03-12 01:15:01');
INSERT INTO `logs_cron` VALUES ('83', '\'获取每日一句成功\'', '2018-03-12 01:16:01');
INSERT INTO `logs_cron` VALUES ('84', '\'获取每日一句成功\'', '2018-03-12 01:17:01');
INSERT INTO `logs_cron` VALUES ('85', '\'获取每日一句成功\'', '2018-03-12 01:18:01');
INSERT INTO `logs_cron` VALUES ('86', '\'获取每日一句成功\'', '2018-03-12 01:19:01');
INSERT INTO `logs_cron` VALUES ('87', '\'获取每日一句成功\'', '2018-03-12 01:20:01');
INSERT INTO `logs_cron` VALUES ('88', '\'获取每日一句成功\'', '2018-03-12 01:21:01');
INSERT INTO `logs_cron` VALUES ('89', '\'获取每日一句成功\'', '2018-03-12 01:22:02');
INSERT INTO `logs_cron` VALUES ('90', '\'获取每日一句成功\'', '2018-03-12 01:23:01');
INSERT INTO `logs_cron` VALUES ('91', '\'获取每日一句成功\'', '2018-03-12 01:24:01');
INSERT INTO `logs_cron` VALUES ('92', '\'获取每日一句成功\'', '2018-03-12 01:25:01');
INSERT INTO `logs_cron` VALUES ('93', '\'获取每日一句成功\'', '2018-03-12 01:26:01');
INSERT INTO `logs_cron` VALUES ('94', '\'获取每日一句成功\'', '2018-03-12 01:27:01');
INSERT INTO `logs_cron` VALUES ('95', '\'获取每日一句成功\'', '2018-03-12 01:28:01');
INSERT INTO `logs_cron` VALUES ('96', '\'获取每日一句成功\'', '2018-03-12 01:29:01');
INSERT INTO `logs_cron` VALUES ('97', '\'获取每日一句成功\'', '2018-03-12 01:30:01');
INSERT INTO `logs_cron` VALUES ('98', '\'获取每日一句成功\'', '2018-03-12 01:31:01');
INSERT INTO `logs_cron` VALUES ('99', '\'获取每日一句成功\'', '2018-03-12 01:32:01');
INSERT INTO `logs_cron` VALUES ('100', '\'获取每日一句成功\'', '2018-03-12 01:33:01');
INSERT INTO `logs_cron` VALUES ('101', '\'获取每日一句成功\'', '2018-03-12 01:34:01');
INSERT INTO `logs_cron` VALUES ('102', '\'获取每日一句成功\'', '2018-03-12 01:35:01');
INSERT INTO `logs_cron` VALUES ('103', '\'获取每日一句成功\'', '2018-03-12 01:36:02');
INSERT INTO `logs_cron` VALUES ('104', '\'获取每日一句成功\'', '2018-03-12 01:37:01');
INSERT INTO `logs_cron` VALUES ('105', '\'获取每日一句成功\'', '2018-03-12 01:38:01');
INSERT INTO `logs_cron` VALUES ('106', '\'获取每日一句成功\'', '2018-03-12 01:39:01');
INSERT INTO `logs_cron` VALUES ('107', '\'获取每日一句成功\'', '2018-03-12 01:40:01');
INSERT INTO `logs_cron` VALUES ('108', '\'获取每日一句成功\'', '2018-03-12 01:41:01');
INSERT INTO `logs_cron` VALUES ('109', '\'获取每日一句成功\'', '2018-03-12 01:42:01');
INSERT INTO `logs_cron` VALUES ('110', '\'获取每日一句成功\'', '2018-03-12 01:43:01');
INSERT INTO `logs_cron` VALUES ('111', '\'获取每日一句成功\'', '2018-03-12 01:44:01');
INSERT INTO `logs_cron` VALUES ('112', '\'获取每日一句成功\'', '2018-03-12 01:45:01');
INSERT INTO `logs_cron` VALUES ('113', '\'获取每日一句成功\'', '2018-03-12 01:46:01');
INSERT INTO `logs_cron` VALUES ('114', '\'获取每日一句成功\'', '2018-03-12 01:47:01');
INSERT INTO `logs_cron` VALUES ('115', '\'获取每日一句成功\'', '2018-03-12 01:48:01');
INSERT INTO `logs_cron` VALUES ('116', '\'获取每日一句成功\'', '2018-03-12 01:49:02');
INSERT INTO `logs_cron` VALUES ('117', '\'获取每日一句成功\'', '2018-03-12 01:50:01');
INSERT INTO `logs_cron` VALUES ('118', '\'获取每日一句成功\'', '2018-03-12 01:51:01');
INSERT INTO `logs_cron` VALUES ('119', '\'获取每日一句成功\'', '2018-03-12 01:52:01');
INSERT INTO `logs_cron` VALUES ('120', '\'获取每日一句成功\'', '2018-03-12 01:53:01');
INSERT INTO `logs_cron` VALUES ('121', '\'获取每日一句成功\'', '2018-03-12 01:54:01');
INSERT INTO `logs_cron` VALUES ('122', '\'获取每日一句成功\'', '2018-03-12 01:55:01');
INSERT INTO `logs_cron` VALUES ('123', '\'获取每日一句成功\'', '2018-03-12 01:56:01');
INSERT INTO `logs_cron` VALUES ('124', '\'获取每日一句成功\'', '2018-03-12 01:57:01');
INSERT INTO `logs_cron` VALUES ('125', '\'获取每日一句成功\'', '2018-03-12 01:58:01');
INSERT INTO `logs_cron` VALUES ('126', '\'获取每日一句成功\'', '2018-03-12 01:59:01');
INSERT INTO `logs_cron` VALUES ('127', '\'获取每日一句成功\'', '2018-03-13 01:00:01');
INSERT INTO `logs_cron` VALUES ('128', '\'获取每日一句成功\'', '2018-03-13 01:01:01');
INSERT INTO `logs_cron` VALUES ('129', '\'获取每日一句成功\'', '2018-03-13 01:02:01');
INSERT INTO `logs_cron` VALUES ('130', '\'获取每日一句成功\'', '2018-03-13 01:03:01');
INSERT INTO `logs_cron` VALUES ('131', '\'获取每日一句成功\'', '2018-03-13 01:04:02');
INSERT INTO `logs_cron` VALUES ('132', '\'获取每日一句成功\'', '2018-03-13 01:05:01');
INSERT INTO `logs_cron` VALUES ('133', '\'获取每日一句成功\'', '2018-03-13 01:06:01');
INSERT INTO `logs_cron` VALUES ('134', '\'获取每日一句成功\'', '2018-03-13 01:07:01');
INSERT INTO `logs_cron` VALUES ('135', '\'获取每日一句成功\'', '2018-03-13 01:08:01');
INSERT INTO `logs_cron` VALUES ('136', '\'获取每日一句成功\'', '2018-03-13 01:09:01');
INSERT INTO `logs_cron` VALUES ('137', '\'获取每日一句成功\'', '2018-03-13 01:10:01');
INSERT INTO `logs_cron` VALUES ('138', '\'获取每日一句成功\'', '2018-03-13 01:11:01');
INSERT INTO `logs_cron` VALUES ('139', '\'获取每日一句成功\'', '2018-03-13 01:12:01');
INSERT INTO `logs_cron` VALUES ('140', '\'获取每日一句成功\'', '2018-03-13 01:13:01');
INSERT INTO `logs_cron` VALUES ('141', '\'获取每日一句成功\'', '2018-03-13 01:14:01');
INSERT INTO `logs_cron` VALUES ('142', '\'获取每日一句成功\'', '2018-03-13 01:15:01');
INSERT INTO `logs_cron` VALUES ('143', '\'获取每日一句成功\'', '2018-03-13 01:16:02');
INSERT INTO `logs_cron` VALUES ('144', '\'获取每日一句成功\'', '2018-03-13 01:17:01');
INSERT INTO `logs_cron` VALUES ('145', '\'获取每日一句成功\'', '2018-03-13 01:18:01');
INSERT INTO `logs_cron` VALUES ('146', '\'获取每日一句成功\'', '2018-03-13 01:19:01');
INSERT INTO `logs_cron` VALUES ('147', '\'获取每日一句成功\'', '2018-03-13 01:20:01');
INSERT INTO `logs_cron` VALUES ('148', '\'获取每日一句成功\'', '2018-03-13 01:21:01');
INSERT INTO `logs_cron` VALUES ('149', '\'获取每日一句成功\'', '2018-03-13 01:22:01');
INSERT INTO `logs_cron` VALUES ('150', '\'获取每日一句成功\'', '2018-03-13 01:23:01');
INSERT INTO `logs_cron` VALUES ('151', '\'获取每日一句成功\'', '2018-03-13 01:24:01');
INSERT INTO `logs_cron` VALUES ('152', '\'获取每日一句成功\'', '2018-03-13 01:25:01');
INSERT INTO `logs_cron` VALUES ('153', '\'获取每日一句成功\'', '2018-03-13 01:26:01');
INSERT INTO `logs_cron` VALUES ('154', '\'获取每日一句成功\'', '2018-03-13 01:27:01');
INSERT INTO `logs_cron` VALUES ('155', '\'获取每日一句成功\'', '2018-03-13 01:28:01');
INSERT INTO `logs_cron` VALUES ('156', '\'获取每日一句成功\'', '2018-03-13 01:29:01');
INSERT INTO `logs_cron` VALUES ('157', '\'获取每日一句成功\'', '2018-03-13 01:30:01');
INSERT INTO `logs_cron` VALUES ('158', '\'获取每日一句成功\'', '2018-03-13 01:31:01');
INSERT INTO `logs_cron` VALUES ('159', '\'获取每日一句成功\'', '2018-03-13 01:32:01');
INSERT INTO `logs_cron` VALUES ('160', '\'获取每日一句成功\'', '2018-03-13 01:33:01');
INSERT INTO `logs_cron` VALUES ('161', '\'获取每日一句成功\'', '2018-03-13 01:34:01');
INSERT INTO `logs_cron` VALUES ('162', '\'获取每日一句成功\'', '2018-03-13 01:35:01');
INSERT INTO `logs_cron` VALUES ('163', '\'获取每日一句成功\'', '2018-03-13 01:36:01');
INSERT INTO `logs_cron` VALUES ('164', '\'获取每日一句成功\'', '2018-03-13 01:37:01');
INSERT INTO `logs_cron` VALUES ('165', '\'获取每日一句成功\'', '2018-03-13 01:38:01');
INSERT INTO `logs_cron` VALUES ('166', '\'获取每日一句成功\'', '2018-03-13 01:39:01');
INSERT INTO `logs_cron` VALUES ('167', '\'获取每日一句成功\'', '2018-03-13 01:40:01');
INSERT INTO `logs_cron` VALUES ('168', '\'获取每日一句成功\'', '2018-03-13 01:41:01');
INSERT INTO `logs_cron` VALUES ('169', '\'获取每日一句成功\'', '2018-03-13 01:42:01');
INSERT INTO `logs_cron` VALUES ('170', '\'获取每日一句成功\'', '2018-03-13 01:43:02');
INSERT INTO `logs_cron` VALUES ('171', '\'获取每日一句成功\'', '2018-03-13 01:44:01');
INSERT INTO `logs_cron` VALUES ('172', '\'获取每日一句成功\'', '2018-03-13 01:45:01');
INSERT INTO `logs_cron` VALUES ('173', '\'获取每日一句成功\'', '2018-03-13 01:46:01');
INSERT INTO `logs_cron` VALUES ('174', '\'获取每日一句成功\'', '2018-03-13 01:47:01');
INSERT INTO `logs_cron` VALUES ('175', '\'获取每日一句成功\'', '2018-03-13 01:48:01');
INSERT INTO `logs_cron` VALUES ('176', '\'获取每日一句成功\'', '2018-03-13 01:49:01');
INSERT INTO `logs_cron` VALUES ('177', '\'获取每日一句成功\'', '2018-03-13 01:50:01');
INSERT INTO `logs_cron` VALUES ('178', '\'获取每日一句成功\'', '2018-03-13 01:51:01');
INSERT INTO `logs_cron` VALUES ('179', '\'获取每日一句成功\'', '2018-03-13 01:52:01');
INSERT INTO `logs_cron` VALUES ('180', '\'获取每日一句成功\'', '2018-03-13 01:53:01');
INSERT INTO `logs_cron` VALUES ('181', '\'获取每日一句成功\'', '2018-03-13 01:54:01');
INSERT INTO `logs_cron` VALUES ('182', '\'获取每日一句成功\'', '2018-03-13 01:55:01');
INSERT INTO `logs_cron` VALUES ('183', '\'获取每日一句成功\'', '2018-03-13 01:56:01');
INSERT INTO `logs_cron` VALUES ('184', '\'获取每日一句成功\'', '2018-03-13 01:57:02');
INSERT INTO `logs_cron` VALUES ('185', '\'获取每日一句成功\'', '2018-03-13 01:58:01');
INSERT INTO `logs_cron` VALUES ('186', '\'获取每日一句成功\'', '2018-03-13 01:59:01');
INSERT INTO `logs_cron` VALUES ('187', '\'获取每日一句成功\'', '2018-03-14 01:00:01');
INSERT INTO `logs_cron` VALUES ('188', '\'获取每日一句成功\'', '2018-03-14 01:01:01');
INSERT INTO `logs_cron` VALUES ('189', '\'获取每日一句成功\'', '2018-03-14 01:02:01');
INSERT INTO `logs_cron` VALUES ('190', '\'获取每日一句成功\'', '2018-03-14 01:03:01');
INSERT INTO `logs_cron` VALUES ('191', '\'获取每日一句成功\'', '2018-03-14 01:04:01');
INSERT INTO `logs_cron` VALUES ('192', '\'获取每日一句成功\'', '2018-03-14 01:05:01');
INSERT INTO `logs_cron` VALUES ('193', '\'获取每日一句成功\'', '2018-03-14 01:06:01');
INSERT INTO `logs_cron` VALUES ('194', '\'获取每日一句成功\'', '2018-03-14 01:07:01');
INSERT INTO `logs_cron` VALUES ('195', '\'获取每日一句成功\'', '2018-03-14 01:08:01');
INSERT INTO `logs_cron` VALUES ('196', '\'获取每日一句成功\'', '2018-03-14 01:09:01');
INSERT INTO `logs_cron` VALUES ('197', '\'获取每日一句成功\'', '2018-03-14 01:10:02');
INSERT INTO `logs_cron` VALUES ('198', '\'获取每日一句成功\'', '2018-03-14 01:11:01');
INSERT INTO `logs_cron` VALUES ('199', '\'获取每日一句成功\'', '2018-03-14 01:12:01');
INSERT INTO `logs_cron` VALUES ('200', '\'获取每日一句成功\'', '2018-03-14 01:13:01');
INSERT INTO `logs_cron` VALUES ('201', '\'获取每日一句成功\'', '2018-03-14 01:14:01');
INSERT INTO `logs_cron` VALUES ('202', '\'获取每日一句成功\'', '2018-03-14 01:15:01');
INSERT INTO `logs_cron` VALUES ('203', '\'获取每日一句成功\'', '2018-03-14 01:16:01');
INSERT INTO `logs_cron` VALUES ('204', '\'获取每日一句成功\'', '2018-03-14 01:17:01');
INSERT INTO `logs_cron` VALUES ('205', '\'获取每日一句成功\'', '2018-03-14 01:18:01');
INSERT INTO `logs_cron` VALUES ('206', '\'获取每日一句成功\'', '2018-03-14 01:19:01');
INSERT INTO `logs_cron` VALUES ('207', '\'获取每日一句成功\'', '2018-03-14 01:20:01');
INSERT INTO `logs_cron` VALUES ('208', '\'获取每日一句成功\'', '2018-03-14 01:21:01');
INSERT INTO `logs_cron` VALUES ('209', '\'获取每日一句成功\'', '2018-03-14 01:22:01');
INSERT INTO `logs_cron` VALUES ('210', '\'获取每日一句成功\'', '2018-03-14 01:23:02');
INSERT INTO `logs_cron` VALUES ('211', '\'获取每日一句成功\'', '2018-03-14 01:24:01');
INSERT INTO `logs_cron` VALUES ('212', '\'获取每日一句成功\'', '2018-03-14 01:25:01');
INSERT INTO `logs_cron` VALUES ('213', '\'获取每日一句成功\'', '2018-03-14 01:26:01');
INSERT INTO `logs_cron` VALUES ('214', '\'获取每日一句成功\'', '2018-03-14 01:27:01');
INSERT INTO `logs_cron` VALUES ('215', '\'获取每日一句成功\'', '2018-03-14 01:28:01');
INSERT INTO `logs_cron` VALUES ('216', '\'获取每日一句成功\'', '2018-03-14 01:29:01');
INSERT INTO `logs_cron` VALUES ('217', '\'获取每日一句成功\'', '2018-03-14 01:30:01');
INSERT INTO `logs_cron` VALUES ('218', '\'获取每日一句成功\'', '2018-03-14 01:31:01');
INSERT INTO `logs_cron` VALUES ('219', '\'获取每日一句成功\'', '2018-03-14 01:32:01');
INSERT INTO `logs_cron` VALUES ('220', '\'获取每日一句成功\'', '2018-03-14 01:33:01');
INSERT INTO `logs_cron` VALUES ('221', '\'获取每日一句成功\'', '2018-03-14 01:34:01');
INSERT INTO `logs_cron` VALUES ('222', '\'获取每日一句成功\'', '2018-03-14 01:35:01');
INSERT INTO `logs_cron` VALUES ('223', '\'获取每日一句成功\'', '2018-03-14 01:36:01');
INSERT INTO `logs_cron` VALUES ('224', '\'获取每日一句成功\'', '2018-03-14 01:37:02');
INSERT INTO `logs_cron` VALUES ('225', '\'获取每日一句成功\'', '2018-03-14 01:38:01');
INSERT INTO `logs_cron` VALUES ('226', '\'获取每日一句成功\'', '2018-03-14 01:39:01');
INSERT INTO `logs_cron` VALUES ('227', '\'获取每日一句成功\'', '2018-03-14 01:40:01');
INSERT INTO `logs_cron` VALUES ('228', '\'获取每日一句成功\'', '2018-03-14 01:41:01');
INSERT INTO `logs_cron` VALUES ('229', '\'获取每日一句成功\'', '2018-03-14 01:42:01');
INSERT INTO `logs_cron` VALUES ('230', '\'获取每日一句成功\'', '2018-03-14 01:43:01');
INSERT INTO `logs_cron` VALUES ('231', '\'获取每日一句成功\'', '2018-03-14 01:44:01');
INSERT INTO `logs_cron` VALUES ('232', '\'获取每日一句成功\'', '2018-03-14 01:45:01');
INSERT INTO `logs_cron` VALUES ('233', '\'获取每日一句成功\'', '2018-03-14 01:46:01');
INSERT INTO `logs_cron` VALUES ('234', '\'获取每日一句成功\'', '2018-03-14 01:47:01');
INSERT INTO `logs_cron` VALUES ('235', '\'获取每日一句成功\'', '2018-03-14 01:48:01');
INSERT INTO `logs_cron` VALUES ('236', '\'获取每日一句成功\'', '2018-03-14 01:49:01');
INSERT INTO `logs_cron` VALUES ('237', '\'获取每日一句成功\'', '2018-03-14 01:50:02');
INSERT INTO `logs_cron` VALUES ('238', '\'获取每日一句成功\'', '2018-03-14 01:51:01');
INSERT INTO `logs_cron` VALUES ('239', '\'获取每日一句成功\'', '2018-03-14 01:52:01');
INSERT INTO `logs_cron` VALUES ('240', '\'获取每日一句成功\'', '2018-03-14 01:53:01');
INSERT INTO `logs_cron` VALUES ('241', '\'获取每日一句成功\'', '2018-03-14 01:54:01');
INSERT INTO `logs_cron` VALUES ('242', '\'获取每日一句成功\'', '2018-03-14 01:55:01');
INSERT INTO `logs_cron` VALUES ('243', '\'获取每日一句成功\'', '2018-03-14 01:56:01');
INSERT INTO `logs_cron` VALUES ('244', '\'获取每日一句成功\'', '2018-03-14 01:57:01');
INSERT INTO `logs_cron` VALUES ('245', '\'获取每日一句成功\'', '2018-03-14 01:58:01');
INSERT INTO `logs_cron` VALUES ('246', '\'获取每日一句成功\'', '2018-03-14 01:59:01');
INSERT INTO `logs_cron` VALUES ('247', '\'获取每日一句成功\'', '2018-03-15 01:00:01');
INSERT INTO `logs_cron` VALUES ('248', '\'获取每日一句成功\'', '2018-03-15 01:01:02');
INSERT INTO `logs_cron` VALUES ('249', '\'获取每日一句成功\'', '2018-03-15 01:02:01');
INSERT INTO `logs_cron` VALUES ('250', '\'获取每日一句成功\'', '2018-03-15 01:03:01');
INSERT INTO `logs_cron` VALUES ('251', '\'获取每日一句成功\'', '2018-03-15 01:04:01');
INSERT INTO `logs_cron` VALUES ('252', '\'获取每日一句成功\'', '2018-03-15 01:05:01');
INSERT INTO `logs_cron` VALUES ('253', '\'获取每日一句成功\'', '2018-03-15 01:06:01');
INSERT INTO `logs_cron` VALUES ('254', '\'获取每日一句成功\'', '2018-03-15 01:07:01');
INSERT INTO `logs_cron` VALUES ('255', '\'获取每日一句成功\'', '2018-03-15 01:08:01');
INSERT INTO `logs_cron` VALUES ('256', '\'获取每日一句成功\'', '2018-03-15 01:09:01');
INSERT INTO `logs_cron` VALUES ('257', '\'获取每日一句成功\'', '2018-03-15 01:10:01');
INSERT INTO `logs_cron` VALUES ('258', '\'获取每日一句成功\'', '2018-03-15 01:11:01');
INSERT INTO `logs_cron` VALUES ('259', '\'获取每日一句成功\'', '2018-03-15 01:12:01');
INSERT INTO `logs_cron` VALUES ('260', '\'获取每日一句成功\'', '2018-03-15 01:13:01');
INSERT INTO `logs_cron` VALUES ('261', '\'获取每日一句成功\'', '2018-03-15 01:14:02');
INSERT INTO `logs_cron` VALUES ('262', '\'获取每日一句成功\'', '2018-03-15 01:15:01');
INSERT INTO `logs_cron` VALUES ('263', '\'获取每日一句成功\'', '2018-03-15 01:16:01');
INSERT INTO `logs_cron` VALUES ('264', '\'获取每日一句成功\'', '2018-03-15 01:17:01');
INSERT INTO `logs_cron` VALUES ('265', '\'获取每日一句成功\'', '2018-03-15 01:18:01');
INSERT INTO `logs_cron` VALUES ('266', '\'获取每日一句成功\'', '2018-03-15 01:19:01');
INSERT INTO `logs_cron` VALUES ('267', '\'获取每日一句成功\'', '2018-03-15 01:20:01');
INSERT INTO `logs_cron` VALUES ('268', '\'获取每日一句成功\'', '2018-03-15 01:21:01');
INSERT INTO `logs_cron` VALUES ('269', '\'获取每日一句成功\'', '2018-03-15 01:22:01');
INSERT INTO `logs_cron` VALUES ('270', '\'获取每日一句成功\'', '2018-03-15 01:23:01');
INSERT INTO `logs_cron` VALUES ('271', '\'获取每日一句成功\'', '2018-03-15 01:24:01');
INSERT INTO `logs_cron` VALUES ('272', '\'获取每日一句成功\'', '2018-03-15 01:25:01');
INSERT INTO `logs_cron` VALUES ('273', '\'获取每日一句成功\'', '2018-03-15 01:26:01');
INSERT INTO `logs_cron` VALUES ('274', '\'获取每日一句成功\'', '2018-03-15 01:27:02');
INSERT INTO `logs_cron` VALUES ('275', '\'获取每日一句成功\'', '2018-03-15 01:28:01');
INSERT INTO `logs_cron` VALUES ('276', '\'获取每日一句成功\'', '2018-03-15 01:29:01');
INSERT INTO `logs_cron` VALUES ('277', '\'获取每日一句成功\'', '2018-03-15 01:30:01');
INSERT INTO `logs_cron` VALUES ('278', '\'获取每日一句成功\'', '2018-03-15 01:31:01');
INSERT INTO `logs_cron` VALUES ('279', '\'获取每日一句成功\'', '2018-03-15 01:32:01');
INSERT INTO `logs_cron` VALUES ('280', '\'获取每日一句成功\'', '2018-03-15 01:33:01');
INSERT INTO `logs_cron` VALUES ('281', '\'获取每日一句成功\'', '2018-03-15 01:34:01');
INSERT INTO `logs_cron` VALUES ('282', '\'获取每日一句成功\'', '2018-03-15 01:35:01');
INSERT INTO `logs_cron` VALUES ('283', '\'获取每日一句成功\'', '2018-03-15 01:36:01');
INSERT INTO `logs_cron` VALUES ('284', '\'获取每日一句成功\'', '2018-03-15 01:37:01');
INSERT INTO `logs_cron` VALUES ('285', '\'获取每日一句成功\'', '2018-03-15 01:38:01');
INSERT INTO `logs_cron` VALUES ('286', '\'获取每日一句成功\'', '2018-03-15 01:39:02');
INSERT INTO `logs_cron` VALUES ('287', '\'获取每日一句成功\'', '2018-03-15 01:40:01');
INSERT INTO `logs_cron` VALUES ('288', '\'获取每日一句成功\'', '2018-03-15 01:41:01');
INSERT INTO `logs_cron` VALUES ('289', '\'获取每日一句成功\'', '2018-03-15 01:42:01');
INSERT INTO `logs_cron` VALUES ('290', '\'获取每日一句成功\'', '2018-03-15 01:43:01');
INSERT INTO `logs_cron` VALUES ('291', '\'获取每日一句成功\'', '2018-03-15 01:44:01');
INSERT INTO `logs_cron` VALUES ('292', '\'获取每日一句成功\'', '2018-03-15 01:45:01');
INSERT INTO `logs_cron` VALUES ('293', '\'获取每日一句成功\'', '2018-03-15 01:46:01');
INSERT INTO `logs_cron` VALUES ('294', '\'获取每日一句成功\'', '2018-03-15 01:47:01');
INSERT INTO `logs_cron` VALUES ('295', '\'获取每日一句成功\'', '2018-03-15 01:48:01');
INSERT INTO `logs_cron` VALUES ('296', '\'获取每日一句成功\'', '2018-03-15 01:49:01');
INSERT INTO `logs_cron` VALUES ('297', '\'获取每日一句成功\'', '2018-03-15 01:50:01');
INSERT INTO `logs_cron` VALUES ('298', '\'获取每日一句成功\'', '2018-03-15 01:51:01');
INSERT INTO `logs_cron` VALUES ('299', '\'获取每日一句成功\'', '2018-03-15 01:52:01');
INSERT INTO `logs_cron` VALUES ('300', '\'获取每日一句成功\'', '2018-03-15 01:53:02');
INSERT INTO `logs_cron` VALUES ('301', '\'获取每日一句成功\'', '2018-03-15 01:54:01');
INSERT INTO `logs_cron` VALUES ('302', '\'获取每日一句成功\'', '2018-03-15 01:55:01');
INSERT INTO `logs_cron` VALUES ('303', '\'获取每日一句成功\'', '2018-03-15 01:56:01');
INSERT INTO `logs_cron` VALUES ('304', '\'获取每日一句成功\'', '2018-03-15 01:57:01');
INSERT INTO `logs_cron` VALUES ('305', '\'获取每日一句成功\'', '2018-03-15 01:58:01');
INSERT INTO `logs_cron` VALUES ('306', '\'获取每日一句成功\'', '2018-03-15 01:59:01');
INSERT INTO `logs_cron` VALUES ('307', '\'获取每日一句成功\'', '2018-03-16 01:00:01');
INSERT INTO `logs_cron` VALUES ('308', '\'获取每日一句成功\'', '2018-03-16 01:01:01');
INSERT INTO `logs_cron` VALUES ('309', '\'获取每日一句成功\'', '2018-03-16 01:02:01');
INSERT INTO `logs_cron` VALUES ('310', '\'获取每日一句成功\'', '2018-03-16 01:03:01');
INSERT INTO `logs_cron` VALUES ('311', '\'获取每日一句成功\'', '2018-03-16 01:04:01');
INSERT INTO `logs_cron` VALUES ('312', '\'获取每日一句成功\'', '2018-03-16 01:05:01');
INSERT INTO `logs_cron` VALUES ('313', '\'获取每日一句成功\'', '2018-03-16 01:06:01');
INSERT INTO `logs_cron` VALUES ('314', '\'获取每日一句成功\'', '2018-03-16 01:07:01');
INSERT INTO `logs_cron` VALUES ('315', '\'获取每日一句成功\'', '2018-03-16 01:08:02');
INSERT INTO `logs_cron` VALUES ('316', '\'获取每日一句成功\'', '2018-03-16 01:09:01');
INSERT INTO `logs_cron` VALUES ('317', '\'获取每日一句成功\'', '2018-03-16 01:10:01');
INSERT INTO `logs_cron` VALUES ('318', '\'获取每日一句成功\'', '2018-03-16 01:11:01');
INSERT INTO `logs_cron` VALUES ('319', '\'获取每日一句成功\'', '2018-03-16 01:12:01');
INSERT INTO `logs_cron` VALUES ('320', '\'获取每日一句成功\'', '2018-03-16 01:13:01');
INSERT INTO `logs_cron` VALUES ('321', '\'获取每日一句成功\'', '2018-03-16 01:14:01');
INSERT INTO `logs_cron` VALUES ('322', '\'获取每日一句成功\'', '2018-03-16 01:15:01');
INSERT INTO `logs_cron` VALUES ('323', '\'获取每日一句成功\'', '2018-03-16 01:16:01');
INSERT INTO `logs_cron` VALUES ('324', '\'获取每日一句成功\'', '2018-03-16 01:17:01');
INSERT INTO `logs_cron` VALUES ('325', '\'获取每日一句成功\'', '2018-03-16 01:18:01');
INSERT INTO `logs_cron` VALUES ('326', '\'获取每日一句成功\'', '2018-03-16 01:19:01');
INSERT INTO `logs_cron` VALUES ('327', '\'获取每日一句成功\'', '2018-03-16 01:20:01');
INSERT INTO `logs_cron` VALUES ('328', '\'获取每日一句成功\'', '2018-03-16 01:21:02');
INSERT INTO `logs_cron` VALUES ('329', '\'获取每日一句成功\'', '2018-03-16 01:22:01');
INSERT INTO `logs_cron` VALUES ('330', '\'获取每日一句成功\'', '2018-03-16 01:23:01');
INSERT INTO `logs_cron` VALUES ('331', '\'获取每日一句成功\'', '2018-03-16 01:24:01');
INSERT INTO `logs_cron` VALUES ('332', '\'获取每日一句成功\'', '2018-03-16 01:25:01');
INSERT INTO `logs_cron` VALUES ('333', '\'获取每日一句成功\'', '2018-03-16 01:26:01');
INSERT INTO `logs_cron` VALUES ('334', '\'获取每日一句成功\'', '2018-03-16 01:27:01');
INSERT INTO `logs_cron` VALUES ('335', '\'获取每日一句成功\'', '2018-03-16 01:28:01');
INSERT INTO `logs_cron` VALUES ('336', '\'获取每日一句成功\'', '2018-03-16 01:29:01');
INSERT INTO `logs_cron` VALUES ('337', '\'获取每日一句成功\'', '2018-03-16 01:30:01');
INSERT INTO `logs_cron` VALUES ('338', '\'获取每日一句成功\'', '2018-03-16 01:31:01');
INSERT INTO `logs_cron` VALUES ('339', '\'获取每日一句成功\'', '2018-03-16 01:32:01');
INSERT INTO `logs_cron` VALUES ('340', '\'获取每日一句成功\'', '2018-03-16 01:33:01');
INSERT INTO `logs_cron` VALUES ('341', '\'获取每日一句成功\'', '2018-03-16 01:34:01');
INSERT INTO `logs_cron` VALUES ('342', '\'获取每日一句成功\'', '2018-03-16 01:35:02');
INSERT INTO `logs_cron` VALUES ('343', '\'获取每日一句成功\'', '2018-03-16 01:36:01');
INSERT INTO `logs_cron` VALUES ('344', '\'获取每日一句成功\'', '2018-03-16 01:37:01');
INSERT INTO `logs_cron` VALUES ('345', '\'获取每日一句成功\'', '2018-03-16 01:38:01');
INSERT INTO `logs_cron` VALUES ('346', '\'获取每日一句成功\'', '2018-03-16 01:39:01');
INSERT INTO `logs_cron` VALUES ('347', '\'获取每日一句成功\'', '2018-03-16 01:40:01');
INSERT INTO `logs_cron` VALUES ('348', '\'获取每日一句成功\'', '2018-03-16 01:41:01');
INSERT INTO `logs_cron` VALUES ('349', '\'获取每日一句成功\'', '2018-03-16 01:42:01');
INSERT INTO `logs_cron` VALUES ('350', '\'获取每日一句成功\'', '2018-03-16 01:43:01');
INSERT INTO `logs_cron` VALUES ('351', '\'获取每日一句成功\'', '2018-03-16 01:44:01');
INSERT INTO `logs_cron` VALUES ('352', '\'获取每日一句成功\'', '2018-03-16 01:45:01');
INSERT INTO `logs_cron` VALUES ('353', '\'获取每日一句成功\'', '2018-03-16 01:46:01');
INSERT INTO `logs_cron` VALUES ('354', '\'获取每日一句成功\'', '2018-03-16 01:47:01');
INSERT INTO `logs_cron` VALUES ('355', '\'获取每日一句成功\'', '2018-03-16 01:48:01');
INSERT INTO `logs_cron` VALUES ('356', '\'获取每日一句成功\'', '2018-03-16 01:49:02');
INSERT INTO `logs_cron` VALUES ('357', '\'获取每日一句成功\'', '2018-03-16 01:50:01');
INSERT INTO `logs_cron` VALUES ('358', '\'获取每日一句成功\'', '2018-03-16 01:51:01');
INSERT INTO `logs_cron` VALUES ('359', '\'获取每日一句成功\'', '2018-03-16 01:52:01');
INSERT INTO `logs_cron` VALUES ('360', '\'获取每日一句成功\'', '2018-03-16 01:53:01');
INSERT INTO `logs_cron` VALUES ('361', '\'获取每日一句成功\'', '2018-03-16 01:54:01');
INSERT INTO `logs_cron` VALUES ('362', '\'获取每日一句成功\'', '2018-03-16 01:55:01');
INSERT INTO `logs_cron` VALUES ('363', '\'获取每日一句成功\'', '2018-03-16 01:56:01');
INSERT INTO `logs_cron` VALUES ('364', '\'获取每日一句成功\'', '2018-03-16 01:57:01');
INSERT INTO `logs_cron` VALUES ('365', '\'获取每日一句成功\'', '2018-03-16 01:58:01');
INSERT INTO `logs_cron` VALUES ('366', '\'获取每日一句成功\'', '2018-03-16 01:59:01');
INSERT INTO `logs_cron` VALUES ('367', '\'获取每日一句成功\'', '2018-03-17 01:00:01');
INSERT INTO `logs_cron` VALUES ('368', '\'获取每日一句成功\'', '2018-03-17 01:01:01');
INSERT INTO `logs_cron` VALUES ('369', '\'获取每日一句成功\'', '2018-03-17 01:02:01');
INSERT INTO `logs_cron` VALUES ('370', '\'获取每日一句成功\'', '2018-03-17 01:03:01');
INSERT INTO `logs_cron` VALUES ('371', '\'获取每日一句成功\'', '2018-03-17 01:04:01');
INSERT INTO `logs_cron` VALUES ('372', '\'获取每日一句成功\'', '2018-03-17 01:05:01');
INSERT INTO `logs_cron` VALUES ('373', '\'获取每日一句成功\'', '2018-03-17 01:06:01');
INSERT INTO `logs_cron` VALUES ('374', '\'获取每日一句成功\'', '2018-03-17 01:07:01');
INSERT INTO `logs_cron` VALUES ('375', '\'获取每日一句成功\'', '2018-03-17 01:08:01');
INSERT INTO `logs_cron` VALUES ('376', '\'获取每日一句成功\'', '2018-03-17 01:09:01');
INSERT INTO `logs_cron` VALUES ('377', '\'获取每日一句成功\'', '2018-03-17 01:10:01');
INSERT INTO `logs_cron` VALUES ('378', '\'获取每日一句成功\'', '2018-03-17 01:11:01');
INSERT INTO `logs_cron` VALUES ('379', '\'获取每日一句成功\'', '2018-03-17 01:12:01');
INSERT INTO `logs_cron` VALUES ('380', '\'获取每日一句成功\'', '2018-03-17 01:13:01');
INSERT INTO `logs_cron` VALUES ('381', '\'获取每日一句成功\'', '2018-03-17 01:14:01');
INSERT INTO `logs_cron` VALUES ('382', '\'获取每日一句成功\'', '2018-03-17 01:15:01');
INSERT INTO `logs_cron` VALUES ('383', '\'获取每日一句成功\'', '2018-03-17 01:16:02');
INSERT INTO `logs_cron` VALUES ('384', '\'获取每日一句成功\'', '2018-03-17 01:17:01');
INSERT INTO `logs_cron` VALUES ('385', '\'获取每日一句成功\'', '2018-03-17 01:18:01');
INSERT INTO `logs_cron` VALUES ('386', '\'获取每日一句成功\'', '2018-03-17 01:19:01');
INSERT INTO `logs_cron` VALUES ('387', '\'获取每日一句成功\'', '2018-03-17 01:20:01');
INSERT INTO `logs_cron` VALUES ('388', '\'获取每日一句成功\'', '2018-03-17 01:21:01');
INSERT INTO `logs_cron` VALUES ('389', '\'获取每日一句成功\'', '2018-03-17 01:22:01');
INSERT INTO `logs_cron` VALUES ('390', '\'获取每日一句成功\'', '2018-03-17 01:23:01');
INSERT INTO `logs_cron` VALUES ('391', '\'获取每日一句成功\'', '2018-03-17 01:24:01');
INSERT INTO `logs_cron` VALUES ('392', '\'获取每日一句成功\'', '2018-03-17 01:25:01');
INSERT INTO `logs_cron` VALUES ('393', '\'获取每日一句成功\'', '2018-03-17 01:26:01');
INSERT INTO `logs_cron` VALUES ('394', '\'获取每日一句成功\'', '2018-03-17 01:27:01');
INSERT INTO `logs_cron` VALUES ('395', '\'获取每日一句成功\'', '2018-03-17 01:28:01');
INSERT INTO `logs_cron` VALUES ('396', '\'获取每日一句成功\'', '2018-03-17 01:29:01');
INSERT INTO `logs_cron` VALUES ('397', '\'获取每日一句成功\'', '2018-03-17 01:30:02');
INSERT INTO `logs_cron` VALUES ('398', '\'获取每日一句成功\'', '2018-03-17 01:31:01');
INSERT INTO `logs_cron` VALUES ('399', '\'获取每日一句成功\'', '2018-03-17 01:32:01');
INSERT INTO `logs_cron` VALUES ('400', '\'获取每日一句成功\'', '2018-03-17 01:33:01');
INSERT INTO `logs_cron` VALUES ('401', '\'获取每日一句成功\'', '2018-03-17 01:34:01');
INSERT INTO `logs_cron` VALUES ('402', '\'获取每日一句成功\'', '2018-03-17 01:35:01');
INSERT INTO `logs_cron` VALUES ('403', '\'获取每日一句成功\'', '2018-03-17 01:36:01');
INSERT INTO `logs_cron` VALUES ('404', '\'获取每日一句成功\'', '2018-03-17 01:37:01');
INSERT INTO `logs_cron` VALUES ('405', '\'获取每日一句成功\'', '2018-03-17 01:38:01');
INSERT INTO `logs_cron` VALUES ('406', '\'获取每日一句成功\'', '2018-03-17 01:39:01');
INSERT INTO `logs_cron` VALUES ('407', '\'获取每日一句成功\'', '2018-03-17 01:40:01');
INSERT INTO `logs_cron` VALUES ('408', '\'获取每日一句成功\'', '2018-03-17 01:41:01');
INSERT INTO `logs_cron` VALUES ('409', '\'获取每日一句成功\'', '2018-03-17 01:42:01');
INSERT INTO `logs_cron` VALUES ('410', '\'获取每日一句成功\'', '2018-03-17 01:43:02');
INSERT INTO `logs_cron` VALUES ('411', '\'获取每日一句成功\'', '2018-03-17 01:44:01');
INSERT INTO `logs_cron` VALUES ('412', '\'获取每日一句成功\'', '2018-03-17 01:45:01');
INSERT INTO `logs_cron` VALUES ('413', '\'获取每日一句成功\'', '2018-03-17 01:46:01');
INSERT INTO `logs_cron` VALUES ('414', '\'获取每日一句成功\'', '2018-03-17 01:47:01');
INSERT INTO `logs_cron` VALUES ('415', '\'获取每日一句成功\'', '2018-03-17 01:48:01');
INSERT INTO `logs_cron` VALUES ('416', '\'获取每日一句成功\'', '2018-03-17 01:49:01');
INSERT INTO `logs_cron` VALUES ('417', '\'获取每日一句成功\'', '2018-03-17 01:50:01');
INSERT INTO `logs_cron` VALUES ('418', '\'获取每日一句成功\'', '2018-03-17 01:51:01');
INSERT INTO `logs_cron` VALUES ('419', '\'获取每日一句成功\'', '2018-03-17 01:52:01');
INSERT INTO `logs_cron` VALUES ('420', '\'获取每日一句成功\'', '2018-03-17 01:53:01');
INSERT INTO `logs_cron` VALUES ('421', '\'获取每日一句成功\'', '2018-03-17 01:54:01');
INSERT INTO `logs_cron` VALUES ('422', '\'获取每日一句成功\'', '2018-03-17 01:55:01');
INSERT INTO `logs_cron` VALUES ('423', '\'获取每日一句成功\'', '2018-03-17 01:56:02');
INSERT INTO `logs_cron` VALUES ('424', '\'获取每日一句成功\'', '2018-03-17 01:57:01');
INSERT INTO `logs_cron` VALUES ('425', '\'获取每日一句成功\'', '2018-03-17 01:58:01');
INSERT INTO `logs_cron` VALUES ('426', '\'获取每日一句成功\'', '2018-03-17 01:59:01');
INSERT INTO `logs_cron` VALUES ('427', '\'获取每日一句成功\'', '2018-03-18 01:00:01');
INSERT INTO `logs_cron` VALUES ('428', '\'获取每日一句成功\'', '2018-03-18 01:01:01');
INSERT INTO `logs_cron` VALUES ('429', '\'获取每日一句成功\'', '2018-03-18 01:02:01');
INSERT INTO `logs_cron` VALUES ('430', '\'获取每日一句成功\'', '2018-03-18 01:03:01');
INSERT INTO `logs_cron` VALUES ('431', '\'获取每日一句成功\'', '2018-03-18 01:04:01');
INSERT INTO `logs_cron` VALUES ('432', '\'获取每日一句成功\'', '2018-03-18 01:05:01');
INSERT INTO `logs_cron` VALUES ('433', '\'获取每日一句成功\'', '2018-03-18 01:06:01');
INSERT INTO `logs_cron` VALUES ('434', '\'获取每日一句成功\'', '2018-03-18 01:07:01');
INSERT INTO `logs_cron` VALUES ('435', '\'获取每日一句成功\'', '2018-03-18 01:08:01');
INSERT INTO `logs_cron` VALUES ('436', '\'获取每日一句成功\'', '2018-03-18 01:09:01');
INSERT INTO `logs_cron` VALUES ('437', '\'获取每日一句成功\'', '2018-03-18 01:10:01');
INSERT INTO `logs_cron` VALUES ('438', '\'获取每日一句成功\'', '2018-03-18 01:11:01');
INSERT INTO `logs_cron` VALUES ('439', '\'获取每日一句成功\'', '2018-03-18 01:12:01');
INSERT INTO `logs_cron` VALUES ('440', '\'获取每日一句成功\'', '2018-03-18 01:13:01');
INSERT INTO `logs_cron` VALUES ('441', '\'获取每日一句成功\'', '2018-03-18 01:14:01');
INSERT INTO `logs_cron` VALUES ('442', '\'获取每日一句成功\'', '2018-03-18 01:15:01');
INSERT INTO `logs_cron` VALUES ('443', '\'获取每日一句成功\'', '2018-03-18 01:16:01');
INSERT INTO `logs_cron` VALUES ('444', '\'获取每日一句成功\'', '2018-03-18 01:17:01');
INSERT INTO `logs_cron` VALUES ('445', '\'获取每日一句成功\'', '2018-03-18 01:18:01');
INSERT INTO `logs_cron` VALUES ('446', '\'获取每日一句成功\'', '2018-03-18 01:19:01');
INSERT INTO `logs_cron` VALUES ('447', '\'获取每日一句成功\'', '2018-03-18 01:20:01');
INSERT INTO `logs_cron` VALUES ('448', '\'获取每日一句成功\'', '2018-03-18 01:21:01');
INSERT INTO `logs_cron` VALUES ('449', '\'获取每日一句成功\'', '2018-03-18 01:22:01');
INSERT INTO `logs_cron` VALUES ('450', '\'获取每日一句成功\'', '2018-03-18 01:23:02');
INSERT INTO `logs_cron` VALUES ('451', '\'获取每日一句成功\'', '2018-03-18 01:24:01');
INSERT INTO `logs_cron` VALUES ('452', '\'获取每日一句成功\'', '2018-03-18 01:25:01');
INSERT INTO `logs_cron` VALUES ('453', '\'获取每日一句成功\'', '2018-03-18 01:26:01');
INSERT INTO `logs_cron` VALUES ('454', '\'获取每日一句成功\'', '2018-03-18 01:27:01');
INSERT INTO `logs_cron` VALUES ('455', '\'获取每日一句成功\'', '2018-03-18 01:28:01');
INSERT INTO `logs_cron` VALUES ('456', '\'获取每日一句成功\'', '2018-03-18 01:29:01');
INSERT INTO `logs_cron` VALUES ('457', '\'获取每日一句成功\'', '2018-03-18 01:30:01');
INSERT INTO `logs_cron` VALUES ('458', '\'获取每日一句成功\'', '2018-03-18 01:31:01');
INSERT INTO `logs_cron` VALUES ('459', '\'获取每日一句成功\'', '2018-03-18 01:32:01');
INSERT INTO `logs_cron` VALUES ('460', '\'获取每日一句成功\'', '2018-03-18 01:33:01');
INSERT INTO `logs_cron` VALUES ('461', '\'获取每日一句成功\'', '2018-03-18 01:34:01');
INSERT INTO `logs_cron` VALUES ('462', '\'获取每日一句成功\'', '2018-03-18 01:35:01');
INSERT INTO `logs_cron` VALUES ('463', '\'获取每日一句成功\'', '2018-03-18 01:36:01');
INSERT INTO `logs_cron` VALUES ('464', '\'获取每日一句成功\'', '2018-03-18 01:37:02');
INSERT INTO `logs_cron` VALUES ('465', '\'获取每日一句成功\'', '2018-03-18 01:38:01');
INSERT INTO `logs_cron` VALUES ('466', '\'获取每日一句成功\'', '2018-03-18 01:39:01');
INSERT INTO `logs_cron` VALUES ('467', '\'获取每日一句成功\'', '2018-03-18 01:40:01');
INSERT INTO `logs_cron` VALUES ('468', '\'获取每日一句成功\'', '2018-03-18 01:41:01');
INSERT INTO `logs_cron` VALUES ('469', '\'获取每日一句成功\'', '2018-03-18 01:42:01');
INSERT INTO `logs_cron` VALUES ('470', '\'获取每日一句成功\'', '2018-03-18 01:43:01');
INSERT INTO `logs_cron` VALUES ('471', '\'获取每日一句成功\'', '2018-03-18 01:44:01');
INSERT INTO `logs_cron` VALUES ('472', '\'获取每日一句成功\'', '2018-03-18 01:45:01');
INSERT INTO `logs_cron` VALUES ('473', '\'获取每日一句成功\'', '2018-03-18 01:46:01');
INSERT INTO `logs_cron` VALUES ('474', '\'获取每日一句成功\'', '2018-03-18 01:47:01');
INSERT INTO `logs_cron` VALUES ('475', '\'获取每日一句成功\'', '2018-03-18 01:48:01');
INSERT INTO `logs_cron` VALUES ('476', '\'获取每日一句成功\'', '2018-03-18 01:49:01');
INSERT INTO `logs_cron` VALUES ('477', '\'获取每日一句成功\'', '2018-03-18 01:50:02');
INSERT INTO `logs_cron` VALUES ('478', '\'获取每日一句成功\'', '2018-03-18 01:51:01');
INSERT INTO `logs_cron` VALUES ('479', '\'获取每日一句成功\'', '2018-03-18 01:52:01');
INSERT INTO `logs_cron` VALUES ('480', '\'获取每日一句成功\'', '2018-03-18 01:53:01');
INSERT INTO `logs_cron` VALUES ('481', '\'获取每日一句成功\'', '2018-03-18 01:54:01');
INSERT INTO `logs_cron` VALUES ('482', '\'获取每日一句成功\'', '2018-03-18 01:55:01');
INSERT INTO `logs_cron` VALUES ('483', '\'获取每日一句成功\'', '2018-03-18 01:56:01');
INSERT INTO `logs_cron` VALUES ('484', '\'获取每日一句成功\'', '2018-03-18 01:57:01');
INSERT INTO `logs_cron` VALUES ('485', '\'获取每日一句成功\'', '2018-03-18 01:58:01');
INSERT INTO `logs_cron` VALUES ('486', '\'获取每日一句成功\'', '2018-03-18 01:59:01');
INSERT INTO `logs_cron` VALUES ('487', '\'获取每日一句成功\'', '2018-03-19 01:00:01');
INSERT INTO `logs_cron` VALUES ('488', '\'获取每日一句成功\'', '2018-03-19 01:01:01');
INSERT INTO `logs_cron` VALUES ('489', '\'获取每日一句成功\'', '2018-03-19 01:02:01');
INSERT INTO `logs_cron` VALUES ('490', '\'获取每日一句成功\'', '2018-03-19 01:03:01');
INSERT INTO `logs_cron` VALUES ('491', '\'获取每日一句成功\'', '2018-03-19 01:04:01');
INSERT INTO `logs_cron` VALUES ('492', '\'获取每日一句成功\'', '2018-03-19 01:05:02');
INSERT INTO `logs_cron` VALUES ('493', '\'获取每日一句成功\'', '2018-03-19 01:06:01');
INSERT INTO `logs_cron` VALUES ('494', '\'获取每日一句成功\'', '2018-03-19 01:07:01');
INSERT INTO `logs_cron` VALUES ('495', '\'获取每日一句成功\'', '2018-03-19 01:08:01');
INSERT INTO `logs_cron` VALUES ('496', '\'获取每日一句成功\'', '2018-03-19 01:09:01');
INSERT INTO `logs_cron` VALUES ('497', '\'获取每日一句成功\'', '2018-03-19 01:10:01');
INSERT INTO `logs_cron` VALUES ('498', '\'获取每日一句成功\'', '2018-03-19 01:11:01');
INSERT INTO `logs_cron` VALUES ('499', '\'获取每日一句成功\'', '2018-03-19 01:12:01');
INSERT INTO `logs_cron` VALUES ('500', '\'获取每日一句成功\'', '2018-03-19 01:13:01');
INSERT INTO `logs_cron` VALUES ('501', '\'获取每日一句成功\'', '2018-03-19 01:14:01');
INSERT INTO `logs_cron` VALUES ('502', '\'获取每日一句成功\'', '2018-03-19 01:15:01');
INSERT INTO `logs_cron` VALUES ('503', '\'获取每日一句成功\'', '2018-03-19 01:16:01');
INSERT INTO `logs_cron` VALUES ('504', '\'获取每日一句成功\'', '2018-03-19 01:17:01');
INSERT INTO `logs_cron` VALUES ('505', '\'获取每日一句成功\'', '2018-03-19 01:18:01');
INSERT INTO `logs_cron` VALUES ('506', '\'获取每日一句成功\'', '2018-03-19 01:19:02');
INSERT INTO `logs_cron` VALUES ('507', '\'获取每日一句成功\'', '2018-03-19 01:20:01');
INSERT INTO `logs_cron` VALUES ('508', '\'获取每日一句成功\'', '2018-03-19 01:21:01');
INSERT INTO `logs_cron` VALUES ('509', '\'获取每日一句成功\'', '2018-03-19 01:22:01');
INSERT INTO `logs_cron` VALUES ('510', '\'获取每日一句成功\'', '2018-03-19 01:23:01');
INSERT INTO `logs_cron` VALUES ('511', '\'获取每日一句成功\'', '2018-03-19 01:24:01');
INSERT INTO `logs_cron` VALUES ('512', '\'获取每日一句成功\'', '2018-03-19 01:25:01');
INSERT INTO `logs_cron` VALUES ('513', '\'获取每日一句成功\'', '2018-03-19 01:26:01');
INSERT INTO `logs_cron` VALUES ('514', '\'获取每日一句成功\'', '2018-03-19 01:27:01');
INSERT INTO `logs_cron` VALUES ('515', '\'获取每日一句成功\'', '2018-03-19 01:28:01');
INSERT INTO `logs_cron` VALUES ('516', '\'获取每日一句成功\'', '2018-03-19 01:29:01');
INSERT INTO `logs_cron` VALUES ('517', '\'获取每日一句成功\'', '2018-03-19 01:30:01');
INSERT INTO `logs_cron` VALUES ('518', '\'获取每日一句成功\'', '2018-03-19 01:31:01');
INSERT INTO `logs_cron` VALUES ('519', '\'获取每日一句成功\'', '2018-03-19 01:32:02');
INSERT INTO `logs_cron` VALUES ('520', '\'获取每日一句成功\'', '2018-03-19 01:33:01');
INSERT INTO `logs_cron` VALUES ('521', '\'获取每日一句成功\'', '2018-03-19 01:34:01');
INSERT INTO `logs_cron` VALUES ('522', '\'获取每日一句成功\'', '2018-03-19 01:35:01');
INSERT INTO `logs_cron` VALUES ('523', '\'获取每日一句成功\'', '2018-03-19 01:36:01');
INSERT INTO `logs_cron` VALUES ('524', '\'获取每日一句成功\'', '2018-03-19 01:37:01');
INSERT INTO `logs_cron` VALUES ('525', '\'获取每日一句成功\'', '2018-03-19 01:38:01');
INSERT INTO `logs_cron` VALUES ('526', '\'获取每日一句成功\'', '2018-03-19 01:39:01');
INSERT INTO `logs_cron` VALUES ('527', '\'获取每日一句成功\'', '2018-03-19 01:40:01');
INSERT INTO `logs_cron` VALUES ('528', '\'获取每日一句成功\'', '2018-03-19 01:41:01');
INSERT INTO `logs_cron` VALUES ('529', '\'获取每日一句成功\'', '2018-03-19 01:42:01');
INSERT INTO `logs_cron` VALUES ('530', '\'获取每日一句成功\'', '2018-03-19 01:43:01');
INSERT INTO `logs_cron` VALUES ('531', '\'获取每日一句成功\'', '2018-03-19 01:44:01');
INSERT INTO `logs_cron` VALUES ('532', '\'获取每日一句成功\'', '2018-03-19 01:45:02');
INSERT INTO `logs_cron` VALUES ('533', '\'获取每日一句成功\'', '2018-03-19 01:46:01');
INSERT INTO `logs_cron` VALUES ('534', '\'获取每日一句成功\'', '2018-03-19 01:47:01');
INSERT INTO `logs_cron` VALUES ('535', '\'获取每日一句成功\'', '2018-03-19 01:48:01');
INSERT INTO `logs_cron` VALUES ('536', '\'获取每日一句成功\'', '2018-03-19 01:49:01');
INSERT INTO `logs_cron` VALUES ('537', '\'获取每日一句成功\'', '2018-03-19 01:50:01');
INSERT INTO `logs_cron` VALUES ('538', '\'获取每日一句成功\'', '2018-03-19 01:51:01');
INSERT INTO `logs_cron` VALUES ('539', '\'获取每日一句成功\'', '2018-03-19 01:52:01');
INSERT INTO `logs_cron` VALUES ('540', '\'获取每日一句成功\'', '2018-03-19 01:53:01');
INSERT INTO `logs_cron` VALUES ('541', '\'获取每日一句成功\'', '2018-03-19 01:54:01');
INSERT INTO `logs_cron` VALUES ('542', '\'获取每日一句成功\'', '2018-03-19 01:55:01');
INSERT INTO `logs_cron` VALUES ('543', '\'获取每日一句成功\'', '2018-03-19 01:56:01');
INSERT INTO `logs_cron` VALUES ('544', '\'获取每日一句成功\'', '2018-03-19 01:57:01');
INSERT INTO `logs_cron` VALUES ('545', '\'获取每日一句成功\'', '2018-03-19 01:58:01');
INSERT INTO `logs_cron` VALUES ('546', '\'获取每日一句成功\'', '2018-03-19 01:59:02');
INSERT INTO `logs_cron` VALUES ('547', '\'获取每日一句成功\'', '2018-03-20 01:00:02');
INSERT INTO `logs_cron` VALUES ('548', '\'获取每日一句成功\'', '2018-03-20 01:01:01');
INSERT INTO `logs_cron` VALUES ('549', '\'获取每日一句成功\'', '2018-03-20 01:02:01');
INSERT INTO `logs_cron` VALUES ('550', '\'获取每日一句成功\'', '2018-03-20 01:03:01');
INSERT INTO `logs_cron` VALUES ('551', '\'获取每日一句成功\'', '2018-03-20 01:04:01');
INSERT INTO `logs_cron` VALUES ('552', '\'获取每日一句成功\'', '2018-03-20 01:05:01');
INSERT INTO `logs_cron` VALUES ('553', '\'获取每日一句成功\'', '2018-03-20 01:06:01');
INSERT INTO `logs_cron` VALUES ('554', '\'获取每日一句成功\'', '2018-03-20 01:07:01');
INSERT INTO `logs_cron` VALUES ('555', '\'获取每日一句成功\'', '2018-03-20 01:08:01');
INSERT INTO `logs_cron` VALUES ('556', '\'获取每日一句成功\'', '2018-03-20 01:09:01');
INSERT INTO `logs_cron` VALUES ('557', '\'获取每日一句成功\'', '2018-03-20 01:10:01');
INSERT INTO `logs_cron` VALUES ('558', '\'获取每日一句成功\'', '2018-03-20 01:11:01');
INSERT INTO `logs_cron` VALUES ('559', '\'获取每日一句成功\'', '2018-03-20 01:12:01');
INSERT INTO `logs_cron` VALUES ('560', '\'获取每日一句成功\'', '2018-03-20 01:13:01');
INSERT INTO `logs_cron` VALUES ('561', '\'获取每日一句成功\'', '2018-03-20 01:14:01');
INSERT INTO `logs_cron` VALUES ('562', '\'获取每日一句成功\'', '2018-03-20 01:15:01');
INSERT INTO `logs_cron` VALUES ('563', '\'获取每日一句成功\'', '2018-03-20 01:16:01');
INSERT INTO `logs_cron` VALUES ('564', '\'获取每日一句成功\'', '2018-03-20 01:17:01');
INSERT INTO `logs_cron` VALUES ('565', '\'获取每日一句成功\'', '2018-03-20 01:18:01');
INSERT INTO `logs_cron` VALUES ('566', '\'获取每日一句成功\'', '2018-03-20 01:19:01');
INSERT INTO `logs_cron` VALUES ('567', '\'获取每日一句成功\'', '2018-03-20 01:20:01');
INSERT INTO `logs_cron` VALUES ('568', '\'获取每日一句成功\'', '2018-03-20 01:21:01');
INSERT INTO `logs_cron` VALUES ('569', '\'获取每日一句成功\'', '2018-03-20 01:22:01');
INSERT INTO `logs_cron` VALUES ('570', '\'获取每日一句成功\'', '2018-03-20 01:23:01');
INSERT INTO `logs_cron` VALUES ('571', '\'获取每日一句成功\'', '2018-03-20 01:24:01');
INSERT INTO `logs_cron` VALUES ('572', '\'获取每日一句成功\'', '2018-03-20 01:25:01');
INSERT INTO `logs_cron` VALUES ('573', '\'获取每日一句成功\'', '2018-03-20 01:26:01');
INSERT INTO `logs_cron` VALUES ('574', '\'获取每日一句成功\'', '2018-03-20 01:27:02');
INSERT INTO `logs_cron` VALUES ('575', '\'获取每日一句成功\'', '2018-03-20 01:28:01');
INSERT INTO `logs_cron` VALUES ('576', '\'获取每日一句成功\'', '2018-03-20 01:29:01');
INSERT INTO `logs_cron` VALUES ('577', '\'获取每日一句成功\'', '2018-03-20 01:30:01');
INSERT INTO `logs_cron` VALUES ('578', '\'获取每日一句成功\'', '2018-03-20 01:31:01');
INSERT INTO `logs_cron` VALUES ('579', '\'获取每日一句成功\'', '2018-03-20 01:32:01');
INSERT INTO `logs_cron` VALUES ('580', '\'获取每日一句成功\'', '2018-03-20 01:33:01');
INSERT INTO `logs_cron` VALUES ('581', '\'获取每日一句成功\'', '2018-03-20 01:34:01');
INSERT INTO `logs_cron` VALUES ('582', '\'获取每日一句成功\'', '2018-03-20 01:35:01');
INSERT INTO `logs_cron` VALUES ('583', '\'获取每日一句成功\'', '2018-03-20 01:36:01');
INSERT INTO `logs_cron` VALUES ('584', '\'获取每日一句成功\'', '2018-03-20 01:37:01');
INSERT INTO `logs_cron` VALUES ('585', '\'获取每日一句成功\'', '2018-03-20 01:38:01');
INSERT INTO `logs_cron` VALUES ('586', '\'获取每日一句成功\'', '2018-03-20 01:39:01');
INSERT INTO `logs_cron` VALUES ('587', '\'获取每日一句成功\'', '2018-03-20 01:40:02');
INSERT INTO `logs_cron` VALUES ('588', '\'获取每日一句成功\'', '2018-03-20 01:41:01');
INSERT INTO `logs_cron` VALUES ('589', '\'获取每日一句成功\'', '2018-03-20 01:42:01');
INSERT INTO `logs_cron` VALUES ('590', '\'获取每日一句成功\'', '2018-03-20 01:43:01');
INSERT INTO `logs_cron` VALUES ('591', '\'获取每日一句成功\'', '2018-03-20 01:44:01');
INSERT INTO `logs_cron` VALUES ('592', '\'获取每日一句成功\'', '2018-03-20 01:45:01');
INSERT INTO `logs_cron` VALUES ('593', '\'获取每日一句成功\'', '2018-03-20 01:46:01');
INSERT INTO `logs_cron` VALUES ('594', '\'获取每日一句成功\'', '2018-03-20 01:47:01');
INSERT INTO `logs_cron` VALUES ('595', '\'获取每日一句成功\'', '2018-03-20 01:48:01');
INSERT INTO `logs_cron` VALUES ('596', '\'获取每日一句成功\'', '2018-03-20 01:49:01');
INSERT INTO `logs_cron` VALUES ('597', '\'获取每日一句成功\'', '2018-03-20 01:50:01');
INSERT INTO `logs_cron` VALUES ('598', '\'获取每日一句成功\'', '2018-03-20 01:51:01');
INSERT INTO `logs_cron` VALUES ('599', '\'获取每日一句成功\'', '2018-03-20 01:52:01');
INSERT INTO `logs_cron` VALUES ('600', '\'获取每日一句成功\'', '2018-03-20 01:53:02');
INSERT INTO `logs_cron` VALUES ('601', '\'获取每日一句成功\'', '2018-03-20 01:54:01');
INSERT INTO `logs_cron` VALUES ('602', '\'获取每日一句成功\'', '2018-03-20 01:55:01');
INSERT INTO `logs_cron` VALUES ('603', '\'获取每日一句成功\'', '2018-03-20 01:56:01');
INSERT INTO `logs_cron` VALUES ('604', '\'获取每日一句成功\'', '2018-03-20 01:57:01');
INSERT INTO `logs_cron` VALUES ('605', '\'获取每日一句成功\'', '2018-03-20 01:58:01');
INSERT INTO `logs_cron` VALUES ('606', '\'获取每日一句成功\'', '2018-03-20 01:59:01');
INSERT INTO `logs_cron` VALUES ('607', '\'获取每日一句成功\'', '2018-03-21 01:00:01');
INSERT INTO `logs_cron` VALUES ('608', '\'获取每日一句成功\'', '2018-03-21 01:01:01');
INSERT INTO `logs_cron` VALUES ('609', '\'获取每日一句成功\'', '2018-03-21 01:02:01');
INSERT INTO `logs_cron` VALUES ('610', '\'获取每日一句成功\'', '2018-03-21 01:03:01');
INSERT INTO `logs_cron` VALUES ('611', '\'获取每日一句成功\'', '2018-03-21 01:04:01');
INSERT INTO `logs_cron` VALUES ('612', '\'获取每日一句成功\'', '2018-03-21 01:05:02');
INSERT INTO `logs_cron` VALUES ('613', '\'获取每日一句成功\'', '2018-03-21 01:06:01');
INSERT INTO `logs_cron` VALUES ('614', '\'获取每日一句成功\'', '2018-03-21 01:07:01');
INSERT INTO `logs_cron` VALUES ('615', '\'获取每日一句成功\'', '2018-03-21 01:08:01');
INSERT INTO `logs_cron` VALUES ('616', '\'获取每日一句成功\'', '2018-03-21 01:09:01');
INSERT INTO `logs_cron` VALUES ('617', '\'获取每日一句成功\'', '2018-03-21 01:10:01');
INSERT INTO `logs_cron` VALUES ('618', '\'获取每日一句成功\'', '2018-03-21 01:11:01');
INSERT INTO `logs_cron` VALUES ('619', '\'获取每日一句成功\'', '2018-03-21 01:12:01');
INSERT INTO `logs_cron` VALUES ('620', '\'获取每日一句成功\'', '2018-03-21 01:13:01');
INSERT INTO `logs_cron` VALUES ('621', '\'获取每日一句成功\'', '2018-03-21 01:14:01');
INSERT INTO `logs_cron` VALUES ('622', '\'获取每日一句成功\'', '2018-03-21 01:15:01');
INSERT INTO `logs_cron` VALUES ('623', '\'获取每日一句成功\'', '2018-03-21 01:16:01');
INSERT INTO `logs_cron` VALUES ('624', '\'获取每日一句成功\'', '2018-03-21 01:17:01');
INSERT INTO `logs_cron` VALUES ('625', '\'获取每日一句成功\'', '2018-03-21 01:18:01');
INSERT INTO `logs_cron` VALUES ('626', '\'获取每日一句成功\'', '2018-03-21 01:19:02');
INSERT INTO `logs_cron` VALUES ('627', '\'获取每日一句成功\'', '2018-03-21 01:20:01');
INSERT INTO `logs_cron` VALUES ('628', '\'获取每日一句成功\'', '2018-03-21 01:21:01');
INSERT INTO `logs_cron` VALUES ('629', '\'获取每日一句成功\'', '2018-03-21 01:22:01');
INSERT INTO `logs_cron` VALUES ('630', '\'获取每日一句成功\'', '2018-03-21 01:23:01');
INSERT INTO `logs_cron` VALUES ('631', '\'获取每日一句成功\'', '2018-03-21 01:24:01');
INSERT INTO `logs_cron` VALUES ('632', '\'获取每日一句成功\'', '2018-03-21 01:25:01');
INSERT INTO `logs_cron` VALUES ('633', '\'获取每日一句成功\'', '2018-03-21 01:26:01');
INSERT INTO `logs_cron` VALUES ('634', '\'获取每日一句成功\'', '2018-03-21 01:27:01');
INSERT INTO `logs_cron` VALUES ('635', '\'获取每日一句成功\'', '2018-03-21 01:28:01');
INSERT INTO `logs_cron` VALUES ('636', '\'获取每日一句成功\'', '2018-03-21 01:29:01');
INSERT INTO `logs_cron` VALUES ('637', '\'获取每日一句成功\'', '2018-03-21 01:30:01');
INSERT INTO `logs_cron` VALUES ('638', '\'获取每日一句成功\'', '2018-03-21 01:31:01');
INSERT INTO `logs_cron` VALUES ('639', '\'获取每日一句成功\'', '2018-03-21 01:32:02');
INSERT INTO `logs_cron` VALUES ('640', '\'获取每日一句成功\'', '2018-03-21 01:33:01');
INSERT INTO `logs_cron` VALUES ('641', '\'获取每日一句成功\'', '2018-03-21 01:34:01');
INSERT INTO `logs_cron` VALUES ('642', '\'获取每日一句成功\'', '2018-03-21 01:35:01');
INSERT INTO `logs_cron` VALUES ('643', '\'获取每日一句成功\'', '2018-03-21 01:36:01');
INSERT INTO `logs_cron` VALUES ('644', '\'获取每日一句成功\'', '2018-03-21 01:37:01');
INSERT INTO `logs_cron` VALUES ('645', '\'获取每日一句成功\'', '2018-03-21 01:38:01');
INSERT INTO `logs_cron` VALUES ('646', '\'获取每日一句成功\'', '2018-03-21 01:39:01');
INSERT INTO `logs_cron` VALUES ('647', '\'获取每日一句成功\'', '2018-03-21 01:40:01');
INSERT INTO `logs_cron` VALUES ('648', '\'获取每日一句成功\'', '2018-03-21 01:41:01');
INSERT INTO `logs_cron` VALUES ('649', '\'获取每日一句成功\'', '2018-03-21 01:42:01');
INSERT INTO `logs_cron` VALUES ('650', '\'获取每日一句成功\'', '2018-03-21 01:43:01');
INSERT INTO `logs_cron` VALUES ('651', '\'获取每日一句成功\'', '2018-03-21 01:44:01');
INSERT INTO `logs_cron` VALUES ('652', '\'获取每日一句成功\'', '2018-03-21 01:45:02');
INSERT INTO `logs_cron` VALUES ('653', '\'获取每日一句成功\'', '2018-03-21 01:46:01');
INSERT INTO `logs_cron` VALUES ('654', '\'获取每日一句成功\'', '2018-03-21 01:47:01');
INSERT INTO `logs_cron` VALUES ('655', '\'获取每日一句成功\'', '2018-03-21 01:48:01');
INSERT INTO `logs_cron` VALUES ('656', '\'获取每日一句成功\'', '2018-03-21 01:49:01');
INSERT INTO `logs_cron` VALUES ('657', '\'获取每日一句成功\'', '2018-03-21 01:50:01');
INSERT INTO `logs_cron` VALUES ('658', '\'获取每日一句成功\'', '2018-03-21 01:51:01');
INSERT INTO `logs_cron` VALUES ('659', '\'获取每日一句成功\'', '2018-03-21 01:52:01');
INSERT INTO `logs_cron` VALUES ('660', '\'获取每日一句成功\'', '2018-03-21 01:53:01');
INSERT INTO `logs_cron` VALUES ('661', '\'获取每日一句成功\'', '2018-03-21 01:54:01');
INSERT INTO `logs_cron` VALUES ('662', '\'获取每日一句成功\'', '2018-03-21 01:55:01');
INSERT INTO `logs_cron` VALUES ('663', '\'获取每日一句成功\'', '2018-03-21 01:56:01');
INSERT INTO `logs_cron` VALUES ('664', '\'获取每日一句成功\'', '2018-03-21 01:57:01');
INSERT INTO `logs_cron` VALUES ('665', '\'获取每日一句成功\'', '2018-03-21 01:58:02');
INSERT INTO `logs_cron` VALUES ('666', '\'获取每日一句成功\'', '2018-03-21 01:59:01');
INSERT INTO `logs_cron` VALUES ('667', '\'获取每日一句成功\'', '2018-03-22 01:00:01');
INSERT INTO `logs_cron` VALUES ('668', '\'获取每日一句成功\'', '2018-03-22 01:01:01');
INSERT INTO `logs_cron` VALUES ('669', '\'获取每日一句成功\'', '2018-03-22 01:02:01');
INSERT INTO `logs_cron` VALUES ('670', '\'获取每日一句成功\'', '2018-03-22 01:03:01');
INSERT INTO `logs_cron` VALUES ('671', '\'获取每日一句成功\'', '2018-03-22 01:04:01');
INSERT INTO `logs_cron` VALUES ('672', '\'获取每日一句成功\'', '2018-03-22 01:05:01');
INSERT INTO `logs_cron` VALUES ('673', '\'获取每日一句成功\'', '2018-03-22 01:06:01');
INSERT INTO `logs_cron` VALUES ('674', '\'获取每日一句成功\'', '2018-03-22 01:07:01');
INSERT INTO `logs_cron` VALUES ('675', '\'获取每日一句成功\'', '2018-03-22 01:08:01');
INSERT INTO `logs_cron` VALUES ('676', '\'获取每日一句成功\'', '2018-03-22 01:09:01');
INSERT INTO `logs_cron` VALUES ('677', '\'获取每日一句成功\'', '2018-03-22 01:10:01');
INSERT INTO `logs_cron` VALUES ('678', '\'获取每日一句成功\'', '2018-03-22 01:11:02');
INSERT INTO `logs_cron` VALUES ('679', '\'获取每日一句成功\'', '2018-03-22 01:12:01');
INSERT INTO `logs_cron` VALUES ('680', '\'获取每日一句成功\'', '2018-03-22 01:13:01');
INSERT INTO `logs_cron` VALUES ('681', '\'获取每日一句成功\'', '2018-03-22 01:14:01');
INSERT INTO `logs_cron` VALUES ('682', '\'获取每日一句成功\'', '2018-03-22 01:15:01');
INSERT INTO `logs_cron` VALUES ('683', '\'获取每日一句成功\'', '2018-03-22 01:16:01');
INSERT INTO `logs_cron` VALUES ('684', '\'获取每日一句成功\'', '2018-03-22 01:17:01');
INSERT INTO `logs_cron` VALUES ('685', '\'获取每日一句成功\'', '2018-03-22 01:18:01');
INSERT INTO `logs_cron` VALUES ('686', '\'获取每日一句成功\'', '2018-03-22 01:19:01');
INSERT INTO `logs_cron` VALUES ('687', '\'获取每日一句成功\'', '2018-03-22 01:20:01');
INSERT INTO `logs_cron` VALUES ('688', '\'获取每日一句成功\'', '2018-03-22 01:21:01');
INSERT INTO `logs_cron` VALUES ('689', '\'获取每日一句成功\'', '2018-03-22 01:22:01');
INSERT INTO `logs_cron` VALUES ('690', '\'获取每日一句成功\'', '2018-03-22 01:23:01');
INSERT INTO `logs_cron` VALUES ('691', '\'获取每日一句成功\'', '2018-03-22 01:24:01');
INSERT INTO `logs_cron` VALUES ('692', '\'获取每日一句成功\'', '2018-03-22 01:25:01');
INSERT INTO `logs_cron` VALUES ('693', '\'获取每日一句成功\'', '2018-03-22 01:26:01');
INSERT INTO `logs_cron` VALUES ('694', '\'获取每日一句成功\'', '2018-03-22 01:27:01');
INSERT INTO `logs_cron` VALUES ('695', '\'获取每日一句成功\'', '2018-03-22 01:28:01');
INSERT INTO `logs_cron` VALUES ('696', '\'获取每日一句成功\'', '2018-03-22 01:29:01');
INSERT INTO `logs_cron` VALUES ('697', '\'获取每日一句成功\'', '2018-03-22 01:30:01');
INSERT INTO `logs_cron` VALUES ('698', '\'获取每日一句成功\'', '2018-03-22 01:31:01');
INSERT INTO `logs_cron` VALUES ('699', '\'获取每日一句成功\'', '2018-03-22 01:32:01');
INSERT INTO `logs_cron` VALUES ('700', '\'获取每日一句成功\'', '2018-03-22 01:33:01');
INSERT INTO `logs_cron` VALUES ('701', '\'获取每日一句成功\'', '2018-03-22 01:34:01');
INSERT INTO `logs_cron` VALUES ('702', '\'获取每日一句成功\'', '2018-03-22 01:35:01');
INSERT INTO `logs_cron` VALUES ('703', '\'获取每日一句成功\'', '2018-03-22 01:36:01');
INSERT INTO `logs_cron` VALUES ('704', '\'获取每日一句成功\'', '2018-03-22 01:37:01');
INSERT INTO `logs_cron` VALUES ('705', '\'获取每日一句成功\'', '2018-03-22 01:38:02');
INSERT INTO `logs_cron` VALUES ('706', '\'获取每日一句成功\'', '2018-03-22 01:39:01');
INSERT INTO `logs_cron` VALUES ('707', '\'获取每日一句成功\'', '2018-03-22 01:40:01');
INSERT INTO `logs_cron` VALUES ('708', '\'获取每日一句成功\'', '2018-03-22 01:41:01');
INSERT INTO `logs_cron` VALUES ('709', '\'获取每日一句成功\'', '2018-03-22 01:42:01');
INSERT INTO `logs_cron` VALUES ('710', '\'获取每日一句成功\'', '2018-03-22 01:43:01');
INSERT INTO `logs_cron` VALUES ('711', '\'获取每日一句成功\'', '2018-03-22 01:44:01');
INSERT INTO `logs_cron` VALUES ('712', '\'获取每日一句成功\'', '2018-03-22 01:45:01');
INSERT INTO `logs_cron` VALUES ('713', '\'获取每日一句成功\'', '2018-03-22 01:46:01');
INSERT INTO `logs_cron` VALUES ('714', '\'获取每日一句成功\'', '2018-03-22 01:47:01');
INSERT INTO `logs_cron` VALUES ('715', '\'获取每日一句成功\'', '2018-03-22 01:48:01');
INSERT INTO `logs_cron` VALUES ('716', '\'获取每日一句成功\'', '2018-03-22 01:49:01');
INSERT INTO `logs_cron` VALUES ('717', '\'获取每日一句成功\'', '2018-03-22 01:50:01');
INSERT INTO `logs_cron` VALUES ('718', '\'获取每日一句成功\'', '2018-03-22 01:51:02');
INSERT INTO `logs_cron` VALUES ('719', '\'获取每日一句成功\'', '2018-03-22 01:52:01');
INSERT INTO `logs_cron` VALUES ('720', '\'获取每日一句成功\'', '2018-03-22 01:53:01');
INSERT INTO `logs_cron` VALUES ('721', '\'获取每日一句成功\'', '2018-03-22 01:54:01');
INSERT INTO `logs_cron` VALUES ('722', '\'获取每日一句成功\'', '2018-03-22 01:55:01');
INSERT INTO `logs_cron` VALUES ('723', '\'获取每日一句成功\'', '2018-03-22 01:56:01');
INSERT INTO `logs_cron` VALUES ('724', '\'获取每日一句成功\'', '2018-03-22 01:57:01');
INSERT INTO `logs_cron` VALUES ('725', '\'获取每日一句成功\'', '2018-03-22 01:58:01');
INSERT INTO `logs_cron` VALUES ('726', '\'获取每日一句成功\'', '2018-03-22 01:59:01');
INSERT INTO `logs_cron` VALUES ('727', '\'获取每日一句成功\'', '2018-03-23 01:00:01');
INSERT INTO `logs_cron` VALUES ('728', '\'获取每日一句成功\'', '2018-03-23 01:01:01');
INSERT INTO `logs_cron` VALUES ('729', '\'获取每日一句成功\'', '2018-03-23 01:02:01');
INSERT INTO `logs_cron` VALUES ('730', '\'获取每日一句成功\'', '2018-03-23 01:03:01');
INSERT INTO `logs_cron` VALUES ('731', '\'获取每日一句成功\'', '2018-03-23 01:04:01');
INSERT INTO `logs_cron` VALUES ('732', '\'获取每日一句成功\'', '2018-03-23 01:05:02');
INSERT INTO `logs_cron` VALUES ('733', '\'获取每日一句成功\'', '2018-03-23 01:06:01');
INSERT INTO `logs_cron` VALUES ('734', '\'获取每日一句成功\'', '2018-03-23 01:07:01');
INSERT INTO `logs_cron` VALUES ('735', '\'获取每日一句成功\'', '2018-03-23 01:08:01');
INSERT INTO `logs_cron` VALUES ('736', '\'获取每日一句成功\'', '2018-03-23 01:09:01');
INSERT INTO `logs_cron` VALUES ('737', '\'获取每日一句成功\'', '2018-03-23 01:10:01');
INSERT INTO `logs_cron` VALUES ('738', '\'获取每日一句成功\'', '2018-03-23 01:11:01');
INSERT INTO `logs_cron` VALUES ('739', '\'获取每日一句成功\'', '2018-03-23 01:12:01');
INSERT INTO `logs_cron` VALUES ('740', '\'获取每日一句成功\'', '2018-03-23 01:13:01');
INSERT INTO `logs_cron` VALUES ('741', '\'获取每日一句成功\'', '2018-03-23 01:14:01');
INSERT INTO `logs_cron` VALUES ('742', '\'获取每日一句成功\'', '2018-03-23 01:15:01');
INSERT INTO `logs_cron` VALUES ('743', '\'获取每日一句成功\'', '2018-03-23 01:16:01');
INSERT INTO `logs_cron` VALUES ('744', '\'获取每日一句成功\'', '2018-03-23 01:17:01');
INSERT INTO `logs_cron` VALUES ('745', '\'获取每日一句成功\'', '2018-03-23 01:18:02');
INSERT INTO `logs_cron` VALUES ('746', '\'获取每日一句成功\'', '2018-03-23 01:19:01');
INSERT INTO `logs_cron` VALUES ('747', '\'获取每日一句成功\'', '2018-03-23 01:20:01');
INSERT INTO `logs_cron` VALUES ('748', '\'获取每日一句成功\'', '2018-03-23 01:21:01');
INSERT INTO `logs_cron` VALUES ('749', '\'获取每日一句成功\'', '2018-03-23 01:22:01');
INSERT INTO `logs_cron` VALUES ('750', '\'获取每日一句成功\'', '2018-03-23 01:23:01');
INSERT INTO `logs_cron` VALUES ('751', '\'获取每日一句成功\'', '2018-03-23 01:24:01');
INSERT INTO `logs_cron` VALUES ('752', '\'获取每日一句成功\'', '2018-03-23 01:25:01');
INSERT INTO `logs_cron` VALUES ('753', '\'获取每日一句成功\'', '2018-03-23 01:26:01');
INSERT INTO `logs_cron` VALUES ('754', '\'获取每日一句成功\'', '2018-03-23 01:27:01');
INSERT INTO `logs_cron` VALUES ('755', '\'获取每日一句成功\'', '2018-03-23 01:28:01');
INSERT INTO `logs_cron` VALUES ('756', '\'获取每日一句成功\'', '2018-03-23 01:29:01');
INSERT INTO `logs_cron` VALUES ('757', '\'获取每日一句成功\'', '2018-03-23 01:30:01');
INSERT INTO `logs_cron` VALUES ('758', '\'获取每日一句成功\'', '2018-03-23 01:31:02');
INSERT INTO `logs_cron` VALUES ('759', '\'获取每日一句成功\'', '2018-03-23 01:32:01');
INSERT INTO `logs_cron` VALUES ('760', '\'获取每日一句成功\'', '2018-03-23 01:33:01');
INSERT INTO `logs_cron` VALUES ('761', '\'获取每日一句成功\'', '2018-03-23 01:34:01');
INSERT INTO `logs_cron` VALUES ('762', '\'获取每日一句成功\'', '2018-03-23 01:35:01');
INSERT INTO `logs_cron` VALUES ('763', '\'获取每日一句成功\'', '2018-03-23 01:36:01');
INSERT INTO `logs_cron` VALUES ('764', '\'获取每日一句成功\'', '2018-03-23 01:37:01');
INSERT INTO `logs_cron` VALUES ('765', '\'获取每日一句成功\'', '2018-03-23 01:38:01');
INSERT INTO `logs_cron` VALUES ('766', '\'获取每日一句成功\'', '2018-03-23 01:39:01');
INSERT INTO `logs_cron` VALUES ('767', '\'获取每日一句成功\'', '2018-03-23 01:40:01');
INSERT INTO `logs_cron` VALUES ('768', '\'获取每日一句成功\'', '2018-03-23 01:41:01');
INSERT INTO `logs_cron` VALUES ('769', '\'获取每日一句成功\'', '2018-03-23 01:42:01');
INSERT INTO `logs_cron` VALUES ('770', '\'获取每日一句成功\'', '2018-03-23 01:43:01');
INSERT INTO `logs_cron` VALUES ('771', '\'获取每日一句成功\'', '2018-03-23 01:44:01');
INSERT INTO `logs_cron` VALUES ('772', '\'获取每日一句成功\'', '2018-03-23 01:45:02');
INSERT INTO `logs_cron` VALUES ('773', '\'获取每日一句成功\'', '2018-03-23 01:46:01');
INSERT INTO `logs_cron` VALUES ('774', '\'获取每日一句成功\'', '2018-03-23 01:47:01');
INSERT INTO `logs_cron` VALUES ('775', '\'获取每日一句成功\'', '2018-03-23 01:48:01');
INSERT INTO `logs_cron` VALUES ('776', '\'获取每日一句成功\'', '2018-03-23 01:49:01');
INSERT INTO `logs_cron` VALUES ('777', '\'获取每日一句成功\'', '2018-03-23 01:50:01');
INSERT INTO `logs_cron` VALUES ('778', '\'获取每日一句成功\'', '2018-03-23 01:51:01');
INSERT INTO `logs_cron` VALUES ('779', '\'获取每日一句成功\'', '2018-03-23 01:52:01');
INSERT INTO `logs_cron` VALUES ('780', '\'获取每日一句成功\'', '2018-03-23 01:53:01');
INSERT INTO `logs_cron` VALUES ('781', '\'获取每日一句成功\'', '2018-03-23 01:54:01');
INSERT INTO `logs_cron` VALUES ('782', '\'获取每日一句成功\'', '2018-03-23 01:55:01');
INSERT INTO `logs_cron` VALUES ('783', '\'获取每日一句成功\'', '2018-03-23 01:56:01');
INSERT INTO `logs_cron` VALUES ('784', '\'获取每日一句成功\'', '2018-03-23 01:57:01');
INSERT INTO `logs_cron` VALUES ('785', '\'获取每日一句成功\'', '2018-03-23 01:58:02');
INSERT INTO `logs_cron` VALUES ('786', '\'获取每日一句成功\'', '2018-03-23 01:59:01');
INSERT INTO `logs_cron` VALUES ('787', '\'获取每日一句成功\'', '2018-03-24 01:00:02');
INSERT INTO `logs_cron` VALUES ('788', '\'获取每日一句成功\'', '2018-03-24 01:01:01');
INSERT INTO `logs_cron` VALUES ('789', '\'获取每日一句成功\'', '2018-03-24 01:02:01');
INSERT INTO `logs_cron` VALUES ('790', '\'获取每日一句成功\'', '2018-03-24 01:03:01');
INSERT INTO `logs_cron` VALUES ('791', '\'获取每日一句成功\'', '2018-03-24 01:04:01');
INSERT INTO `logs_cron` VALUES ('792', '\'获取每日一句成功\'', '2018-03-24 01:05:01');
INSERT INTO `logs_cron` VALUES ('793', '\'获取每日一句成功\'', '2018-03-24 01:06:01');
INSERT INTO `logs_cron` VALUES ('794', '\'获取每日一句成功\'', '2018-03-24 01:07:01');
INSERT INTO `logs_cron` VALUES ('795', '\'获取每日一句成功\'', '2018-03-24 01:08:01');
INSERT INTO `logs_cron` VALUES ('796', '\'获取每日一句成功\'', '2018-03-24 01:09:01');
INSERT INTO `logs_cron` VALUES ('797', '\'获取每日一句成功\'', '2018-03-24 01:10:01');
INSERT INTO `logs_cron` VALUES ('798', '\'获取每日一句成功\'', '2018-03-24 01:11:01');
INSERT INTO `logs_cron` VALUES ('799', '\'获取每日一句成功\'', '2018-03-24 01:12:01');
INSERT INTO `logs_cron` VALUES ('800', '\'获取每日一句成功\'', '2018-03-24 01:13:02');
INSERT INTO `logs_cron` VALUES ('801', '\'获取每日一句成功\'', '2018-03-24 01:14:01');
INSERT INTO `logs_cron` VALUES ('802', '\'获取每日一句成功\'', '2018-03-24 01:15:01');
INSERT INTO `logs_cron` VALUES ('803', '\'获取每日一句成功\'', '2018-03-24 01:16:01');
INSERT INTO `logs_cron` VALUES ('804', '\'获取每日一句成功\'', '2018-03-24 01:17:01');
INSERT INTO `logs_cron` VALUES ('805', '\'获取每日一句成功\'', '2018-03-24 01:18:01');
INSERT INTO `logs_cron` VALUES ('806', '\'获取每日一句成功\'', '2018-03-24 01:19:01');
INSERT INTO `logs_cron` VALUES ('807', '\'获取每日一句成功\'', '2018-03-24 01:20:01');
INSERT INTO `logs_cron` VALUES ('808', '\'获取每日一句成功\'', '2018-03-24 01:21:01');
INSERT INTO `logs_cron` VALUES ('809', '\'获取每日一句成功\'', '2018-03-24 01:22:01');
INSERT INTO `logs_cron` VALUES ('810', '\'获取每日一句成功\'', '2018-03-24 01:23:01');
INSERT INTO `logs_cron` VALUES ('811', '\'获取每日一句成功\'', '2018-03-24 01:24:01');
INSERT INTO `logs_cron` VALUES ('812', '\'获取每日一句成功\'', '2018-03-24 01:25:01');
INSERT INTO `logs_cron` VALUES ('813', '\'获取每日一句成功\'', '2018-03-24 01:26:01');
INSERT INTO `logs_cron` VALUES ('814', '\'获取每日一句成功\'', '2018-03-24 01:27:02');
INSERT INTO `logs_cron` VALUES ('815', '\'获取每日一句成功\'', '2018-03-24 01:28:01');
INSERT INTO `logs_cron` VALUES ('816', '\'获取每日一句成功\'', '2018-03-24 01:29:01');
INSERT INTO `logs_cron` VALUES ('817', '\'获取每日一句成功\'', '2018-03-24 01:30:01');
INSERT INTO `logs_cron` VALUES ('818', '\'获取每日一句成功\'', '2018-03-24 01:31:01');
INSERT INTO `logs_cron` VALUES ('819', '\'获取每日一句成功\'', '2018-03-24 01:32:01');
INSERT INTO `logs_cron` VALUES ('820', '\'获取每日一句成功\'', '2018-03-24 01:33:01');
INSERT INTO `logs_cron` VALUES ('821', '\'获取每日一句成功\'', '2018-03-24 01:34:01');
INSERT INTO `logs_cron` VALUES ('822', '\'获取每日一句成功\'', '2018-03-24 01:35:01');
INSERT INTO `logs_cron` VALUES ('823', '\'获取每日一句成功\'', '2018-03-24 01:36:01');
INSERT INTO `logs_cron` VALUES ('824', '\'获取每日一句成功\'', '2018-03-24 01:37:01');
INSERT INTO `logs_cron` VALUES ('825', '\'获取每日一句成功\'', '2018-03-24 01:38:01');
INSERT INTO `logs_cron` VALUES ('826', '\'获取每日一句成功\'', '2018-03-24 01:39:01');
INSERT INTO `logs_cron` VALUES ('827', '\'获取每日一句成功\'', '2018-03-24 01:40:02');
INSERT INTO `logs_cron` VALUES ('828', '\'获取每日一句成功\'', '2018-03-24 01:41:01');
INSERT INTO `logs_cron` VALUES ('829', '\'获取每日一句成功\'', '2018-03-24 01:42:01');
INSERT INTO `logs_cron` VALUES ('830', '\'获取每日一句成功\'', '2018-03-24 01:43:01');
INSERT INTO `logs_cron` VALUES ('831', '\'获取每日一句成功\'', '2018-03-24 01:44:01');
INSERT INTO `logs_cron` VALUES ('832', '\'获取每日一句成功\'', '2018-03-24 01:45:01');
INSERT INTO `logs_cron` VALUES ('833', '\'获取每日一句成功\'', '2018-03-24 01:46:01');
INSERT INTO `logs_cron` VALUES ('834', '\'获取每日一句成功\'', '2018-03-24 01:47:01');
INSERT INTO `logs_cron` VALUES ('835', '\'获取每日一句成功\'', '2018-03-24 01:48:01');
INSERT INTO `logs_cron` VALUES ('836', '\'获取每日一句成功\'', '2018-03-24 01:49:01');
INSERT INTO `logs_cron` VALUES ('837', '\'获取每日一句成功\'', '2018-03-24 01:50:01');
INSERT INTO `logs_cron` VALUES ('838', '\'获取每日一句成功\'', '2018-03-24 01:51:01');
INSERT INTO `logs_cron` VALUES ('839', '\'获取每日一句成功\'', '2018-03-24 01:52:01');
INSERT INTO `logs_cron` VALUES ('840', '\'获取每日一句成功\'', '2018-03-24 01:53:01');
INSERT INTO `logs_cron` VALUES ('841', '\'获取每日一句成功\'', '2018-03-24 01:54:02');
INSERT INTO `logs_cron` VALUES ('842', '\'获取每日一句成功\'', '2018-03-24 01:55:01');
INSERT INTO `logs_cron` VALUES ('843', '\'获取每日一句成功\'', '2018-03-24 01:56:01');
INSERT INTO `logs_cron` VALUES ('844', '\'获取每日一句成功\'', '2018-03-24 01:57:01');
INSERT INTO `logs_cron` VALUES ('845', '\'获取每日一句成功\'', '2018-03-24 01:58:01');
INSERT INTO `logs_cron` VALUES ('846', '\'获取每日一句成功\'', '2018-03-24 01:59:01');
INSERT INTO `logs_cron` VALUES ('847', '\'获取每日一句成功\'', '2018-03-25 01:00:01');
INSERT INTO `logs_cron` VALUES ('848', '\'获取每日一句成功\'', '2018-03-25 01:01:01');
INSERT INTO `logs_cron` VALUES ('849', '\'获取每日一句成功\'', '2018-03-25 01:02:01');
INSERT INTO `logs_cron` VALUES ('850', '\'获取每日一句成功\'', '2018-03-25 01:03:01');
INSERT INTO `logs_cron` VALUES ('851', '\'获取每日一句成功\'', '2018-03-25 01:04:01');
INSERT INTO `logs_cron` VALUES ('852', '\'获取每日一句成功\'', '2018-03-25 01:05:01');
INSERT INTO `logs_cron` VALUES ('853', '\'获取每日一句成功\'', '2018-03-25 01:06:01');
INSERT INTO `logs_cron` VALUES ('854', '\'获取每日一句成功\'', '2018-03-25 01:07:01');
INSERT INTO `logs_cron` VALUES ('855', '\'获取每日一句成功\'', '2018-03-25 01:08:01');
INSERT INTO `logs_cron` VALUES ('856', '\'获取每日一句成功\'', '2018-03-25 01:09:01');
INSERT INTO `logs_cron` VALUES ('857', '\'获取每日一句成功\'', '2018-03-25 01:10:01');
INSERT INTO `logs_cron` VALUES ('858', '\'获取每日一句成功\'', '2018-03-25 01:11:01');
INSERT INTO `logs_cron` VALUES ('859', '\'获取每日一句成功\'', '2018-03-25 01:12:01');
INSERT INTO `logs_cron` VALUES ('860', '\'获取每日一句成功\'', '2018-03-25 01:13:01');
INSERT INTO `logs_cron` VALUES ('861', '\'获取每日一句成功\'', '2018-03-25 01:14:01');
INSERT INTO `logs_cron` VALUES ('862', '\'获取每日一句成功\'', '2018-03-25 01:15:01');
INSERT INTO `logs_cron` VALUES ('863', '\'获取每日一句成功\'', '2018-03-25 01:16:01');
INSERT INTO `logs_cron` VALUES ('864', '\'获取每日一句成功\'', '2018-03-25 01:17:01');
INSERT INTO `logs_cron` VALUES ('865', '\'获取每日一句成功\'', '2018-03-25 01:18:01');
INSERT INTO `logs_cron` VALUES ('866', '\'获取每日一句成功\'', '2018-03-25 01:19:01');
INSERT INTO `logs_cron` VALUES ('867', '\'获取每日一句成功\'', '2018-03-25 01:20:01');
INSERT INTO `logs_cron` VALUES ('868', '\'获取每日一句成功\'', '2018-03-25 01:21:01');
INSERT INTO `logs_cron` VALUES ('869', '\'获取每日一句成功\'', '2018-03-25 01:22:02');
INSERT INTO `logs_cron` VALUES ('870', '\'获取每日一句成功\'', '2018-03-25 01:23:01');
INSERT INTO `logs_cron` VALUES ('871', '\'获取每日一句成功\'', '2018-03-25 01:24:01');
INSERT INTO `logs_cron` VALUES ('872', '\'获取每日一句成功\'', '2018-03-25 01:25:01');
INSERT INTO `logs_cron` VALUES ('873', '\'获取每日一句成功\'', '2018-03-25 01:26:01');
INSERT INTO `logs_cron` VALUES ('874', '\'获取每日一句成功\'', '2018-03-25 01:27:01');
INSERT INTO `logs_cron` VALUES ('875', '\'获取每日一句成功\'', '2018-03-25 01:28:01');
INSERT INTO `logs_cron` VALUES ('876', '\'获取每日一句成功\'', '2018-03-25 01:29:01');
INSERT INTO `logs_cron` VALUES ('877', '\'获取每日一句成功\'', '2018-03-25 01:30:01');
INSERT INTO `logs_cron` VALUES ('878', '\'获取每日一句成功\'', '2018-03-25 01:31:01');
INSERT INTO `logs_cron` VALUES ('879', '\'获取每日一句成功\'', '2018-03-25 01:32:01');
INSERT INTO `logs_cron` VALUES ('880', '\'获取每日一句成功\'', '2018-03-25 01:33:01');
INSERT INTO `logs_cron` VALUES ('881', '\'获取每日一句成功\'', '2018-03-25 01:34:01');
INSERT INTO `logs_cron` VALUES ('882', '\'获取每日一句成功\'', '2018-03-25 01:35:01');
INSERT INTO `logs_cron` VALUES ('883', '\'获取每日一句成功\'', '2018-03-25 01:36:01');
INSERT INTO `logs_cron` VALUES ('884', '\'获取每日一句成功\'', '2018-03-25 01:37:01');
INSERT INTO `logs_cron` VALUES ('885', '\'获取每日一句成功\'', '2018-03-25 01:38:01');
INSERT INTO `logs_cron` VALUES ('886', '\'获取每日一句成功\'', '2018-03-25 01:39:01');
INSERT INTO `logs_cron` VALUES ('887', '\'获取每日一句成功\'', '2018-03-25 01:40:01');
INSERT INTO `logs_cron` VALUES ('888', '\'获取每日一句成功\'', '2018-03-25 01:41:01');
INSERT INTO `logs_cron` VALUES ('889', '\'获取每日一句成功\'', '2018-03-25 01:42:01');
INSERT INTO `logs_cron` VALUES ('890', '\'获取每日一句成功\'', '2018-03-25 01:43:01');
INSERT INTO `logs_cron` VALUES ('891', '\'获取每日一句成功\'', '2018-03-25 01:44:01');
INSERT INTO `logs_cron` VALUES ('892', '\'获取每日一句成功\'', '2018-03-25 01:45:01');
INSERT INTO `logs_cron` VALUES ('893', '\'获取每日一句成功\'', '2018-03-25 01:46:01');
INSERT INTO `logs_cron` VALUES ('894', '\'获取每日一句成功\'', '2018-03-25 01:47:01');
INSERT INTO `logs_cron` VALUES ('895', '\'获取每日一句成功\'', '2018-03-25 01:48:01');
INSERT INTO `logs_cron` VALUES ('896', '\'获取每日一句成功\'', '2018-03-25 01:49:02');
INSERT INTO `logs_cron` VALUES ('897', '\'获取每日一句成功\'', '2018-03-25 01:50:01');
INSERT INTO `logs_cron` VALUES ('898', '\'获取每日一句成功\'', '2018-03-25 01:51:01');
INSERT INTO `logs_cron` VALUES ('899', '\'获取每日一句成功\'', '2018-03-25 01:52:01');
INSERT INTO `logs_cron` VALUES ('900', '\'获取每日一句成功\'', '2018-03-25 01:53:01');
INSERT INTO `logs_cron` VALUES ('901', '\'获取每日一句成功\'', '2018-03-25 01:54:01');
INSERT INTO `logs_cron` VALUES ('902', '\'获取每日一句成功\'', '2018-03-25 01:55:01');
INSERT INTO `logs_cron` VALUES ('903', '\'获取每日一句成功\'', '2018-03-25 01:56:01');
INSERT INTO `logs_cron` VALUES ('904', '\'获取每日一句成功\'', '2018-03-25 01:57:01');
INSERT INTO `logs_cron` VALUES ('905', '\'获取每日一句成功\'', '2018-03-25 01:58:01');
INSERT INTO `logs_cron` VALUES ('906', '\'获取每日一句成功\'', '2018-03-25 01:59:01');
INSERT INTO `logs_cron` VALUES ('907', '\'获取每日一句成功\'', '2018-03-26 01:00:01');
INSERT INTO `logs_cron` VALUES ('908', '\'获取每日一句成功\'', '2018-03-26 01:01:01');
INSERT INTO `logs_cron` VALUES ('909', '\'获取每日一句成功\'', '2018-03-26 01:02:01');
INSERT INTO `logs_cron` VALUES ('910', '\'获取每日一句成功\'', '2018-03-26 01:03:02');
INSERT INTO `logs_cron` VALUES ('911', '\'获取每日一句成功\'', '2018-03-26 01:04:01');
INSERT INTO `logs_cron` VALUES ('912', '\'获取每日一句成功\'', '2018-03-26 01:05:01');
INSERT INTO `logs_cron` VALUES ('913', '\'获取每日一句成功\'', '2018-03-26 01:06:01');
INSERT INTO `logs_cron` VALUES ('914', '\'获取每日一句成功\'', '2018-03-26 01:07:01');
INSERT INTO `logs_cron` VALUES ('915', '\'获取每日一句成功\'', '2018-03-26 01:08:01');
INSERT INTO `logs_cron` VALUES ('916', '\'获取每日一句成功\'', '2018-03-26 01:09:01');
INSERT INTO `logs_cron` VALUES ('917', '\'获取每日一句成功\'', '2018-03-26 01:10:01');
INSERT INTO `logs_cron` VALUES ('918', '\'获取每日一句成功\'', '2018-03-26 01:11:01');
INSERT INTO `logs_cron` VALUES ('919', '\'获取每日一句成功\'', '2018-03-26 01:12:01');
INSERT INTO `logs_cron` VALUES ('920', '\'获取每日一句成功\'', '2018-03-26 01:13:01');
INSERT INTO `logs_cron` VALUES ('921', '\'获取每日一句成功\'', '2018-03-26 01:14:01');
INSERT INTO `logs_cron` VALUES ('922', '\'获取每日一句成功\'', '2018-03-26 01:15:01');
INSERT INTO `logs_cron` VALUES ('923', '\'获取每日一句成功\'', '2018-03-26 01:16:01');
INSERT INTO `logs_cron` VALUES ('924', '\'获取每日一句成功\'', '2018-03-26 01:17:01');
INSERT INTO `logs_cron` VALUES ('925', '\'获取每日一句成功\'', '2018-03-26 01:18:01');
INSERT INTO `logs_cron` VALUES ('926', '\'获取每日一句成功\'', '2018-03-26 01:19:01');
INSERT INTO `logs_cron` VALUES ('927', '\'获取每日一句成功\'', '2018-03-26 01:20:01');
INSERT INTO `logs_cron` VALUES ('928', '\'获取每日一句成功\'', '2018-03-26 01:21:01');
INSERT INTO `logs_cron` VALUES ('929', '\'获取每日一句成功\'', '2018-03-26 01:22:01');
INSERT INTO `logs_cron` VALUES ('930', '\'获取每日一句成功\'', '2018-03-26 01:23:01');
INSERT INTO `logs_cron` VALUES ('931', '\'获取每日一句成功\'', '2018-03-26 01:24:01');
INSERT INTO `logs_cron` VALUES ('932', '\'获取每日一句成功\'', '2018-03-26 01:25:01');
INSERT INTO `logs_cron` VALUES ('933', '\'获取每日一句成功\'', '2018-03-26 01:26:01');
INSERT INTO `logs_cron` VALUES ('934', '\'获取每日一句成功\'', '2018-03-26 01:27:01');
INSERT INTO `logs_cron` VALUES ('935', '\'获取每日一句成功\'', '2018-03-26 01:28:01');
INSERT INTO `logs_cron` VALUES ('936', '\'获取每日一句成功\'', '2018-03-26 01:29:01');
INSERT INTO `logs_cron` VALUES ('937', '\'获取每日一句成功\'', '2018-03-26 01:30:02');
INSERT INTO `logs_cron` VALUES ('938', '\'获取每日一句成功\'', '2018-03-26 01:31:01');
INSERT INTO `logs_cron` VALUES ('939', '\'获取每日一句成功\'', '2018-03-26 01:32:01');
INSERT INTO `logs_cron` VALUES ('940', '\'获取每日一句成功\'', '2018-03-26 01:33:01');
INSERT INTO `logs_cron` VALUES ('941', '\'获取每日一句成功\'', '2018-03-26 01:34:01');
INSERT INTO `logs_cron` VALUES ('942', '\'获取每日一句成功\'', '2018-03-26 01:35:01');
INSERT INTO `logs_cron` VALUES ('943', '\'获取每日一句成功\'', '2018-03-26 01:36:01');
INSERT INTO `logs_cron` VALUES ('944', '\'获取每日一句成功\'', '2018-03-26 01:37:01');
INSERT INTO `logs_cron` VALUES ('945', '\'获取每日一句成功\'', '2018-03-26 01:38:01');
INSERT INTO `logs_cron` VALUES ('946', '\'获取每日一句成功\'', '2018-03-26 01:39:01');
INSERT INTO `logs_cron` VALUES ('947', '\'获取每日一句成功\'', '2018-03-26 01:40:01');
INSERT INTO `logs_cron` VALUES ('948', '\'获取每日一句成功\'', '2018-03-26 01:41:01');
INSERT INTO `logs_cron` VALUES ('949', '\'获取每日一句成功\'', '2018-03-26 01:42:01');
INSERT INTO `logs_cron` VALUES ('950', '\'获取每日一句成功\'', '2018-03-26 01:43:02');
INSERT INTO `logs_cron` VALUES ('951', '\'获取每日一句成功\'', '2018-03-26 01:44:01');
INSERT INTO `logs_cron` VALUES ('952', '\'获取每日一句成功\'', '2018-03-26 01:45:01');
INSERT INTO `logs_cron` VALUES ('953', '\'获取每日一句成功\'', '2018-03-26 01:46:01');
INSERT INTO `logs_cron` VALUES ('954', '\'获取每日一句成功\'', '2018-03-26 01:47:01');
INSERT INTO `logs_cron` VALUES ('955', '\'获取每日一句成功\'', '2018-03-26 01:48:01');
INSERT INTO `logs_cron` VALUES ('956', '\'获取每日一句成功\'', '2018-03-26 01:49:01');
INSERT INTO `logs_cron` VALUES ('957', '\'获取每日一句成功\'', '2018-03-26 01:50:01');
INSERT INTO `logs_cron` VALUES ('958', '\'获取每日一句成功\'', '2018-03-26 01:51:01');
INSERT INTO `logs_cron` VALUES ('959', '\'获取每日一句成功\'', '2018-03-26 01:52:01');
INSERT INTO `logs_cron` VALUES ('960', '\'获取每日一句成功\'', '2018-03-26 01:53:01');
INSERT INTO `logs_cron` VALUES ('961', '\'获取每日一句成功\'', '2018-03-26 01:54:01');
INSERT INTO `logs_cron` VALUES ('962', '\'获取每日一句成功\'', '2018-03-26 01:55:01');
INSERT INTO `logs_cron` VALUES ('963', '\'获取每日一句成功\'', '2018-03-26 01:56:02');
INSERT INTO `logs_cron` VALUES ('964', '\'获取每日一句成功\'', '2018-03-26 01:57:01');
INSERT INTO `logs_cron` VALUES ('965', '\'获取每日一句成功\'', '2018-03-26 01:58:01');
INSERT INTO `logs_cron` VALUES ('966', '\'获取每日一句成功\'', '2018-03-26 01:59:01');

-- ----------------------------
-- Table structure for site_info
-- ----------------------------
DROP TABLE IF EXISTS `site_info`;
CREATE TABLE `site_info` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `url` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of site_info
-- ----------------------------
INSERT INTO `site_info` VALUES ('1', 'yeves.cn', 'hxl个人网站', 'blog,brady,brady个人博客,brady网站,brady博客,brady的网站', '个人网站', '2018-03-05 15:08:36');

-- ----------------------------
-- Table structure for tag
-- ----------------------------
DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(50) NOT NULL,
  `tag_button_type` varchar(50) DEFAULT 'success',
  PRIMARY KEY (`id`,`tag_name`)
) ENGINE=InnoDB AUTO_INCREMENT=210 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tag
-- ----------------------------
INSERT INTO `tag` VALUES ('173', 'markdown', 'tagc2');
INSERT INTO `tag` VALUES ('174', 'linux', 'tagc3');
INSERT INTO `tag` VALUES ('175', 'centos', 'tagc1');
INSERT INTO `tag` VALUES ('176', 'svn', 'tagc4');
INSERT INTO `tag` VALUES ('177', 'lnmp', 'tagc1');
INSERT INTO `tag` VALUES ('178', 'virtualbox', 'tagc2');
INSERT INTO `tag` VALUES ('179', 'php', 'tagc5');
INSERT INTO `tag` VALUES ('180', 'vagrant', 'tagc4');
INSERT INTO `tag` VALUES ('181', 'web', 'tagc5');
INSERT INTO `tag` VALUES ('182', '缓存', 'tagc1');
INSERT INTO `tag` VALUES ('183', 'git', 'tagc1');
INSERT INTO `tag` VALUES ('184', 'file_put_contents', 'tagc5');
INSERT INTO `tag` VALUES ('207', 'bootstrap', 'tagc5');
INSERT INTO `tag` VALUES ('208', '分页', 'tagc1');
INSERT INTO `tag` VALUES ('209', 'tar', 'tagc5');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) CHARACTER SET latin1 NOT NULL,
  `password` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `face` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'brady', '199f86bbeade937163455a59da9b285f', '/upload/user_info/20180320141801_3272_300.jpeg');

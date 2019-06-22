
====================================================================================================

概述

http://silviomoreto.github.io/bootstrap-select

--------------------------------------------------

安装

<link rel="stylesheet" href="xxx/bootstrap-select-1.12.2/css/bootstrap-select.min.css">
<script src="xxx/bootstrap-select-1.12.2/js/bootstrap-select.min.js"></script>
<script src="xxx/bootstrap-select-1.12.2/js/i18n/defaults-*.min.js"></script>

--------------------------------------------------

基本用法
<select class="selectpicker">
    <option>Mustard</option>
    <option>Ketchup</option>
    <option>Relish</option>
</select>

====================================================================================================

核心选项 Core options

http://silviomoreto.github.io/bootstrap-select/options/#core-options

# 可以通过 data 属性或 js 脚本传递选项
<select class="selectpicker" data-style="btn-info" data-size="4">
    <option>Mustard</option>
    <option>Ketchup</option>
    <option>Relish</option>
</select>

$('.selectpicker').selectpicker({
    style: 'btn-info',
    size: 4
});

# 选项名  取值范围  默认值

--------------------------------------------------

actionsBox  boolean  false

# 多选select框，在菜单顶部添加两个按钮：全选、全不选

--------------------------------------------------

countSelectedText  string|function  function

# 当selectedTextFormat为'count'或'count > x'，当满足条件时的显示内容
# string: 显示文本，其中{0}变量为勾选数，{1}变量为总数量
# function: 若触发函数，函数第一个变量为勾选数，第二个变量为总数，且返回值必须为一个字符串

--------------------------------------------------

deselectAllText  string  'Deselect All'

# actionsBox使能，全不选按钮的文字内容

--------------------------------------------------

dropdownAlignRight  boolean|'auto'  false

# 菜单下拉的时候默认左对齐
# 'auto': 默认左对齐，当右边位置不足则改为右对齐
# true: 总是右对齐

--------------------------------------------------

dropupAuto  boolean  true

# true: 根据窗口空间自动选择菜单上拉或下拉，哪边空间大就往哪边拉
# false: 总是下拉

--------------------------------------------------

header  string  false

# 在下拉菜单顶部添加一个标题，该标题默认包含一个关闭按钮

--------------------------------------------------

iconBase  string  'glyphicon'

# 当需要更换icon库来取代Glyphicons的时候，将该值设为新库。
# 通常还需要同时修改tickIcon，因为新库icon命名体系可能有所不同。

--------------------------------------------------

liveSearch  boolean  false

# 在下拉菜单顶端添加一个搜索框
# 可以在option中添加token作为搜索的关键字

<select class="selectpicker" data-live-search="true">
  <option data-tokens="ketchup mustard">Hot Dog, Fries and a Soda</option>
  <option data-tokens="mustard">Burger, Shake and a Smile</option>
  <option data-tokens="frosting">Sugar, Spice and all things nice</option>
</select>

--------------------------------------------------

liveSearchPlaceholder  string  null

# 给搜索框加一个placeholder

--------------------------------------------------

liveSearchStyle  string  'contains'

# 'contains': 只要内容中包括搜索关键字的option都会展示出来
# 'startsWith': 只有option开头包括搜索关键字的会被展示出来

--------------------------------------------------

maxOptions  integer|false  false

# 多选的时候，勾选项不能超过该值
# 该选项还兼容于<optgroup>，表示该optgroup下的勾选上限

--------------------------------------------------

maxOptionsText  string|array|function  function

# 使能maxOptions，当勾选项超过上限时显示的内容
# string: 显示文本，select元素和optgroup都显示该文本
# array: [0]select供元素显示，[1]供optgroup显示
# function: 触发自定义函数，函数返回值必须是一个数组，[0] 供select元素显示，[1]供optgroup显示

--------------------------------------------------

multipleSeparator  string  ', '

# 多选select框，各选项的分隔字符

--------------------------------------------------

noneSelectedText  string  'Nothing selected'

# 多选select框，未勾选任何option时select框显示的内容

--------------------------------------------------

selectAllText  string  'Select All'

# actionsBox使能，全选按钮的文字内容

--------------------------------------------------

selectedTextFormat  'values'|'static'|'count'|'count > x'  'values'

# 多选select框，select框内容显示格式
# 'values': 选择什么，就显示什么，通过multipleSeparator分隔
# 'static': 显示固定字符串，可以通过title赋值
# 'count': 当勾选项超过一项，显示已勾选的数量
# 'count > x': x必须为一个整数，当勾选项超过x项，显示已勾选的数量，否则按照'values'方式显示

--------------------------------------------------

showContent  boolean  true

# 当option定义了content，该选项控制content内容要不要在select框一起显示出来，仅针对非multi-select有效

<select class="selectpicker">
    <option data-content="<span class='label label-success'>Relish</span>">Relish</option>
</select>

--------------------------------------------------

showIcon  boolean  true

# 当option定义了icon，该选项控制icon要不要在select框一起显示出来，仅针对非multi-select有效

# option定义icon:

<select class="selectpicker">
    <option data-icon="glyphicon-heart">Ketchup</option>
</select>

--------------------------------------------------

showSubtext  boolean  false

# 当option定义了subtext，该选项控制subtext内容要不要在select框一起显示出来，仅针对非multi-select有效

# 给每一个option加subtext可以显示副标，同时subtext内容可以作为liveSearch的搜索关键字

<select class="selectpicker">
    <option value="1044" data-subtext="1044">深圳市班德施贸易有限公司</option>
    <option value="1048" data-subtext="1048">宜兴市丁蜀镇一九一壶紫砂经营部</option>
    ......
    <option value="1066" data-subtext="1066">北京钰诚康商贸中心</option>
    <option value="1068" data-subtext="1068">深圳市禾金子贸易公司</option>
</select>

--------------------------------------------------

showTick  boolean  false

# 当select框没有加multiple属性时，使能该选项可以显示勾选标识

--------------------------------------------------

size  'auto'|integer|false  'auto'

# 'auto': 根据窗口大小尽可能显示option数量
# integer: 按照给定值显示option个数
# false: 总是显示所有option

--------------------------------------------------

style  string  'btn-default'

# 当对style赋值的时候，该值会作为样式类添加到按钮的class

--------------------------------------------------

tickIcon  string  'glyphicon-ok'

# 设置被勾选的option的icon

--------------------------------------------------

title  string|null  null

# select框的默认标题
# 若将title设置到option，那么当option被勾选时，select框显示title值

<select class="selectpicker">
    <option title="Combo 1">Hot Dog, Fries and a Soda</option>
    <option title="Combo 2">Burger, Shake and a Smile</option>
    <option title="Combo 3">Sugar, Spice and all things nice</option>
</select>

--------------------------------------------------

width  'auto'|'fit'|css-width|false  false

# 'auto': 根据option内容长度自动调整select框宽度
# 'fit': 根据selected option内容长度实时调整
# css-width: 值需要是一个字符串，内容为CSS宽度，例如'200px', '50%'
# false: 移除所有宽度设置

====================================================================================================

事件 Events

http://silviomoreto.github.io/bootstrap-select/options/#events

$('#mySelect').on('hidden.bs.select', function (e) {
  // do something...
});

--------------------------------------------------

show.bs.select

# 下拉菜单开始显示的时候调用

--------------------------------------------------

shown.bs.select

# 下拉菜单显示完毕的时候调用

--------------------------------------------------

hide.bs.select

# 下拉菜单开始收起的时候调用

--------------------------------------------------

hidden.bs.select

# 下拉菜单收起完毕的时候调用.

--------------------------------------------------

loaded.bs.select

# select框初始化时调用

--------------------------------------------------

rendered.bs.select

# 触发render时调用

--------------------------------------------------

refreshed.bs.select

# 触发refresh时调用

--------------------------------------------------

changed.bs.select

# 下拉框出现值改变时调用

====================================================================================================

四、	方法
http://silviomoreto.github.io/bootstrap-select/methods/

--------------------------------------------------

.selectpicker('val')

# 对select框进行赋值，若只传'val'参数表示获取当前select框的值

$('.selectpicker').selectpicker('val', 'Mustard');
$('.selectpicker').selectpicker('val', ['Mustard','Relish']);

--------------------------------------------------

.selectpicker('selectAll')

# multi-select选择全部

--------------------------------------------------

.selectpicker('deselectAll')

# multi-select全部不择

--------------------------------------------------

.selectpicker('setStyle')

# 替换select样式：
$('.selectpicker').addClass('col-lg-12').selectpicker('setStyle');
$('.selectpicker').selectpicker('setStyle', 'btn-danger');

# 添加select样式：
$('.selectpicker').selectpicker('setStyle', 'btn-large', 'add');

# 移除select样式：
$('.selectpicker').selectpicker('setStyle', 'btn-large', 'remove');

--------------------------------------------------

.selectpicker('refresh')

# 刷新select框

# 编辑option
<select class="selectpicker remove-example">
    <option value="Mustard">Mustard</option>
    <option value="Ketchup">Ketchup</option>
    <option value="Relish">Relish</option>
</select>

<button class="btn btn-warning rm-mustard">Remove Mustard</button>
<button class="btn btn-danger rm-ketchup">Remove Ketchup</button>
<button class="btn btn-success rm-relish">Remove Relish</button>

$('.rm-mustard').click(function () {
    $('.remove-example').find('[value=Mustard]').remove();
    $('.remove-example').selectpicker('refresh');
});

# disabling / enabling
<button class="btn btn-default ex-disable">Disable</button>
<button class="btn btn-default ex-enable">Enable</button>

$('.ex-disable').click(function () {
    $('.disable-example').prop('disabled', true);
    $('.disable-example').selectpicker('refresh');
});

$('.ex-enable').click(function () {
    $('.disable-example').prop('disabled', false);
    $('.disable-example').selectpicker('refresh');
});

--------------------------------------------------

.selectpicker('toggle')

# 切换select菜单的下拉或收起状态

--------------------------------------------------

.selectpicker('hide')

# 收起select菜单

--------------------------------------------------

.selectpicker('show')

# 下拉select菜单

--------------------------------------------------

.selectpicker('destroy')

# 注销 select 菜单

====================================================================================================

五、	应用

--------------------------------------------------

Multiple 支持多选

<select class="selectpicker" name="user[]" multiple>
    <option>Maru</option>
    <option>Carter</option>
    <option>Jason</option>
    <option>James</option>
</select>

--------------------------------------------------

multi-select 搜索框，若每次搜完选了一个值，想马上清除搜索框内容，进行重新搜索

<select class="selectpicker" multiple>
    <option value="xxx">xxx</option>
    ......
</select>

$('#supplier').selectpicker({
    liveSearch: true
});

$('.selectpicker').on('changed.bs.select', function(e) {
    $('.selectpicker').selectpicker('toggle').selectpicker('toggle');
});

--------------------------------------------------

optgroup

# 支持optgrup标签

<select class="selectpicker">
    <optgroup label="Picnic">
        <option>Mustard</option>
        <option>Ketchup</option>
        <option>Relish</option>
    </optgroup>
    <optgroup label="Camping">
        <option>Tent</option>
        <option>Flashlight</option>
        <option>Toilet Paper</option>
    </optgroup>
</select>

--------------------------------------------------

menu arrow

<select class="selectpicker show-menu-arrow">
    <option>Mustard</option>
    <option>Ketchup</option>
    <option>Relish</option>
</select>

--------------------------------------------------

分割线

<select class="selectpicker">
    <option>Mustard</option>
    <option>Ketchup</option>
    <option data-divider="true"></option>
    <option>Relish</option>
</select>

--------------------------------------------------

Disabled

<select class="selectpicker" disabled>
    <option>Mustard</option>
    <option>Ketchup</option>
    <option>Relish</option>
</select>

# 支持指定option disabled
<select class="selectpicker">
    <option>Mustard</option>
    <option disabled>Ketchup</option>
    <option>Relish</option>
</select>

# 支持指定optgroup disabled
<select class="selectpicker test">
    <optgroup label="Picnic" disabled>
        <option>Mustard</option>
        <option>Ketchup</option>
        <option>Relish</option>
    </optgroup>
    <optgroup label="Camping">
        <option>Tent</option>
        <option>Flashlight</option>
        <option>Toilet Paper</option>
    </optgroup>
</select>

====================================================================================================

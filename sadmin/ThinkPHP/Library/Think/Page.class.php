<?php
namespace Think;

class Page {
    public $firstRow; // 起始行数
    public $listRows; // 列表每页显示行数
    public $parameter; // 分页跳转时要带的参数
    public $totalRows; // 总行数
    public $totalPages; // 分页总页面数
    public $rollPage = 11; // 分页栏每页显示的页数

    private $p       = 'p'; //分页参数名
    private $url     = ''; //当前链接URL
    private $nowPage = 1;

    // 分页显示定制
    private $config  = array(
        'prev'   => '上一页',
        'next'   => '下一页',
        'first'  => '首页',
        'last'   => '尾页',
        'goto'   => '跳转至',
        'total'  => '总共 %TOTAL_PAGE% 页， %TOTAL_ROW% 行',
        'errmsg' => '页数必须为一个数字',
    );

    /**
     * 架构函数
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows, $listRows = 20, $parameter = array()) {
        // 设置分页参数名称
        C('VAR_PAGE') && $this->p = C('VAR_PAGE');

        /* 基础设置 */
        $this->totalRows  = $totalRows; // 设置总记录数
        $this->listRows   = $listRows;  // 设置每页显示行数
        $this->parameter  = empty($parameter) ? $_GET : $parameter;
        $this->nowPage    = empty($_GET[$this->p]) ? 1 : intval($_GET[$this->p]);
        $this->nowPage    = $this->nowPage > 0 ? $this->nowPage : 1;
        $this->firstRow   = $this->listRows * ($this->nowPage - 1);
    }

    /**
     * 定制分页链接设置
     * @param string $name  设置名称
     * @param string $value 设置值
     */
    public function setConfig($name, $value) {
        if (isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    /**
     * 获取当前页页数
     */
    public function getCurPage() {
        return $this->nowPage;
    }

    /**
     * 生成链接URL
     * @param  integer $page 页码
     * @return string
     */
    private function url($page) {
        return str_replace('[PAGE]', $page, $this->url);
    }

    /**
     * 组装分页链接
     * @return string
     */
    public function show() {
        if (0 == $this->totalRows) {
            return '';
        }

        /* 生成URL */
        $this->parameter[$this->p] = '[PAGE]';
        $paramStr = http_build_query($this->parameter);
        $this->url = U()."?".urldecode($paramStr);

        /* 计算分页信息：总页数 */
        $this->totalPages = intval(ceil($this->totalRows / $this->listRows));
        if (!empty($this->totalPages) && $this->nowPage > $this->totalPages) {
            // 当前页超过总页数则设为总页数
            $this->nowPage = $this->totalPages;
        }

        // 总共只有一页，那就不需要分页，返回空字符串
        if ($this->totalPages <= 1) {
            return "";
        }

        /* 计算分页临时变量 */
        $nowCoolPage = $this->rollPage / 2;
        $nowCoolPageCeil = ceil($nowCoolPage);

        // 上一页
        $upRow  = $this->nowPage - 1;
        if ($upRow > 0) {
            $upPage = '<li><a href="'.$this->url($upRow).'" aria-label="Previous"><span aria-hidden="true">'.$this->config['prev'].'</span></a></li>';
        } else {
            $upPage = '<li class="disabled"><a aria-label="Previous"><span aria-hidden="true">'.$this->config['prev'].'</span></a></li>';
        }

        // 下一页
        $downRow  = $this->nowPage + 1;
        if ($downRow <= $this->totalPages) {
            $downPage = '<li><a href="'.$this->url($downRow).'" aria-label="Next"><span aria-hidden="true">'.$this->config['next'].'</span></a></li>';
        } else {
            $downPage = '<li class="disabled"><a aria-label="Next"><span>'.$this->config['next'].'</span></a></li>';
        }

        // 第一页
        $theFirst = '';
        if ($this->totalPages > $this->rollPage && ($this->nowPage - $nowCoolPage) >= 1) {
            $theFirst = '<li><a class="first" href="'.$this->url(1).'"><span aria-hidden="true">'.$this->config['first'].'</span></a></li>';
        }

        // 最后一页
        $theEnd = '';
        if ($this->totalPages > $this->rollPage && ($this->nowPage + $nowCoolPage) < $this->totalPages) {
            $theEnd = '<li><a class="end" href="'.$this->url($this->totalPages).'"><span aria-hidden="true">'.$this->config['last'].'</span></a></li>';
        }

        // 数字连接
        $linkPage = "";
        for ($i = 1; $i <= $this->rollPage; $i++) {
            if (($this->nowPage - $nowCoolPage) <= 0) {
                $page = $i;
            } else if (($this->nowPage + $nowCoolPage - 1) >= $this->totalPages) {
                $page = $this->totalPages - $this->rollPage + $i;
            } else {
                $page = $this->nowPage - $nowCoolPageCeil + $i;
            }
            if ($page > 0 && $page != $this->nowPage) {
                if ($page <= $this->totalPages) {
                    $linkPage .= '<li><a href="'.$this->url($page).'">'.$page.'</a></li>';
                } else {
                    break;
                }
            } else {
                if ($page > 0 && $this->totalPages != 1) {
                    $linkPage .= '<li class="active"><a>'.$page.'<span class="sr-only">(current)</span></a></li>';
                }
            }
        }

        // pagination
        $pagination = '<ul class="pagination pagination-sm">';
        $pagination .= $theFirst.$upPage.$linkPage.$downPage.$theEnd;
        $pagination .= '</ul>';

        // goto
        $searchArr = array('%TOTAL_ROW%', '%TOTAL_PAGE%');
        $replaceArr = array($this->totalRows, $this->totalPages);
        $goto = '<div class="input-group input-group-sm col-sm-3 def-btm-edge">';
        $goto .= '<span class="input-group-btn">';
        $goto .= '<button class="btn btn-primary" id="gotoBtn" type="button" data-url="'.$this->url("[PAGE]").'">';
        $goto .= $this->config['goto'];
        $goto .= '</button>';
        $goto .= '</span>';
        $goto .= '<input class="form-control" id="gotoPage" type="text" />';
        $goto .= '<div class="input-group-addon">';
        $goto .= str_replace($searchArr, $replaceArr, $this->config['total']);
        $goto .= '</div>';
        $goto .= '</div>';

        $nav = "<nav>{$pagination}{$goto}</nav>";

        $script = "<script>";
        $script .= "$('#gotoBtn').on('click', pageGoto);";
        $script .= "$('#gotoPage').keydown(function() {";
        $script .= "    if (event.keyCode == 13) {";
        $script .= "        pageGoto();";
        $script .= "    }";
        $script .= "});";
        $script .= "function pageGoto() {";
        $script .= "    var page = $.tool.trim($('#gotoPage').val());";
        $script .= "    var urlTpl = $('#gotoBtn').data('url');";
        $script .= "    if (page == '') {";
        $script .= "        $.zmsg.error('".$this->config['errmsg']."');";
        $script .= "        return false;";
        $script .= "    } else if (isNaN(page)) {";
        $script .= "        $.zmsg.error('".$this->config['errmsg']."');";
        $script .= "        return false;";
        $script .= "    }";
        $script .= "    window.location = urlTpl.replace('[PAGE]', page);";
        $script .= "}";
        $script .= "</script>";

        return $nav.$script;
    }
}

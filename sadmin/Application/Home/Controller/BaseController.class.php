<?php
namespace Home\Controller;

use Think\Controller;

class BaseController extends Controller {

    public function _initialize()
    {
        $this->assign('fileVersion',  time());
    }

    /**
     * 检查请求方式是否为 ajax
     */
    protected function checkIsAjax()
    {
        if (!IS_AJAX) {
            E("Bad Request, only ajax is accepted!");
        }
        return true;
    }

    /**
     * 检查是否为命令行模式
     */
    protected function checkIsCli()
    {
        if (!IS_CLI) {
            E("Only cli mode is accepted!");
        }
        return true;
    }

    /**
     * 页面传入基础数据
     */
    protected function assignBaseData()
    {
        $uInfo = C('G_USER');
        $viewAssign = array(
            'regionMap' => get_region_map($uInfo['roleid']), // 授权游戏 map
            'gameId' => $uInfo['gameid'],      // 当前游戏地区 id
            'mCode' => C('G_ACCESS_MAIN'),     // 当前一级目录
            'sCode' => C('G_ACCESS_SUBLEVEL'), // 当前二级目录
            'tCode' => C('G_ACCESS_THIRD'),    // 当前三级目录
            'nav' => C('G_NAV_MAP'),           // 导航目录结构
        );
        $this->assign($viewAssign);
    }
}

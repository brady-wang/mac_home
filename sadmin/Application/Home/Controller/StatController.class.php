<?php
namespace Home\Controller;

use Common\Service\ValidatorService;
use Home\Logic\StatLogic;
use Home\Model\GameModel;
use Home\Model\GameChannelModel;
use Home\Model\StatLandpageModel;
use Home\Model\StatClubEconomicModel;
use Home\Model\StatClubIncomeModel;
use Home\Model\StatClubPromoterModel;
use Home\Model\StatDiamondProduceModel;
use Home\Model\StatGameitemModel;
use Home\Model\StatGameRoomModel;
use Home\Model\StatGameShareModel;
use Home\Model\StatOnlineModel;
use Home\Model\StatUserBehaveModel;
use Home\Model\StatUserChannelModel;
use Home\Model\StatUserDailyModel;
use Home\Model\StatUserRankModel;
use Home\Model\StatUserRegisterModel;
use Home\Model\StatUserRemainModel;
use Home\Model\StatUserTotalModel;
use Home\Model\SysCacheModel;

class StatController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->assignBaseData();
    }

    /************************************ 实时数据 ************************************/

    // 在线人数
    public function realtimeOnline()
    {
        $viewAssign = array();
        // 页面 title
        $viewAssign['title'] = "数据统计 | 在线人数";

        $dbModel = new StatOnlineModel();
        $statLogic = new StatLogic();
        //选择游戏，选择数据库
        $gameId = C('G_USER.gameid');

        $viewAssign['selView'] = empty(I('get.viewType')) ? 0 : I('get.viewType');
        $getStartTime = strtotime(I('get.start')); // 开始时间
        $selDay = array();
        if ($getStartTime) {
            $startDate = date('Y-m-d', $getStartTime); // 开始时间：格式2017-06-03

            $viewAssign['start'] =  $startDate;
            if (!empty(I('get.select_days'))) {
                $selDays = I('get.select_days');
                foreach ($selDays as $v) {
                    $createDate[] = $v; // 页面图表显示时间
                    $selectTime[] = strtotime($v);
                    $legendShowStatus[$v] = true;
                }
                $selDay = $selDays;
            }
        } else {
            $startDate = date('Y-m-d', time());
            $viewAssign['start'] = $startDate;
            // 取相邻7天时间
            for ($i = 0; $i < 7; $i++) {
                $dataTime = strtotime($startDate." 00:00:00");
                $times = $dataTime - $i * 86400;
                $tempData = date("Y-m-d", $times);
                $createDate[] =  $tempData; // 页面图表显示时间
                $selectTime[] = $times;
                $legendShowStatus[$tempData] = true;
                $selDay[] = $tempData;
            }
        }
        $viewAssign['selDays'] = $selDay;
        $viewAssign['legendShowStatus'] = $legendShowStatus;

        $hours = $statLogic->showKeyList;

        // 获取数据
        $where = array(
            'game_id' => $gameId
        );
        if(is_array($selectTime)){
            $where['data_time'] =  array( 'IN', $selectTime);
        }else{
            $where['data_time'] =  $selectTime ;
        }

        $modRet = $dbModel->getGameOnlineStatList($where) ;
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $dftData = $modRet;
        }

        //数据处理逻辑
        $data = $statLogic->formatOnlineData($dftData['data'],$selectTime);

        $viewAssign['hours'] = $hours;
        $viewAssign['createDate'] = $createDate;
        $viewAssign['titleBt'] = $createDate;
        $viewAssign['statData'] = array_values($data);

        $this->assign($viewAssign);
        $this->display();
    }

    // 新增人数
    public function realtimeRegister()
    {
        $viewAssign = array();
        // 页面 title
        $viewAssign['title'] = "数据统计 | 新增人数";

        $dbModel = new StatOnlineModel();
        $statLogic = new StatLogic();
        //选择游戏，选择数据库
        $gameId = C('G_USER.gameid');

        $viewAssign['selView'] = empty(I('get.viewType')) ? 0 : I('get.viewType');
        $getStartTime = strtotime(I('get.start')); // 开始时间
        $selDay = array();
        if ($getStartTime) {
            $startDate = date('Y-m-d', $getStartTime); // 开始时间：格式2017-06-03

            $viewAssign['start'] =  $startDate;
            if (!empty(I('get.select_days'))) {
                $selDays = I('get.select_days');
                foreach ($selDays as $v) {
                    $createDate[] = $v; // 页面图表显示时间
                    $selectTime[] = strtotime($v);
                    $legendShowStatus[$v] = true;
                }
                $selDay = $selDays;
            }
        } else {
            $startDate = date('Y-m-d', time());
            $viewAssign['start'] = $startDate;
            // 取相邻7天时间
            for ($i = 0; $i < 7; $i++) {
                $dataTime = strtotime($startDate." 00:00:00");
                $times = $dataTime - $i * 86400;
                $tempData = date("Y-m-d", $times);
                $createDate[] =  $tempData; // 页面图表显示时间
                $selectTime[] = $times;
                $legendShowStatus[$tempData] = true;
                $selDay[] = $tempData;
            }
        }
        $viewAssign['selDays'] = $selDay;
        $viewAssign['legendShowStatus'] = $legendShowStatus;

        // 获取数据
        //读取后端数据库的在线数据
        $where = array(
            'game_id' => $gameId
        );
        if(is_array($selectTime)){
            $where['data_time'] =  array( 'IN', $selectTime);
        }else{
            $where['data_time'] =  $selectTime ;
        }

        $modRet = $dbModel->getGameOnlineStatList($where);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $dftData = $modRet;
        }

        //数据处理逻辑
        $modRet = $statLogic->formatRegisterData($dftData['data'], $selectTime, $displayTime);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $data = $modRet['data'];
        }

        $viewAssign['hours'] = $displayTime;
        $viewAssign['createDate'] = $createDate;
        $viewAssign['titleBt'] = $createDate;
        $viewAssign['statData'] = array_values($data);

        $this->assign($viewAssign);
        $this->display();
    }

    /************************************ 市场数据 ************************************/

    /**
     * 代理概况
     * @author Carter
     */
    public function clubPromoter()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "代理概况 | 市场数据";

        $vldSer = new ValidatorService();
        $statMod = new StatClubPromoterModel();

        // 校验输入
        $attr = I('get.', '', 'trim');
        if (is_null($attr['query_type'])) {
            $attr['query_type'] = 1;
        }
        $rules = array(
            // 查看方式
            array('query_type', 1, array(
                array('in', '1,2', '未知查看方式'),
            )),
            // 开始日期
            array('start_date', 0, array(
                array('require_if', 'query_type,2', '请选择开始日期'),
            )),
            array('start_date', 1, array(
                array('date', null, '开始日期格式有误'),
            )),
            // 结束日期
            array('end_date', 0, array(
                array('require_if', 'query_type,2', '请选择结束日期'),
            )),
            array('end_date', 1, array(
                array('date', null, '结束日期格式有误'),
                array('date_after', empty($attr['start_date']) ? '1970-01-01' : $attr['start_date'], '结束日期不能早于开始日期'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
        }
        // 时间区间方式，跨度不能超过60天
        if (2 == $attr['query_type']) {
            if (strtotime($attr['end_date']) - strtotime($attr['start_date']) > 60 * 86400) {
                $viewAssign['errMsg'] = "时间区间跨度不能超过60天";
            }
        }
        $viewAssign['query'] = $attr;

        $gameId = C('G_USER.gameid');

        $modRet = $statMod->queryClubPromoterDetail($gameId, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['data'] = $modRet['data'];
        }

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 代理概况数据下载
     * @author Carter
     */
    public function iframeClubPromoterDownFile()
    {
        $vldSer = new ValidatorService();
        $statMod = new StatClubPromoterModel();

        $srcMap = array(
            'inc' => array(
                'field' => 'stat_date,promoter_count,transfer_count',
                'title' => array(
                    'stat_date' => '统计日期',
                    'promoter_count' => '新开通代理',
                    'transfer_count' => '新转正代理',
                ),
                'name' => '新增代理',
            ),
            'eff' => array(
                'field' => 'stat_date,effective_transfer,effective_active,effective_recharge',
                'title' => array(
                    'stat_date' => '统计日期',
                    'effective_transfer' => '累计转正',
                    'effective_active' => '当日活跃',
                    'effective_recharge' => '当日充值总额',
                ),
                'name' => '有效代理数据',
            ),
            'clb' => array(
                'field' => 'stat_date,effective_transfer,club_active,club_recharge',
                'title' => array(
                    'stat_date' => '统计日期',
                    'effective_transfer' => '累计转正',
                    'club_active' => '当日活跃',
                    'club_recharge' => '当日充值总额',
                ),
                'name' => '亲友圈代理分析',
            ),
            'rtl' => array(
                'field' => 'stat_date,effective_transfer,retail_active,retail_recharge',
                'title' => array(
                    'stat_date' => '统计日期',
                    'effective_transfer' => '累计转正',
                    'retail_active' => '当日活跃',
                    'retail_recharge' => '当日充值总额',
                ),
                'name' => '散户代理分析',
            ),
        );

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('source', 0, array(
                array('require', null, "类型缺失"),
                array('in', implode(',', array_keys($srcMap)), '未知类型'),
            )),
            // 查看方式
            array('query_type', 0, array(
                array('require', null, "查看方式缺失"),
                array('in', '1,2', '未知查看方式'),
            )),
            // 开始日期
            array('start_date', 0, array(
                array('require_if', 'query_type,2', '请选择开始日期'),
            )),
            array('start_date', 1, array(
                array('date', null, '开始日期格式有误'),
            )),
            // 结束日期
            array('end_date', 0, array(
                array('require_if', 'query_type,2', '请选择结束日期'),
            )),
            array('end_date', 1, array(
                array('date', null, '结束日期格式有误'),
                array('date_after', empty($attr['start_date']) ? '1970-01-01' : $attr['start_date'], '结束日期不能早于开始日期'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $errMsg = $vRet;
            goto ERR_RET;
        }

        $gameId = C('G_USER.gameid');

        // 数据列表
        $modRet = $statMod->queryClubPromoterListForDown($gameId, $attr, $srcMap[$attr['source']]['field']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $errMsg = $modRet['msg'];
            goto ERR_RET;
        }
        $list = $modRet['data'];

        $exportTitle = $srcMap[$attr['source']]['title'];

        switch ($attr['source']) {
            case 'eff':
                foreach ($list as $k => $v) {
                    $list[$k]['effective_recharge'] = round($v['effective_recharge'] / 100);
                }
                break;
            case 'clb':
                foreach ($list as $k => $v) {
                    $list[$k]['club_recharge'] = round($v['club_recharge'] / 100);
                }
                break;
            case 'rtl':
                foreach ($list as $k => $v) {
                    $list[$k]['retail_recharge'] = round($v['retail_recharge'] / 100);
                }
                break;
        }
        $exportData = $list;

        $fileName = "代理概况_{$srcMap[$attr['source']]['name']}.csv";
        export_file($fileName, $exportTitle, $exportData);

    ERR_RET:
        $httpReferer = I('server.HTTP_REFERER');
        $url = parse_url($httpReferer);
        $param = array('errMsg' => "导出失败：{$errMsg}");
        foreach (explode('&amp;', $url['query']) as $query) {
            list($p, $v) = explode('=', $query);
            $param[$p] = $v;
        }
        redirect($url['scheme'].'://'.$url['host'].':'.$url['port'].$url['path'].'?'.http_build_query($param));
    }

    /**
     * 收入趋势
     * @author Carter
     */
    public function clubIncome()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "收入趋势 | 市场数据";

        $vldSer = new ValidatorService();
        $statMod = new StatClubIncomeModel();

        // 校验输入
        $attr = I('get.', '', 'trim');
        if (is_null($attr['query_type'])) {
            $attr['query_type'] = 1;
        }
        $rules = array(
            // 查看方式
            array('query_type', 1, array(
                array('in', '1,2', '未知查看方式'),
            )),
            // 开始日期
            array('start_date', 0, array(
                array('require_if', 'query_type,2', '请选择开始日期'),
            )),
            array('start_date', 1, array(
                array('date', null, '开始日期格式有误'),
            )),
            // 结束日期
            array('end_date', 0, array(
                array('require_if', 'query_type,2', '请选择结束日期'),
            )),
            array('end_date', 1, array(
                array('date', null, '结束日期格式有误'),
                array('date_after', empty($attr['start_date']) ? '1970-01-01' : $attr['start_date'], '结束日期不能早于开始日期'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
        }
        // 时间区间方式，跨度不能超过60天
        if (2 == $attr['query_type']) {
            if (strtotime($attr['end_date']) - strtotime($attr['start_date']) > 60 * 86400) {
                $viewAssign['errMsg'] = "时间区间跨度不能超过60天";
            }
        }
        $viewAssign['query'] = $attr;

        $gameId = C('G_USER.gameid');

        $modRet = $statMod->queryClubIncomeDetail($gameId, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['data'] = $modRet['data'];
        }

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 收入趋势数据下载
     * @author Carter
     */
    public function iframeClubIncomeDownFile()
    {
        $vldSer = new ValidatorService();
        $statMod = new StatClubIncomeModel();

        $srcMap = array(
            'amt' => array(
                'field' => 'stat_date,income_amount',
                'title' => array(
                    'stat_date' => '统计日期',
                    'income_amount' => '付费金额',
                ),
                'name' => '付费趋势',
            ),
            'num' => array(
                'field' => 'stat_date,pay_num',
                'title' => array(
                    'stat_date' => '统计日期',
                    'pay_num' => '付费人数',
                ),
                'name' => '付费人数',
            ),
            'nam' => array(
                'field' => 'stat_date,pay_type_one_amount,pay_type_two_amount,pay_type_three_amount,pay_type_four_amount',
                'title' => array(
                    'stat_date' => '统计日期',
                    'pay_type_one_amount' => '当日新增付费',
                    'pay_type_two_amount' => '1-3日新增付费',
                    'pay_type_three_amount' => '4-7日新增付费',
                    'pay_type_four_amount' => '7日以上新增付费',
                ),
                'name' => '新增付费（付费额度）',
            ),
            'nnm' => array(
                'field' => 'stat_date,pay_type_one_num,pay_type_two_num,pay_type_three_num,pay_type_four_num',
                'title' => array(
                    'stat_date' => '统计日期',
                    'pay_type_one_num' => '当日新增付费',
                    'pay_type_two_num' => '1-3日新增付费',
                    'pay_type_three_num' => '4-7日新增付费',
                    'pay_type_four_num' => '7日以上新增付费',
                ),
                'name' => '新增付费（付费人数）',
            ),
        );

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('source', 0, array(
                array('require', null, "类型缺失"),
                array('in', implode(',', array_keys($srcMap)), '未知类型'),
            )),
            // 查看方式
            array('query_type', 0, array(
                array('require', null, "查看方式缺失"),
                array('in', '1,2', '未知查看方式'),
            )),
            // 开始日期
            array('start_date', 0, array(
                array('require_if', 'query_type,2', '请选择开始日期'),
            )),
            array('start_date', 1, array(
                array('date', null, '开始日期格式有误'),
            )),
            // 结束日期
            array('end_date', 0, array(
                array('require_if', 'query_type,2', '请选择结束日期'),
            )),
            array('end_date', 1, array(
                array('date', null, '结束日期格式有误'),
                array('date_after', empty($attr['start_date']) ? '1970-01-01' : $attr['start_date'], '结束日期不能早于开始日期'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $errMsg = $vRet;
            goto ERR_RET;
        }

        $gameId = C('G_USER.gameid');

        // 数据列表
        $modRet = $statMod->queryClubIncomeListForDown($gameId, $attr, $srcMap[$attr['source']]['field']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $errMsg = $modRet['msg'];
            goto ERR_RET;
        }
        $list = $modRet['data'];

        $exportTitle = $srcMap[$attr['source']]['title'];

        switch ($attr['source']) {
            case 'amt':
                foreach ($list as $k => $v) {
                    $list[$k]['income_amount'] = round($v['income_amount'] / 100);
                }
                break;
            case 'nam':
                foreach ($list as $k => $v) {
                    $list[$k]['pay_type_one_amount'] = round($v['pay_type_one_amount'] / 100);
                    $list[$k]['pay_type_two_amount'] = round($v['pay_type_two_amount'] / 100);
                    $list[$k]['pay_type_three_amount'] = round($v['pay_type_three_amount'] / 100);
                    $list[$k]['pay_type_four_amount'] = round($v['pay_type_four_amount'] / 100);
                }
                break;
        }
        $exportData = $list;

        $fileName = "收入趋势_{$srcMap[$attr['source']]['name']}.csv";
        export_file($fileName, $exportTitle, $exportData);

    ERR_RET:
        $httpReferer = I('server.HTTP_REFERER');
        $url = parse_url($httpReferer);
        $param = array('errMsg' => "导出失败：{$errMsg}");
        foreach (explode('&amp;', $url['query']) as $query) {
            list($p, $v) = explode('=', $query);
            $param[$p] = $v;
        }
        redirect($url['scheme'].'://'.$url['host'].':'.$url['port'].$url['path'].'?'.http_build_query($param));
    }

    /**
     * 经济分析
     * @author Carter
     */
    public function clubEconomic()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "经济分析 | 市场数据";

        $vldSer = new ValidatorService();
        $statMod = new StatClubEconomicModel();

        // 校验输入
        $attr = I('get.', '', 'trim');
        if (is_null($attr['query_type'])) {
            $attr['query_type'] = 1;
        }
        $rules = array(
            // 查看方式
            array('query_type', 1, array(
                array('in', '1,2', '未知查看方式'),
            )),
            // 开始日期
            array('start_date', 0, array(
                array('require_if', 'query_type,2', '请选择开始日期'),
            )),
            array('start_date', 1, array(
                array('date', null, '开始日期格式有误'),
            )),
            // 结束日期
            array('end_date', 0, array(
                array('require_if', 'query_type,2', '请选择结束日期'),
            )),
            array('end_date', 1, array(
                array('date', null, '结束日期格式有误'),
                array('date_after', empty($attr['start_date']) ? '1970-01-01' : $attr['start_date'], '结束日期不能早于开始日期'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
        }
        // 时间区间方式，跨度不能超过60天
        if (2 == $attr['query_type']) {
            if (strtotime($attr['end_date']) - strtotime($attr['start_date']) > 60 * 86400) {
                $viewAssign['errMsg'] = "时间区间跨度不能超过60天";
            }
        }
        $viewAssign['query'] = $attr;

        $gameId = C('G_USER.gameid');

        $modRet = $statMod->queryClubEconomicDetail($gameId, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['data'] = $modRet['data'];
        }

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 经济分析数据下载
     * @author Carter
     */
    public function iframeClubEconomicDownFile()
    {
        $vldSer = new ValidatorService();
        $statMod = new StatClubEconomicModel();

        $srcMap = array(
            'dmd' => array(
                'field' => 'stat_date,diamond_produce,diamond_consume,diamond_remain_active_agent',
                'title' => array(
                    'stat_date' => '统计日期',
                    'diamond_produce' => '当日钻石产出',
                    'diamond_consume' => '当日钻石消耗',
                    'diamond_remain_active_agent' => '活跃代理钻石结余',
                ),
                'name' => '钻石产出消耗',
            ),
            'rmn' => array(
                'field' => 'stat_date,diamond_produce_active_agent,diamond_remain_active_agent,diamond_produce_active_game,diamond_remain_game',
                'title' => array(
                    'stat_date' => '统计日期',
                    'diamond_produce_active_agent' => '活跃代理发放',
                    'diamond_remain_active_agent' => '活跃代理钻石结余',
                    'diamond_produce_active_game' => '活跃用户发放',
                    'diamond_remain_game' => '用户结余',
                ),
                'name' => '钻石结余',
            ),
            'abn' => array(
                'field' => 'stat_date,abnormal_get,abnormal_transfer',
                'title' => array(
                    'stat_date' => '统计日期',
                    'abnormal_get' => '一次性发放超过300',
                    'abnormal_transfer' => '亲友圈转移钻石代理数',
                ),
                'name' => '异常用户',
            ),
        );

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('source', 0, array(
                array('require', null, "类型缺失"),
                array('in', implode(',', array_keys($srcMap)), '未知类型'),
            )),
            // 查看方式
            array('query_type', 0, array(
                array('require', null, "查看方式缺失"),
                array('in', '1,2', '未知查看方式'),
            )),
            // 开始日期
            array('start_date', 0, array(
                array('require_if', 'query_type,2', '请选择开始日期'),
            )),
            array('start_date', 1, array(
                array('date', null, '开始日期格式有误'),
            )),
            // 结束日期
            array('end_date', 0, array(
                array('require_if', 'query_type,2', '请选择结束日期'),
            )),
            array('end_date', 1, array(
                array('date', null, '结束日期格式有误'),
                array('date_after', empty($attr['start_date']) ? '1970-01-01' : $attr['start_date'], '结束日期不能早于开始日期'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $errMsg = $vRet;
            goto ERR_RET;
        }

        $gameId = C('G_USER.gameid');

        // 数据列表
        $modRet = $statMod->queryClubEconomicListForDown($gameId, $attr, $srcMap[$attr['source']]['field']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $errMsg = $modRet['msg'];
            goto ERR_RET;
        }
        $exportData = $modRet['data'];

        $exportTitle = $srcMap[$attr['source']]['title'];

        $fileName = "经济分析_{$srcMap[$attr['source']]['name']}.csv";
        export_file($fileName, $exportTitle, $exportData);

    ERR_RET:
        $httpReferer = I('server.HTTP_REFERER');
        $url = parse_url($httpReferer);
        $param = array('errMsg' => "导出失败：{$errMsg}");
        foreach (explode('&amp;', $url['query']) as $query) {
            list($p, $v) = explode('=', $query);
            $param[$p] = $v;
        }
        redirect($url['scheme'].'://'.$url['host'].':'.$url['port'].$url['path'].'?'.http_build_query($param));
    }

    /************************************ 数据汇总 ************************************/

    /**
     * 数据汇总页面
     * @author liyao
     */
    public function staticsCount() {
        $viewAssign = array();

        $vldSer = new ValidatorService();
        $statMod = new StatUserDailyModel();
        $gameMod = new GameModel();

        // 校验输入
        $attr = I('get.', '', 'trim');
        $rules = array(
            array('select_month', 1, array(
                array('date', null, '日期格式有误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
        }

        // 获取有效游戏列表，仅获取有效的
        $where = array('game_status' => $gameMod::GAME_STATUS_ON);
        $field = 'game_id';
        $modRet = $gameMod->queryGameAllList($where, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
            $this->assign($viewAssign);
            $this->display();
            return;
        }
        $gameList = array_column($modRet['data'], 'game_id');
        $viewAssign['gameCount'] = count($gameList);

        if (empty($attr['select_month'])) {
            $attr['start_date'] = date("Y-m-01");
            $attr['end_date'] = date("Y-m-t");
            $attr['select_month'] = date("Y-m");
        } else {
            $attr['start_date'] = $attr['select_month']."-01";
            $tm = strtotime($attr['start_date']);
            $attr['end_date'] = date("Y-m-t", $tm);
        }
        $viewAssign['query'] = $attr;
        $param = array(
            'gameIds' => $gameList,
            'sDataTime' => strtotime($attr["start_date"]),
            'eDataTime' => strtotime($attr["end_date"]),
        );
        // 数据列表
        $field = 'data_time,sum(add_user) as add_user,sum(login_user) as login_user,sum(active_user) as active_user';
        $modRet = $statMod->queryStatDailyGroupByAttr($param, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['list'] = $modRet['data']['list'];
            $count = $modRet['data']['count'];
            $dayCount = count($viewAssign['list']);
            $viewAssign['total_register'] = intval($count["sum_add_user"]);
            $viewAssign['total_login'] = intval($count["sum_login_user"]);
            $viewAssign['total_active'] = intval($count["sum_active_user"]);
            $viewAssign['avg_register'] = intval($count["sum_add_user"]/$dayCount);
            $viewAssign['avg_login'] = intval($count["sum_login_user"]/$dayCount);
            $viewAssign['avg_active'] = intval($count["sum_active_user"]/$dayCount);
            $viewAssign['pagination'] = $modRet['data']['pagination'];
        }
        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 数据汇总 - 报表导出
     * @author liyao
     */
    public function iframeStaticsCountDownFile()
    {
        $vldSer = new ValidatorService();
        $statMod = new StatUserDailyModel();
        $gameMod = new GameModel();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('select_month', 1, array(
                array('date', null, '日期格式有误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $errMsg = $vRet;
            goto ERR_RET;
        }

        // 获取有效游戏列表，仅获取有效的
        $where = array('game_status' => $gameMod::GAME_STATUS_ON);
        $field = 'game_id';
        $modRet = $gameMod->queryGameAllList($where, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $errMsg = $modRet['msg'];
            goto ERR_RET;
        }
        $gameList = array_column($modRet['data'], 'game_id');
        $gameCount = count($gameList);

        if (empty($attr['select_month'])) {
            $attr['start_date'] = date("Y-m-01");
            $attr['end_date'] = date("Y-m-t");
            $attr['select_month'] = date("Y-m");
        } else {
            $attr['start_date'] = $attr['select_month']."-01";
            $tm = strtotime($attr['start_date']);
            $attr['end_date'] = date("Y-m-t", $tm);
        }
        $param = array(
            'gameIds' => $gameList,
            'sDataTime' => strtotime($attr["start_date"]),
            'eDataTime' => strtotime($attr["end_date"]),
        );
        // 数据列表
        $field = 'data_time,sum(add_user) as add_user,sum(login_user) as login_user,sum(active_user) as active_user';
        $modRet = $statMod->queryStatDailyGroupByAttr($param, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $errMsg = $modRet['msg'];
            goto ERR_RET;
        }
        $list = $modRet['data']['list'];

        $exportTitle = array(
            "data_time" => "日期",
            "avg_add_user" => "平均-新增注册",
            "avg_login_user" => "平均-登录人数",
            "avg_active_user" => "平均-活跃人数",
            "add_user" => "新增注册",
            "login_user" => "登录人数",
            "active_user" => "活跃人数",
        );

        $exportData = array();
        foreach ($list as $v) {
            $v["data_time"] = date("Y-m-d", $v["data_time"]);
            $v["avg_add_user"] = intval($v["add_user"]/$gameCount);
            $v["avg_login_user"] = intval($v["login_user"]/$gameCount);
            $v["avg_active_user"] = intval($v["active_user"]/$gameCount);
            $exportData[] = $v;
        }

        $fileName = "数据汇总_{$gameId}_{$attr["start_date"]}-{$attr["end_date"]}.csv";
        export_file($fileName, $exportTitle, $exportData);

    ERR_RET:
        $httpReferer = I('server.HTTP_REFERER');
        $url = parse_url($httpReferer);
        $param = array('errMsg' => "导出失败：{$errMsg}");
        foreach (explode('&amp;', $url['query']) as $query) {
            list($p, $v) = explode('=', $query);
            $param[$p] = $v;
        }
        redirect($url['scheme'].'://'.$url['host'].':'.$url['port'].$url['path'].'?'.http_build_query($param));
    }

    /************************************ 用户数据 ************************************/

    public function user()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "数据统计 | 用户数据";

        $third = I('get.third');
        if (empty($third)) {
            $third = 'daily';
        }
        switch ($third) {
            // 每日简报
            case 'daily':
                $this->_userDaily($viewAssign);
                $displayPage = "userDaily";
                break;

            // 累计数据
            case 'total':
                $this->_userTotal($viewAssign);
                $displayPage = "userTotal";
                break;

            // 用户留存
            case 'remain':
                $this->_userRemain($viewAssign);
                $displayPage = "userRemain";
                break;

            // 行为统计
            case 'behave':
                $this->_userBehave($viewAssign);
                $displayPage = "userBehave";
                break;

            // 注册来源
            case 'register':
                $this->_userRegister($viewAssign);
                $displayPage = "userRegister";
                break;

            // 用户排行
            case 'rank':
                $this->_userRank($viewAssign);
                $displayPage = "userRank";
                break;

            // 渠道统计
            case 'channel':
                $this->_userChannel($viewAssign);
                $displayPage = "userChannel";
                break;

            default:
                // 未知三级目录
                redirect('/Auth/logout');
        }

        $this->assign($viewAssign);
        $this->display($displayPage);
    }

    /**
     * 用户数据 - 每日简报
     * @author Carter
     */
    private function _userDaily(&$viewAssign)
    {
        $vldSer = new ValidatorService();
        $cacheMod = new SysCacheModel();
        $statMod = new StatUserDailyModel();

        // 校验输入
        $attr = I('get.', '', 'trim');
        $rules = array(
            array('select_month', 1, array(
                array('date', null, '日期格式有误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
        }

        $attr['game_id'] = $gameId = C('G_USER.gameid');

        // 最新统计时间
        $modRet = $cacheMod->querySysCacheByKey($gameId, 'DBCACHE_STAT_USER_DAILY_CUTOFFTIME');
        if (ERRCODE_SUCCESS !== $modRet['code'] || empty($modRet['data'])) {
            $viewAssign['cutTime'] = '-';
        } else {
            $viewAssign['cutTime'] = date('Y-m-d H:i', $modRet['data']['cache_sting']);
        }

        if (empty($attr['select_month'])) {
            $attr['start_date'] = date("Y-m-01");
            $attr['end_date'] = date("Y-m-t");
            $attr['select_month'] = date("Y-m");
        } else {
            $attr['start_date'] = $attr['select_month']."-01";
            $tm = strtotime($attr['start_date']);
            $attr['end_date'] = date("Y-m-t", $tm);
        }
        $viewAssign['query'] = $attr;
        $param = array(
            'gameId' => $gameId,
            'sDataTime' => strtotime($attr["start_date"]),
            'eDataTime' => strtotime($attr["end_date"]),
        );
        // 数据列表
        $field = 'id,data_time,add_user,login_user,active_user,consume_prop';
        $modRet = $statMod->queryStatDailyListByAttr($param, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['list'] = $modRet['data']['list'];
            $count = $modRet['data']['count'];
            $viewAssign['total_register'] = intval($count["sum_add_user"]);
            $viewAssign['total_login'] = intval($count["sum_login_user"]);
            $viewAssign['total_active'] = intval($count["sum_active_user"]);
            $viewAssign['total_consume'] = intval($count['sum_consume_prop']);
            $viewAssign['pagination'] = $modRet['data']['pagination'];
        }
    }

    /**
     * 每日简报 - 报表导出
     * @author Carter
     */
    public function iframeUserDailyDownFile()
    {
        $statMod = new StatUserDailyModel();
        $vldSer = new ValidatorService();

        //验证
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('select_month', 1, array(
                array('date', null, '日期格式有误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $errMsg = $vRet;
            goto ERR_RET;
        }
        if (empty($attr['select_month'])) {
            $attr['start_date'] = date("Y-m-01");
            $attr['end_date'] = date("Y-m-t");
        } else {
            $attr['start_date'] = $attr['select_month']."-01";
            $tm = strtotime($attr['start_date']);
            $attr['end_date'] = date("Y-m-t", $tm);
        }

        $gameId = C('G_USER.gameid');

        $param = array(
            'gameId' => $gameId,
            'sDataTime' => strtotime($attr["start_date"]),
            'eDataTime' => strtotime($attr["end_date"]),
        );
        $field = 'data_time,add_user,login_user,active_user,consume_prop';
        $modRet = $statMod->queryStatDailyListByAttr($param, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $errMsg = $modRet['msg'];
            goto ERR_RET;
        }
        $list = $modRet['data']['list'];

        $exportTitle = array(
            "data_time" => "日期",
            "add_user" => "新增注册",
            "login_user" => "登录人数",
            "active_user" => "活跃人数",
            "consume_prop" => "消耗钻石",
        );

        $exportData = array();
        foreach ($list as $v) {
            $v["data_time"] = date("Y-m-d", $v["data_time"]);
            $exportData[] = $v;
        }

        $fileName = "每日简报_{$gameId}_{$attr["start_date"]}-{$attr["end_date"]}.csv";
        export_file($fileName, $exportTitle, $exportData);

    ERR_RET:
        $httpReferer = I('server.HTTP_REFERER');
        $url = parse_url($httpReferer);
        $param = array('errMsg' => "导出失败：{$errMsg}");
        foreach (explode('&amp;', $url['query']) as $query) {
            list($p, $v) = explode('=', $query);
            $param[$p] = $v;
        }
        redirect($url['scheme'].'://'.$url['host'].':'.$url['port'].$url['path'].'?'.http_build_query($param));
    }

    /**
     * 每日简报 - 获取地区数据
     * @author Carter
     */
    public function iframeGetRegionInfo($pid, $type)
    {
        $viewAssign = array();

        $statLgc = new StatLogic();

        $lgcRet = $statLgc->getStatUserDailyRegionPie($pid, $type);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $viewAssign['errMsg'] = $lgcRet['msg'];
        } else {
            $viewAssign['pieTitle'] = $lgcRet['data']['title'];
            $viewAssign['regionData'] = $lgcRet['data']['region'];
        }

        layout(false);
        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 用户数据 - 累计数据
     * @author Carter
     */
    private function _userTotal(&$viewAssign)
    {
        $statMod = new StatUserTotalModel();

        $where = array();
        $pageParam = array();
        $where["game_id"] = C('G_USER.gameid');
        $stime = strtotime(I('request.stime'));
        $etime = strtotime(I('request.etime'));
        if (!empty($stime)) {
            $where["data_time"][] = array('egt', $stime);
            $pageParam["stime"] = urlencode(I('request.stime'));
        }
        if (!empty($etime)) {
            $where["data_time"][] = array('elt', $etime);
            $pageParam["etime"] = urlencode(I('request.etime'));
        }

        $modRet = $statMod->queryStatUserTotalData($where, $pageParam);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign["pageshow"] = $modRet["data"]["pagination"];
            $viewAssign["list"] = $modRet["data"]["list"];
        }

        if (isset($_REQUEST['export'])) {
            $modRet = $statMod->queryStatUserTotalAllData($where);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $viewAssign['errMsg'] = $modRet['msg'];
            } else {
                $exportData = array();
                foreach ($modRet['data']['list'] as $v) {
                    $v["data_time"] = date("Y-m-d", $v["data_time"]);
                    $exportData[] = $v;
                }
                export_file("累计数据.csv", array("data_time" => "日期",
                    "register_num" => "累计注册", "consume_prop" => "累计消耗钻石/房卡"), $exportData);
            }
        }

        // 图形数据
        $viewAssign["titleLink"] = array("register_num" => "累计注册", "consume_prop" => "累计消耗钻石/房卡");
        $modRet = $statMod->queryStatUserTotalChartData($where, $viewAssign["titleLink"], I('request.stime'), I('request.etime'));
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign["chartData"] = $modRet['data']['list'];
        }

        // 自通过权限校验至今的时间，可视为程序执行时间，传给页面
        $viewAssign['exceTime'] = G('begin', 'end', 2);
        $viewAssign["stime"] = I('request.stime');
        $viewAssign["etime"] = I('request.etime');
    }

    /**
     * 用户数据 - 用户留存
     * @author Carter
     */
    private function _userRemain(&$viewAssign)
    {
        $statMod = new StatUserRemainModel();

        $where = array();
        $pageParam = array();
        $where["game_id"] = C('G_USER.gameid');
        $stime = strtotime(I('request.stime'));
        $etime = strtotime(I('request.etime'));
        if (!empty($stime)) {
            $where["data_time"][] = array('egt', $stime);
            $pageParam["stime"] = urlencode(I('request.stime'));
        }
        if (!empty($etime)) {
            $where["data_time"][] = array('elt', $etime);
            $pageParam["etime"] = urlencode(I('request.etime'));
        }

        $modRet = $statMod->queryStatUserRemainData($where, $pageParam);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign["pageshow"] = $modRet["data"]["pagination"];
            $viewAssign["list"] = $this->_delRemainData($modRet['data']['list']);
        }

        if (isset($_REQUEST['export'])) {
            $modRet = $statMod->queryStatUserRemainAllData($where);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $viewAssign['errMsg'] = $modRet['msg'];
            } else {
                $showData = $this->_delRemainData($modRet['data']['list']);
                $exportData = array();
                foreach ($showData as $v) {
                    $v["data_time"] = date("Y-m-d", $v["data_time"]);
                    $exportData[] = $v;
                }
                export_file("用户留存.csv", array(
                    "data_time" => "日期",
                    "add_register" => "新增注册",
                    "keep_day1" => "次日留存",
                    "keep_day3" => "3日留存",
                    "keep_day7" => "7日留存",
                    "keep_day15" => "15日留存",
                    "keep_day30" => "30日留存",
                ), $exportData);
            }
        }

        // 图形数据
        $viewAssign["titleLink"] = array(
            "add_register" => "新增注册",
            "keep_day1" => "次日留存",
            "keep_day3" => "3日留存",
            "keep_day7" => "7日留存",
            "keep_day15" => "15日留存",
            "keep_day30" => "30日留存",
        );
        $modRet = $statMod->queryStatUserRemainChartData($where, $viewAssign["titleLink"], I('request.stime'), I('request.etime'));
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign["chartData"] = $modRet['data']['list'];
        }

        // 自通过权限校验至今的时间，可视为程序执行时间，传给页面
        $viewAssign['exceTime'] = G('begin', 'end', 2);
        $viewAssign["stime"] = I('request.stime');
        $viewAssign["etime"] = I('request.etime');
    }

    /**
     * 返回显示的留存数据
     */
    private function _delRemainData($list)
    {
        $showData = array();
        $tmnow = time();
        foreach ($list as $v) {
            $b = ($tmnow - $v["data_time"] - 2 * 24 * 3600 > 0) ? true : false;
            $v["keep_day1"] = ($v["keep_day1"] == 0 ? ($b ? "0%" : "") : round($v["keep_day1"], 2).'%');
            $b = ($tmnow - $v["data_time"] - 4 * 24 * 3600 > 0) ? true : false;
            $v["keep_day3"] = ($v["keep_day3"] == 0 ? ($b ? "0%" : "") : round($v["keep_day3"], 2).'%');
            $b = ($tmnow - $v["data_time"] - 8 * 24 * 3600 > 0) ? true : false;
            $v["keep_day7"] = ($v["keep_day7"] == 0 ? ($b ? "0%" : "") : round($v["keep_day7"], 2).'%');
            $b = ($tmnow - $v["data_time"] - 16 * 24 * 3600 > 0) ? true : false;
            $v["keep_day15"] = ($v["keep_day15"] == 0 ? ($b ? "0%" : "") : round($v["keep_day15"], 2).'%');
            $b = ($tmnow - $v["data_time"] - 31 * 24 * 3600 > 0) ? true : false;
            $v["keep_day30"] = ($v["keep_day30"] == 0 ? ($b ? "0%" : "") : round($v["keep_day30"], 2).'%');
            $showData[] = $v;
        }
        return $showData;
    }

    /**
     * 用户数据 - 行为统计
     * @author Carter
     */
    private function _userBehave(&$viewAssign)
    {
        $statMod = new StatUserBehaveModel();

        $where = array();
        $pageParam = array();
        $where["game_id"] = C('G_USER.gameid');
        $stime = strtotime(I('request.stime'));
        $etime = strtotime(I('request.etime'));
        if (!empty($stime)) {
            $where["data_time"][] = array('egt', $stime);
            $pageParam["stime"] = urlencode(I('request.stime'));
        }
        if (!empty($etime)) {
            $where["data_time"][] = array('elt', $etime);
            $pageParam["etime"] = urlencode(I('request.etime'));
        }

        $modRet = $statMod->queryStatUserBehaveData($where, $pageParam);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign["pageshow"] = $modRet["data"]["pagination"];
            $list = $modRet["data"]["list"];
        }
        if (isset($_REQUEST['export'])) {
            $modRet = $statMod->queryStatUserBehaveAllData($where);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $viewAssign['errMsg'] = $modRet['msg'];
            } else {
                $list = $modRet["data"]["list"];
            }
        }
        $whTime = array();
        foreach ($list as $v) {
            $whTime[] = $v['data_time'];
        }
        if (count($whTime) > 0) {
            $wh = array("game_id" => C('G_USER.gameid'));
            $wh['data_time'] = array('in', $whTime);
            $mod = new StatLandpageModel();
            $modRet = $mod->queryLandpageStaticsData($wh);
            if ($modRet) {
                if (ERRCODE_SUCCESS !== $modRet['code']) {
                    $viewAssign['errMsg'] = $modRet['msg'];
                } else {
                    $landData = $modRet["data"]["list"];
                    foreach ($list as $k=>$v) {
                        $list[$k]['load_nums'] = $list[$k]['down_nums'] = 0;
                        foreach ($landData as $kk=>$vv) {
                            if ($v['data_time'] == $vv['data_time']) {
                                $loadNum = $vv['ios_load'] + $vv['android_load'] - $vv['tencent_load'];
                                if ($loadNum < 0)
                                    $loadNum = 0;
                                $list[$k]['load_nums'] = $loadNum;
                                $list[$k]['down_nums'] = $vv['android_click'];
                                break;
                            }
                        }
                    }
                }
            }
        }
	$viewAssign["list"] = $list;
        if (isset($_REQUEST['export'])) {
            $exportData = array();
            foreach ($list as $v) {
                $v["data_time"] = date("Y-m-d", $v["data_time"]);
                $exportData[] = $v;
            }
            export_file("行为统计.csv", array("data_time" => "日期",
                "login_user" => "登录人数", "active_user" => "参与牌局人数",
                "share_games" => "分享游戏次数", "load_nums" => "分享链接点击次数",
                "down_nums" => "落地页点击次数", "share_rooms" => "分享房间次数",
                "invite_ids" => "填写邀请人ID次数", "invite_friends" => "邀请好友次数"), $exportData);
        }

        // 图形数据
        $viewAssign["titleLink"] = array("login_user" => "登录人数", "active_user" => "参与牌局人数",
                "share_games" => "分享游戏次数", "load_nums" => "分享链接点击次数",
                "down_nums" => "落地页点击次数", "share_rooms" => "分享房间次数",
                "invite_ids" => "填写邀请人ID次数", "invite_friends" => "邀请好友次数");
        $modRet = $statMod->queryStatUserBehaveChartData($where, $viewAssign["titleLink"], I('request.stime'), I('request.etime'));
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $list = $modRet["data"]["list"];
            $whTime = array();
            foreach ($list[0]["xAxis"] as $v) {
                $whTime[] = strtotime($v);
            }
            if (count($whTime) > 0) {
                $wh = array("game_id" => C('G_USER.gameid'));
                $wh['data_time'] = array('in', $whTime);
                $mod = new StatLandpageModel();
                $modRet = $mod->queryLandpageStaticsData($wh);
                if ($modRet) {
                    if (ERRCODE_SUCCESS !== $modRet['code']) {
                        $viewAssign['errMsg'] = $modRet['msg'];
                    } else {
                        $landData = $modRet["data"]["list"];
                        foreach ($list as $k => $v) {
                            if ($v["key"] == "load_nums") {
                                $idxLoad = $k;
                            }
                            if ($v["key"] == "down_nums") {
                                $idxDown = $k;
                            }
                        }
                        foreach ($list[0]["xAxis"] as $v) {
                            $loadNum = $downNum = 0;
                            foreach ($landData as $kk=>$vv) {
                                if (strtotime($v) == $vv['data_time']) {
                                    $loadNum = $vv['ios_load'] + $vv['android_load'] - $vv['tencent_load'];
                                    if ($loadNum < 0)
                                        $loadNum = 0;
                                    $downNum = $vv['android_click'];
                                    break;
                                }
                            }
                            $list[$idxLoad]['data'][] = $loadNum;
                            $list[$idxDown]['data'][] = $downNum;
                        }
                    }
                }
            }
            $viewAssign["chartData"] = $list;
        }

        // 自通过权限校验至今的时间，可视为程序执行时间，传给页面
        $viewAssign['exceTime'] = G('begin', 'end', 2);
        $viewAssign["stime"] = I('request.stime');
        $viewAssign["etime"] = I('request.etime');
    }

    /**
     * 用户数据 - 注册来源
     * @author Carter
     */
    private function _userRegister(&$viewAssign)
    {
        $statMod = new StatUserRegisterModel();

        $where = array();
        $pageParam = array();
        $where["game_id"] = C('G_USER.gameid');
        $stime = strtotime(I('request.stime'));
        $etime = strtotime(I('request.etime'));
        if (!empty($stime)) {
            $where["data_time"][] = array('egt', $stime);
            $pageParam["stime"] = urlencode(I('request.stime'));
        }
        if (!empty($etime)) {
            $where["data_time"][] = array('elt', $etime);
            $pageParam["etime"] = urlencode(I('request.etime'));
        }

        $modRet = $statMod->queryStatUserRegisterData($where, $pageParam);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign["pageshow"] = $modRet["data"]["pagination"];
            $viewAssign["list"] = $modRet["data"]["list"];
        }

        if (isset($_REQUEST['export'])) {
            $modRet = $statMod->queryStatUserRegisterAllData($where);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $viewAssign['errMsg'] = $modRet['msg'];
            } else {
                $exportData = array();
                foreach ($modRet['data']['list'] as $v) {
                    $v["data_time"] = date("Y-m-d", $v["data_time"]);
                    $exportData[] = $v;
                }
                export_file("注册来源.csv", array("data_time" => "日期",
                    "appstore" => "App Store", "app_store" => "落地页（安卓）"), $exportData);
            }
        }

        // 图形数据
        $viewAssign["titleLink"] = array("appstore" => "App Store", "app_store" => "落地页（安卓）");
        $modRet = $statMod->queryStatUserRegisterChartData($where, $viewAssign["titleLink"], I('request.stime'), I('request.etime'));
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign["chartData"] = $modRet['data']['list'];
        }

        // 自通过权限校验至今的时间，可视为程序执行时间，传给页面
        $viewAssign['exceTime'] = G('begin', 'end', 2);
        $viewAssign["stime"] = I('request.stime');
        $viewAssign["etime"] = I('request.etime');
    }

    /**
     * 用户数据 - 用户排行
     * @author Carter
     */
    private function _userRank(&$viewAssign)
    {
        $statMod = new StatUserRankModel();

        $sDate = I('request.stime');
        $eDate = I('request.etime');
        if (empty($sDate) && empty($eDate)) {
            $tm = strtotime("-1 day");
            $sDate = $eDate = date("Y-m-d", $tm);
        }
        $where = array();
        $pageParam = array();
        $where["game_id"] = C('G_USER.gameid');
        $stime = strtotime($sDate);
        $etime = strtotime($eDate);
        if (!empty($stime)) {
            $where["data_time"][] = array('egt', $stime);
            $pageParam["stime"] = urlencode($sDate);
        }
        if (!empty($etime)) {
            $where["data_time"][] = array('elt', $etime);
            $pageParam["etime"] = urlencode($eDate);
        }

        // day
        if (!empty($sDate) && !empty($eDate)) {
            $modRet = $statMod->queryStatUserRankInterval($where, $sDate, $eDate);
            if ($modRet) {
                if (ERRCODE_SUCCESS !== $modRet['code']) {
                    $viewAssign['errMsg'] = $modRet['msg'];
                } else {
                    $viewAssign["rankDayList"] = $modRet["data"]["list"];
                }
            }
        }

        if (isset($_REQUEST['export'])) {
            $exportData = array();
            $idx = 1;
            foreach ($viewAssign["rankDayList"]["data"] as $v) {
                $v["no"] = $idx++;
                $v["prop_user_name"] = CsvVal($v["prop_user_name"]);
                $v["win_user_name"] = CsvVal($v["win_user_name"]);
                $v["record_user_name"] = CsvVal($v["record_user_name"]);
                $v["record4_user_name"] = CsvVal($v["record4_user_name"]);
                $exportData[] = $v;
            }
            export_file("用户排行.csv", array("no" => "排行",
                "prop_user_id" => "消耗钻石用户id", "prop_user_name" => "昵称", "prop_nums" => "颗数",
                "win_user_id" => "大赢家用户id", "win_user_name" => "昵称", "win_nums" => "次数",
                "record_user_id" => "参加牌局用户id", "record_user_name" => "昵称", "record_nums" => "局数",
                "record4_user_id" => "4人牌局用户id", "record4_user_name" => "昵称", "record4_nums" => "局数"), $exportData);
        }

        // 自通过权限校验至今的时间，可视为程序执行时间，传给页面
        $viewAssign['exceTime'] = G('begin', 'end', 2);
        $viewAssign["stime"] = $sDate;
        $viewAssign["etime"] = $eDate;
    }

    /**
     * 用户数据 - 渠道统计
     * @author Carter
     */
    private function _userChannel(&$viewAssign)
    {
        $statMod = new StatUserChannelModel();
        $chnMod = new GameChannelModel();

        $where = array();
        $pageParam = array();
        $where["game_id"] = C('G_USER.gameid');
        $stime = strtotime(I('request.stime'));
        $etime = strtotime(I('request.etime'));
        if (!empty($stime)) {
            $where["data_time"][] = array('egt', $stime);
            $pageParam["stime"] = urlencode(I('request.stime'));
        }
        if (!empty($etime)) {
            $where["data_time"][] = array('elt', $etime);
            $pageParam["etime"] = urlencode(I('request.etime'));
        }
        if (!empty(I('request.os'))) {
            $where["os"] = I('request.os');
            $pageParam["os"] = urlencode(I('request.os'));
        }
        if (!empty(I('request.type'))) {
            $where["type"] = I('request.type');
            $pageParam["type"] = urlencode(I('request.type'));
        } else {
            $where["type"] = 1;
            $pageParam["type"] = 1;
        }

        $modRet = $statMod->queryStatUserChannelData($where, $pageParam);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign["pageshow"] = $modRet["data"]["pagination"];
            $viewAssign["list"] = $modRet["data"]["list"];
        }

        $viewAssign['areaMap'] = array();
        $modRet = $chnMod->getChannelCode($where);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $list = $modRet["data"]["list"];
            foreach ($list as $v) {
                $viewAssign['areaMap'][$v['code']] = $v['name'];
            }
        }
        $modRet = $chnMod->getOsType();
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $viewAssign['errMsg'] = $modRet['msg'];
            return;
        }
        $osType = $modRet['data']['list'];
        $osMap = array();
        foreach ($osType as $v) {
            switch ($v['os']) {
                case '1':
                    $osMap['1'] = 'Android';
                    break;
                case '2':
                    $osMap['2'] = 'IOS';
                    break;
                case '3':
                    $osMap['3'] = 'IOS企业签';
                    break;
            }
        }
        $viewAssign['osMap'] = $osMap;

        if (isset($_REQUEST['export'])) {
            $modRet = $statMod->queryStatUserChannelAllData($where);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $viewAssign['errMsg'] = $modRet['msg'];
            } else {
                $arrTitle = array("data_time" => "日期");
                foreach ($viewAssign['areaMap'] as $k => $v) {
                    $arrTitle[$k] = $v;
                }
                $exportData = array();
                foreach ($modRet['data']['list'] as $v) {
                    $v["data_time"] = date("Y-m-d", $v["data_time"]);
                    foreach ($v["channel"] as $vv) {
                        $v[$vv["code"]] = $vv["value"];
                    }
                    $exportData[] = $v;
                }
                export_file("渠道统计.csv", $arrTitle, $exportData);
            }
        }

        // 图形数据
        $viewAssign["titleLink"] = $viewAssign['areaMap'];
        $modRet = $statMod->queryStatUserChannelChartData($where, $viewAssign['areaMap'], I('request.stime'), I('request.etime'));
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign["chartData"] = $modRet['data']['list'];
        }

        // 自通过权限校验至今的时间，可视为程序执行时间，传给页面
        $viewAssign['exceTime'] = G('begin', 'end', 2);
        $viewAssign["stime"] = I('request.stime');
        $viewAssign["etime"] = I('request.etime');
        if (!empty(I('request.os'))) {
            $viewAssign["os"] = I('request.os');
        }
        if (!empty(I('request.type'))) {
            $viewAssign["type"] = I('request.type');
        }
    }

    /**
     * 用户数据 - 分享统计
     * @author Carter
     */
    public function userShare()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "分享统计 | 用户数据";

        $statMod = new StatGameShareModel();
        $vldSer = new ValidatorService();

        //验证
        $attr = I('get.', '', 'trim');
        $rules = array(
            array('start_date', 1, array(
                array('date', null, '开始日期格式有误'),
            )),
            array('end_date', 1, array(
                array('date', null, '结束日期格式有误'),
                array('date_after', empty($attr['start_date']) ? '1970-01-01' : $attr['start_date'], '结束日期不能早于开始日期'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
        }
        $viewAssign['query'] = json_encode($attr);

        if ($attr['errMsg']) {
            $viewAssign['errMsg'] = $attr['errMsg'];
        }

        $attr['game_id'] = C('G_USER.gameid');

        $modRet = $statMod->queryStatGameShareListByAttr($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['list'] = $modRet['data']['list'];
            $viewAssign['pagination'] = $modRet['data']['pagination'];
        }

        // 图形数据
        $modRet = $statMod->queryGameShareChartData($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign["xAxis"] = $modRet['data']['xAxis'];
            $viewAssign["chartMap"] = $modRet['data']['chartMap'];
        }

        $this->assign($viewAssign);
        $this->display();
    }

    /************************************ 游戏数据 ************************************/

    /**
     * 游戏数据
     * @author Carter
     */
    public function game()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "数据统计 | 游戏数据";

        $third = I('get.third');
        if (empty($third)) {
            $third = 'round';
        }
        switch ($third) {
            // 钻石消耗
            case 'consume':
                //选择游戏，选择数据库
                $gameId = C('G_USER.gameid');

                $params = array(
                    'gameId' => $gameId
                );

                //时间区间选择，赋值给页面
                $startTime = strtotime( I('start') );
                $endTime = strtotime(  I('end') );
                if ($startTime) {
                    $this->assign('start',date('Y-m-d',$startTime) );
                }
                if ($endTime) {
                    $this->assign('end',date('Y-m-d',$endTime) );
                }

                //数据处理逻辑
                $statLogic = new StatLogic();
                $where = array("game_id" => $gameId);
                if (!empty($startTime)) {
                    $where["data_time"][] = array('egt', $startTime);
                    $viewAssign["start"] = I('start');
                }
                if (!empty($endTime)) {
                    $where["data_time"][] = array('elt', $endTime);
                    $viewAssign["end"] = I('end');
                }

                $data   = $statLogic->getOneGameDiamondsConsumeStatList($where); //根据条件查询获取游戏的钻石消耗。

                $this->assign('list',$data);
                $this->assign('pageshow',$data['pages']);// 赋值分页输出

                $displayPage = "gameConsume";

                $mod = new \Home\Model\StatGameitemConsumeModel();
                if (isset($_REQUEST['export'])) {
                    $modRet = $mod->getGameitemConsumeAllData($where);
                    if (ERRCODE_SUCCESS !== $modRet['code']) {
                        $viewAssign['errMsg'] = $modRet['msg'];
                    } else {
                        $exportData = array();
                        foreach ($modRet['data']['list'] as $v) {
                            $v["data_time"] = date("Y-m-d", $v["data_time"]);
                            $exportData[] = $v;
                        }
                        export_file("钻石消耗.csv", array("data_time" => "日期",
                            "diamond_count" => "总消耗", "club_diamond_count" => "俱乐部总消耗",
                            "club_four_count" => "俱乐部4人局消耗", "club_three_count" => "俱乐部3人局消耗",
                            "club_two_count" => "俱乐部2人局消耗", "four_diamond" => "4人局消耗",
                            "three_diamond" => "3人局消耗", "two_diamond" => "2人局消耗",
                            "manage_minus" => "后台扣除玩家钻石", "agent_diamond" => "后台扣除代理商钻石"), $exportData);
                    }
                }
                // 图形数据
                $viewAssign["titleLink"] = array("diamond_count" => "总消耗", "club_diamond_count" => "俱乐部总消耗",
                            "club_four_count" => "俱乐部4人局消耗", "club_three_count" => "俱乐部3人局消耗",
                            "club_two_count" => "俱乐部2人局消耗", "four_diamond" => "4人局消耗",
                            "three_diamond" => "3人局消耗", "two_diamond" => "2人局消耗",
                            "manage_minus" => "后台扣除玩家钻石", "agent_diamond" => "后台扣除代理商钻石");
                $modRet = $mod->queryGameitemConsumeChartData($where, $viewAssign['titleLink'], I('request.start'), I('request.end'));
                if (ERRCODE_SUCCESS !== $modRet['code']) {
                    $viewAssign['errMsg'] = $modRet['msg'];
                } else {
                    $viewAssign["chartData"] = $modRet['data']['list'];
                }
                break;

            default:
                // 未知三级目录
                redirect('/Auth/logout');
        }

        $this->assign($viewAssign);
        $this->display($displayPage);
    }

    /**
     * 对局统计
     * @author Carter
     */
    public function gameRound()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "数据统计 | 游戏数据";

        $statMod = new StatGameitemModel();
        $vldSer = new ValidatorService();

        //验证
        $attr = I('get.', '', 'trim');
        $rules = array(
            array('start_date', 1, array(
                array('date', null, '开始日期格式有误'),
            )),
            array('end_date', 1, array(
                array('date', null, '结束日期格式有误'),
                array('date_after', empty($attr['start_date']) ? '1970-01-01' : $attr['start_date'], '结束日期不能早于开始日期'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
        }
        $viewAssign['query'] = json_encode($attr);

        if ($attr['errMsg']) {
            $viewAssign['errMsg'] = $attr['errMsg'];
        }

        $attr['game_id'] = C('G_USER.gameid');

        $modRet = $statMod->queryStatGameItemListByAttr($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['list'] = $modRet['data']['list'];
            $viewAssign['pagination'] = $modRet['data']['pagination'];
        }

        // 图形数据
        $viewAssign["titleLink"] = $titleLink = array(
            "create_count" => "开局次数",
            "create_access_count" => "成功开局次数",
            "total_average_time" => "平均每场时长",
            "item_average_time" => "平均每局时长",
            "win_average_integral" => "大赢家平均胜分",
            "average_integral" => "每局平均胜分",
        );
        $modRet = $statMod->queryGameRoundChartData($attr, $titleLink);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign["chartData"] = $modRet['data'];
        }

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 对局统计 - 报表导出
     * @author Carter
     */
    public function iframeGameRoundDownFile()
    {
        $statMod = new StatGameitemModel();
        $vldSer = new ValidatorService();

        //验证
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('start_date', 0, array(
                array('require', null, '未选择开始日期'),
                array('date', null, '开始日期格式有误'),
            )),
            array('end_date', 0, array(
                array('require', null, '未选择结束日期'),
                array('date', null, '结束日期格式有误'),
                array('date_after', empty($attr['start_date']) ? '1970-01-01' : $attr['start_date'], '结束日期不能早于开始日期'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $errMsg = $vRet;
            goto ERR_RET;
        }

        $param = array(
            'game_id' => C('G_USER.gameid'),
            'start_time' => strtotime($attr["start_date"]),
            'end_time' => strtotime($attr["end_date"]),
        );
        $modRet = $statMod->queryStatGameItemAllList($param);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $errMsg = $modRet['msg'];
            goto ERR_RET;
        }
        $list = $modRet['data'];

        $exportTitle = array(
            "data_time" => "日期",
            "create_count" => "开局次数",
            "create_access_count" => "成功开局次数",
            "total_average_time" => "平均每场时长",
            "item_average_time" => "平均每局时长",
            "win_average_integral" => "大赢家平均胜分",
            "average_integral" => "每局平均胜分",
        );

        $exportData = array();
        foreach ($list as $v) {
            $v["data_time"] = date("Y-m-d", $v["data_time"]);
            $v["total_average_time"] = format_second_time($v["total_average_time"]);
            $v["item_average_time"] = format_second_time($v["item_average_time"]);
            $v["win_average_integral"] = format_milli($v["win_average_integral"]);
            $v["average_integral"] = format_milli($v["average_integral"]);
            $exportData[] = $v;
        }

        $fileName = "对局统计_{$attr["start_date"]}-{$attr["end_date"]}.csv";
        export_file($fileName, $exportTitle, $exportData);

    ERR_RET:
        $httpReferer = I('server.HTTP_REFERER');
        $url = parse_url($httpReferer);
        $param = array('errMsg' => "导出失败：{$errMsg}");
        foreach (explode('&amp;', $url['query']) as $query) {
            list($p, $v) = explode('=', $query);
            $param[$p] = $v;
        }
        redirect($url['scheme'].'://'.$url['host'].':'.$url['port'].$url['path'].'?'.http_build_query($param));
    }

    /**
     * 玩法统计
     * @author Carter
     */
    public function gameRoom()
    {
        $viewAssign = array();

        $roomMod = new StatGameRoomModel();

        // 页面 title
        $viewAssign['title'] = "数据统计 | 游戏数据";

        // 标签页映射表
        $tabsMap = array(
            'playtype' => array(
                'name' => "按玩法",
                'uri' => "/Stat/gameRoom/tabs/playtype",
                'active' => 0,
            ),
            'number' => array(
                'name' => "按人数",
                'uri' => "/Stat/gameRoom/tabs/number",
                'active' => 0,
            ),
        );

        // 默认为按玩法
        $tab = I('get.tabs') ? : "playtype";
        switch ($tab) {
            // 按玩法
            case "playtype":
                $this->_gameRoomPlaytype($viewAssign);
                $tabsMap['playtype']['active'] = 1;
                $displayPage = "gameRoomPlaytype";
                break;

            // 按人数
            case "number":
                $this->_gameRoomNumber($viewAssign);
                $tabsMap['number']['active'] = 1;
                $displayPage = "gameRoomNumber";
                break;

            default:
                // 未知值，跳转到登录页
                redirect('/Auth/logout');
        }
        $viewAssign['tabsMap'] = $tabsMap;

        $this->assign($viewAssign);
        $this->display($displayPage);
    }

    /**
     * 玩法统计 - 按玩法
     * @author Carter
     */
    private function _gameRoomPlaytype(&$viewAssign)
    {
        $vldSer = new ValidatorService();
        $roomMod = new StatGameRoomModel();

        // 查询参数
        $attr = I('get.', '', 'trim');
        $attr['data_type'] = $attr['data_type'] ? : $roomMod::DATA_TYPE_COUNT;
        $rules = array(
            array('start_date', 1, array(
                array('date', null, '开始日期时间格式有误'),
            )),
            array('end_date', 1, array(
                array('date', null, '结束日期时间格式有误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
        }
        $viewAssign['query'] = $attr;

        $gameId = C('G_USER.gameid');

        // 子玩法数据类型 map
        $viewAssign['dataTypeMap'] = $roomMod->dataTypeMap;

        // 房间类型 map
        $viewAssign['roomTypeMap'] = $roomMod->roomTypeMap;

        // 子玩法 map
        $viewAssign['playMap'] = $playMap = get_game_play_map($gameId);

        // 需展示玩法项
        if (!empty($attr['play_id'])) {
            // 参数输入是字符串，要转为整型，否则很影响效率（tp model in select 时字符串与整型混用会极大降低性能）
            $showPlay = array_intersect(array_flip(array_flip($attr['play_id'])), array_keys($playMap));
        } else {
            $showPlay = array_keys($playMap);
        }
        $viewAssign['showPlay'] = $showPlay;

        // 获取展示数据
        $modRet = $roomMod->queryGameRoomListForPlaytype($gameId, $showPlay, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['list'] = $modRet['data']['list'];
            $viewAssign['pagination'] = $modRet['data']['pagination'];
        }

        // 图形数据
        $titleLink = array(
            "create_count" => "开局次数",
            "create_access_count" => "成功开局次数",
        );
        foreach ($showPlay as $v) {
            $titleLink["play_{$v}"] = $playMap[$v];
        }
        $viewAssign["titleLink"] = $titleLink;

        $modRet = $roomMod->queryGameRoomPlayChartData($gameId, $attr, $showPlay, $titleLink);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign["chartData"] = $modRet['data'];
        }
    }

    /**
     * 玩法统计（按玩法） - 报表导出
     * @author Carter
     */
    public function iframeGameRoomPlayDownFile()
    {
        $roomMod = new StatGameRoomModel();
        $vldSer = new ValidatorService();

        $gameId = C('G_USER.gameid');

        // 子玩法数据类型 map
        $dataTypeMap = $roomMod->dataTypeMap;

        //验证
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('data_type', 0, array(
                array('require', null, '缺失子玩法数据类型'),
                array('in', implode(',', array_keys($dataTypeMap)), '子玩法数据类型有误'),
            )),
            array('start_date', 0, array(
                array('require', null, '未选择开始日期'),
                array('date', null, '开始日期格式有误'),
            )),
            array('end_date', 0, array(
                array('require', null, '未选择结束日期'),
                array('date', null, '结束日期格式有误'),
                array('date_after', empty($attr['start_date']) ? '1970-01-01' : $attr['start_date'], '结束日期不能早于开始日期'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $errMsg = $vRet;
            goto ERR_RET;
        }

        // 子玩法 map
        $playMap = get_game_play_map($gameId);

        $modRet = $roomMod->queryGameRoomPlayExportData($gameId, $playMap, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $errMsg = $modRet['msg'];
            goto ERR_RET;
        }
        $exportData = $modRet['data'];

        $exportTitle = array(
            "data_time" => "日期",
            "create_count" => "开局次数",
            "create_access_count" => "成功开局次数",
        );
        foreach ($playMap as $playId => $playName) {
            $exportTitle["play_{$playId}"] = $playName;
        }

        $fileName = "玩法统计（按玩法）_子玩法为{$dataTypeMap[$attr['data_type']]}_";
        if ($attr['room_type']) {
            $fileName .= $roomMod->roomTypeMap[$attr['room_type']]."_";
        }
        $fileName .= "{$attr["start_date"]}-{$attr["end_date"]}.csv";
        export_file($fileName, $exportTitle, $exportData);

    ERR_RET:
        $httpReferer = I('server.HTTP_REFERER');
        $url = parse_url($httpReferer);
        $param = array('errMsg' => "导出失败：{$errMsg}");
        foreach (explode('&amp;', $url['query']) as $query) {
            list($p, $v) = explode('=', $query);
            $param[$p] = $v;
        }
        redirect($url['scheme'].'://'.$url['host'].':'.$url['port'].$url['path'].'?'.http_build_query($param));
    }

    /**
     * 玩法统计 - 按人数
     * @author Carter
     */
    private function _gameRoomNumber(&$viewAssign)
    {
        $vldSer = new ValidatorService();
        $roomMod = new StatGameRoomModel();

        // 查询参数
        $attr = I('get.', '', 'trim');
        $rules = array(
            array('start_date', 1, array(
                array('date', null, '开始日期时间格式有误'),
            )),
            array('end_date', 1, array(
                array('date', null, '结束日期时间格式有误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
        }
        $viewAssign['query'] = $attr;

        $gameId = C('G_USER.gameid');

        // 房间类型 map
        $viewAssign['roomTypeMap'] = $roomMod->roomTypeMap;

        // 获取展示数据
        $modRet = $roomMod->queryGameRoomListForNumber($gameId, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['list'] = $modRet['data']['list'];
            $viewAssign['pagination'] = $modRet['data']['pagination'];
        }

        // 图形数据
        $titleLink = array(
            "create_count" => "开局次数",
            "create_access_count" => "成功开局次数",
            "four_count" => "4人局次数",
            "three_count" => "3人局次数",
            "two_count" => "2人局次数",
        );
        $viewAssign["titleLink"] = $titleLink;

        $modRet = $roomMod->queryGameRoomNumChartData($gameId, $attr, $titleLink);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign["chartData"] = $modRet['data'];
        }
    }

    /**
     * 玩法统计（按人数） - 报表导出
     * @author Carter
     */
    public function iframeGameRoomNumDownFile()
    {
        $roomMod = new StatGameRoomModel();
        $vldSer = new ValidatorService();

        $gameId = C('G_USER.gameid');

        //验证
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('start_date', 0, array(
                array('require', null, '未选择开始日期'),
                array('date', null, '开始日期格式有误'),
            )),
            array('end_date', 0, array(
                array('require', null, '未选择结束日期'),
                array('date', null, '结束日期格式有误'),
                array('date_after', empty($attr['start_date']) ? '1970-01-01' : $attr['start_date'], '结束日期不能早于开始日期'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $errMsg = $vRet;
            goto ERR_RET;
        }

        $modRet = $roomMod->queryGameRoomNumExportData($gameId, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $errMsg = $modRet['msg'];
            goto ERR_RET;
        }
        $exportData = $modRet['data'];

        $exportTitle = array(
            "data_time" => "日期",
            "create_count" => "开局次数",
            "create_access_count" => "成功开局次数",
            "four_count" => "4人局次数",
            "three_count" => "3人局次数",
            "two_count" => "2人局次数",
        );

        $fileName = "玩法统计（按人数）_";
        if ($attr['room_type']) {
            $fileName .= $roomMod->roomTypeMap[$attr['room_type']]."_";
        }
        $fileName .= "{$attr["start_date"]}-{$attr["end_date"]}.csv";
        export_file($fileName, $exportTitle, $exportData);

    ERR_RET:
        $httpReferer = I('server.HTTP_REFERER');
        $url = parse_url($httpReferer);
        $param = array('errMsg' => "导出失败：{$errMsg}");
        foreach (explode('&amp;', $url['query']) as $query) {
            list($p, $v) = explode('=', $query);
            $param[$p] = $v;
        }
        redirect($url['scheme'].'://'.$url['host'].':'.$url['port'].$url['path'].'?'.http_build_query($param));
    }

    /**
     * 钻石产出
     * @author Carter
     */
    public function gameProduce()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "数据统计 | 游戏数据";

        $statMod = new StatDiamondProduceModel();
        $vldSer = new ValidatorService();

        //验证
        $attr = I('get.', '', 'trim');
        $rules = array(
            array('start_date', 1, array(
                array('date', null, '开始日期格式有误'),
            )),
            array('end_date', 1, array(
                array('date', null, '结束日期格式有误'),
                array('date_after', empty($attr['start_date']) ? '1970-01-01' : $attr['start_date'], '结束日期不能早于开始日期'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
        }
        $viewAssign['query'] = json_encode($attr);

        if ($attr['errMsg']) {
            $viewAssign['errMsg'] = $attr['errMsg'];
        }

        $attr['game_id'] = C('G_USER.gameid');

        $modRet = $statMod->queryStatDiamondProduceListByAttr($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['list'] = $modRet['data']['list'];
            $viewAssign['pagination'] = $modRet['data']['pagination'];
        }

        // 图形数据
        $viewAssign["titleLink"] = array(
            "diamond_amount" => "总产出",
            "gift_agent" => "赠送代理商钻石",
            "gift_exclusive" => "赠送专属钻石",
            "superior_award" => "代理商返钻",
            "agent_purchase" => "代理商购买",
            "mall_purchase" => "游戏内购",
            "share_award" => "每日分享",
            "invite_award" => "邀请好友",
            "admin_deliver" => "赠送玩家钻石",
        );
        $attr["start_time"] = strtotime($attr["start_date"]);
        $attr["end_time"] = strtotime($attr["end_date"]);
        $modRet = $statMod->queryStatDiamondProduceChartData($attr, $viewAssign['titleLink'], I('request.start_date'), I('request.end_date'));
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign["chartData"] = $modRet['data']['list'];
        }

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 钻石产出 - 报表导出
     * @author Carter
     */
    public function iframeGameProduceDownFile()
    {
        $statMod = new StatDiamondProduceModel();
        $vldSer = new ValidatorService();

        //验证
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('start_date', 0, array(
                array('require', null, '未选择开始日期'),
                array('date', null, '开始日期格式有误'),
            )),
            array('end_date', 0, array(
                array('require', null, '未选择结束日期'),
                array('date', null, '结束日期格式有误'),
                array('date_after', empty($attr['start_date']) ? '1970-01-01' : $attr['start_date'], '结束日期不能早于开始日期'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $errMsg = $vRet;
            goto ERR_RET;
        }

        $param = array(
            'game_id' => C('G_USER.gameid'),
            'start_time' => strtotime($attr["start_date"]),
            'end_time' => strtotime($attr["end_date"]),
        );
        $modRet = $statMod->queryStatDiamondProduceAllList($param);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $errMsg = $modRet['msg'];
            goto ERR_RET;
        }
        $list = $modRet['data'];

        $exportTitle = array(
            "stat_time" => "日期",
            "diamond_amount" => "总产出",
            "gift_agent" => "赠送代理商钻石",
            "gift_exclusive" => "赠送专属钻石",
            "superior_award" => "代理商返钻",
            "agent_purchase" => "代理商购买",
            "mall_purchase" => "游戏内购",
            "share_award" => "每日分享",
            "invite_award" => "邀请好友",
            "admin_deliver" => "赠送玩家钻石",
        );

        $exportData = array();
        foreach ($list as $v) {
            $v["stat_time"] = date("Y-m-d", $v["stat_time"]);
            $exportData[] = $v;
        }

        $fileName = "钻石产出_{$attr["start_date"]}-{$attr["end_date"]}.csv";
        export_file($fileName, $exportTitle, $exportData);

    ERR_RET:
        $httpReferer = I('server.HTTP_REFERER');
        $url = parse_url($httpReferer);
        $param = array('errMsg' => "导出失败：{$errMsg}");
        foreach (explode('&amp;', $url['query']) as $query) {
            list($p, $v) = explode('=', $query);
            $param[$p] = $v;
        }
        redirect($url['scheme'].'://'.$url['host'].':'.$url['port'].$url['path'].'?'.http_build_query($param));
    }
}

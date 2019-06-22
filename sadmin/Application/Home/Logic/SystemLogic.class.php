<?php
namespace Home\Logic;

use Home\Model\DatabasesConfModel;
use Home\Model\GameModel;
use Home\Model\SysDbsqlModel;
use Home\Model\SysDbsqlStatementModel;
use Home\Model\SysErrlogModel;
use Home\Model\SysOperlogModel;
use Home\Model\SysUserModel;

class SystemLogic
{
    /****************************** 数据管理 ******************************/

    /**
     * 删除游戏配置，删除游戏后，同时删除该游戏对应的所有外库配置
     * @author Carter
     */
    public function removeGameByGameId($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $gameMod = new GameModel();
        $dbMod = new DatabasesConfModel();

        // 删除游戏配置
        $modRet = $gameMod->deleteGameByGameId($gameId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 删除外库配置
        $modRet = $dbMod->deleteDbConfByGameId($gameId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        return $ret;
    }

    /**
     * 检测并且格式化 sql 语句
     * @author Carter
     */
    private function _formatSqlStatement($sql, &$nameList)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        // 回车统一为 \n ，将所有 \r 替换为 \n
        $sql = str_replace("\r", "\n", $sql);

        // 每一行开头与结尾都不留空格且不允许出现连续空行
        $sql = preg_replace('/(\s*)(\n+)(\s*)/', "\n", $sql);

        // 左括号前、右括号后追加一个空格
        $sql = preg_replace('/\(/', ' (', $sql);
        $sql = preg_replace('/\)/', ') ', $sql);

        // 不允许连续空格
        $sql = preg_replace('/( +)/', ' ', $sql);

        // 根据语句首个单词判断不同语法，不同语法存在不同的格式化条件
        list($firstWord, $bodyStr) = explode(" ", str_replace("\n", " ", $sql), 2);
        switch (strtoupper($firstWord)) {
            case "CREATE":
                list($secondWord, $tableName, $mainBody) = explode(" ", $bodyStr, 3);
                $nameList[] = $tableName = trim($tableName, "`");
                break;

            case "ALTER":
            case "DROP":
            case "INSERT":
            case "DELETE":
                list($secondWord, $tableName, $mainBody) = explode(" ", $bodyStr, 3);
                $tableName = trim($tableName, "`");
                if (!in_array($tableName, $nameList)) {
                    $ret['code'] = ERRCODE_PARAM_INVALID;
                    $ret['msg'] = "数据表{$tableName}不存在";
                    return $ret;
                }
                break;

            case "UPDATE":
                list($tableName, $mainBody) = explode(" ", $bodyStr, 2);
                $tableName = trim($tableName, "`");
                if (!in_array($tableName, $nameList)) {
                    $ret['code'] = ERRCODE_PARAM_INVALID;
                    $ret['msg'] = "数据表{$tableName}不存在";
                    return $ret;
                }
                break;

            default:
                $ret['code'] = ERRCODE_PARAM_INVALID;
                $ret['msg'] = '只允许提交 CREATE, ALTER, DROP, INSERT, UPDATE, DELETE 语句';
                return $ret;
        }

        $ret['data'] = array(
            'sql' => trim($sql).";",
            'tableName' => $tableName,
        );
        return $ret;
    }

    /**
     * 获取数据库修改记录
     * @author Carter
     */
    public function getSysSqlExceList($attr)
    {
        $sqlMod = new SysDbsqlModel();
        $stmMod = new SysDbsqlStatementModel();

        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(
                'list' => array(),
                'pagination' => '',
            ),
        );

        // 获取执行列表
        $modRet = $sqlMod->querySysSqlExceList($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $sqlList = $modRet['data']['list'];
        $pagination = $modRet['data']['pagination'];

        // 列表为空，直接返回，否则后续查询会报错
        if (empty($sqlList)) {
            return $ret;
        }

        $idArr = array();
        $list = array();
        foreach ($sqlList as $v) {
            $idArr[] = $v['id'];

            $list[$v['id']] = array(
                'requester' => $v['requester_id'],
                'describe' => $v['sql_describe'],
                'status' => $v['status'],
                'statement' => array(),
                'spreadFlag' => 0,
                'spreadCont' => "",
            );
        }

        // 获取语句列表
        $modRet = $stmMod->querySysSqlStatementList($idArr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $statementList = $modRet['data'];

        foreach ($statementList as $v) {
            $list[$v['sql_id']]['statement'][] = $v;
        }

        foreach ($list as $k => $v) {
            // 只要超过一条语句或单条语句超过 72 字节，都要做收缩显示
            $firstStatement = current($v['statement']);
            if (count($v['statement']) > 1 || strlen($firstStatement['sql_statement']) > 72) {
                $list[$k]['spreadFlag'] = 1;

                $crPos = strpos($firstStatement['sql_statement'], "\n");
                if ($crPos && $crPos < 72) {
                    $subLen = $crPos;
                } else {
                    $subLen = 72;
                }
                $list[$k]['spreadCont'] = substr($firstStatement['sql_statement'], 0, $subLen);
            }
        }

        $ret['data']['list'] = $list;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }

    /**
     * 根据 id 获取数据库记录详细信息
     * @author Carter
     */
    public function getSysSqlInfoById($id)
    {
        $sqlMod = new SysDbsqlModel();
        $stmMod = new SysDbsqlStatementModel();
        $userMod = new SysUserModel();

        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $field = 'id,status,table_name,sql_describe,requester_id,request_time,executor_id,execute_time,remark';
        $modRet = $sqlMod->querySysSqlInfoById($id, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $sqlInfo = $modRet['data'];

        $modRet = $stmMod->querySysSqlStatementList(array($id));
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $sqlInfo['statement'] = $modRet['data'];

        // 申请人
        $modRet = $userMod->querySysUserInfoByUid($sqlInfo['requester_id'], 'username');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $sqlInfo['requester'] = "";
        } else {
            $sqlInfo['requester'] = $modRet['data']['username'];
        }

        // 执行人
        $sqlInfo['executor'] = "";
        if ($sqlInfo['executor_id']) {
            $modRet = $userMod->querySysUserInfoByUid($sqlInfo['executor_id'], 'username');
            if (ERRCODE_SUCCESS === $modRet['code']) {
                $sqlInfo['executor'] = $modRet['data']['username'];
            }
        }

        $ret['data'] = $sqlInfo;
        return $ret;
    }

    /**
     * 添加数据库语句申请
     * @author Carter
     */
    public function addSqlApplyStatement($attr)
    {
        $sqlMod = new SysDbsqlModel();
        $stmMod = new SysDbsqlStatementModel();

        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $modRet = $sqlMod->querySysDbTableStruct();
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $tableNameList = array_column($modRet['data'], 'tbname');

        $sqlArr = array();

        // 以 ; 分割，逐一拼出每条语句
        $arr = explode(";", html_entity_decode($attr['sql_statement']));
        $partStr = "";
        foreach ($arr as $v) {
            // 单引号或双引号必须成对出现，否则表示语句未结束
            // 计算单引号个数前先去除转义单引号，如果单引号数量为基数，表示语句未结束
            $filterSingleStr = str_replace("\'", "", $partStr.$v);
            if (substr_count($filterSingleStr, "'") % 2 !== 0) {
                $partStr .= $v.";";
                continue;
            }

            // 计算双引号个数前先去除转义双引号，如果双引号数量为基数，表示语句未结束
            $filterDoubleStr = str_replace('\"', '', $partStr.$v);
            if (substr_count($filterDoubleStr, '"') % 2 !== 0) {
                $partStr .= $v.";";
                continue;
            }

            // 如果分号既不在单引号内也不在双引号内表示语句结束
            $sqlArr[] = $partStr.$v;
            $partStr = "";
        }

        $statement = array();
        $markName = "";
        foreach ($sqlArr as $k => $v) {
            // 进行语句格式化前需要先去除头尾空白字符，格式化过程不做这一步
            $v = trim($v);

            // 空语句，直接过滤掉
            if (empty($v)) {
                continue;
            }

            // 检查语句语法并且进行格式化处理
            $priRet = $this->_formatSqlStatement($v, $tableNameList);
            if (ERRCODE_SUCCESS !== $priRet['code']) {
                $ret['code'] = $priRet['code'];
                $ret['msg'] = $priRet['msg'];
                return $ret;
            }
            if ("" === $markName) {
                $markName = $priRet['data']['tableName'];
            } else if ($markName !== $priRet['data']['tableName']) {
                $ret['code'] = ERRCODE_PARAM_INVALID;
                $ret['msg'] = '只允许针对同一表格进行修改';
                return $ret;
            }

            $statement[] = $priRet['data']['sql'];
        }
        if (empty($statement)) {
            $ret['code'] = ERRCODE_PARAM_NULL;
            $ret['msg'] = '不能提交空语句';
            return $ret;
        }

        $operUid = C('G_USER.uid');

        $modRet = $sqlMod->insertSysSqlApply($markName, $attr['sql_describe'], $operUid);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $ret['data']['id'] = $sqlId = $modRet['data']['id'];
        $ret['data']['sql'] = $modRet['data'];

        $ret['data']['statement'] = array();
        foreach ($statement as $v) {
            $modRet = $stmMod->insertSysSqlStatement($sqlId, $v);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $ret['data']['statement'][] = $modRet['data'];
        }

        return $ret;
    }

    /**
     * 修改数据库语句
     * @author Carter
     */
    public function editSqlStatement($attr)
    {
        $sqlMod = new SysDbsqlModel();
        $stmMod = new SysDbsqlStatementModel();

        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $modRet = $sqlMod->querySysDbTableStruct();
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $tableNameList = array_column($modRet['data'], 'tbname');

        // 以 ; 分割，只提取第一条语句，多余的忽略
        $arr = explode(";", html_entity_decode($attr['sql_statement']));
        $statement = "";
        foreach ($arr as $v) {
            // 单引号或双引号必须成对出现，否则表示语句未结束
            // 计算单引号个数前先去除转义单引号，如果单引号数量为基数，表示语句未结束
            $filterSingleStr = str_replace("\'", "", $v);
            if (substr_count($filterSingleStr, "'") % 2 !== 0) {
                $statement .= $v.";";
                continue;
            }

            // 计算双引号个数前先去除转义双引号，如果双引号数量为基数，表示语句未结束
            $filterDoubleStr = str_replace('\"', '', $v);
            if (substr_count($filterDoubleStr, '"') % 2 !== 0) {
                $statement .= $v.";";
                continue;
            }

            // 如果分号既不在单引号内也不在双引号内表示语句结束
            $statement .= $v;
            break;
        }

        // 进行语句格式化前需要先去除头尾空白字符，格式化过程不做这一步
        $statement = trim($statement);

        // 空语句，直接过滤掉
        if (empty($statement)) {
            $ret['code'] = ERRCODE_PARAM_INVALID;
            $ret['msg'] = '不能修改为空语句';
            return $ret;
        }

        // 检查语句语法并且进行格式化处理
        $priRet = $this->_formatSqlStatement($statement, $tableNameList);
        if (ERRCODE_SUCCESS !== $priRet['code']) {
            $ret['code'] = $priRet['code'];
            $ret['msg'] = $priRet['msg'];
            return $ret;
        }
        $statement = $priRet['data']['sql'];
        $tableName = $priRet['data']['tableName'];

        // 获取语句父表相关信息，必须先校验一次修改权限
        $modRet = $sqlMod->querySysSqlInfoById($attr['sql_id'], 'status,table_name,requester_id');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $sqlInfo = $modRet['data'];

        // 状态必须为待审核状态的语句才可以修改
        if ($sqlMod::STATUS_PENDING != $sqlInfo['status']) {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = '只有待审核状态的语句才允许修改';
            return $ret;
        }

        // 不能把表给改了
        if ($tableName != $sqlInfo['table_name']) {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "该语句原本表名为 {$sqlInfo['table_name']}，不能改为{$tableName}";
            return $ret;
        }

        // 只能修改自己申请的语句
        $uid = C('G_USER.uid');
        if ($uid != $sqlInfo['requester_id']) {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "不能修改其他用户申请的语句，该语句由{$sqlInfo['requester_id']}申请";
            return $ret;
        }

        $modRet = $stmMod->updateSysSqlStatement($attr['statement_id'], $attr['sql_id'], $statement);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }

        return $ret;
    }

    /**
     * 执行数据库语句
     * @author Carter
     */
    public function executeSqlStatement($attr)
    {
        $sqlMod = new SysDbsqlModel();
        $stmMod = new SysDbsqlStatementModel();

        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $sqlId = $attr['id'];

        $modRet = $sqlMod->querySysSqlInfoById($sqlId, 'status');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $sqlInfo = $modRet['data'];

        // 只允许执行待审核状态的语句
        if ($sqlMod::STATUS_PENDING != $sqlInfo['status']) {
            set_exception(__FILE__, __LINE__, "[executeSqlStatement] status {$sqlInfo['status']}");
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = '只有待审核状态的语句可以进行执行操作';
            return $ret;
        }

        // 执行语句，然后根据执行结果更新父语句信息
        $modRet = $stmMod->execExecuteSysSqlStatement($sqlId);
        if (ERRCODE_SUCCESS === $modRet['code']) {
            $sqlModRet = $sqlMod->updateSysSqlRow($sqlId, $sqlMod::STATUS_SUCCESS, $attr['remark']);
        } else {
            $sqlModRet = $sqlMod->updateSysSqlRow($sqlId, $sqlMod::STATUS_FAILED, $attr['remark']);

            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
        }
        if (ERRCODE_SUCCESS !== $sqlModRet['code']) {
            $ret['code'] = $sqlModRet['code'];
            $ret['msg'] = $sqlModRet['msg'];
            return $ret;
        }

        return $ret;
    }

    /****************************** 流水查询 ******************************/

    /**
     * 获取系统操作流水列表
     * @author Carter
     */
    public function getSysOperationLogList($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $logMod = new SysOperlogModel();
        $userMod = new SysUserModel();

        $where = array();
        if ($attr['uid']) {
            $where['uid'][] = array('eq', $attr['uid']);
        }
        if ($attr['username']) {
            // 通过用户名查询用户 id
            $modRet = $userMod->queryAllSysUserByAttr(array('username' => $attr['username']), "uid");
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            if (empty($modRet['data'])) {
                $where['id'] = 0;
            } else {
                $uidArr = array();
                foreach ($modRet['data'] as $v) {
                    $uidArr[] = $v['uid'];
                }
                $where['uid'][] = array('in', $uidArr);
            }
        }
        if ($attr['start_date']) {
            // 确保时间是从指定日期的0点0分0秒开始
            $startStamp = strtotime(date('Y-m-d', strtotime($attr['start_date'])));
            $where['create_time'][] = array('egt', $startStamp);
        }
        if ($attr['end_date']) {
            // 确保时间是从指定日期的23点59分59秒结束
            $endStamp = strtotime(date('Y-m-d 23:59:59', strtotime($attr['end_date'])));
            $where['create_time'][] = array('elt', $endStamp);
        }
        if ($attr['cont_key']) {
            if (strlen($attr['con_key']) > 6) {
                $where['_string'] = "MATCH (oper_cont) AGAINST ('{$attr['cont_key']}' IN BOOLEAN MODE)";
            } else {
                $where['oper_cont'] = array('like', "%{$attr['cont_key']}%");
            }
        }
        if ($attr['game_id']) {
            $where['game_id'] = $attr['game_id'];
        }
        if ($attr['main_code']) {
            $where['main_code'] = $attr['main_code'];
        }
        if ($attr['sublevel_code']) {
            $where['sublevel_code'] = $attr['sublevel_code'];
        }
        if ($attr['third_code']) {
            $where['third_code'] = $attr['third_code'];
        }

        $field = "id,uid,game_id,main_code,sublevel_code,third_code,oper_code,oper_cont,create_time";
        $modRet = $logMod->querySysOperateLogByWhere($where, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $logList = $modRet['data']['list'];
        $pagination = $modRet['data']['pagination'];

        $uidArr = array();
        foreach ($logList as $v) {
            $uidArr[] = $v['uid'];
        }
        $uidArr = array_flip(array_flip($uidArr));

        // 用户名称 map。用户名称做成映射，只查询一次，不要一条记录查一次用户
        $modRet = $userMod->queryAllSysUserByAttr(array('uid' => $uidArr), "uid,username");
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $userMap = array();
        foreach ($modRet['data'] as $v) {
            $userMap[$v['uid']] = $v['username'];
        }

        foreach ($logList as $k => $v) {
            // 将操作内容转化为可视格式
            $unseriData = unserialize($v['oper_cont']);
            $showCont = $unseriData['msg'];
            if (!empty($unseriData['data'])) {
                $showCont .= "\n".var_export($unseriData['data'], true);
            }
            $logList[$k]['show_cont'] = $showCont;
        }

        $ret['data'] = array(
            'userMap' => $userMap,
            'list' => $logList,
            'pagination' => $pagination,
        );
        return $ret;
    }

    /**
     * 获取错误流水详情
     * @author Carter
     */
    public function getSysErrorLogInfo($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $logMod = new SysErrlogModel();
        $userMod = new SysUserModel();

        // 获取流水
        $modRet = $logMod->querySysExceptionById($id);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $info = $modRet['data'];

        // 处理者
        if ($info['handler_id'] > 0) {
            $modRet = $userMod->querySysUserInfoByUid($info['handler_id'], "username");
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $info['handler'] = $modRet['data']['username'];
        } else {
            $info['handler'] = "";
        }

        $ret['data'] = $info;

        return $ret;
    }
}

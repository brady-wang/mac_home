<?php
namespace Home\Model\ClubLog;

use Think\Model;

class LogClubModel extends Model
{
    // 初始配置
    protected $connection = 'CONF_DBTYPE_CLUB_LOG';
    protected $trueTableName = 'log_club';

    const LOG_TYPE_CREATE = 0; // 生成亲友圈
    const LOG_TYPE_TRANSFER = 1; // 转正成功
    const LOG_TYPE_TFAIL = 2; // 转正失败

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据条件获取亲友圈流水
     * @author Carter
     */
    public function queryClubLogByAttr($attr, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if (isset($attr['gameId'])) {
            $where['gameId'] = $attr['gameId'];
        }
        if (isset($attr['clubType'])) {
            $where['clubType'] = $attr['clubType'];
        }
        if (isset($attr['createDate'])) {
            $where['createDate'] = $attr['createDate'];
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubLogByAttr] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }
}

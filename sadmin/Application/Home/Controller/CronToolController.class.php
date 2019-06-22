<?php
namespace Home\Controller;

use Home\Model\StatUserDailyUsercacheModel;
use Home\Model\SysCronlogModel;

class CronToolController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        // 正式服仅支持CLI模式
        if ('production' === APP_STATUS) {
            $this->checkIsCli();
        }

        // 统计脚本要确保脚本可完整执行，不能中途中断
        ignore_user_abort();
        set_time_limit(0);
        ini_set('memory_limit', '2G');
    }

    /**
     * 定期清除系统冗余数据，包括上传临时文件
     * 执行时间： 每天下午2点
     * cron:
     * 0 14 * * * flock -xn /tmp/sad_cron_clear_system_redunance_data.lock -c
     *    'php /rootdir/index.php Home/CronTool/clearSystemRedunanceData > /dev/null 2>&1'
     * @author Carter
     */
    public function clearSystemRedunanceData()
    {
        $cronMod = new SysCronlogModel();
        $ucacheMod = new StatUserDailyUsercacheModel();

        $cronType = $cronMod::CRON_TYPE_TOOL_CLEAR_SYS_REDUNANCE;
        $retCode = $cronMod::RET_CODE_SUCCESS;
        $retData = "";

        // 脚本开始时间
        $startTime = time();

        // 清除期限，清除三天前的数据
        $cutTimeStamp = strtotime("-3 day");

        // 清理目录过期文件
        $funcRet = $this->_cleanDirByCutTime(ROOT_PATH.'FileUpload/', $cutTimeStamp);
        if (ERRCODE_SUCCESS !== $funcRet['code']) {
            $retCode = $cronMod::RET_CODE_FAIL;
            $retData .= "[Error] clean fileupload dir failed: {$funcRet['msg']}\n";
        } else {
            if (empty($funcRet['data'])) {
                $retData .= "[Success] no file deleted\n";
            } else {
                foreach ($funcRet['data'] as $v) {
                    if (0 === $v['flag']) {
                        $retCode = $cronMod::RET_CODE_WARNING;
                        $retData .= "[Warning] delete file {$v['file']} failed\n";
                    } else {
                        $retData .= "[Info] delete file {$v['file']} success\n";
                    }
                }
            }
        }

        // 清理数据库表 sad_stat_user_daily_usercache 过期数据
        $modRet = $ucacheMod->deleteStatDailyUserCacheByTime($cutTimeStamp);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retCode = $cronMod::RET_CODE_FAIL;
            $retData .= "[Error] clean daily user cache failed: {$modRet['msg']}\n";
        } else {
            $retData .= "[Info] delete {$modRet['data']} daily user cache success\n";
        }

    CRON_END:
        // 脚本结束时间
        $endTime = time();

        // 记录定时器流水
        $cronMod->insertSysCronlog($cronType, $startTime, $endTime, $retCode, $retData);

        return true;
    }

    /**
     * 清除指定目录下的所有超过截止时间的文件
     * @author Carter
     */
    private function _cleanDirByCutTime($dir, $cutTimeStamp)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $failFlag = 0;

        if (!is_dir($dir)) {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "{$dir} is not a dir";
            return $ret;
        }

        $dh = opendir($dir);
        if (false === $dh) {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "{$dir} open dir failed";
            return $ret;
        }
        while (1) {
            $file = readdir($dh);
            if (!$file) {
                break;
            }
            // 不处理特殊句柄
            if ($file == "." || $file == "..") {
                continue;
            }

            $fileUrl = $dir."/".$file;
            if (is_dir($fileUrl)) {
                $funcRet = $this->_cleanDirByCutTime($fileUrl, $cutTimeStamp);
                if (ERRCODE_SUCCESS !== $funcRet['code']) {
                    closedir($dh);
                    $ret['code'] = $funcRet['code'];
                    $ret['msg'] = $funcRet['msg'];
                    return $ret;
                }
                $ret['data'] = array_merge($ret['data'], $funcRet['data']);
            } else {
                if (filemtime($fileUrl) < $cutTimeStamp) {
                    if (unlink($fileUrl)) {
                        $ret['data'][] = array(
                            'flag' => 1,
                            'file' => basename($fileUrl),
                        );
                    } else {
                        $ret['data'][] = array(
                            'flag' => 0,
                            'file' => basename($fileUrl),
                        );
                    }
                }
            }
        }
        closedir($dh);

        return $ret;
    }
}

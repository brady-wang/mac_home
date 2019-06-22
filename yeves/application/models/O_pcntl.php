<?php
/**
 * Pcntl 功能类
 * @author Terry
 */
class O_pcntl extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function tps_pcntl_wait($childProcessCode){

        $pid = pcntl_fork();
        if($pid>0){

            pcntl_wait($status);
            @$this->db->reconnect();
        }elseif($pid==0){

            eval($childProcessCode);
            exit;
        }else{
            die('Cannot fork.');
        }
    }

    /**
     * 创建子进程
     * @author: derrick
     * @date: 2017年3月27日
     * @param: @param Closure $callback
     * @reurn: return_type
     */
    public function fork_child(Closure $callback) {
        while ( true ) {
            $pid = pcntl_fork ();
            if ($pid == - 1) {
                die ( 'could not fork' );
            }
            break;
        }
        if ($pid) {
            // 父进程
            pcntl_wait ($status, WNOHANG);
//     		exit(1);
        } else {
            $callback();
            exit (0);
        }
    }

    /**
     * Daemon log
     * @author: xia.rong
     * @date: 2017年7月10日
     * @param string $msg
     * @param int $io, 1->just echo, 2->just write, 3->echo & write
     */
    public function logs($msg,$x)
    {
        $datetime = date('Y-m-d H:i:s');
        $msg = "[{$datetime}] {$msg}\n";
        file_put_contents("/tmp/daemon$x.log", $msg, FILE_APPEND | LOCK_EX);
    }
    /**
     * @author: xia.rong
     * @date: 2017年7月10日
     */
    public function burst($num,Closure $callback) {
//        pcntl_signal(SIGCLD, SIG_IGN);
        pcntl_signal(SIGCHLD, SIG_IGN);//不等待子进程结束，交由系统回收
        for ($x = 0; $x < $num; $x++) {
            $pid = pcntl_fork();
            if($pid>0){
//                $this->logs('子進程PID：'.$pid,'9999');
                //            @$this->db->reconnect();//重连数据库
            }elseif($pid==0){
                $this->eternal($x,$callback);
                exit;
            }else{
                $this->logs("error");
            }
        }
    }
    /**
     * @author: xia.rong
     * @date: 2017年7月10日
     */
    public function eternal($x,$callback) {
        declare(ticks = 1) {
            while (TRUE) {
                if(0){//分出新子进程
                    $this->Child_process();
                    try {
//                        sleep(1);
//                        $this->logs($x);
                    } catch (Exception $e) {
                        $this->logs($e->getMessage(), '');
                    }
                }  else {//不分子进程
                    try {
//                            sleep(1);
//                            eval($callback);
                        $callback($x);
                        $this->logs('xxxxxxxxxxx',$x);
                    } catch (Exception $e) {
//                            $this->logs($e->getMessage(), '');
                        $this->db->insert('debug_rx', array(
                            'content' => var_export($e->getMessage(), 1),
                            'content1' => '进程报错'
                        ));
                    }
                }
            }
        }
    }
    /**
     * @author: xia.rong
     * @date: 2017年7月10日.
     * 创建新的子进程
     */
    public function Child_process(){
        $pid = pcntl_fork();
        if($pid>0){
            pcntl_wait($status, WNOHANG); //等待子进程结束,将其回收
            exit;
        }
    }


    /**
     * @author Able
     * @param $callback
     * 创建子进程
     */
    public function fork_child_new($callback) {
        $pid = pcntl_fork();
        if($pid>0){
            pcntl_wait($status);
            @$this->db->reconnect();
        }elseif($pid==0){
            $callback();
            exit;
        }else{
            die('Cannot fork.');
        }
    }
}

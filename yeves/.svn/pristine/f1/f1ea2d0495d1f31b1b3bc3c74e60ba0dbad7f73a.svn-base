<?php
/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/4/8
 * Time: 11:35
 */

class  test extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function test1()
    {
        $this->load->model("O_es");
        $this->load->model("Tb_articles");
        $this->O_es->init(config_item("db")['db'],'articles');

        //$res = $this->Tb_articles->get_list(1,1000,'*','desc');

//        $this->O_es->del_index();
//        foreach($res as $v){
//            $res = $this->O_es->add_doc($v['id'],$v);
//            dump("索引".$v['id'].$res);
//        }

//        $res = $this->O_es->add_doc('1',['testField' => 'hello 天安门']);
        //$res = $this->O_es->add_doc('1',['title' => 'Quick brown rabbits','body'=>'Brown rabbits are commonly seen.']);
       // $res = $this->O_es->add_doc('2',['title' => 'Keeping pets healthy','body'=>'My quick brown fox eats rabbits on a regular basis.']);
        //dump($res);
        $list = $this->O_es->search_index_mul(['title','content'],'bootstrap');
        if(!empty($list))
        {
            foreach($list as $v){
                dump($v['_source']);
            }
        }
    }

    public function search()
    {
        $this->load->view('test/search');
    }
}
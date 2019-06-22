<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/2/2
 * Time: 11:23
 */
class Tb_articles extends MY_Model
{
    protected $table = "articles";
    protected $select_field ;

    public function __construct()
    {
        parent::__construct();
        $this->select_field = 'id,author,title,description,category,tag,create_time,pv,is_hot,is_del,line-num,image';
        $this->load->model("tb_category");
    }

    /**
     * 获取文章列表
     */
    public function get_list($page,$page_size,$field='',$order='asc')
    {
        if(empty($field)){
            $field = $this->select_field;
        }
        $start = ($page - 1 ) * $page_size;
        $res = $this->db->select($field)->from($this->table)->where(['is_del'=>0])->order_by('create_time '.$order)->limit($page_size,$start)->get()->result_array();
        if (!empty($res)) {
            $cate_ids = [];
            foreach ($res as $v) {
                $cate_ids[] = $v['category'];
            }
        }
        $cate_ids = array_unique($cate_ids);
        $cate_names = $this->tb_category->get_cate_names($cate_ids);

        //$Parsedown = new Parsedown();
        foreach ($res as &$v) {
            $v['cate_name'] = isset($cate_names[$v['category']]) ? $cate_names[$v['category']]['category'] : '';
           // $v['description'] = mb_substr(strip_tags($Parsedown->text($v['content'])), 0, 200);
        }
        return $res;
    }

    public function get_list_by_cate($page,$page_size,$cate_id)
    {
        $start = ($page - 1 ) * $page_size;
        $res = $this->db->select($this->select_field)->from($this->table)->where(['is_del'=>0,'category'=>$cate_id])->order_by('id desc')->limit($page_size,$start)->get()->result_array();
        if (!empty($res)) {
            $cate_ids = [];
            foreach ($res as $v) {
                $cate_ids[] = $v['category'];
            }
        }
        $cate_ids = array_unique($cate_ids);
        $cate_names = $this->tb_category->get_cate_names($cate_ids);

        foreach ($res as &$v) {
            $v['cate_name'] = isset($cate_names[$v['category']]) ? $cate_names[$v['category']]['category'] : '';
        }
        return $res;
    }

    public function get_list_by_tag($page,$page_size,$tag_id)
    {
        $start = ($page - 1 ) * $page_size;

        $res = $this->db->from('article_tag as a')
            ->select("b.id,b.author,b.title,b.description,b.category,b.tag,b.create_time,b.pv,b.is_hot,b.is_del,b.line-num")
            ->join('articles as b','a.article_id = b.id')
            ->where(['a.tag_id'=>$tag_id,'b.is_del'=>0])
            ->limit($page_size,$start)
            ->get()
            ->result_array();
        if (!empty($res)) {
            $cate_ids = [];
            foreach ($res as $v) {
                $cate_ids[] = $v['category'];
            }
        }
        $cate_ids = array_unique($cate_ids);
        $cate_names = $this->tb_category->get_cate_names($cate_ids);

        foreach ($res as &$v) {
            $v['cate_name'] = isset($cate_names[$v['category']]) ? $cate_names[$v['category']]['category'] : '';
        }
        return $res;
    }

    public function get_list_by_year_month($page,$page_size,$year_month)
    {
        $start = ($page - 1 ) * $page_size;
        $res = $this->db->select($this->select_field)->from($this->table)->where(['is_del'=>0,'article_month'=>$year_month])->order_by('id desc')->limit($page_size,$start)->get()->result_array();
        if (!empty($res)) {
            $cate_ids = [];
            foreach ($res as $v) {
                $cate_ids[] = $v['category'];
            }
        }
        $cate_ids = array_unique($cate_ids);
        $cate_names = $this->tb_category->get_cate_names($cate_ids);

        foreach ($res as &$v) {
            $v['cate_name'] = isset($cate_names[$v['category']]) ? $cate_names[$v['category']]['category'] : '';
        }
        return $res;
    }


    public function get_total_rows()
    {
        $res = $this->db->from($this->table)
            ->select("count(0) as number")
            ->where(['is_del'=>0])
            ->get()
            ->row_array();
        if(!empty($res)) {
            return $res['number'];
        } else {
            return 0;
        }
    }

    public function get_total_rows_by_cate($cate_id,$admin=false)
    {
        if($admin) {
            $where = ['category'=>$cate_id];
        } else {
            $where = ['is_del'=>0,'category'=>$cate_id];
        }
        $res = $this->db->from($this->table)
            ->select("count(0) as number")
            ->where($where)
            ->get()
            ->row_array();
        if(!empty($res)) {
            return $res['number'];
        } else {
            return 0;
        }
    }

    public function get_total_rows_by_tag($tag_id)
    {
       $res = $this->db->from('article_tag as a')
           ->select("count(0) as number")
           ->join('articles as b','a.article_id = b.id')
           ->where(['a.tag_id'=>$tag_id,'b.is_del'=>0])
           ->get()
           ->row_array();
        return count($res['number']);
    }

    public function get_total_rows_by_year_month($year_month)
    {
        $res = $this->db->from($this->table)
            ->select("count(0) as number")
            ->where(['is_del'=>0,'article_month'=>$year_month])
            ->get()
            ->row_array();
        if(!empty($res)) {
            return $res['number'];
        } else {
            return 0;
        }
    }

    public function add_article($data)
    {
        $data['create_time'] = date("Y-m-d H:i:s", time());
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * 获取热门推荐 五条
     */
    public function get_hot_recommend()
    {
        $param = [
            'select'=>'id,title,pv,create_time',
            'where'=>[
                'is_hot'=>1
            ],
            'order_by'=>'id desc',
            'limit'=>'5'
        ];
        return $this->get($param);
    }
    //前端显示,获取十条点击排行文章
    public function get_click_rank()
    {
        $param = [
            'select'=>'id,title,pv',
            'order by'=>'pv desc',
            'limit'=>'10'
        ];
        return $this->get($param);
    }
    //文章详情
    public function get_article_detail($id)
    {
        $res = $this->db->from($this->table)
            ->select($this->table.".*,category.category as cate_name")
            ->join('category',$this->table.'.category = category.id','left')
            ->where([$this->table.'.id'=>$id])
            ->get()
            ->row_array();
        if(!empty($res)){
            $Parsedown = new Parsedown();
            $res['content'] = $Parsedown->text($res['content']);
        }
        return $res;
    }


    //删除文章
  public function delete_article($id)
  {
      $this->db->delete($this->table,['id'=>$id]);
      return $this->db->affected_rows();
  }


//上下架文章
  public function up_down_article($id,$is_del)
  {
      $this->db->update($this->table,['is_del'=>$is_del,'update_time'=>date("Y-m-d H:i:s",time())],['id'=>$id]);
      return $this->db->affected_rows();
  }
    //根据月份统计
    public function sum_by_month()
    {
        $sql  = 'SELECT count(*) as number,article_month from articles where is_del = "0" group by article_month ORDER BY article_month desc';
        $res = $this->db->query($sql)->result_array();
        return $res;
    }

    public function sum_by_cate()
    {
        $sql  = 'SELECT count(*) as number,category from articles where is_del = "0" group by category ORDER BY category desc';
        $res = $this->db->query($sql)->result_array();
        return $res;
    }

    public function getAllArticles(){
        $this->load->database();
        $sql="select * from articles";
        $query=$this->db->query($sql);
        foreach($query->result_array() as $row){
            $data[]=$row;
        }
        return $data;
    }
    public function getArticle($id){
        $this->load->database();
        $sql="select * from articles where id={$id}";
        $data =$this->db->query($sql)->row_array();
        return $data;
    }


    public function getArticlesTag($tag_id){
        $this->load->database();
        $sql="select c.id, c.title,c.4252,c.category, c.tag, a.id as tag_id, a.tag_name, a.tag_button_type from tag as a join article_tag as b on b.tag_id=a.id join articles as c on c.id=b.article_id where a.id='{$tag_id}'";
        $data = $this->db->query($sql)->result_array();
        return $data;
    }





}
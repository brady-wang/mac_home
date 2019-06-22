<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Articles extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tb_articles');
        $this->load->model('tb_category');
        $this->_viewData['cur_title'] = __CLASS__;
    }

    //后台文章列表
    public function index()
    {

        $this->load->model('tb_articles');

        //接收参数
        $page = intval($this->input->get('page', true));
        $page_size = intval($this->input->get('page_size', true));

        //参数与处理
        $page = ($page > 1) ? $page : 1;
        $page_size = ($page_size <= 0) ? config_item("admin_page_size") : $page_size;

        //获取总数
        $total_rows = $this->tb_articles->get([
            'select' => 'id',
        ], false, true);

        $data = [];
        $data['page_size'] = $page_size;

        if ($total_rows < 0) {
            $data['list'] = [];
            $data['total_rows'] = 0;
            $data['page'] = 1;
            $data['total_page'] = 1;
        } else {
            $total_page = ceil($total_rows / $page_size);
            $page = ($page > $total_page) ? $total_page : $page;
            $list = $this->tb_articles->get([
                'select' => '*',
                'limit' => [
                    'page_size' => $page_size,
                    'page' => $page
                ],
                'order_by' => 'id desc '
            ]);
            $data['list'] = $list;
            $data['page'] = $page;
            $data['total_rows'] = $total_rows;
            $data['total_page'] = ceil($total_rows / $page_size);
        }
        $this->_viewData['data'] = $data;
        parent::index('admin/article_index');
    }

    //文章编辑
    public function edit($id = 0)
    {

        $data = $this->Tb_articles->getArticle($id);
        $this->_viewData['data'] = $data;
        parent::index("admin/article_add");

    }

    //文章新增
    public function add()
    {
        parent::index("admin/article_add");

    }

    //do 新增和编辑
    public function update()
    {
        header("Content-type:text/html;charset=utf-8");
        $id = intval($this->input->post('id'));
        $is_del = $this->input->post("is_del");
        $title = $this->input->post("title");
        $seo_keyword = $this->input->post("seo_keyword");
        $seo_description = $this->input->post("seo_description");
        $content = $this->input->post("content");
        $create_time = $this->input->post("create_time");
        $category = $this->input->post("category");
        $tag = $this->input->post("tag");
        $create_time = empty($create_time) ? date("Y-m-d H:i:s", time()) : date("Y-m-d H:i:s",strtotime($create_time));

        try{
            if(empty($title)){
                throw new Exception("标题不能为空");
            }
//            if(empty($is_del)){
//                throw new Exception("请选择是否上架");
//            }

            if(empty($category)){
                throw new Exception("请选择分类");
            }

            if(empty($tag)){
                throw new Exception("请输入标签");
            }

            if(empty($content)){
                throw new Exception("请输入内容");
            }

            $Parsedown = new Parsedown();
            $description = strip_tags($Parsedown->text($content));
            $pattern = '/\s/';
            $description = mb_substr(preg_replace($pattern, '', $description),0,200,'utf-8');
            $data['data'] = array(
                'id' => $id,
                'is_del' => empty($is_del) ? 0 : 1,
                'title' => empty($title) ? '' : $title,
                'seo_keyword' => empty($seo_keyword) ? '' : $seo_keyword,
                'description'=> $description,
                'seo_description' => empty($seo_description) ? '' : $seo_description,
                'content' => $content,
                'create_time' => $create_time,
                'article_month' => substr($create_time, 0, 7),
                'category' => $category,
                'pv' => '1',
                'tag' =>$tag
            );

            //获取表中该文章相关的标签
            $this->load->model('Tb_article_tag');
            $this->load->model('Tb_tag');

            //事务开始
            $this->db->trans_begin();

            //标签插入
            if(!empty($tag)){
                $tag_arr = explode(',',$tag);
            }else {
                $tag_arr = [];
            }

            $this->Tb_tag->random_color_tag($tag_arr);

            //图片上传
            $this->load->model("O_upload");
            if(!empty($_POST['image'])){
                if(preg_match('/^\/upload/',$_POST['image'])){
                    $data['data']['image'] = $_POST['image'];
                } else {
                    $img_path = $this->O_upload->do_upload('article_list',$base64_img=$_POST['image'],'300');
                    $data['data']['image'] = $img_path;
                }
            }


             if (!empty($data['data']['id'])) {

                $data['data']['update_time'] = date("Y-m-d H:i:s", time());
                $this->Tb_articles->update($data['data'], ['id' => $data['data']['id']]);
                //标签 更新
                $this->Tb_article_tag->update_article_tag($id,$tag_arr);
            } else {
                $article_id = $this->Tb_articles->add($data['data']);
                $this->Tb_article_tag->add_article_tag($article_id, $tag_arr);
            }


            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                throw new Exception("操作失败");
            } else {
                $this->db->trans_commit();
                $this->success_response("操作成功");
            }
        }catch(Exception $e){
            $this->error_response($e->getMessage());
        }


    }

    //文章删除
    public function delete()
    {

        $id = intval($this->input->post('id', true));

        try {
            if ($id <= 0) {
                throw new Exception("参数不合法");
            }

            //检查是否存在,如果已经别删除了返回系统错误 不存在的记录
            $article_info = $this->Tb_articles->get([
                'select' => 'id',
                'where' => [
                    'id' => $id
                ],
                'limit' => 1
            ], true);
            if (empty($article_info)) {
                throw new Exception("系统错误 不存在的文章");
            }

            $res = $this->Tb_articles->delete_article($id);
            if ($res > 0) {
                $this->success_response("删除成功");
            } else {
                throw new Exception("删除失败");
            }


        } catch (Exception $e) {
            $this->error_response($e->getMessage());
        }

    }

    //文章上下架
    public function up_down_article()
    {
        $id = intval($this->input->post('id', true));

        try {
            if ($id <= 0) {
                throw new Exception("参数不合法");
            }

            //检查是否存在,如果已经别删除了返回系统错误 不存在的记录
            $article_info = $this->Tb_articles->get([
                'select' => 'id,is_del',
                'where' => [
                    'id' => $id
                ],
                'limit' => 1
            ], true);
            if (empty($article_info)) {
                throw new Exception("系统错误 不存在的文章");
            }

            $is_del = ($article_info['is_del'] == 1) ? 0 : 1;
            if ($is_del == 0) {
                $msg_success = "上架成功";
                $msg_failed = "上架失败";
            } else {
                $msg_success = "下架成功";
                $msg_failed = "下架失败";
            }
            $res = $this->Tb_articles->up_down_article($id, $is_del);
            if ($res > 0) {
                $this->success_response($msg_success);
            } else {
                throw new Exception($msg_failed);
            }

        } catch (Exception $e) {
            $this->error_response($e->getMessage());
        }

    }

}

<?php
namespace Home\Model\DsqpActivity;

use Think\Model;
use Common\Service\ApiService;

class MallModel extends Model
{
    // 初始配置
    protected $connection = 'GAME_ACTIVITY_DB';
    protected $trueTableName = 'act_mall';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 分页查询数据列表
     * @param array $where 默认显示数据
     * @param int $limit 查询数据条数
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function getGoodsListByPage($where = array() ,  $limit = 20)
    {
        $result = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => ''
        );

        $page = (int)I('get.p');
        if($page < 1){
            $page = 1 ;
        }

        try {
            $data = $this->where( (array) $where)->limit($limit)->page($page)->order(' id DESC ')->select();
            foreach($data as $key => $info ){
                $priceDate = explode(":", $info['price']);
                list($priceType,$priceNumber) = $priceDate;
                $data[$key]['priceType'] = $priceType;
                $data[$key]['priceNumber'] = $priceNumber;
            }

            $count = $this->where($where)->count();// 查询满足要求的总记录数
            $Page = new \Think\Page($count,$limit);// 实例化分页类 传入总记录数和每页显示的记录数
            $show = $Page->show();// 分页显示输出

            $result['data'] = array(
                'data' =>$data,
                'pagehtml' => $show
            );
        } catch(\Exception $e) { //初始化数据库model失败
            $result['msg'] = "查询SQL错误：".$e->getMessage();
            $result['code'] = ERRCODE_DB_DATA_EMPTY ;
            return $result ;
        }

        return $result;
    }

    /**
     * 查询商品信息
     * @param int $id 查询ID
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function getGoodsInfoById($id)
    {
        if($id < 1 ){
            return false;
        }
        $where = array(
            'id' => (int)$id
        );
        $data = $this->where(  $where)->find();

        $serviceUrl = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT');

        $data['icon'] = empty($data['icon']) ?  '' : $serviceUrl.$data['icon']  ;
        $tempArray = explode('|', $data['img']);

        if( !empty($tempArray) ){
            foreach ($tempArray as $index => $img){
                $tempArray[$index] = empty($img) ?  '' : $serviceUrl . $img ;
            }

            $data['img'] = implode('|', $tempArray);
        }

        return $data;
    }

    /**
     * 查询商品信息
     * @param int $id 查询ID
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function getGoodsInsertId()
    {

        $data = $this->field('id')->order('id desc')->find();

        return $data['id'] + 1;
    }

    /**
     * 删除商品信息
     * @param int $id 查询ID
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function delGoodsData($id)
    {
        $result =  array(
            'code' => ERRCODE_PARAM_INVALID  ,
            'data' => '' ,
            'msg' => 'id参数为空'
        );

        if($id < 1 ){

            return $result ;
        }
        $where = array(
            'id' => (int)$id
        );
        $data = $this->where(  $where)->delete();

        if($data){
            $result =  array(
                'code' => ERRCODE_SUCCESS  ,
                'data' => $data ,
                'msg' => '操作成功'
            );
            $this->updateApiCache(); //重载配置
        }

        return $result;
    }

    /**
     * 更新数据
     * @param int $date 默认显示数据
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function updateGoodsData($id, $data)
    {
        if( empty( $data) || empty($id)){
            $result =  array(
                    'code' => ERRCODE_PARAM_INVALID  ,
                    'data' => $info ,
                    'msg' => '数据为空或id参数为空'
                );
            return $result ;
        }


        $data['id'] = $id;

        $where= array(
            'id' => (int) $id
        );
        //处理图片存到资源服务器
        if($data['image']){
            $result = $this->_updateImgToService($data['image']);

            if($result){
                $data['image'] = $result['data'];
            }
        }

        $info = $this->where($where)->save($data);
        if($info){
            $result =  array(
                    'code' => ERRCODE_SUCCESS  ,
                    'data' => $info ,
                    'msg' => '修改成功'
                );
            $this->updateApiCache(); //重载配置
        }else{
            $result =  array(
                    'code' => ERRCODE_DB_UPDATE_ERR  ,
                    'data' => '' ,
                    'msg' => '修改失败'
                );
        }
        return $result;
    }

    /**
     * 更新数据
     * @param int $date 默认显示数据
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function addGoodsData($data)
    {
        if( empty( $data) ){
            $result =  array(
                    'code' => ERRCODE_PARAM_INVALID  ,
                    'data' => $info ,
                    'msg' => '数据为空或id参数为空'
                );
            return $result ;
        }

        $maxId = $this->max('id');
        $data['id'] = $maxId +1 ;

        //处理图片存到资源服务器
        if($data['image']){
            $result = $this->_updateImgToService($data['image']);

            if($result){
                $data['image'] = $result['data'];
            }
        }

        $info = $this->add($data);

        if($info){
            $result =  array(
                'code' => ERRCODE_SUCCESS  ,
                'data' => $maxId ,
                'msg' => '添加成功'
            );

            $this->updateApiCache(); //重载配置

        }else{
            $result =  array(
                'code' => ERRCODE_DB_UPDATE_ERR  ,
                'data' => '' ,
                'msg' => '添加失败'
            );
        }

        return $result;
    }

    /**
     * 上传图片到资源服务器
     * @param string $reoucre 操作类型1新增2更新
     * @param array $data 显示数据
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    private function _updateImgToService($reoucre){
        $serviceUrl = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT');

        $apiSer = new ApiService();

        //为空，或者已经是服务器图片地址了，不处理
        if(empty($reoucre) ){
            $result['code'] = ERRCODE_SUCCESS;
            $result['data'] = $reoucre ;
            return $result;
        }

        $tempdata = explode('|', $reoucre);

        foreach($tempdata as $key =>$img){
            $tempname = str_replace("/FileUpload/Temp/",'', $img);
            $imgSource = ROOT_PATH."FileUpload/".$tempname;

            //资源服务器地址或者已经是服务器图片相对地址，不处理重复上传
            if(is_url($img) ||  preg_match("/^\/?BI\/GameConfig\/Mall\/[0-9a-zA-Z]+\.(png|jpg|gif|jpeg)/", $str)    ){
                $imgArray[] = $img ;

                $result['code'] = ERRCODE_SUCCESS ;
                $result['data'] = implode('|', $imgArray) ;
            }else{
                if (empty($img) || !file_exists($imgSource)) {
                    $result['code'] = ERRCODE_FILE_NOEXISTENT;
                    $result['msg'] = '图片资源不存在，请重新上传';
                    $result['data'] = $img;
                    return $result;
                }

                $svrPath = "BI/MallConfig/Mall/".$tempname ;

                $serRet = $apiSer->resourceServerUploadImg($svrPath, $imgSource);
                if (ERRCODE_SUCCESS !== $serRet['code']) {
                    $result['code'] = $serRet['code'];
                    $result['msg'] = $serRet['msg'];
                }else{
                    $imgArray[] = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT').'/'.$svrPath;
                    $result['code'] = ERRCODE_SUCCESS ;
                    $result['data'] = implode('|', $imgArray) ;
                }
            }
        }

        return $result ;
    }

    //更新服务器缓存
    private function updateApiCache()
    {
        $api = new ApiService();
        return $api->commonCleanCacheApi('/api/config?act=reload','activity');  //重载兑换商城的Cache
    }
}

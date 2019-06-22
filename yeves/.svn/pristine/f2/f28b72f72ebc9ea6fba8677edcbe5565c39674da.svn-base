
<?php
/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/4/8
 * Time: 10:31
 * esticsearch helper
 */

require APPPATH.'third_party/vendor/autoload.php';

use Elasticsearch\ClientBuilder;


class O_es
{
    protected $db_name;
    protected $tb_name;
    protected $client;
    protected $host;

    //初始化
    public function init($db_name,$tb_name)
    {

        try{
            $this->db_name = $db_name;
            $this->tb_name = $tb_name;
            $this->host = config_item('db')['host'];
//            if(ENVIRONMENT !== 'development'){
//                $host=['172.18.30.69:9200'];
//                $this->client =  ClientBuilder::create()->setHosts($host)->build();
//            } else {
                $this->client =  ClientBuilder::create()->build();
           // }

            return $this->client;
        } catch(Exception $e){
            $msg = $e->getMessage();
            echo json_encode(['success'=>false,'msg'=>$msg]);exit;
        }
    }

   public function add_doc($id,$body=[])
   {
       $params = [
           'index' => $this->db_name,
           'type' => $this->tb_name,
           'id' => $id,
           'body' => $body
       ];
       $response = $this->client->index($params);
       if( isset($response['_shards']['successful']) && $response['_shards']['successful'] >0){
           return true;
       } else {
           return false;
       }
   }

    //根据id获取完整的搜索结果
    public function get_doc($id)
    {

        try{
            $params = [
                'index' => $this->db_name,
                'type' => $this->tb_name,
                'id' => (string)$id
            ];

            $response = $this->client->get($params);
            return $response;
        } catch(Exception $e){
            $msg = $e->getMessage();
            echo json_encode(['success'=>false,'msg'=>$msg]);exit;
        }

    }

    //根据id获取搜索结果
    public function get_doc_source($id)
    {
        $params = [
            'index' => $this->db_name,
            'type' => $this->tb_name,
            'id' => $id
        ];

        $response = $this->client->getSource($params);
        return $response;
    }

    //文档搜索
    public function search_index($search_index,$search_val)
    {
        try {

            $params = [
                'index' => $this->db_name,
                'type' => $this->tb_name,
                'body' => [
                    'query' => [
                        'match' => [
                            $search_index => $search_val
                        ]
                    ]
                ]
            ];

            $response = $this->client->search($params);
           if($response['hits']['total'] > 0) {
               return $response['hits']['hits'];
           } else {
               return false;//未搜索到
           }

        } catch(Exception $e){
            $msg = $e->getMessage();
            echo json_encode(['success'=>false,'msg'=>$msg]);exit;
        }
    }

    public function search_index_mul($field,$string,$type='dis_max',$tie_breaker = 0.3)
    {

        try {

            foreach($field as $v){
                $querys[] = ['match'=>[$v=>$string]];
            }


            switch($type)
            {
                case 'dis_max':
                {
                    $body = [
                        'query'=>[
                            $type=>[
                                'queries'=>[

                                ],
                                'tie_breaker'=>$tie_breaker
                            ]
                        ]
                    ];
                    $body['query'][$type]['queries'] = $querys;
                }
                break;

                case 'bool':
                {
                    $body = [
                        'query'=>[
                            $type=>[
                                'should'=>[

                                ]
                            ]
                        ]
                    ];
                    $body['query'][$type]['should'] = $querys;
                }
                    break;

                default :
                {
                    $body = [
                        'query'=>[
                            $type=>[
                                'queries'=>[

                                ]
                            ]
                        ]
                    ];
                    $body['query'][$type]['queries'] = $querys;
                }
                    break;

            }

            $params = [
                'index' => $this->db_name,
                'type' => $this->tb_name,
                'body' => $body
            ];
            $response = $this->client->search($params);
            if($response['hits']['total'] > 0) {
                return $response['hits']['hits'];
            } else {
                return [];//未搜索到
            }

        } catch(Exception $e){
            $msg = $e->getMessage();
            echo json_encode(['success'=>false,'msg'=>$msg]);exit;
        }
    }

    //文档删除
    public function del_doc($id)
    {
        try{
            $params = [
                'index' => $this->db_name,
                'type' => $this->tb_name,
                'id' => (string)$id
            ];
            $response = $this->client->delete($params);
            return $response;
        } catch(Exception $e){
            $msg = $e->getMessage();
            echo json_encode(['success'=>false,'msg'=>$msg]);exit;
        }
    }

    public function add_index()
    {
        $params = [
            'index' => $this->db_name,
            'body' => [
                'settings' => [
                    'number_of_shards' => 2,
                    'number_of_replicas' => 0
                ]
            ]
        ];

        $response = $this->client->indices()->create($params);
        print_r($response);
    }

    public function del_index()
    {
        $deleteParams = [
            'index' => $this->db_name
        ];
        $response = $this->client->indices()->delete($deleteParams);
        return $response;
    }



}
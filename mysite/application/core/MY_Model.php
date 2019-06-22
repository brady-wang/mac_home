<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/2/2
 * Time: 11:36
 */
class MY_Model extends CI_Model
{
    protected $table;

    public function __construct()
    {
        $this->load->database();
    }

    public function add($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($data,$where)
    {
        $this->db->update($this->table,$data,$where);
        return $this->db->affected_rows();
    }

    public function delete($where)
    {
        $this->db->delete($this->table,$where);
        return $this->db->affected_rows();
    }

    public function replace($data)
    {
        $this->db->replace($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * @param array $params 查询条件
     * @param bool $get_one 是否只查询一行
     * @param bool $get_rows 是否是查询行数
     */
    public function get($params, $get_one = false, $get_rows = false)
    {
        $this->db->from($this->table);

        if ($get_rows) {
            $this->db->select("count(0) as number");
        } else {
            if (isset($params['select'])) {
                $this->db->select($params['select']);
            }
        }

        //where
        if (isset($params['where']) && is_array($params['where'])) {
            $this->db->where($params['where']);
        }

        //where in
        if (isset($params['where_in']) && is_array($params['where_in'])) {
            $this->db->where_in($params['where_in']['key'], $params['where_in']['value']);
        }

        //连表
        if (isset($params['join'])) {
            foreach ($params['join'] as $item) {
                $this->db->join($item['table'], $item['where'], $item['type']);
            }
        }

        //分页
        if (isset($params['limit'])) {
            if (is_array($params['limit']) && isset($params['limit']['page']) && isset($params['limit']['page_size'])) {
                $this->db->limit($params['limit']['page_size'], ($params['limit']['page'] - 1) * $params['limit']['page_size']);
            } else {
                $this->db->limit($params['limit']);
            }
        }

        //分组
        if (isset($params['group'])) {
            $this->db->group_by($params['group']);
        }
        //排序
        if (isset($params['order_by'])) {
            if (is_array($params['order_by'])) {
                foreach ($params['order_by'] as $v) {
                    $this->db->order_by($v['key'], $v['value']);
                }
            } else {
                $this->db->order_by($params['order_by']);
            }
        }

        $result = $this->db->get();

        if (!$get_one) {
            if ($get_rows) {
                return $result ? $result->row_array()['number'] : 0;
            } else {
                if ($result) {
                    return ($result->num_rows() > 0 ? $result->result_array() : array());
                } else {
                    return array();
                }

            }

        } else {
            if ($get_rows) {
                return $result ? $result->row_array()['number'] : 0;
            } else {
                if ($result) {
                    return ($result->num_rows() > 0 ? $result->row_array() : array());
                } else {
                    return array();
                }

            }
        }

    }




}
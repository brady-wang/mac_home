<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/2/9
 * Time: 14:28
 */
class Exception_model extends CI_Model
{
    public function check($data)
    {
        foreach($data as $key=>$value){
            switch($key){
                case 'old_pwd':
                {
                    if(empty($value)){
                        throw new Exception("原密码不能为空");
                    }
                }

                case 'new_pwd':
                {
                    if(empty($value)){
                        throw new Exception("新密码不能为空");
                    }
                }

                case 'new_pwd_re':
                {
                    if(empty($value)){
                        throw new Exception("请再次输入新密码");
                    }
                }
            }
        }

    }
}
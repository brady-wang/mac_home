<?php
/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/3/15
 * Time: 14:10
 */
class O_upload extends MY_Model
{
    protected $table = '';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $dir 图片路径,以upload下面为准  比如 admin  前后都不要 /
     * @param $base64_img  base64位的图片
     * @param int $width  缩略图片
     * @return string
     * @throws Exception
     */
    public function do_upload($dir,$base64_img,$width=300)
    {
        $up_dir = './upload/'.$dir."/";//存放在当前目录的upload文件夹下
        if(!file_exists($up_dir)){
            mkdir($up_dir,0777);
        }
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)){
            $type = $result[2];
            if(in_array($type,array('pjpeg','jpeg','jpg','gif','bmp','png'))){
                $new_file = $up_dir.date('YmdHis_').rand(1000,9999).'.'.$type;
                if(file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_img)))){
                    $last_file = $this->resize_img($new_file,$width);
                    unlink($new_file);
                    $img_path = substr($last_file,1);
                    return $img_path;
                }else{
                    throw new Exception("图片上传失败");

                }
            }else{
                //文件类型错误
                throw new Exception('图片上传类型错误');
            }

        }else{
            //文件错误
            throw new Exception("文件错误");
        }
    }

    public function resize_img($file,$size)
    {
        $config['image_library'] = 'gd2';
        $config['source_image'] = $file;
        $config['create_thumb'] = TRUE;
        $config['thumb_marker'] = "_".$size;
        $config['maintain_ratio'] = TRUE;
        $config['width']     = $size;

        $this->load->library('image_lib', $config);

        $this->image_lib->resize();
        if ( ! $this->image_lib->resize()){
            throw new Exception($this->image_lib->display_errors());
        } else {
            $type = pathinfo($file,PATHINFO_EXTENSION );
            $len = strlen($type) + 1;
            $last_file = substr($file,0,-$len);
            return $last_file."_".$size.".".$type;
        }
    }
}
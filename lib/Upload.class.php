<?php
class Upload{
    /**
     * @param $dir 上传的目录
     * @param $file 上传的文件
     * @param $fileName 文件名
     * @return array
     */
    public function upload_img($dir,$file,$fileName){
        //date_default_timezone_set("Asia/Shanghai");
        if (!$this->file_type($file)) {
            $msg["msg"]="只支持jpg jpeg png gif图片格式";
            return $msg;
        } else if (!$this->file_size($file)) {
            $msg["msg"]="图片不能大于2M";
            return $msg;
        } else {
            //判断是否上传成功
            if (is_uploaded_file($file["tmp_name"])) {
                //$newFileName = time() . rand(10000, 99999) . substr($file["name"], strrpos($file["name"], "."));
                $newFileName = $fileName . substr($file["name"], strrpos($file["name"], "."));
                //把文件转存到指定目录
                $uploadPath=$dir."/".$newFileName;
                if (move_uploaded_file($file["tmp_name"],$uploadPath)) {
                    $msg["msg"]="success";
                    $msg["pic"]=$newFileName;
                    return $msg;
                } else {
                    $msg["msg"]="上传失败请重试";
                    return $msg;
                }
            }
        }
    }

    private function file_type($file)
    {
        $type= substr($file["name"], strrpos($file["name"], "."));
        $type=substr($type,1);
        if ($type == "gif"
            || $type== "jpg"
            || $type== "png"
            || $type== "jpeg"
        ) {
            return true;
        } else {
            return false;
        }
    }

    private function file_size($file)
    {
        if (($file["size"] < 2 * 1024 * 1024)) {
            return true;
        } else {
            return false;
        }
    }
}

?>

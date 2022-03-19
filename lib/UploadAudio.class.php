<?php
class UploadAudio{
    /**
     * UploadImg constructor.
     * @param $dir 上传的目录
     * @param $file 上传的文件
     * @param $fileName 文件名
     */
    public function upload($dir,$file,$fileName)
    {
        if (!$this->file_type($file)) {
            $msg["msg"]="只支持wav mp3格式";
            return $msg;
        } else if (!$this->file_size($file)) {
            $msg["msg"]="文件过大";
            return $msg;
        } else {
            //判断是否上传成功
            if (is_uploaded_file($file["tmp_name"])) {
               // $fileName = time() . rand(10000, 99999) .".wav";
                 $fileName=$fileName .".wav";
                $uploadPath = $dir . "/" . $fileName;
                //把文件转存到指定目录
                if (move_uploaded_file($file["tmp_name"], $uploadPath)) {
                    $msg["pic"]=$fileName;
                    $msg["msg"]="success";
                    //上传成功
                    return $msg;
                } else {
                    $msg["msg"]="上传失败,请重试";
                    return $msg;
                }
            }
        }
    }

    private function file_type($file)
    {
        $type=$file["type"];
        if ($type == "audio/wav"
            || $type== "audio/mp3"
        ) {
            return true;
        } else {
            return false;
        }
    }

    private function file_size($file)
    {
        if (($file["size"] < 20 * 1024 * 1024)) {
            return true;
        } else {
            return false;
        }
    }
}

?>

<?php
//$file：图片地址
//Filetype: JPEG,PNG,GIF
function base64($file){
    if ($fp = fopen($file, "rb", 0)) {
        $gambar = fread($fp, filesize($file));
        fclose($fp);
        $base64 = chunk_split(base64_encode($gambar));
        // 输出
       return $base64;
    }
}
?>

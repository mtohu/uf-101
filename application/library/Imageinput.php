<?php
class Imageinput{

    public function __construct(){
    }

    public function img_cop($filepath=null,$filename="",$sizear =array(),$watermark=TRUE){
        $width = $sizear[0];
        $height = $sizear[1];
        $meta=null;
        $im = new imagick($filepath);
        if(($im->getImageWidth() > $width || $im->getImageHeight() > $height)){
            //$filename = sprintf("%ss.%s", $file['raw_name'], $file['ext']);
            $im->cropThumbnailImage($width, $height);
            $data = $im->getImageBlob();
            $im->writeImage($filepath);
            $size = $im->getImageSize();
            $meta= array('size' => $size, 'w' => $im->getImageWidth(), 'h' => $im->getImageHeight());
        }

        return $meta;
    }

}

?>

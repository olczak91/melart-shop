<?php

class Helpers {

    public function createThumbnail($imageName,$newWidth,$newHeight,$uploadDir,$moveToDir) {
        
        $path = $uploadDir . '/' . $imageName;

        $mime = getimagesize($path);

        if($mime['mime']=='image/png'){ $src_img = imagecreatefrompng($path); }
        if($mime['mime']=='image/jpg'){ $src_img = imagecreatefromjpeg($path); }
        if($mime['mime']=='image/jpeg'){ $src_img = imagecreatefromjpeg($path); }
        if($mime['mime']=='image/pjpeg'){ $src_img = imagecreatefromjpeg($path); }

        $old_x = imageSX($src_img);
        $old_y = imageSY($src_img);

        if($old_x > $old_y)
        {
            $thumb_w    =   $newWidth;
            $thumb_h    =   $old_y/$old_x*$newWidth;
        }

        if($old_x < $old_y)
        {
            $thumb_w    =   $old_x/$old_y*$newHeight;
            $thumb_h    =   $newHeight;
        }

        if($old_x == $old_y)
        {
            $thumb_w    =   $newWidth;
            $thumb_h    =   $newHeight;
        }

        $dst_img        =   ImageCreateTrueColor($thumb_w,$thumb_h);

        imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);


        // New save location
        $new_thumb_loc = $moveToDir . $imageName;

        if($mime['mime']=='image/png'){ $result = imagepng($dst_img,$new_thumb_loc,7); }
        if($mime['mime']=='image/jpg'){ $result = imagejpeg($dst_img,$new_thumb_loc,80); }
        if($mime['mime']=='image/jpeg'){ $result = imagejpeg($dst_img,$new_thumb_loc,80); }
        if($mime['mime']=='image/pjpeg'){ $result = imagejpeg($dst_img,$new_thumb_loc,80); }

        imagedestroy($dst_img);
        imagedestroy($src_img);
        return $result;
    }

    public function createNicelink($string) {

        return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
    
    }
}
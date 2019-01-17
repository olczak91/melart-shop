<?php

class Images {

    public function getProductImages($product_id) {

        $product_images = new Application_Model_DbTable_ProductImages();

        $images_result = $product_images->fetchAll(
            $product_images
                ->select()
                ->where('belongs_to_product = ?', $product_id)
                ->order('image_order ASC')
        );

        return $images_result;

    }
    public function getImage($image_id) {

        $product_images = new Application_Model_DbTable_ProductImages();
        
        $image_result = $product_images->fetchRow(
            $product_images
                ->select()
                ->where('product_image_id = ?', $image_id)
        );

        return $image_result;

    }
}
<?php

class Products {

    public function getAllMainProducts() {

        $products = new Application_Model_DbTable_Products();
        
        $products_result = $products->fetchAll(
            $products
                ->select()
                ->where('belongs_to_product = ?', 0)
                ->order('product_id DESC')
        );

        return $products_result;
    }
    public function getProduct($product_id) {

        $products = new Application_Model_DbTable_Products();
        
        $product_result = $products->fetchRow(
            $products
                ->select()
                ->where('product_id = ?', $product_id)
        );

        return $product_result;

    }
    public function deleteProduct($product_id) {

        $products = new Application_Model_DbTable_Products();
        $product_images_model = new Application_Model_DbTable_ProductImages();
        $product_images = $this->getProductImages($product_id);
        if (count($product_images)) {
            for ($i=0; $i < count($product_images); $i++) { 
                unlink('products/'.$product_images[$i]->image);
                unlink('products/min/'.$product_images[$i]->image);
                $product_images_model->deleteImage($product_images[$i]->product_image_id);
            }
        }
        $products->deleteProduct($product_id);
    }
}
<?php

class Application_Model_DbTable_ProductImages extends Zend_Db_Table_Abstract {

    protected $_name = 'product_images';

    public function addProductImage($image, $belongs_to_product) {

    	$product = array(
    		'image' => $image,
    		'belongs_to_product' => $belongs_to_product,
    	);
    	$this->insert($product);

    }

    public function deleteImage( $image_id ) {

         $this->delete('product_image_id =' . (int)$image_id);

    }

    public function changeOrder($id, $order) {

        $data = array(
            'image_order' => $order,
        );

        $where = $this->getAdapter()->quoteInto('product_image_id = ?', $id);
        $this->update($data, $where);

    }

}


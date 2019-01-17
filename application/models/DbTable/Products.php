<?php

class Application_Model_DbTable_Products extends Zend_Db_Table_Abstract {

    protected $_name = 'products';

    public function addProduct($product_name, $product_price, $product_description, $belongs_to_category, $product_nicelink, $belongs_to_product) {

    	$product = array(
    		'product_name' => $product_name,
    		'product_price' => $product_price,
    		'product_description' => $product_description,
    		'belongs_to_category' => $belongs_to_category,
    		'product_nicelink' => $product_nicelink,
            'belongs_to_product' => $belongs_to_product,
    	);
    	$this->insert($product);

    }

    public function updateVariant($product_id, $product_name, $product_price, $product_description, $product_nicelink) {

        $product = array(
            'product_name' => $product_name,
            'product_price' => $product_price,
            'product_description' => $product_description,
            'product_nicelink' => $product_nicelink,
        );
        $where = $this->getAdapter()->quoteInto('product_id = ?', $product_id);
        $this->update($product, $where);

    }

    public function updateProduct($product_id, $product_name, $product_price, $product_description, $product_nicelink, $belongs_to_category) {

        $product = array(
            'product_name' => $product_name,
            'product_price' => $product_price,
            'product_description' => $product_description,
            'product_nicelink' => $product_nicelink,
            'belongs_to_category' => $belongs_to_category,
        );
        $where = $this->getAdapter()->quoteInto('product_id = ?', $product_id);
        $this->update($product, $where);

    }

    public function deleteProduct($product_id) {

        $this->delete('product_id =' . (int)$product_id);

    }

}


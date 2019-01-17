<?php

class ProductController extends Zend_Controller_Action
{

    public function init() {

    	Zend_Session::start();
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->active = 'collection';
    }

    public function productAction() {
      
    	$product_id = $this->_getParam('product_id', 0);

    	$categories = new Application_Model_DbTable_Categories();
		$products = new Application_Model_DbTable_Products();
		$this->view->product = $products->fetchRow($products->select()->where('product_id = ?', $product_id));

		if ($this->view->product) {
			$this->view->title = 'Melart Home - Armchairs, sofas - ' . $this->view->product->product_name;
			$product_images = new Application_Model_DbTable_ProductImages();
			$this->view->product_images = $product_images->fetchAll($product_images->select()->where('belongs_to_product = ?', $product_id)->order('image_order ASC'));
            $this->view->messages = $this->_helper->FlashMessenger->getMessages('actions');
            $this->view->parent_category = $categories->fetchRow($categories->select()->where('category_id = ?', $this->view->product->belongs_to_category));
            $slides = new Application_Model_DbTable_Slides();
            $this->view->slides = $slides->fetchAll();
		} else $this->_helper->redirector('index', 'collection');
      
    }

}






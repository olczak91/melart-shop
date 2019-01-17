<?php

class CollectionController extends Zend_Controller_Action
{

    public function init() {

        Zend_Session::start();
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $slides = new Application_Model_DbTable_Slides();
        $this->view->slides = $slides->fetchAll();

    }

    public function indexAction() {
      
        $this->view->active = 'collection';
    	$this->view->title = 'Melart Home - Armchairs, sofas - Collection';
        $products = new Application_Model_DbTable_Products();
        $categories = new Application_Model_DbTable_Categories();
        $this->view->categories = $categories->fetchAll();
    	$this->view->products = $products->fetchAll($products->select()->where('belongs_to_product = ?', 0)->order('product_id DESC'));
      
    }

    public function categoryAction() {

        $category_id = $this->_getParam('category_id', 0);

        $categories = new Application_Model_DbTable_Categories();
        $products = new Application_Model_DbTable_Products();
        $this->view->category = $categories->fetchRow($categories->select()->where('category_id = ?', $category_id));

        if ($this->view->category) {
            $this->view->active = 'collection';
            $this->view->title = 'Melart Home - Armchairs, sofas - ' . $this->view->category->category_name;
            $this->view->categories = $categories->fetchAll();
            $this->view->products = $products->fetchAll($products->select()->where('belongs_to_category = ?', $category_id)->where('belongs_to_product = ?', 0)->order('product_id DESC'));
        } else $this->_helper->redirector('index', 'collection');

    }

}






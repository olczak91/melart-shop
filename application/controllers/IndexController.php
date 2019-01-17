<?php

class IndexController extends Zend_Controller_Action
{

    public function init() {

        Zend_Session::start();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

    public function indexAction() {
      
        $this->view->active = 'home';
    	$this->view->title = 'Melart Home - Armchairs, sofas - Home';
        
    	$products = new Application_Model_DbTable_Products();
        $this->view->products = $products->fetchAll($products->select()->where('belongs_to_product = ?', 0)->order('product_id DESC')->limit(15));
        $slides = new Application_Model_DbTable_Slides();
        $this->view->slides = $slides->fetchAll();
      
    }

}






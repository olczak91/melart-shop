<?php

class AboutController extends Zend_Controller_Action
{

    public function init() {

    	Zend_Session::start();
        $this->view->baseUrl = $this->_request->getBaseUrl();
        
    }

    public function indexAction() {
      
      	$this->view->active = 'about';
    	$this->view->title = 'Melart Home - Armchairs, sofas - About';
    	$slides = new Application_Model_DbTable_Slides();
        $this->view->slides = $slides->fetchAll();

    }

}






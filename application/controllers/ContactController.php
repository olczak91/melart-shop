<?php

class ContactController extends Zend_Controller_Action
{

    public function init() {

    	Zend_Session::start();
        $this->view->baseUrl = $this->_request->getBaseUrl();

    }

    public function indexAction() {

    	$this->view->active = 'contact';
    	$this->view->title = 'Melart Home - Armchairs, sofas - Contact';
    	$slides = new Application_Model_DbTable_Slides();
        $this->view->slides = $slides->fetchAll();
      
    }

}






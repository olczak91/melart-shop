<?php

class TermsAndConditionsController extends Zend_Controller_Action
{

    public function init() {

    	Zend_Session::start();
        $this->view->baseUrl = $this->_request->getBaseUrl();

    }

    public function indexAction() {

    	$this->view->active = 'terms';
    	$this->view->title = 'Melart Home - Armchairs, sofas - Terms and Conditions';
    	$slides = new Application_Model_DbTable_Slides();
        $this->view->slides = $slides->fetchAll();
      
    }

}






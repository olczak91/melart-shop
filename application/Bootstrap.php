<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

	protected function _initRoutes() {
		
	    $router = Zend_Controller_Front::getInstance()->getRouter();
	    include APPLICATION_PATH . "/configs/routes.php";

	    $category = new Zend_Controller_Router_Route_Regex(
	        'collection/(.+),(\d+)',
	        array(
	            'controller' => 'collection',
	            'action'     => 'category'
	        ),
	        array(
	            2 => 'category_id',
	        ),
	        '%s.html'
    	);

    	$product = new Zend_Controller_Router_Route_Regex(
	        'product/(.+),(\d+)',
	        array(
	            'controller' => 'product',
	            'action'     => 'product'
	        ),
	        array(
	            2 => 'product_id',
	        ),
	        '%s.html'
    	);

    	$router->addRoute('categoryArchive', $category);
    	$router->addRoute('productArchive', $product);

	}
}


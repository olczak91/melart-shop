<?php

class CartController extends Zend_Controller_Action
{

    public function init() {

        Zend_Session::start();
        $this->view->baseUrl = $this->_request->getBaseUrl();
        
    }

    public function indexAction() {
      
		$this->view->title = 'Melart Home - Armchairs, sofas - Cart';

        if (isset($_SESSION['cart'])) {

            if (count($_SESSION['cart'] > 0)) {
            
                $products = new Application_Model_DbTable_Products();
                foreach ($_SESSION['cart'] as $key => $value) {
                    $products_cart[] = $products->fetchRow($products->select()->where('product_id = ?', $key));
                }
                $this->view->products = $products_cart;

                $total_price = 0;
                foreach ($_SESSION['cart'] as $key => $value) {
                    $product = $products->fetchRow($products->select()->where('product_id = ?', $key));
                    $total_price = number_format($total_price + $product->product_price * $_SESSION['cart'][$key]['product_quantity'], 2, ".", "");
                }

                $this->view->total_cart_price = $total_price;

                if (isset($_SESSION['client'])) {
                    $this->view->client = $_SESSION['client'];
                }

            }

        }

    }

    public function confirmationAction() {

    	$this->view->title = 'Melart Home - Armchairs, sofas - Order Confirmation';

        if($this->getRequest()->isPost()) {

            if (isset($_SESSION['cart'])) {

                if (count($_SESSION['cart'] > 0)) {

                    $filter = new Zend_Filter_StripTags();
                    $first_name = $filter->filter($this->_request->getPost('first_name'));
                    $last_name = $filter->filter($this->_request->getPost('last_name'));
                    $company_name = $filter->filter($this->_request->getPost('company_name'));
                    $country = $filter->filter($this->_request->getPost('country'));
                    $city = $filter->filter($this->_request->getPost('city'));
                    $address = $filter->filter($this->_request->getPost('address'));
                    $appartment = $filter->filter($this->_request->getPost('appartment'));
                    $code = $filter->filter($this->_request->getPost('code'));
                    $telephone = $filter->filter($this->_request->getPost('telephone'));
                    $email = $filter->filter($this->_request->getPost('email'));
                    $message = $filter->filter($this->_request->getPost('message'));

                    if( empty($first_name) || empty($last_name) || empty($country) || empty($city) || empty($address) || empty($code) || empty($telephone) || empty($email) ) {
                        $this->_helper->redirector('index', 'cart');
                    } else {

                        $data = array(
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'company_name' => $company_name,
                            'country' => $country,
                            'city' => $city,
                            'address' => $address,
                            'appartment' => $appartment,
                            'code' => $code,
                            'telephone' => $telephone,
                            'email' => $email,
                            'message' => $message,
                        );

                        $_SESSION['client'] = $data;
                        $this->view->client = $_SESSION['client'];

                        $products = new Application_Model_DbTable_Products();
                        foreach ($_SESSION['cart'] as $key => $value) {
                            $products_cart[] = $products->fetchRow($products->select()->where('product_id = ?', $key));
                        }
                        $this->view->products = $products_cart;

                        $total_price = 0;
                        foreach ($_SESSION['cart'] as $key => $value) {
                            $product = $products->fetchRow($products->select()->where('product_id = ?', $key));
                            $total_price = number_format($total_price + $product->product_price * $_SESSION['cart'][$key]['product_quantity'], 2, ".", "");
                        }

                        $this->view->total_cart_price = $total_price;

                    }

                } else $this->_helper->redirector('index', 'cart');

            } else $this->_helper->redirector('index', 'cart');

        } else $this->_helper->redirector('index', 'cart');
    	
    }

    public function updateCartAction() {

         $this->_helper->viewRenderer->setNoRender();
         if (isset($_POST['quantity']) && isset($_POST['product_id'])) {

            $filter = new Zend_Filter_StripTags();
            $product_id = $filter->filter($this->_request->getPost('product_id'));
            $product_quantity = $filter->filter($this->_request->getPost('quantity'));

            $products = new Application_Model_DbTable_Products();
            $product = $products->fetchRow($products->select()->where('product_id = ?', $product_id));

            if ($product) {

                $filter_options = array( 
                    'options' => array(
                        'min_range' => 1,
                        'max_range' => 100,
                    )
                );

                if( filter_var( $product_quantity, FILTER_VALIDATE_INT, $filter_options ) !== FALSE) {

                    foreach ($_SESSION['cart'] as $key => $value) {
                        if ($key == $product_id) {

                           $total_product_price = number_format($product_quantity * $product->product_price, 2, ".", "");
                           $_SESSION['cart'][$key]['product_quantity'] = $product_quantity;
                           $_SESSION['cart'][$key]['total_product_price'] = $total_product_price;

                           $this->_helper->redirector('index', 'cart');

                        }
                    }

                } else $this->_helper->redirector('index', 'collection');

            } else $this->_helper->redirector('index', 'collection');

         } else $this->_helper->redirector('index', 'collection');

    }

    public function addToCartAction() {

        $this->_helper->viewRenderer->setNoRender();
        if (isset($_POST['qty']) && isset($_POST['product_id'])) {
            
            $filter = new Zend_Filter_StripTags();
            $product_id = $filter->filter($this->_request->getPost('product_id'));
            $product_quantity = $filter->filter($this->_request->getPost('qty'));

            $products = new Application_Model_DbTable_Products();
            $product = $products->fetchRow($products->select()->where('product_id = ?', $product_id));

            if ($product) {
            
                $filter_options = array( 
                    'options' => array(
                        'min_range' => 1,
                        'max_range' => 100,
                    )
                );

                if( filter_var( $product_quantity, FILTER_VALIDATE_INT, $filter_options ) !== FALSE) {

                    $total_product_price = number_format($product_quantity * $product->product_price, 2, ".", "");

                    $parameters = array(
                        'product_id' => $product_id,
                        'product_quantity' => $product_quantity,
                        'total_product_price' => $total_product_price,
                    );

                    if ( !empty($_SESSION['cart'][$product_id])  ) {
                        $_SESSION['cart'][$product_id]['product_quantity'] += $product_quantity;
                        $_SESSION['cart'][$product_id]['total_product_price'] += number_format($product_quantity * $product->product_price, 2, ".", "");
                    }

                    else {
                        $_SESSION['cart'][$product_id] = $parameters;
                    }

                    $data = array('product_id' => $product_id, );
                    $this->_helper->FlashMessenger->addMessage("<p class='message'>Product has been added to cart</p>", 'actions');

                    if ($product->belongs_to_product == 0) {
                        header('Location: '. $this->view->baseUrl . '/product/' . $product->product_nicelink . ',' . $product->product_id );
                    } else header('Location: '. $this->view->baseUrl . '/variant/' . $product->product_nicelink . ',' . $product->product_id );

                } else $this->_helper->redirector('index', 'collection');

            } else $this->_helper->redirector('index', 'collection');

        } else $this->_helper->redirector('index', 'collection');
    }

    public function removeProductAction() {

        $this->_helper->viewRenderer->setNoRender();
        $product_id = $this->_getParam('id', 0);

        foreach ($_SESSION['cart'] as $key => $value) {
            if ($key == $product_id) {
               unset($_SESSION['cart'][$key]);
            }
        }
        $this->_helper->redirector('index', 'cart');

    }

    public function processOrderAction() {

        $this->_helper->viewRenderer->setNoRender();
        if (isset($_SESSION['cart'])) {
            if (count($_SESSION['cart'] > 0)) {
                if (isset($_SESSION['client'])) {
                    if ( !empty($_SESSION['client']['first_name']) || !empty($_SESSION['client']['last_name']) || !empty($_SESSION['client']['telephone']) || !empty($_SESSION['client']['email']) ) {

                        $name = 'Micadoni Home e-shop';
                        $email ='shop@micadoni.com';
                        $order_number = mt_rand(100000,999999);
                        $subject = 'Micadoni Home - new order - ' . $order_number;
                        
                        $message = '
                        <html>
                            <head>
                            </head>
                            <body>
                                <img style="display: block; margin: auto auto 30px auto;" src="http://www.micadoni.com/img/logo.jpg" width="100">
                                <p style="width:100%; padding: 5px 0; font-size: 13px; font-weight: bold; background: #000; color: #fff; text-align: center;">Order number: '.$order_number.'</p>
                                <h1 style="width:100%; margin-bottom: 20px; font-size: 22px; font-weight: bold;">Hello '.$_SESSION['client']['first_name'].',</h1>
                                <p style="width:100%; padding: 5px 0; font-size: 13px; font-weight: bold; background: #000; color: #fff; text-align: center;">Client details:</p>
                                <ul style="margin-left: 0; padding-left: 0;">
                                    <li style="display: block; font-size: 13px; margin-bottom: 5px; color: #000;"><strong>First name</strong>: '.$_SESSION['client']['first_name'].'</li>
                                    <li style="display: block; font-size: 13px; margin-bottom: 5px; color: #000;"><strong>Last name</strong>: '.$_SESSION['client']['last_name'].'</li>';
                                    if (!empty($_SESSION['client']['company_name'])) {
                                        $message .= '<li style="display: block; font-size: 13px; margin-bottom: 5px; color: #000;"><strong>Company name</strong>: '.$_SESSION['client']['company_name'].'</li>';
                                    }
                                    $message .='
                                    <li style="display: block; font-size: 13px; margin-bottom: 5px; color: #000;"><strong>Country</strong>: '.$_SESSION['client']['country'].'</li>
                                    <li style="display: block; font-size: 13px; margin-bottom: 5px; color: #000;"><strong>City</strong>: '.$_SESSION['client']['city'].'</li>
                                    <li style="display: block; font-size: 13px; margin-bottom: 5px; color: #000;"><strong>Address</strong>: '.$_SESSION['client']['address'].'</li>';
                                    if (!empty($_SESSION['client']['apartment'])) {
                                        $message .= '<li style="display: block; font-size: 13px; margin-bottom: 5px; color: #000;"><strong>Company name</strong>: '.$_SESSION['client']['apartment'].'</li>';
                                    }
                                    $message .= '
                                    <li style="display: block; font-size: 13px; margin-bottom: 5px; color: #000;"><strong>Postal code</strong>: '.$_SESSION['client']['code'].'</li>
                                    <li style="display: block; font-size: 13px; margin-bottom: 5px; color: #000;"><strong>Telephone</strong>: '.$_SESSION['client']['telephone'].'</li>
                                    <li style="display: block; font-size: 13px; margin-bottom: 5px; color: #000;"><strong>E-mail</strong>: '.$_SESSION['client']['email'].'</li>';
                                    if (!empty($_SESSION['client']['message'])) {
                                        $message .= '<li style="display: block; font-size: 13px; margin-bottom: 5px; color: #000;"><strong>Message</strong>: '.$_SESSION['client']['message'].'</li>';
                                    }
                                    $message .= '
                                </ul>
                                <p style="width:100%; padding: 5px 0; margin: 10px 0; font-size: 13px; font-weight: bold; background: #000; color: #fff; text-align: center;">Order details:</p>';

                                $products = new Application_Model_DbTable_Products();
                                foreach ($_SESSION['cart'] as $key => $value) {
                                    $product = $products->fetchRow($products->select()->where('product_id = ?', $key));
                                $message .= '<div style="padding: 5px 0; border-bottom: 1px solid rgb(220,220,220); font-size: 13px;">
                                                <strong>Product name:</strong> '.$product->product_name.'<br>
                                                <span><strong>Quantity:</strong> '.$_SESSION['cart'][$key]['product_quantity'].'</span>
                                            </div>';

                                }

                                $total_price = 0;
                                foreach ($_SESSION['cart'] as $key => $value) {
                                    $product = $products->fetchRow($products->select()->where('product_id = ?', $key));
                                    $total_price = number_format($total_price + $product->product_price * $_SESSION['cart'][$key]['product_quantity'], 2, ".", "");
                                }

                            $message .='
                                <h2 style="width:100%; margin-bottom: 20px; font-size: 22px; font-weight: bold;">Total: '.$total_price.' &euro;</h2>
                                <p style="font-size: 13px; line-height: 26px;">Thank you for order! If ou have any queries please do not hestitate to contact us:<br>shop@micadoni.com.</p>
                            </body>
                        </html>';

                        $headers  = 'MIME-Version: 1.0' . "\r\n";
                        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                        $headers .= 'From: Micadoni Home e-shop <shop@micadoni.com>' . "\r\n";
                        $headers .= 'Reply-To: '.$email. "\r\n";
                        $headers .= 'X-Mailer: PHP/' . phpversion();
                    
                        if(mail("shop@micadoni.com", $subject, $message, $headers) && mail($_SESSION['client']['email'], $subject, $message, $headers)) {
                            
                            $this->_helper->FlashMessenger->addMessage("<p id='response'>".$success."</p>", 'actions');
                            unset($_SESSION['cart']);
                            $this->_helper->redirector('success', 'cart');
                            
                        }
                        
                       
                        else {
                            
                            $this->_helper->FlashMessenger->addMessage("<p id='response'>".$error."</p>", 'actions');
                            $this->_helper->redirector('failure', 'cart');
                            
                        }

                    } else $this->_helper->redirector('index', 'cart');
                } else $this->_helper->redirector('index', 'cart');
            } else $this->_helper->redirector('index', 'cart');
        } else $this->_helper->redirector('index', 'cart');

    }

    public function successAction() {
        $this->view->title = 'Micadoni Home - Armchairs, sofas - Success';
    }

    public function failureAction() {
        $this->view->title = 'Micadoni Home - Armchairs, sofas - Failure';
    }

}






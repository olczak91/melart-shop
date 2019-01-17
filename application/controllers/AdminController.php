<?php

class AdminController extends Zend_Controller_Action
{

    public function init() {
        //$this->_helper->redirector->setUseAbsoluteUri(true);
        $this->view->baseUrl = $this->_request->getBaseUrl();

        Zend_Session::start();

        require_once 'Zend/Custom/Slides.php';
        require_once 'Zend/Custom/Categories.php';
        require_once 'Zend/Custom/Products.php';
        require_once 'Zend/Custom/Images.php';
        require_once 'Zend/Custom/Helpers.php';

        $this->slides = new Slides();
        $this->categories = new Categories();
        $this->products = new Products();
        $this->images = new Images();
        $this->helpers = new Helpers();

    }

    public function indexAction() {
      
    	$this->view->title = 'Admin Panel';
    	if (isset($_SESSION['admin'])) {
    		$this->_helper->redirector('panel', 'admin');
    	}
        $messages = $this->_helper->FlashMessenger->getMessages('actions');
    	if (count($messages)) {
    		$this->view->messages = $this->_helper->FlashMessenger->getMessages('actions');
    	}

    }
    public function panelAction() {

        $this->checkIfUserIsLogged();
        $this->view->title = 'Panel';
        $messages = $this->_helper->FlashMessenger->getMessages('actions');
        if (count($messages)) {
            $this->view->messages = $this->_helper->FlashMessenger->getMessages('actions');
        }

        $this->view->categories = $this->categories->getAllCategories();
        $this->view->main_products = $this->products->getAllMainProducts();

        if($this->getRequest()->isPost()) {

            $filter = new Zend_Filter_StripTags();
            $product_name = $filter->filter($this->_request->getPost('product_name'));
            $product_price = $filter->filter($this->_request->getPost('product_price'));
            $product_description = $this->_request->getPost('product_description');
            $belongs_to_category = $filter->filter($this->_request->getPost('belongs_to_category'));
            $belongs_to_product = $filter->filter($this->_request->getPost('belongs_to_product'));
            $product_nicelink = $this->helpers->createNicelink($product_name);

            if (empty($product_name) || empty($product_price) || empty($product_description) || empty($belongs_to_category)) {
                $this->_helper->FlashMessenger->addMessage("<p class='message error'>Fill all required fields.</p>", 'actions');
                $this->_helper->redirector('panel', 'admin');
            } else {
                if ($belongs_to_product != 0) {
                    $belongs_to_category = 0;
                }
                $products = new Application_Model_DbTable_Products();
                $products->addProduct($product_name, $product_price, $product_description, $belongs_to_category, $product_nicelink, $belongs_to_product);

                $last_inserted_product_id = $products->getAdapter()->lastInsertId();
                
                if ( $last_inserted_product_id > 0 ) {
                    $product_images = new Application_Model_DbTable_ProductImages();
                    $uploaddir = 'products/';
                    $uploaddirthumb = 'products/min/';
                    if ($_FILES['images']['size'][0] > 0) {
                        
                        for($i=0; $i < count($_FILES['images']['size']); $i++) {
                            $temp = explode(".", $_FILES["images"]["name"][$i]);
                            $image = time() . '_' .$i. '.' . end($temp);
                            move_uploaded_file($_FILES['images']['tmp_name'][$i], $uploaddir. $image);
                            $this->helpers->createThumbnail($image, 500, 300, $uploaddir, $uploaddirthumb);
                            $product_images->addProductImage( $image, $last_inserted_product_id );
                        }
                        $this->_helper->FlashMessenger->addMessage("<p class='message success'>Product added succesfully.</p>", 'actions');
                        $this->_helper->redirector('panel', 'admin');

                    } else {
                        $this->_helper->FlashMessenger->addMessage("<p class='message error'>You have to add images - edit product.</p>", 'actions');
                        $this->_helper->redirector('panel', 'admin');
                    }

                } else {
                    $this->_helper->FlashMessenger->addMessage("<p class='message error'>Ann error occured - images not added. Product addded sucessfully.</p>", 'actions');
                    $this->_helper->redirector('panel', 'admin');
                }
            }
        }
    }
    public function sortProductImagesAction() {

        $this->checkIfUserIsLogged();
        $this->_helper->viewRenderer->setNoRender();

        $data = 0;
        $product_images = new Application_Model_DbTable_ProductImages();
        $this->view->data = 1;
        foreach ($_POST["order"] as $id => $order) {
            if (strlen($order) > 0) {
                $product_images->changeOrder($id, $order);
                $data = 1;
            }
        }
        echo $data;

    }
    public function deleteImageAction() {

        $this->checkIfUserIsLogged();
        $this->_helper->viewRenderer->setNoRender();

        $product_id = $this->_getParam('product_id', 0);
        $image_id = $this->_getParam('image_id', 1);
        
        $product = $this->products->getProduct($product_id);
        $image = $this->images->getImage($image_id);

        if ($product) {
            if ($image) {
                $params = array('id' => $product_id,);
                unlink('products/'.$image->image);
                unlink('products/min/'.$image->image);
                $product_images = new Application_Model_DbTable_ProductImages();
                $product_images->deleteImage($image_id);
                $this->_helper->FlashMessenger->addMessage("<p class='message success'>Image deleted succesfully.</p>", 'actions');
                $this->_helper->redirector('edit-product', 'admin', '', $params);
            } else $this->_helper->redirector('manage-products', 'admin');
        } else $this->_helper->redirector('manage-products', 'admin');
    }
    public function deleteProductAction() {

        $this->checkIfUserIsLogged();
        $this->_helper->viewRenderer->setNoRender();

        $product_id = $this->_getParam('id', 0);
        $product = $this->products->getProduct($product_id);

        if (!$product) {
                $this->_helper->redirector('manage-products', 'admin');
            } else {
                $this->deleteProduct($product_id);
                $this->_helper->FlashMessenger->addMessage("<p class='message success'>Product removed successfully.</p>", 'actions');
                $this->_helper->redirector('manage-products', 'admin');
        }
    }
    public function addProductImagesAction() {

        $this->checkIfUserIsLogged();
        $this->_helper->viewRenderer->setNoRender();
        if($this->getRequest()->isPost()) {

            $filter = new Zend_Filter_StripTags();
            $product_id = $filter->filter($this->_request->getPost('product_id'));
            $product = $this->products->getProduct($product_id);
            
            if (!$product) {
                $this->_helper->redirector('manage-products', 'admin');
            } else {

                $params = array('id' => $product_id, );

                $uploaddir = 'products/';
                $uploaddirthumb = 'products/min/';
                if ($_FILES['images']['size'][0] > 0) {                  
                    $product_images = new Application_Model_DbTable_ProductImages();
                    for($i=0; $i < count($_FILES['images']['size']); $i++) {
                        $temp = explode(".", $_FILES["images"]["name"][$i]);
                        $image = time() . '_' .$i. '.' . end($temp);
                        move_uploaded_file($_FILES['images']['tmp_name'][$i], $uploaddir. $image);
                        $this->helpers->createThumbnail($image, 500, 300, $uploaddir, $uploaddirthumb);
                        $product_images->addProductImage( $image, $product_id );
                    }
                    $this->_helper->FlashMessenger->addMessage("<p class='message success'>Product images added succesfully.</p>", 'actions');
                    $this->_helper->redirector('edit-product', 'admin', '', $params);

                } else {
                    $this->_helper->FlashMessenger->addMessage("<p class='message error'>You have to add images.</p>", 'actions');
                    $this->_helper->redirector('edit-product', 'admin', '', $params);
                }

            }

        } else $this->_helper->redirector('manage-products', 'admin');

    }
    public function editProductAction() {

        $this->checkIfUserIsLogged();
        $this->view->title = 'Edit product';
        $product_id = $this->_getParam('id', 0);
        $this->view->product = $product = $this->products->getProduct($product_id);
        if (!$product) {
            $this->_helper->redirector('manage-products', 'admin');
        } else {
            $messages = $this->_helper->FlashMessenger->getMessages('actions');
            if (count($messages)) {
                $this->view->messages = $this->_helper->FlashMessenger->getMessages('actions');
            }
            $this->view->category = $this->categories->getCategory($product->belongs_to_category);
            $this->view->categories = $this->categories->getAllCategories();
            $this->view->images = $this->images->getProductImages($product_id);
        }
    }
    public function updateProductAction() {

        $this->checkIfUserIsLogged();
        $this->_helper->viewRenderer->setNoRender();
        if($this->getRequest()->isPost()) {

            $filter = new Zend_Filter_StripTags();
            $product_id = $filter->filter($this->_request->getPost('product_id'));
            $product_name = $filter->filter($this->_request->getPost('product_name'));
            $product_price = $filter->filter($this->_request->getPost('product_price'));
            $product_description = $this->_request->getPost('product_description');
            $belongs_to_category = $this->_request->getPost('belongs_to_category');
            $product_nicelink = $this->helpers->createNicelink($product_name);
            $params = array('id' => $product_id, );

            if (empty($product_name) || empty($product_price) || empty($product_description) || empty($belongs_to_category)) {
                $this->_helper->FlashMessenger->addMessage("<p class='message error'>Fill all required fields.</p>", 'actions');
                $this->_helper->redirector('edit-product', 'admin', '', $params);
            } else {
                $products = new Application_Model_DbTable_Products();
                $products->updateProduct($product_id, $product_name, $product_price, $product_description, $product_nicelink, $belongs_to_category);
                $this->_helper->FlashMessenger->addMessage("<p class='message success'>Product updated successfully.</p>", 'actions');
                $this->_helper->redirector('edit-product', 'admin', '', $params);
            }

        } else $this->_helper->redirector('manage-products', 'admin');
    }
    public function manageProductsAction() {

        $this->checkIfUserIsLogged();
        $this->view->title = 'Manage products';
        $messages = $this->_helper->FlashMessenger->getMessages('actions');
        if (count($messages)) {
            $this->view->messages = $this->_helper->FlashMessenger->getMessages('actions');
        } 
        $this->view->products = $this->products->getAllMainProducts();

    }
    public function deleteCategoryAction() {

        $this->checkIfUserIsLogged();
        $category_id = $this->_getParam('id', 0);
        $this->view->category = $category = $this->categories->getCategory($category_id);
        if (!$category) {
            $this->_helper->redirector('manage-categories', 'admin');
        } else {
            $categories = new Application_Model_DbTable_Categories();
            $products = new Application_Model_DbTable_Products();
        
            $category_products = $products->fetchAll(
                $products
                    ->select()
                    ->where('belongs_to_category = ?', $category_id)
            );
            for ($i=0; $i < count($category_products); $i++) { 
                $this->deleteProduct($category_products[$i]->product_id);
            }
            $categories->deleteCategory($category_id);
            $this->_helper->FlashMessenger->addMessage("<p class='message success'>Category deleted succesfully.</p>", 'actions');
            $this->_helper->redirector('manage-categories', 'admin');
        }

    }
    public function editCategoryAction() {

        $this->checkIfUserIsLogged();
        $this->view->title = 'Edit category';
        $category_id = $this->_getParam('id', 0);
        $this->view->category = $category = $this->categories->getCategory($category_id);

        if (!$category) {
            $this->_helper->redirector('manage-categories', 'admin');
        } else {
            $messages = $this->_helper->FlashMessenger->getMessages('actions');
            if (count($messages)) {
                $this->view->messages = $this->_helper->FlashMessenger->getMessages('actions');
            }
        }

    }
    public function updateCategoryAction() {

        $this->_helper->viewRenderer->setNoRender();
        if($this->getRequest()->isPost()) {

            $filter = new Zend_Filter_StripTags();
            $category_id = $filter->filter($this->_request->getPost('category_id'));
            $category_name = $filter->filter($this->_request->getPost('category_name'));
            $category_description = $filter->filter($this->_request->getPost('category_description'));
            $params = array('id' => $category_id,);

            if (empty($category_name)) {
                $this->_helper->FlashMessenger->addMessage("<p class='message error'>Fill all required fields.</p>", 'actions');
                $this->_helper->redirector('edit-category', 'admin', '', $params);
            } else {
                if (empty($category_description)) {
                    $category_description = NULL;
                }
                $category_nicelink = $this->helpers->createNicelink($category_name);
                $categories = new Application_Model_DbTable_Categories();
                $categories->updateCategory($category_id, $category_name, $category_description, $category_nicelink);
                $this->_helper->FlashMessenger->addMessage("<p class='message success'>Category updated succesfully.</p>", 'actions');
                $this->_helper->redirector('edit-category', 'admin', '', $params);
            }

        } else $this->_helper->redirector('manage-categories', 'admin');

    }
    public function manageCategoriesAction() {

        $this->checkIfUserIsLogged();
        $this->view->title = 'Manage categories';
        $this->view->categories = $this->categories->getAllCategories();

    }
    public function addCategoryAction() {

        $this->checkIfUserIsLogged();
        $this->view->title = 'Add category';
        $messages = $this->_helper->FlashMessenger->getMessages('actions');
        if (count($messages)) {
            $this->view->messages = $this->_helper->FlashMessenger->getMessages('actions');
        }

        if($this->getRequest()->isPost()) {

            $filter = new Zend_Filter_StripTags();
            $category_name = $filter->filter($this->_request->getPost('category_name'));
            $category_description = $filter->filter($this->_request->getPost('category_description'));

            if (empty($category_name)) {
                $this->_helper->FlashMessenger->addMessage("<p class='message error'>Fill all required fields.</p>", 'actions');
                $this->_helper->redirector('add-category', 'admin');
            } else {
                if (empty($category_description)) {
                    $category_description = NULL;
                }
                $category_nicelink = $this->helpers->createNicelink($category_name);
                $categories = new Application_Model_DbTable_Categories();
                $categories->createCategory($category_name, $category_description, $category_nicelink);
                $this->_helper->FlashMessenger->addMessage("<p class='message success'>Category added succesfully.</p>", 'actions');
                $this->_helper->redirector('add-category', 'admin');
            }
        }

    }
    public function loginAction() {

        $this->_helper->viewRenderer->setNoRender();
        if($this->getRequest()->isPost()) {
            $filter = new Zend_Filter_StripTags();
            $login = $filter->filter($this->_request->getPost('login'));
            $password = $filter->filter($this->_request->getPost('password'));

            if ( empty($login) || empty($password) ) {
                $this->_helper->FlashMessenger->addMessage("<p class='message error'>Fill all required fields.</p>", 'actions');
                $this->_helper->redirector('index', 'admin');
            }

            else {
                $admins = new Application_Model_DbTable_Admins();
                $admin = $admins->fetchRow($admins->select()->where('login = ?', $login));

                if (hash_equals($admin->password, crypt($password, $admin->password))) {
                    $_SESSION['admin'] = $admin->id;
                    $this->_helper->redirector('panel', 'admin');
                }

                else {
                    $this->_helper->FlashMessenger->addMessage("<p class='message error'>Incorrect data.</p>", 'actions');
                    $this->_helper->redirector('index', 'admin');
                }
            }

        } else $this->_helper->redirector('index', 'admin');

    }
    public function manageSlidesAction() {

        $this->checkIfUserIsLogged();
        $this->view->slides = $this->slides->getAllSlides();
        $this->view->title = 'Manage slides';

    }
    public function updateSlideAction() {

        $this->checkIfUserIsLogged();
        $this->_helper->viewRenderer->setNoRender();
        
        if($this->getRequest()->isPost()) {

            $filter = new Zend_Filter_StripTags();
            $slide_id = $filter->filter($this->_request->getPost('slide_id'));
            $slide = $this->slides->getSlide($slide_id);
            if (!$slide_id) {

               $this->_helper->redirector('manage-slides', 'admin');
               
            } else {

                $params = array('id' => $slide_id, );

                $uploaddir = 'slides/';

                if ($_FILES['image']['size'] > 0) {

                    $slides = new Application_Model_DbTable_Slides();
                    $temp = explode(".", $_FILES["image"]["name"]);
                    $image = time().'.'. end($temp);
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploaddir. $image);
                    unlink('slides/' . $slide->image);
                    $slides->updateSlide( $slide_id, $image );

                    $this->_helper->FlashMessenger->addMessage("<p class='message success'>Slide image updated succesfully.</p>", 'actions');
                    $this->_helper->redirector('edit-slide', 'admin', '', $params);

                } else {
                    $this->_helper->FlashMessenger->addMessage("<p class='message success'>Image required.</p>", 'actions');
                    $this->_helper->redirector('edit-slide', 'admin', '', $params);
                }

            }

        } else $this->_helper->redirector('manage-products', 'admin');

    }
    public function editSlideAction() {

        $this->checkIfUserIsLogged();
        $this->view->title = 'Edit slide';
        $slide_id = $this->_getParam('id', 0);
        $this->view->slides = $slide = $this->slides->getSlide($slide_id);
        if (!$slide) {
            $this->_helper->redirector('manage-slides', 'admin');
        } else {
            $messages = $this->_helper->FlashMessenger->getMessages('actions');
            if (count($messages)) {
                $this->view->messages = $this->_helper->FlashMessenger->getMessages('actions');
            }
        }
    }
    public function logoutAction() {

        session_unset();
        $this->_helper->redirector('index', 'admin');

    }
    private function checkIfUserIsLogged() {

        if ( !isset($_SESSION['admin']) ) {
            $this->_helper->redirector('index', 'admin');
        }

    }
}






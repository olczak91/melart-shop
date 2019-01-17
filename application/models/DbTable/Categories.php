<?php 
class Application_Model_DbTable_Categories extends Zend_Db_Table_Abstract {

    protected $_name = 'categories';

    public function createCategory($category_name, $category_description, $category_nicelink) {

    	$category = array(
    		'category_name' => $category_name,
    		'category_description' => $category_description,
    		'category_nicelink' => $category_nicelink,
    	);
    	$this->insert($category);

    }

    public function updateCategory($category_id, $category_name, $category_description, $category_nicelink) {

    	$category = array(
    		'category_name' => $category_name,
    		'category_description' => $category_description,
    		'category_nicelink' => $category_nicelink,
    	);
    	$where = $this->getAdapter()->quoteInto('category_id = ?', $category_id);
    	$this->update($category, $where);

    }

    public function deleteCategory($category_id) {

        $this->delete('category_id =' . (int)$category_id);

    }

}


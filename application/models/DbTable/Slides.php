<?php

class Application_Model_DbTable_Slides extends Zend_Db_Table_Abstract {

    protected $_name = 'slides';

    public function updateSlide($slide_id, $image) {

    	$slide = array(
    		'image' => $image,
    	);
    	$where = $this->getAdapter()->quoteInto('slide_id = ?', $slide_id);
    	$this->update($slide, $where);

    }


}


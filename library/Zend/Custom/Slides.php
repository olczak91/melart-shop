<?php 

class Slides {

    public function getAllSlides() {
        $slides = new Application_Model_DbTable_Slides();
        $slides_result = $slides->fetchAll(
            $slides
                ->select()
                ->order('slide_id ASC')
        );

        return $slides_result;
    }
    public function getSlide($slide_id) {
        $slides = new Application_Model_DbTable_Slides();
        $slides_result = $slides->fetchRow(
            $slides
                ->select()
                ->where('slide_id = ?', $slide_id)
        );

        return $slides_result;
    }
}
<?php

class Categories {

    public function getAllCategories() {

        $categories = new Application_Model_DbTable_Categories();
        
        $categories_result = $categories->fetchAll(
            $categories
                ->select()
                ->order('category_id DESC')
        );

        return $categories_result;
    }
    public function getCategory($category_id) {

        $categories = new Application_Model_DbTable_Categories();

        $category = $categories->fetchRow(
            $categories
                ->select()
                ->where('category_id = ?', $category_id)
        );

        return $category;

    }
}
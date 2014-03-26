<?php

class ModelbaseModel {
    public $db;
    public function __construct() {
    
        $this->db = Yaf_Registry::get("dbadapter");
    }

    public function getTableAritcleContents($aid = 0){
        $mod = $aid % 10;
        return "articles_contents".$mod;
    }   
    
}

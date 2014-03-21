<?php

class ModelbaseModel {
    public $db;
    public function __construct() {
    
        $this->db = Yaf_Registry::get("dbadapter");
    }   
    
}

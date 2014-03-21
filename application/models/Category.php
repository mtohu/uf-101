<?php

class CategoryModel extends ModelbaseModel{
    public function __construct() {
        parent::__construct();
    }   

    public function getCategoryList($fid = -1){
        $wheresql = "";
        if($fid >= 0)
            $wheresql = " AND fid =".intval($fid);

        $sql = sprintf(" SELECT * FROM category WHERE status = 1 %s ",$wheresql);

        
        $query = $this->db->query($sql);
        if($this->db->num_rows($query) < 1 )
            return false;

        $lists = $this->db->fetch_all($sql);
        return $lists;
    }

    public function getCategoryOne($cid = 0){
        if(!$cid)
            return false;

        $sql = sprintf("SELECT * FROM category WHERE id = %d LIMIT 1",$cid);
        $query = $this->db->query($sql);
        if($this->db->num_rows($query) < 1 )
            return false;

        $row   = $this->db->fetch_array($query);
        return $row;
    }

    public function delCategory($cid = 0){
        if(!$cid)
            return false;

        $sql = sprintf("DELETE FROM category WHERE id = %d LIMIT 1",$cid);
        $query = $this->db->query($sql);
    }

}

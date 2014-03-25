<?php

class CategoryModel extends ModelbaseModel{
    public function __construct() {
        parent::__construct();
    }   

    public function getCategoryList($fid = -1,$status = 1,$order = " short DESC"){
        $wheresql = " 1=1";
        if($status)
            $wheresql = " status = 1";
        if($fid >= 0)
            $wheresql .= " AND fid =".intval($fid);

        $sql = sprintf(" SELECT * FROM category WHERE %s ",$wheresql);
        $sql .=" ORDER BY {$order}";
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


    public function updateCategory($cid = 0,$ar = array()){
        if(!$cid || !$ar)
            return false;

        $set = "";
        $n = 0;
        foreach($ar as $k => $v){
            if($n == 0)
                $set .= "{$k}='".$v."'";
            else
                $set .= ",{$k}='".$v."'";
            $n++;
        }
        $sql = sprintf("UPDATE category SET %s WHERE id = %d LIMIT 1",$set,$cid);
        $this->db->query($sql);
        return true;

    }

    public function addCategory($ar = array()){
        if(!is_array($ar)|| !$ar)
            return false;

        $strkey = "";$strval="";$n = 0;
        foreach($ar as $k => $v){
            if($n == 0){
                $strkey .= $k;
                $strval .= "'".$v."'";
            }else{
                $strkey .= ",".$k;
                $strval .= ",'".$v."'";

            }
            $n++;
        }
        $sql = sprintf("INSERT INTO category (%s) VALUES (%s)",$strkey,$strval);
        $this->db->query($sql);
        return true;
    }


    public function delCategory($cid = 0){
        if(!$cid)
            return false;

        $sql = sprintf("DELETE FROM category WHERE id = %d LIMIT 1",$cid);
        $query = $this->db->query($sql);
        $sql = sprintf("DELETE FROM category WHERE fid = %d LIMIT 30",$cid);
        $query = $this->db->query($sql);
        return true;
    }

}

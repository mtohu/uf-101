<?php

class WebsiteModel extends ModelbaseModel{
    public function __construct() {
        parent::__construct();
    }   
    
    public function getWebsiteList($nowpage=1,$limit = 30,$order="id DESC") {
        $form = ($nowpage-1) * $limit;
        $form = $form < 0 ? 0 : $form;

        $sql = sprintf(" SELECT * FROM website ");

        if($order)
            $sql .= sprintf(" ORDER BY %s",$order);

        $sql .= sprintf(" LIMIT %d,%d",$form,$limit);

        $query = $this->db->query($sql);
        if($this->db->num_rows($query) < 1 )
            return false;

        $lists = $this->db->fetch_all($sql);

        return $lists;
    }

    public function getWebsiteone($id = 0){
        if(!$id)
            return false;

        $sql = sprintf("SELECT * FROM website WHERE id = %d LIMIT 1",$id);
        $query = $this->db->query($sql);
        if($this->db->num_rows($query) < 1 )
            return false;
       
        $row = $this->db->fetch_array($query);
        return $row; 
    }

    public function addWebsite($ar = array()){
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
        $sql = sprintf("INSERT INTO website (%s) VALUES (%s)",$strkey,$strval);
        $this->db->query($sql);
        return $this->db->insert_id();
    }



    public function updateWebsite($id = 0,$ar = array()){
        if(!$id || !$ar)
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
        $sql = sprintf("UPDATE website SET %s WHERE id = %d LIMIT 1",$set,$id);
        $this->db->query($sql);
        return true;

    }

    public function delWebsite($id = 0){
        if(!$id)
            return false;

        if(is_array($id)){
            $id = implode(",",$id);
        }
        $sql = sprintf(" DELETE FROM website WHERE id IN (%s) LIMIT 1",$id);
        $this->db->query($sql);
        return true;
    }
}

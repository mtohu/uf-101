<?php

class AdminModel extends ModelbaseModel{
    public function __construct() {
        parent::__construct();
    }   

    public function getAdminList($nowpage = 1,$limit = 10){
        $form = ($nowpage-1) * $limit;
        $form = $form < 0 ? 0:$form;
        $sql = sprintf(" SELECT ad.* FROM admin AS ad LEFT JOIN users AS u ON ad.userid=u.id  LIMIT %d,%d",$form,$limit);
        $query = $this->db->query($sql);
        if($this->db->num_rows($query) < 1 )
            return false;

        $lists = $this->db->fetch_all($sql);
        return $lists;
    }

    public function getAdminOne($id = 0){
        if(!$id)
            return false;

        $sql = sprintf("SELECT * FROM admin WHERE id = %d LIMIT 1",$id);
        $query = $this->db->query($sql);
        if($this->db->num_rows($query) < 1 )
            return false;

        $row   = $this->db->fetch_array($query);
        return $row;
    }

    public function updateAdmin($id = 0,$ar = array()){
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
        $sql = sprintf("UPDATE admin SET %s WHERE id = %d LIMIT 1",$set);
        $this->db->query($sql);

    }

    public function delAdmin($id = 0){
        if(!$id)
            return false;

        $sql = sprintf("DELETE FROM admin WHERE id = %d LIMIT 1",$id);
        $query = $this->db->query($sql);
    }

}

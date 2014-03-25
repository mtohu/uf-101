<?php

class UsersModel extends ModelbaseModel{
    public function __construct() {
        parent::__construct();
    }   

    public function getUsersList($nowpage = 1,$limit = 10){
        $form = ($nowpage-1) * $limit;
        $form = $form < 0 ? 0:$form;
        $sql = sprintf(" SELECT * FROM  users LIMIT %d,%d",$form,$limit);
        $query = $this->db->query($sql);
        if($this->db->num_rows($query) < 1 )
            return false;

        $lists = $this->db->fetch_all($sql);
        return $lists;
    }

    public function getUsersOne($id = 0){
        if(!$id)
            return false;

        $sql = sprintf("SELECT * FROM users WHERE id = %d LIMIT 1",$id);
        $query = $this->db->query($sql);
        if($this->db->num_rows($query) < 1 )
            return false;

        $row   = $this->db->fetch_array($query);
        return $row;
    }

    public function updateUsers($id = 0,$ar = array()){
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
        $sql = sprintf("UPDATE users SET %s WHERE id = %d LIMIT 1",$set,$id);
        $this->db->query($sql);

    }

    public function delusers($id = 0){
        if(!$id)
            return false;

        $sql = sprintf("DELETE FROM users WHERE id = %d LIMIT 1",$id);
        $query = $this->db->query($sql);
    }

}

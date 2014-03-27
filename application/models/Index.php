<?php

class IndexModel extends ModelbaseModel{
    public function __construct() {
        parent::__construct();
    }  

    public function getChannelList($order = "short DESC"){
        $sql = sprintf("SELECT * FROM  `category` WHERE `status` = 1
                ORDER BY %s",$order);
        return $this->db->fetch_all($sql);
    } 


    public function getArticlesList($lang='zh',$wsql="",$closed=0,$nowpage=1,$limit = 30,$order="a.ctime DESC") {
        $form = ($nowpage-1) * $limit;
        $form = $form < 0 ? 0 : $form;

        $wheresql = ' a.closed IN (0,1)';
        if($closed >=0)
            $wheresql = ' a.closed='.$closed;
        //$langsql = ' AND a.'.$lang.'_acid = ac.id';
        if($wsql)
            $wheresql .= $wsql;


        $sql = sprintf(" SELECT a.* FROM articles AS a  WHERE  %s ",$wheresql);

        if($order)
            $sql .= sprintf(" ORDER BY %s",$order);

        $sql .= sprintf(" LIMIT %d,%d",$form,$limit);

        $query = $this->db->query($sql);
        if($this->db->num_rows($query) < 1 )
            return false;

        $lists = $this->db->fetch_all($sql);

        return $lists;
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

    public function getUserFormOne($uid = 0){
        if(!$uid)
            return false;

        $sql = sprintf("SELECT u.id,u.username,u.email,u.status,u.grade,a.id AS adminid, a.adminname,a.status AS astatus 
                        FROM users AS u LEFT JOIN admin AS a ON u.id = a.userid WHERE u.id = %d LIMIT 1"
                        ,$uid);

        $query = $this->db->query($sql);
        if($this->db->num_rows($query) < 1 )
            return false;

        $row   = $this->db->fetch_array($query);
        return $row;
    }

}

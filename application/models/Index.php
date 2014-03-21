<?php

class IndexModel extends ModelbaseModel{
    public function __construct() {
        parent::__construct();
    }   
    
    public function getArticlesList($lang='zh',$cateid = 0,$nowpage=1,$limit = 30,$order="a.ctime DESC") {
        $form = ($nowpage-1) * $limit;
        $form = $form < 0 ? 0 : $form;

        $wheresql = '';
        $langsql = ' AND a.'.$lang.'_acid = ac.id';
        if($cateid)
            $wheresql = sprintf(" AND a.cateid = %d ",$cateid);


        $sql = sprintf(" SELECT a.*,ac.summary FROM articles AS a LEFT JOIN articles_contents AS ac ON a.id = ac.aid %s 
                         WHERE  a.closed = 1 %s ",$langsql,$wheresql);

        if($order)
            $sql .= sprintf(" ORDER BY ",$order);

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

}

<?php

class ArticlesModel extends ModelbaseModel{
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

    public function getArticlesOne($lang='zh',$aid = 0){
        if(!$aid)
            return false;

        $langsql = ' AND a.'.$lang.'_acid = ac.id';

        $sql = sprintf(" SELECT a.*,ac.des,ac.summary,ac.viewnum FROM articles AS a LEFT JOIN articles_contents AS ac
                         ON a.id = ac.aid %s WHERE a.closed = 1 AND a.id = %d LIMIT 1",$langsql,$aid);
        
        $query = $this->db->query($sql);
        if($this->db->num_rows($query) < 1 )
            return false;

        $row   = $this->db->fetch_array($query);
        return $row;
    }

    public function getArticlesOneByLink($lang='zh',$link = ''){
        if(!$link)
            return false;

        $langsql = ' AND a.'.$lang.'_acid = ac.id';

        $sql = sprintf(" SELECT a.*,ac.des,ac.summary,ac.viewnum FROM articles AS a LEFT JOIN articles_contents AS ac
                         ON a.id = ac.aid %s WHERE a.closed = 1 AND a.link = '%s' LIMIT 1",
                         $langsql,mysql_real_escape_string($link));
        
        $query = $this->db->query($sql);
        if($this->db->num_rows($query) < 1 )
            return false;

        $row   = $this->db->fetch_array($query);
        return $row;
    }

    public function delArticle($aid = 0){
        if(!$aid)
            return false;

        if(is_array($aid)){
            $aid = implode(",",$aid);
        }
        $sql = sprintf(" DELETE FROM articles WHERE id IN (%s) LIMIT 1",$aid);
        $this->db->query($sql);
        $sql = sprintf(" DELETE FROM articles_contents WHERE aid IN (%s) LIMIT 4",$aid);
        $this->db->query($sql);

    }
}

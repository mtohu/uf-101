<?php

class ArticlesModel extends ModelbaseModel{
    public function __construct() {
        parent::__construct();
    }   
    
    public function getArticlesList($lang='zh',$wsql="",$closed=1,$nowpage=1,$limit = 30,$order="a.ctime DESC") {
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

    public function getArticlesJoinContentOne($lang='zh',$aid = 0){
        if(!$aid)
            return false;
        
        $tablename = $this->getTableAritcleContents($aid);
        $langsql = ' AND a.'.$lang.'_acid = ac.id';

        $sql = sprintf(" SELECT a.*,ac.des,ac.viewnum FROM articles AS a LEFT JOIN %s AS ac
                         ON a.id = ac.aid %s WHERE a.closed = 1 AND a.id = %d LIMIT 1",$tablename,$langsql,$aid);
        
        $query = $this->db->query($sql);
        if($this->db->num_rows($query) < 1 )
            return false;

        $row   = $this->db->fetch_array($query);
        return $row;
    }

    public function getArticleOne($aid = 0){
        if(!$aid)
            return false;
        
        $sql = sprintf(" SELECT a.* FROM articles AS a 
                        WHERE a.id = %d LIMIT 1",$aid);
        
        $query = $this->db->query($sql);
        if($this->db->num_rows($query) < 1 )
            return false;

        $row   = $this->db->fetch_array($query);
        return $row;
    }

    public function getArticleContents($aid = 0){
        if(!$aid)
            return false;

        $tablename = $this->getTableAritcleContents($aid);
        $sql = sprintf(" SELECT * FROM %s WHERE aid = %d LIMIT 5",$tablename,$aid);
        $query = $this->db->query($sql);
        if($this->db->num_rows($query) < 1 )
            return false;

        $list   = $this->db->fetch_all($sql);
        return $list;
    }

    public function getArticlesOneByLink($lang='zh',$link = ''){
        if(!$link)
            return false;

        $aid =  intval(substr($link,0,strpos($link,"-")));
        $tablename = $this->getTableAritcleContents($aid);
        $langsql = ' AND a.'.$lang.'_acid = ac.id';

        $sql = sprintf(" SELECT a.*,ac.des,ac.viewnum FROM articles AS a LEFT JOIN %s AS ac
                         ON a.id = ac.aid %s WHERE a.closed = 1 AND a.id = %d LIMIT 1",
                         $tablename,$langsql,$aid);
        
        $query = $this->db->query($sql);
        if($this->db->num_rows($query) < 1 )
            return false;

        $row   = $this->db->fetch_array($query);
        return $row;
    }


    public function addArticles($ar = array()){
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
        $sql = sprintf("INSERT INTO articles (%s) VALUES (%s)",$strkey,$strval);
        $this->db->query($sql);
        return $this->db->insert_id();
    }

    public function addArticlesContents($ar = array(),$aid = 0){
        if(!is_array($ar)|| !$ar || !$aid)
            return false;

        $tablename = $this->getTableAritcleContents($aid);
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
        $sql = sprintf("INSERT INTO %s (%s) VALUES (%s)",$tablename,$strkey,$strval);
        $this->db->query($sql);
        return $this->db->insert_id();
    }


    public function updateArticles($id = 0,$ar = array()){
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
        $sql = sprintf("UPDATE articles SET %s WHERE id = %d LIMIT 1",$set,$id);
        $this->db->query($sql);
        return true;

    }


    public function updateArticlesContents($id = 0,$aid=0,$ar = array()){
        if(!$id || !$aid || !$ar)
            return false;

        $tablename = $this->getTableAritcleContents($aid);
        $set = "";$n = 0;
        foreach($ar as $k => $v){
            if($n == 0)
                $set .= "{$k}='".$v."'";
            else
                $set .= ",{$k}='".$v."'";
            $n++;
        }
        $sql = sprintf("UPDATE %s SET %s WHERE id = %d AND aid = %d LIMIT 1",$tablename,$set,$id,$aid);
        $this->db->query($sql);
        return true;

    }

    public function delArticle($aid = 0){
        if(!$aid)
            return false;

        if(is_array($aid)){
            $aid = implode(",",$aid);
        }
        $sql = sprintf(" DELETE FROM articles WHERE id IN (%s) LIMIT 1",$aid);
        $this->db->query($sql);
        $sql = sprintf(" DELETE FROM articles_contents0 WHERE aid IN (%s) LIMIT 4",$aid);
        $this->db->query($sql);
        $sql = sprintf(" DELETE FROM articles_contents1 WHERE aid IN (%s) LIMIT 4",$aid);
        $this->db->query($sql);
        $sql = sprintf(" DELETE FROM articles_contents2 WHERE aid IN (%s) LIMIT 4",$aid);
        $this->db->query($sql);
        $sql = sprintf(" DELETE FROM articles_contents3 WHERE aid IN (%s) LIMIT 4",$aid);
        $this->db->query($sql);
        $sql = sprintf(" DELETE FROM articles_contents4 WHERE aid IN (%s) LIMIT 4",$aid);
        $this->db->query($sql);
        $sql = sprintf(" DELETE FROM articles_contents5 WHERE aid IN (%s) LIMIT 4",$aid);
        $this->db->query($sql);
        $sql = sprintf(" DELETE FROM articles_contents6 WHERE aid IN (%s) LIMIT 4",$aid);
        $this->db->query($sql);
        $sql = sprintf(" DELETE FROM articles_contents7 WHERE aid IN (%s) LIMIT 4",$aid);
        $this->db->query($sql);
        $sql = sprintf(" DELETE FROM articles_contents8 WHERE aid IN (%s) LIMIT 4",$aid);
        $this->db->query($sql);
        $sql = sprintf(" DELETE FROM articles_contents9 WHERE aid IN (%s) LIMIT 4",$aid);
        $this->db->query($sql);

    }
}

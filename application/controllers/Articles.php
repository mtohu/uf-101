<?php

class ArticlesController extends WebbaseController {
       
    public $articleModel = null;
    public function init(){
        parent::init();
        $this->articleModel = new ArticlesModel();
        $website = $this->viewData["website"];
        if(isset($website)){
            $website=$website[0];
            $website["title"]=$website[$this->_langset."title"];
            $website["keyword"]=$website[$this->_langset."keyword"];
            $website["description"]=$website[$this->_langset."description"];
            $this->_setViewData(array('website'=>$website));
        }
    }

    public function indexAction($link = "") {
        $hotarticles = $this->viewData["hotarticles"];
        $row = $this->articleModel->getArticlesOneByLink($this->_langset,$link);
        if(!$row)
            return false;

        if($_COOKIE["viewid"] != "aid_".$row["id"]){
            $this->articleModel->updateArticlesContents($row[$this->_langset."_acid"],$row["id"],
                                                        array("viewnum"=>$row["viewnum"]+1));
            $this->articleModel->updateArticles($row["id"],array("vtotal"=>$row["vtotal"]+1));
            setcookie("viewid", "aid_".$row["id"], time()+3600*2);
        }
        
        $row["vtitle"]=$row[$this->_langset."title"]; 
        $row["vsummary"]=$row[$this->_langset."summary"]; 
        $row["vcatename"]=$row[$this->_langset."catename"]; 
        $row["des"]=htmlspecialchars_decode($row["des"]);
        $this->_setViewData(array('articleone'=>$row,'langset'=>$this->_langset,
                            'hotarticles'=>$hotarticles));
        $this->getView()->assign($this->viewData);
        return TRUE;
    }
}

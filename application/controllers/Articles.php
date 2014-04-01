<?php

class ArticlesController extends WebbaseController {
       
    public $articleModel = null;
    public function init(){
        parent::init();
        $this->articleModel = new ArticlesModel();
    }

    public function indexAction($link = "") {
        $hotarticles = $this->viewData["hotarticles"];
        $row = $this->articleModel->getArticlesOneByLink($this->_langset,$link);
        if(!$row)
            return false;
        
        $row["vtitle"]=$row[$this->_langset."title"]; 
        $row["vsummary"]=$row[$this->_langset."summary"]; 
        $row["des"]=htmlspecialchars_decode($row["des"]);
        $this->_setViewData(array('articleone'=>$row,'langset'=>$this->_langset,
                            'hotarticles'=>$hotarticles));
        $this->getView()->assign($this->viewData);
        return TRUE;
    }
}

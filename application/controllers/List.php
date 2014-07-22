<?php

class ListController extends WebbaseController {
       
    public function init(){
        parent::init();
        $website = $this->viewData["website"];
        if(isset($website)){
            $website=$website[0];
            $website["title"]=$website[$this->_langset."title"];
            $website["keyword"]=$website[$this->_langset."keyword"];
            $website["description"]=$website[$this->_langset."description"];
            $this->_setViewData(array('website'=>$website));
        }
    }
    public function indexAction($catename = "",$page = 0) {
        $page = intval($page)>0?intval($page):1;
        $channellist = $this->viewData["channellist"];
        $hotarticles = $this->viewData["hotarticles"];
        $ctotal = 0;
        $cateid = 0;
        foreach($channellist as $k => $v){
            if($v["enname"] == $catename){
                $ctotal = $v["total"];
                $cateid = $v["id"];
            }
        }
        $lists = null;
        if($ctotal){
            $lists = $this->indexModel->getArticlesList("zh"," AND a.cateid=".$cateid,0,$page,10);
            $PAGESTR = $this->page->getPaginationString($page,$ctotal, 10, 1, "/", "category/".$catename."/page/");
            $this->_setViewData(array('PAGESTR'=>$PAGESTR));
        }
        $this->_setViewData(array('lists'=>$lists,'catename'=>$encatename,
                           'hotarticles'=>$hotarticles,'langset'=>$this->_langset));
        $this->getView()->assign($this->viewData);
    }
}

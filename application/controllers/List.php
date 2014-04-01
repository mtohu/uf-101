<?php

class ListController extends WebbaseController {
       
    public function init(){
        parent::init();
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
            $PAGESTR = $this->page->getPaginationString($page,$ctotal, 10, 1, "/", "/category/page/");
            $this->_setViewData(array('PAGESTR'=>$PAGESTR));
        }
        $this->_setViewData(array('lists'=>$lists,'catename'=>$encatename,
                           'hotarticles'=>$hotarticles,'langset'=>$this->_langset));
        $this->getView()->assign($this->viewData);
    }
}

<?php

class MaindexController extends WebbaseController {
       
    public function init(){
        parent::init();
    }

    public function indexAction($page = 0) {
        $page = intval($page)>0?intval($page):1;
        $channellist = $this->viewData["channellist"];
        $hotarticles = $this->viewData["hotarticles"];
        $ctotal = 0;
        foreach($channellist as $k => $v){
            $ctotal = $ctotal + $v["total"];
        }
        $lists = null;
        if($ctotal){
            $lists = $this->indexModel->getArticlesList("zh","",0,$page,10);
            $PAGESTR = $this->page->getPaginationString($page,$ctotal, 10, 1, "/", "page/");
            $this->_setViewData(array('PAGESTR'=>$PAGESTR));
        }
        $this->_setViewData(array('lists'=>$lists,'langset'=>$this->_langset,
                            'hotarticles'=>$hotarticles));
        $this->getView()->assign($this->viewData);
        return TRUE;
    }

    public function categoryAction($catename = "",$page = 0) {
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
                           'hotarticles'=>$hotarticles));
        $this->getView()->assign($this->viewData);
    }
}

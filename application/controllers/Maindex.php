<?php

class MaindexController extends WebbaseController {
       
    public function init(){
        parent::init();
    }

    public function indexAction($name = "Stranger") {
        $this->_setViewData(array('name'=>"yes"));
        $this->getView()->assign($this->viewData);
        //$this->getView()->display('index.html');
        //fastcgi_finish_request();
        return TRUE;
    }

    public function listAction($id=0,$name = "Stranger") {
        $this->_setViewData(array('name'=>$name,'id'=>$id));
        $this->getView()->assign($this->viewData);
        //$this->getView()->display('list.html');
    }
}

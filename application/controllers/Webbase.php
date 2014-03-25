<?php
class WebbaseController extends Yaf_Controller_Abstract{

  protected $_userInfo = null;
  protected $_pageProg = null;
  protected $_pageMethod = null;
  public $indexModel = null;
  public $_keyarr = null;
  public $basecommon = null;
  public $session = null;
  public $viewData  = array();
  public function init(){
      $this->session = Yaf_Registry::get("session");
      $this->basecommon = new Basecommon();
      $this->indexModel = new IndexModel();
      $this->_keyarr = $this->basecommon->getDomainBykeyAndFolder();
      $ckey=$this->_keyarr['ckey'];$uckey=$this->_keyarr['uckey'];
      $userinfo = null;
      $this->_userInfo = $userInfo = $this->session->userdata("userinfo");
      $cookieArr = $this->basecommon->getCookieValue($ckey.'_auth',$uckey,$userInfo);
      $uid = $cookieArr['uid'];
      $password  = $cookieArr['password'];
      $this->_pageProg = $this->getRequest()->getControllerName()?$this->getRequest()->getControllerName():"maindex";
      $this->_pageMethod = $this->getRequest()->getActionName()?$this->getRequest()->getActionName():"index";


       if($uid && !$userInfo){
           $userInfoArray   = array('userInfo' => $this->indexModel->getUserFormOne($uid));
           $this->session->set_userdata($userInfoArray);
           $this->_userInfo = $userInfo = $this->session->userdata('userInfo');
       }
      
  }


  function _setViewData($d){
      foreach($d as $k => $v){
          $this->viewData[$k] = $v;

      }
      return 1;
  }

}

?>

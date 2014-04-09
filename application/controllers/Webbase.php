<?php
class WebbaseController extends Yaf_Controller_Abstract{

  protected $_userInfo = null;
  protected $_pageProg = null;
  protected $_pageMethod = null;
  public $indexModel = null;
  public $page = null;
  public $_keyarr = null;
  public $basecommon = null;
  public $session = null;

  public $_langset = "zh";
  public $multicache = null;
  public $memSets = null;
  public $_restetMemCached = false;
  public $_memCache = array();
  public $_modelArrayPicks = array();
  public $viewData  = array();
  public function init(){
      $cdnurl = Yaf_Registry::get("config")->get("cdnurl");
      $webhost = Yaf_Registry::get("config")->get("webhost");
      $this->session = Yaf_Registry::get("session");
      $this->basecommon = new Basecommon();
      $this->indexModel = new IndexModel();
      $this->multicache = new Multicache();
      $this->page = new Page();
      $this->_keyarr = $this->basecommon->getDomainBykeyAndFolder();
      $ckey=$this->_keyarr['ckey'];$uckey=$this->_keyarr['uckey'];
      $userinfo = null;
      $this->_userInfo = $userInfo = $this->session->userdata("userinfo");
      $cookieArr = $this->basecommon->getCookieValue($ckey.'_auth',$uckey,$userInfo);
      $uid = $cookieArr['uid'];
      $password  = $cookieArr['password'];
      $this->_pageProg = $this->getRequest()->getControllerName()?strtolower($this->getRequest()->getControllerName()):"maindex";
      $this->_pageMethod = $this->getRequest()->getActionName()?strtolower($this->getRequest()->getActionName()):"index";


      if($uid && !$userInfo){
          $userInfoArray   = array('userInfo' => $this->indexModel->getUserFormOne($uid));
          $this->session->set_userdata($userInfoArray);
          $this->_userInfo = $userInfo = $this->session->userdata('userInfo');
      }

      $this->_setViewData(array('cdnurl'=>$cdnurl,'pageMethod'=>$this->_pageMethod,
                          'pageProg'=>$this->_pageProg,'exectime'=>time(),'webhost'=>$webhost));

      $this->_modelArrayPicks = array('hotarticles'=>10);
      $this->memSets = array(
                       "maindex"=>array("hotarticles","channellist","website"),
                       "articles"=>array("hotarticles","website"),
                       "list"=>array("hotarticles","channellist","website"),
                       "admin"=>array("channellist")
      );

      $memSets = $this->memSets;
      if(!isset($memSets[$this->_pageProg]))
        return;

      if(!$memSets[$this->_pageProg])
        return;

      $this->_memCache=$memCache=$this->multicache->get($memSets[$this->_pageProg]);
      foreach($memSets[$this->_pageProg] as $k => $v)
         $this->getDataFromMemcachKey($v);
 
      
  }


  function resetMem($key = 'maindex'){
      $memSets = $this->memSets;
      foreach($memSets[$this->_pageProg] as $k => $v)
          $this->getDataFromMemcachKey($v);

  }

  function getDataFromMemcachKey($key = null){
      if(!$key)
        return false;

      $modelKey = $data = null;
      if(!$this->_memCache[$key] || $this->_resetMemCached){
        $modelKey = 'indexModel';
        switch($key){
          case 'channellist':
            $data = $this->$modelKey->getChannelList();
            break;
          case 'hotarticles':
            $data = $this->$modelKey->getArticlesList('zh',"",0,1,10,"a.istop DESC,a.isrec DESC,a.ctime DESC");
            break;
          case 'website':
            $data = $this->$modelKey->getWebsiteList(1,1);
            break;
        }
        $this->multicache->set($key, $data, MEMCACHE_COMPRESSED, 77);
        $this->_memCache[$key] = $data;
      }
      $randLimit = isset($this->_modelArrayPicks[$key]) ? $this->_modelArrayPicks[$key] : null;
      if(!$randLimit){
        $this->_setViewData(array($key=>$this->_memCache[$key]));
      }else{
        $this->_setViewData(array($key=>$this->array_pick($this->_memCache[$key],$randLimit)));
      }

      return false;

   }


   //从数组中随机挑值
   function array_pick($hash, $num) {
      //打乱数组
      shuffle($hash);
      //计算大小
      $count = count($hash);
      if ($num <= 0) return array();
      if ($num >= $count) return $hash;
      //随机获得一些数组值
      $keys = array_rand($hash, $count - $num);
      if(is_array($keys))//坏值索引
         foreach ($keys as $k) unset($hash[$k]);
      else
         unset($hash[$keys]);
      //返回剩下的值
      return $hash;
   }



  function _setViewData($d){
      foreach($d as $k => $v){
          $this->viewData[$k] = $v;

      }
      return 1;
  }

}

?>

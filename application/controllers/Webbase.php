<?php
class WebbaseController extends Yaf_Controller_Abstract{

  //public $smarty = null;
  public $viewData  = array();
  public function init(){
      //$this->smarty = Yaf_Registry::get("smarty");
  }


  function _setViewData($d){
      foreach($d as $k => $v){
          $this->viewData[$k] = $v;

      }
      return 1;
  }

}

?>

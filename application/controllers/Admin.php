<?php

class AdminController extends WebbaseController {
      
    public $adminModel = null; 
    public function init(){
        parent::init();
        if(!$this->_userInfo['astatus'] && !in_array($this->_pageMethod,array("login"))){
            $this->forward("login", array("from" => "index"));
            return false;
        }
        $this->adminModel = new AdminModel();
    }

    public function indexAction() {
        return TRUE;
    }

    public function articlelistAction(){
    }

    public function categorylistAction($cid = 0){
        $categoryModel = new CategoryModel();
        $lists = $categoryModel->getCategoryList($cid,0);
        $this->_setViewData(array('lists'=>$lists,"cid"=>$cid));
        $this->getView()->assign($this->viewData);
    }

    public function categoryoperateAction($cid = 0,$current = 0,$d = 0){
        $categoryModel = new CategoryModel();
        if($d){
            $flag=$categoryModel->delCategory($cid);
            if($flag){
                $this->redirect("/admin/categorylist/cid/".$current);
                return TRUE;
            }
        }

        if($this->getRequest()->isPost()){
            $cid = $this->getRequest()->getPost("cid");
            $current = $this->getRequest()->getPost("current");
            $enname = $this->getRequest()->getPost("enname");
            $zhname = $this->getRequest()->getPost("zhname");
            $twname = $this->getRequest()->getPost("twname");
            $fid = $this->getRequest()->getPost("selparend");
            $short = $this->getRequest()->getPost("short");
            $status = $this->getRequest()->getPost("status");
            $strar=array("enname"=>$enname,"zhname"=>$zhname,
                         "twname"=>$twname,"fid"=>$fid,
                         "short"=>$short,"status"=>$status);
            if(intval($cid) > 0){
                $flag=$categoryModel->updateCategory($cid,$strar);
            }else{
                $strar["ctime"]=time();
                $flag= $categoryModel->addCategory($strar);
            }
            if(!$flag){
                die("失败");
            }            
            $this->redirect("/admin/categorylist/cid/".$current);
            return TRUE;
        }else{
            $categoryone= null;
            if($cid > 0)
                $categoryone = $categoryModel->getCategoryOne($cid);

            $lists = $categoryModel->getCategoryList(-1,0);
            $this->_setViewData(array("lists"=>$lists,"categoryone"=>$categoryone,
                                      "cid"=>$cid,"current"=>$current));
            $this->getView()->assign($this->viewData);
        }
        
         
    }

    public function siteinfoAction(){
        
    }

    public function loginAction() {
        if($this->getRequest()->isPost()){
            $username = $this->getRequest()->getPost("username");
            $passwd = $this->getRequest()->getPost ("passwd");
            if(!$passwd || !$username)
                return false;

            $passwds = md5($passwd);
            $row = $this->adminModel->getAdminByadminnameAndPasswd($username,$passwds);
            if($row){
                $userInfoArray   = array('userInfo' => $this->indexModel->getUserFormOne($row["userid"]));
                $this->session->set_userdata($userInfoArray);
                $this->_userInfo = $userInfo = $this->session->userdata('userInfo');
                $pa = $passwd."\t".$row["userid"];
                $authstr = $this->basecommon->authcode($pa,"",$this->_keyarr["uckey"]);
                setcookie($this->_keyarr["ckey"].'_auth',$authstr, time()+60*60*24, '/');
                $this->redirect("/admin");
                return TRUE;
            }
            
        }
        return TRUE;
    }

    public function logoutAction(){
        $this->session->destroy();
        setcookie('iTiQ_c4ec_auth', '', -31536000, '/');
        $this->forward("login", array("from" => "logout"));
        return false;

    }
}

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

    public function articlelistAction($cid=0,$title="",$page=0){
        $page=intval($page)>0?intval($page):1;
        $title = urldecode($title);
        if(!$title){
            $title = "";
        }
        $articleModel = new ArticlesModel();
        /*$channellist = $this->viewData["channellist"];
        $ctotal = 0;
        foreach($channellist as $k => $v){
            $ctotal = $ctotal + $v["total"]; 
        }*/
        $where = " AND (a.entitle LIKE '%".$title."%' OR a.zhtitle LIKE '%".$title."%')";
        $ctotal=$articleModel->getArticlesCount("zh",$where,-1);
        $lists = null;
        if($ctotal){
            $lists = $articleModel->getArticlesList("zh",$where,-1,$page,15);
            $PAGESTR = $this->page->getPaginationString($page,$ctotal, 15, 1, "/", "admin/articlelist/cid/$cid/title/$title/page/");
            $this->_setViewData(array('PAGESTR'=>$PAGESTR));
        }
        $this->_setViewData(array('lists'=>$lists,"cid"=>$cid));
        $this->getView()->assign($this->viewData);

    }

    public function articleoperateAction($aid = 0,$d = 0){
        $articleModel = new ArticlesModel();
        if($d){
            $articleone = $articleModel->getArticleOne($aid);
            if($articleone){
                $flag=$articleModel->delArticle($aid,$articleone["cateid"]);
            }
            if($flag){
                echo "<script>window.history.back();</script>";exit;
                //die(json_encode(array('status'=>0)));
                return TRUE;
            }
        }
        if($this->getRequest()->isPost()){
            $aid = $this->getRequest()->getPost("aid");
            $cateid=$this->getRequest()->getPost("selcate");
            $categoryModel = new CategoryModel();
            $caterow = $categoryModel->getCategoryOne($cateid);
            $strar = array(
                "link"=>$this->getRequest()->getPost("link"),
                "cateid"=>$cateid,
                "entitle"=>mysql_real_escape_string($this->getRequest()->getPost("entitle")),
                "zhtitle"=>mysql_real_escape_string($this->getRequest()->getPost("zhtitle")),
                "twtitle"=>mysql_real_escape_string($this->getRequest()->getPost("twtitle")),
                "ensummary"=>mysql_real_escape_string(htmlspecialchars($this->getRequest()->getPost("ensummary"))),
                "zhsummary"=>mysql_real_escape_string(htmlspecialchars($this->getRequest()->getPost("zhsummary"))),
                "twsummary"=>mysql_real_escape_string(htmlspecialchars($this->getRequest()->getPost("twsummary"))),
                "isrec"=>$this->getRequest()->getPost("isrec"),
                "istop"=>$this->getRequest()->getPost("istop"),
                "closed"=>$this->getRequest()->getPost("closed"),
            );
            if($caterow){
                $strar["encatename"]=$caterow["enname"];
                $strar["zhcatename"]=$caterow["zhname"];
                $strar["twcatename"]=$caterow["twname"];
            }
            $car1 = array("des"=>mysql_real_escape_string(htmlspecialchars($this->getRequest()->getPost("endes"))));
            $car2 = array("des"=>mysql_real_escape_string(htmlspecialchars($this->getRequest()->getPost("zhdes"))));
            $car3 = array("des"=>mysql_real_escape_string(htmlspecialchars($this->getRequest()->getPost("twdes"))));
            $flag=0;
            if(intval($aid)>0){
                $en_acid = $this->getRequest()->getPost("en_acid");
                $zh_acid = $this->getRequest()->getPost("zh_acid");
                $tw_acid = $this->getRequest()->getPost("tw_acid");
                $flag=$articleModel->updateArticles($aid,$strar);
                $flag=$articleModel->updateArticlesContents($en_acid,$aid,$car1);
                $flag=$articleModel->updateArticlesContents($zh_acid,$aid,$car2);
                $flag=$articleModel->updateArticlesContents($tw_acid,$aid,$car3);
                if(!$flag){
                    die("失败");
                }            
                echo "<script>window.history.go(-2);</script>";exit;
            }else{
                $strar["ctime"]=time();
                $strar["adminid"]=$this->_userInfo["adminid"];
                $strar["adminname"]=$this->_userInfo["adminname"];;
                $naid = $articleModel->addArticles($strar);
                if($naid){
                    $car1["aid"]=$naid;
                    $car2["aid"]=$naid;
                    $car3["aid"]=$naid;
                    $caid1=$articleModel->addArticlesContents($car1,$naid);
                    $caid2=$articleModel->addArticlesContents($car2,$naid);
                    $caid3=$articleModel->addArticlesContents($car3,$naid);
                    $flag=$articleModel->updateArticles($naid,array("en_acid"=>$caid1,"zh_acid"=>$caid2,"tw_acid"=>$caid3));
                    if($caterow){
                        $total = $caterow["total"]+1;
                        $categoryModel->updateCategory($caterow["id"],array("total"=>$total));
                    }
                }
            }
            if(!$flag){
                die("失败");
            }            
            $this->redirect("/admin/articlelist");
            return TRUE;
           
        }else{
            $articleone = null;
            if($aid){
                $articleone = $articleModel->getArticleOne($aid);
                if($articleone){
                    $acontents = $articleModel->getArticleContents($aid);
                    if($acontents){
                        foreach($acontents as $k => $v){
                            if($v["id"] == $articleone["en_acid"])
                                $articleone["endes"]= $v["des"];
                            elseif($v["id"] == $articleone["zh_acid"])
                                $articleone["zhdes"]= $v["des"];
                            elseif($v["id"] == $articleone["tw_acid"])
                                $articleone["twdes"]= $v["des"];
                        }
                    }
                }
            }
            $categoryModel = new CategoryModel();
            $lists = $categoryModel->getCategoryList(-1,0);
            $this->_setViewData(array("lists"=>$lists,"articleone"=>$articleone,
                                      "aid"=>$aid));
            $this->getView()->assign($this->viewData);
        }
        
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

    public function articleuploadAction($aid = 0,$id= 0,$d = 0){
        $articleModel = new ArticlesModel();
        $articlesattachModel=new ArticlesattachModel();
        if($d){
            if(!$id)
                return false;

            $row=$articlesattachModel->getArticlesAttachone($id);
            if($row)
                @unlink(APPLICATION_PATH."/".$row["filedir"]."/".$row["filename"].".".$row["type"]);
            
            $flag=$articlesattachModel->delArticleAttach($id);            
            if($flag){
                $this->redirect("/admin/articleupload/aid/".$aid);
                return TRUE;
            }
        }
        if($this->getRequest()->isPost()){
            $aid = $this->getRequest()->getPost("aid");
            if(!$aid)
                return false;

            $sizename = $this->getRequest()->getPost("sizename");
            $handle = new Upload($_FILES['fileupload']);
            if($handle->uploaded){
            $handle->mime_check = true;
            $handle->file_overwrite=true;
            $handle->allowed = array('image/*');
            $filename = $aid;
            $ofilename = $filename;
            $sizear = null;
            if($sizename){
                $sizear = explode("X",$sizename);
                if(!isset($sizear[1])){
                    echo "<script>alert('尺寸输入错误');</script>";
                    $this->redirect("/admin/articleupload/aid/".$aid);
                    return TRUE;
                }
                $filename .="-".$sizename;
            }else{
                $filename =date("YmdHis")."-".$aid."-".rand(10000, 99999);
            }
            if($handle->file_is_image){ 
                echo "<script>alert('不是图片');</script>";
                $this->redirect("/admin/articleupload/aid/".$aid);
                return TRUE;
            }
            $handle->file_new_name_body =$filename;
            //if(isset($sizear)){
                //$handle->image_resize=true;
                //$handle->image_ratio= true;
                //$handle->image_ratio_crop= true;
                //$handle->image_x=intval($sizear[0]);
                //$handle->image_y =intval($sizear[1]);
            //}
            $ymd = date("Ymd");
            $uploaddir ="data/uploadfile/image/".$ymd."/".$aid;
            $handle->rmkdir(APPLICATION_PATH."/".$uploaddir);
            $handle->process(APPLICATION_PATH."/".$uploaddir."/");
            if($handle->processed){
                $asetar = array(
                    "picdir"=>$uploaddir,
                    "picname"=>$ofilename,
                    "ext"=>$handle->file_dst_name_ext
                );
                $newfilesize =getimagesize($handle->file_dst_pathname);
                $aasetar = array(
                    "aid"=>$aid,
                    "filedir"=>$uploaddir,
                    "filename"=>$handle->file_dst_name_body,
                    "type"=>$handle->file_dst_name_ext,
                    "sizename"=>$newfilesize[0]."X".$newfilesize[1],
                    "filesize"=>$handle->file_src_size,
                    "ctime"=>time()
                );
                if(isset($sizear)){
                    $imginput = new Imageinput();
                    $meta=$imginput->img_cop($handle->file_dst_pathname,$handle->file_dst_name,$sizear);
                    if($meta){
                        $aasetar["filesize"]=$meta["size"];
                        $aasetar["sizename"]=$meta["w"]."X".$meta["h"];
                    }
                }
                $flag=$articlesattachModel->addArticlesAttach($aasetar);
                if($flag){
                    $asetar["thumb"]=1;
                    $flag=$articleModel->updateArticles($aid,$asetar);
                }
                $handle->clean();
                if($flag){
                    echo "<script>alert('上传成功');</script>";
                    $this->redirect("/admin/articleupload/aid/".$aid);
                    return TRUE;
                }
            }else{
                echo "<script>alert('".$handle->error."');</script>";
                $this->redirect("/admin/articleupload/aid/".$aid);
                return TRUE;
            }
            }
        }else{
            if(!$aid)
                return false;

            $lists=$articlesattachModel->getArticlesAttachByaid($aid,1,10);
            $this->_setViewData(array("lists"=>$lists,"aid"=>$aid));
            $this->getView()->assign($this->viewData);
        }
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

<?php
/**
 * @name Bootstrap
 * @author root
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{

        public function _initConfig() {
		//把配置保存起来
		$arrConfig = Yaf_Application::app()->getConfig();
		Yaf_Registry::set('config', $arrConfig);
	}

       /* public function _initSmarty(Yaf_Dispatcher $dispatcher) {
                $smarty = new Smarty_Adapter(APPPATH.'/views/', Yaf_Registry::get("config")->get("smarty"));
                Yaf_Dispatcher::getInstance()->setView($smarty);
        }

        public function init(){
                Yaf_Dispatcher::getInstance()->disableView();
        }*/

	public function _initPlugin(Yaf_Dispatcher $dispatcher) {
		//注册一个插件
		$objSamplePlugin = new SamplePlugin();
		$dispatcher->registerPlugin($objSamplePlugin);
	}

	public function _initRoute(Yaf_Dispatcher $dispatcher) {
		//在这里注册自己的路由协议,默认使用简单路由
                $router = $dispatcher->getRouter();
                /*$router->addRoute('dummy', new Yaf_Route_Rewrite("/page/:page",array(
                                  "controller" => "maindex",
                                  "action"     => "index",))
                );*/
                /*$router->addRoute('rewrite', new Yaf_Route_Rewrite("/admin/categorylist/:cid",array(
                                  "controller" => "admin",
                                  "action"     => "categorylist",))
                );*/
                $router->addRoute('regex1', new Yaf_Route_Regex("#^/page/([0-9]+)#",
                                  array("controller" => "maindex","action" => "index"),
                                  array(1=>"page"))
                ); 
                $router->addRoute('regex2', new Yaf_Route_Regex("#^/category/([a-z]+)#",
                                  array("controller" => "list","action" => "index"),
                                  array(1=>"catename",2=>"page"))
                ); 
                $router->addRoute('regex3', new Yaf_Route_Regex("#^/category/([a-z]+)/([0-9]+)#",
                                  array("controller" => "list","action" => "index"),
                                  array(1=>"catename",2=>"page"))
                ); 
                $router->addRoute('regex4', new Yaf_Route_Regex("#^/articles/([0-9a-z_-]+)#",
                                  array("controller" => "articles","action" => "index"),
                                  array(1=>"link"))
                ); 
	}

        public function _initSession(Yaf_Dispatcher $dispatcher) 
        { 
                $session = new Session(); 
                Yaf_Registry::set("session",$session);
        } 
	
	public function _initView(Yaf_Dispatcher $dispatcher){
		//在这里注册自己的view控制器，例如smarty,firekylin
                $smarty = new Smarty_Adapter(NULL, Yaf_Registry::get("config")->get("smarty"));
                Yaf_Dispatcher::getInstance()->setView($smarty);
	}

        public function _initDefaultDbAdapter(){
                $param = Yaf_Registry::get("config")->get("database");
                $dbAdapter = new Dbmysql($param['params']['host'],$param['params']['username'],
                                         $param['params']['password'],$param['params']['dbname']);
                Yaf_Registry::set("dbadapter",$dbAdapter);
        }
}

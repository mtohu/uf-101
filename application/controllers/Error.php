<?php
/**
 * @name ErrorController
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
 * @author root
 */
class ErrorController extends WebbaseController{

	//从2.1开始, errorAction支持直接通过参数获取异常
	public function errorAction($exception) {
            switch ($exception->getCode()) {
                case YAF_ERR_NOTFOUND_MODULE:
                case YAF_ERR_NOTFOUND_CONTROLLER:
                case YAF_ERR_NOTFOUND_ACTION:
                case YAF_ERR_NOTFOUND_VIEW:
                     $this->_setViewData(array('name'=>"ddss"));
                     $this->getView()->assign($this->viewData);
                     return;
                     break;
                default :
                     echo 0, ":", $exception->getMessage();
                     break;
            }
            exit;

	}
}

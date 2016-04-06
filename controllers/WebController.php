<?php
namespace lubaogui\common\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use lubaogui\common\exceptions\LBUserException;

/**
 * WebController 针对PC端的请求的controller基类
 */
class WebController extends Controller
{

    /**
     * @brief 在动作之前执行的一些预处理
     *
     * @return string $action 将要执行的action
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            return true;
        }
        else{ 
            return false;
        }
    }

    /**
     * @brief 渲染模板文件
     *
     * @param string $view 模板 
     * @return array $param 参数
     */
    public function render($view, $param = []){
        return parent::render($view,$param);
    }

    /**
     * @brief controller的action执行之后的处理函数，该函数类似于回调函数
     *
     * @param string $action 执行的action
     * @param mixed $data action返回的结果
     * @return mixed 处理之后的结果
     */
    public function afterAction($action, $data)
    {
        $data = parent::afterAction($action, $data);
        $result = null;
        //自定义处理逻辑,此处仅处理正常流程逻辑，对于异常逻辑，走下面的throwLBUserException方法
        if (is_array($data) || !$data) {
            $result['data'] = $data ? $data : [];
            $result['code'] = 0;
            $result['message'] = '';
            Yii::$app->getResponse()->format = Response::FORMAT_JSON; 
        }
        else {
            $result = $data;
        }
        return $result;
    }

    /**
     * @brief 统一的异常抛出函数
     *
     * @param string $errMsg 错误信息
     * @param int $errCode 错误代码
     * @param array $errors 错误信息数组
     * @param bool $forceExit 是否强制程序返回退出
     *
     */
    public function throwLBUserException($errMsg, $errCode = 1, $errors = []) {
        thow new LBUserException($errMsg, $errCode, $errors);
    }

}

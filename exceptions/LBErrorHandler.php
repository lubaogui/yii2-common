<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace lubaogui\account\exceptions;

use Yii;
use yii\base\Exception;
use yii\base\ErrorException;
use yii\web\HttpException;
use yii\web\Response;
use yii\base\UserException;
use yii\base\LBUserException;
use yii\helpers\VarDumper;

/**
 * ErrorHandler handles uncaught PHP errors and exceptions.
 *
 * ErrorHandler displays these errors using appropriate views based on the
 * nature of the errors and the mode the application runs at.
 *
 * ErrorHandler is configured as an application component in [[\yii\base\Application]] by default.
 * You can access that instance via `Yii::$app->errorHandler`.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Timur Ruziev <resurtm@gmail.com>
 * @since 2.0
 */
class LBErrorHandler extends \yii\base\ErrorHandler
{

    /**
     * @brief 初始化设置，主要设置错误页面模板等信息
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/30 11:30:42
    **/
    public function init() {


    }

    /**
     * Renders the exception.
     * @param \Exception $exception the exception to be rendered.
     */
    protected function renderException($exception)
    {

        //如果存在未提交的事务，则对事务进行回滚
        $transaction = Yii::$app->db->getTransaction();
        if ($transaction) {
            $transaction->rollback();
        }

        //对返回内容进行渲染
        if (Yii::$app->has('response')) {
            $response = Yii::$app->getResponse();

            // reset parameters of response to avoid interference with partially created response data
            // in case the error occurred while sending the response.
            $response->isSent = false;
            $response->stream = null;
            $response->data = null;
            $response->content = null;
        } else {
            $response = new Response();
        }

        $useErrorView = $response->format === Response::FORMAT_HTML && (!YII_DEBUG);

        //如果是用户定义的异常，则需要将异常错误信息抛出,如果是接口类型的并且有model类型的错误
        if ($useErrorView) {
            if ($this->errorAction !== null) {
                $result = Yii::$app->runAction($this->errorAction); 
                if ($result instanceof Response) { 
                    $result = $result;
                } else { 
                    $result->data = $result;
                }
            }
            else {
                //在没有默认异常处理action的情况下，直接渲染文件
                $file = $useErrorView ? $this->errorView : $this->exceptionView; 
                $responseData = $this->renderFile($file, ['exception'=>$exception]);
            }
        }
        else {
            if ($response->format === Response::FORMAT_JSON || $response->format === Response::FORMAT_XML) {
                $response->data = [
                    'code' => $exception->getCode(),
                        'message' => $exception->getMessage(),
                        ];
                if ($exception instanceof LBUserException) {
                    $response->data['errors'] = $exception->getErrors();
                }
            }
            else {
                $response->data = $this->convertExceptionToArray($exception);
            }
        }
    
        //调试状态状态码为500, 非调试状态不抛出异常 
        if (!YII_DEBUG || $exception instanceof LBUserException) {
            $response->setStatusCode(200);
        }
        else {
            $response->setStatusCode(500);
        }

        $response->send();
    }

    /**
     * Converts an exception into an array.
     * @param \Exception $exception the exception being converted
     * @return array the array representation of the exception.
     */
    protected function convertExceptionToArray($exception)
    {
        if (!YII_DEBUG && !$exception instanceof UserException && !$exception instanceof HttpException) {
            $exception = new HttpException(500, 'There was an error at the server.');
        }
        
        $errorCode = $exception->getCode();
        $array = [
            'code' => $errorCode ? $errorCode : 500,
            'message' => $exception->getMessage(),
        ];
        if ($exception instanceof LBUserException) {
            $array['data'] = $exception->getErrors();
        }

        return $array;
    }

}

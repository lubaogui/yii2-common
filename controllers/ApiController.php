<?php
namespace common\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\User;
use common\validators\SignValidator;
use lubaogui\account\exceptions\LBUserException;

/**
 * @brief ApiContoller 基于移动端的controller接口基类
 */
class ApiController extends Controller
{

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'data',
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['contentNegotiator']['formats']['application/xml']);
        return $behaviors;
    }

    /**
     * @brief 在实际执行用户定义的action之前所做的操作，主要做请求有效性验证等和业务逻辑无关联的操作
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $requestValidator = new SignValidator();
            if ($requestValidator->load()->validate())
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterAction($action, $data)
    {
        $data = parent::afterAction($action, $data);
        // your custom code here
        $result = [
            'code'=>$this->code ? $this->code : 0,
            'message'=>$this->message ? $this->message : 0,
            '_meta'=>is_array($this->meta) ? $this->meta : [],
            '_links'=>$this->links ? $this->links : [],
            'data'=>$data,
            ];
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

<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace lubaogui\account\exceptions;

use yii\base\UserException;

/**
 * UserException is the base class for exceptions that are meant to be shown to end users.
 * Such exceptions are often caused by mistakes of end users.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class LBUserException extends UserException
{

    //$_errors保存需要返回给客户端的错误信息，在接口api中特别有用, 如果models有错误信息
    private $_errors;

    /**
     * @brief 构建一个异常,errors代表携带的错误信息
     *
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/20 16:25:18
    **/
    public function __construct($message, $code = 1, $errors = null, $previous = null) {

        if (!empty($errors)) {
            $this->_errors = $errors;
        }
        parent::__construct($message, $code, $previous); 

    }

    /**
     * @brief 获取错误信息
     *
     * @return  array 错误信息数组 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/01/02 12:40:24
    **/
    public function getErrors() {
        return $this->_errors;
    }

}

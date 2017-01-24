<?php
/**
 * @Author: cailihua
 * @Date:   2016-11-08 12:13:00
 * @Last Modified by:   cailihua
 * @Last Modified time: 2016-11-08 12:20:08
 */
namespace backend\components;
use Yii;

class UserRule extends \yii\rbac\rule
{
	public function execute($user,$item,$params){
		if(Yii::$app->controller->action->id !== 'update' || Yii::$app->user->id==$params['id']){
			return true;
		}
		return false;
	}

}
?>

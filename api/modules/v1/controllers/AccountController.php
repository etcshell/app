<?php
namespace api\modules\v1\controllers;

use Yii;
use api\components\ApiController;
use yii\data\ActiveDataProvider;
use frontend\models\SignupForm;

class AccountController extends ApiController
{

    public $modelNmae = 'User';

    public $modelClass = 'common\models\User';

    public function actionCreate()
    {
        $model = new $this->modelClass();
        $model->attributes = Yii::$app->request->post();
        $password = Yii::$app->request->post()['password_hash'];
        $model->setPassword($password);
        $model->generateAuthKey();
        if (! $model->save()) {
            return array_values($model->getFirstErrors())[0];
        }
        return $model;
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionCreate2()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->attributes = Yii::$app->request->post();
        if (! $model->save()) {
            return array_values($model->getFirstErrors())[0];
        }
        return $model;
    }

    public function actionDelete($id)
    {
        return $this->findModel($id)->delete();
    }

    public function actionView($id)
    {
        return $this->findModel($id);
    }

    protected function findModel($id)
    {
        $modelClass = $this->modelClass;
        if (($model = $modelClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        // 检查用户能否访问 $action 和 $model
        // 访问被拒绝应抛出ForbiddenHttpException
        // var_dump($params);exit;
    }

}

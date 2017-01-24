<?php
namespace api\modules\v1\controllers;

use api\components\AuthController;
use yii\data\ActiveDataProvider;

class UserController extends AuthController
{

    //public $modelNmae = 'User';
    public $modelClass = 'common\models\User';

    // public function actions()
    // {
    //     return array_merge(
    //         parent::actions(),
    //         [
    //             'index' => [
    //                 'class' => 'yii\rest\IndexAction',
    //                 'modelClass' => $this->modelClass,
    //                 'checkAccess' => [$this, 'checkAccess'],
    //                 'prepareDataProvider' => function ($action) {
    //                     /* @var $model Post */
    //                     $model = new $this->modelClass;
    //                     $query = $model::find();
    //                     $dataProvider = new ActiveDataProvider(['query' => $query]);
    //                     $model->setAttribute('title', @$_GET['title']);
    //                     $query->andFilterWhere(['like', 'title', $model->title]);
    //                     return $dataProvider;
    //                 }
    //             ]
    //         ]
    //     );
    // }

    public function actionIndex()
    {
        $modelClass = $this->modelClass;
        $query = $modelClass::find();
        return new ActiveDataProvider([
            'query' => $query
        ]);
    }

    public function actionCreate()
    {
        $model = new $this->modelClass();
        // $model->load(Yii::$app->getRequest()
        // ->getBodyParams(), '');
        $model->attributes = Yii::$app->request->post();
        if (! $model->save()) {
            return array_values($model->getFirstErrors())[0];
        }
        return $model;
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

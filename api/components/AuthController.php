<?php
namespace api\components;

use Yii;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\auth\HttpBearerAuth;

class AuthController extends ActiveController
{
  // public $modelClassPath = @common;
  // public $modelName = '';
  // public $modelClass = $modelClassPath.'/models/'.$modelName;
  public $serializer = [
      'class' => 'yii\rest\Serializer',
      'collectionEnvelope' => 'items'
  ];

  public function behaviors()
  {
      return ArrayHelper::merge(parent::behaviors(), [
        'authenticator' => [
          'class' => CompositeAuth::className(),
          'authMethods' => [
            HttpBearerAuth::className(),
            HttpBasicAuth::className(),
            QueryParamAuth::className(),
          ]
        ],
        'rateLimiter'=>[
          'enableRateLimitHeaders' => true
        ]
      ]);
  }

  public function actions()
  {
      $actions = parent::actions();
      unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
      return $actions;
  }

}
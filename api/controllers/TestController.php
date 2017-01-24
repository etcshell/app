<?php

namespace api\controllers;

use Yii;
use api\components\BaseController;
use yii\helpers\Url;
use yii\mongodb\Query;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use common\models\mongodb\Customer;
use common\models\mongodb\ExternalApiUser;

class TestController extends BaseController
{
  public function actionIndex()
  {
    $customer = new ExternalApiUser();
    $customer->userName = 'teddy bear';
    $customer->status = 1;
    $y= [
      "apiKey" =>'dd',
      "publicKey" => "publickey-123",
      "secreteKey" =>"secreteKey-123" // 用来对sign进行签名
    ];
    $customer->apiKeyInfos=$y;
    $customer->save();
    echo $customer->_id;

    $x=$customer->createAuthkey((string)$customer->_id);

  $y= [
    "apiKey" =>$x,
    "publicKey" => "publickey-123",
    "secreteKey" =>"secreteKey-256" // 用来对sign进行签名
];
echo $x;
$customer->apiKeyInfos=$y;
$customer->save();
//$customer->update(['apiKeyInfos'=>$y]);
    die();
      $collection = Yii::$app->mongodb->getCollection ( 'customer' );
      $res = $collection->insert ( [
      'name' => 'John Smithz',
      'status' => 1
      ] );
      var_dump($res);
  }
  public function actionList()
  {
      $query = new Query ();
      $query->select ( [
      'name',
      'status'
      ] )->from ( 'customer' )->offset ( 10 )->limit ( 10 );
      $rows = $query->all ();
      var_dump ( $rows );
  }
  public function x()
  {
      $query = new Query ();
      $row = $query->from ( 'customer' )->one ();
      echo Url::toRoute ( [
      'item/update',
      'id' => ( string ) $row ['_id']
      ] );
      var_dump ( $row ['_id'] );
      var_dump ( ( string ) $row ['_id'] );
  }
  public function actionFind()
  {
      $provider = new ActiveDataProvider ( [
      'query' => Customer::find (),
      'pagination' => [
      'pageSize' => 10
      ]
      ] );
      $models = $provider->getModels ();
      var_dump ( $models );
  }
  public function actionQuery()
  {
      $query = new Query ();
      $query->from ( 'customer' )->where ( [
      'status' => 2
      ] );
      $provider = new ActiveDataProvider ( [
      'query' => $query,
      'pagination' => [
      'pageSize' => 10
      ]
      ] );
      $models = $provider->getModels ();
      var_dump ( $models );
  }
  public function actionSave()
  {
      $res = Customer::saveInfo ();
      var_dump ( $res );
  }
}

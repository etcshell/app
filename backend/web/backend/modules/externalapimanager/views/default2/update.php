<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\mongodb\ExternalApiUser */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'External Api User',
]) . $model->_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'External Api Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->_id, 'url' => ['view', 'id' => (string)$model->_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="external-api-user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

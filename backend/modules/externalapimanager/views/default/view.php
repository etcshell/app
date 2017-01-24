<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\mongodb\ExternalApiUser */

$this->title = $model->_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'External Api Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="external-api-user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => (string)$model->_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            '_id',
            'userName',
            'company',
            'avatar',
            'status',
            'summary',
            'apiKeyInfos',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>

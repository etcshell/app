<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\mongodb\ExternalApiUser */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="external-api-user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'userName') ?>

    <?= $form->field($model, 'company') ?>

    <?= $form->field($model, 'avatar') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'summary') ?>

    <?= $form->field($model, 'apiKeyInfos') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

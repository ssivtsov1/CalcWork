<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'enableAjaxValidation' => false,]); ?>
  
    <?= $form->field($model, 'work')->textarea() ?>
    <?= $form->field($model, 'cast_1')->textInput() ?>
    <?= $form->field($model, 'cast_2')->textInput() ?>
    <?= $form->field($model, 'cast_3')->textInput() ?>
    <?= $form->field($model, 'cast_4')->textInput() ?>
    <?= $form->field($model, 'cast_5')->textInput() ?> 
    <?= $form->field($model, 'cast_6')->textInput() ?> 
    <?= $form->field($model, 'usluga')->textarea() ?>
    <?= $form->field($model, 'brig')->textInput() ?>
    <?= $form->field($model, 'stavka_grn')->textInput() ?>
    <?= $form->field($model, 'time_transp')->textInput() ?>
    <?= $form->field($model, 'type_transp')->textInput() ?>
    
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'ОК' : 'OK', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


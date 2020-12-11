<?php
//namespace app\models;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\spr_res;

?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'enableAjaxValidation' => false,]); ?>
  
    <?= $form->field($model, 'transport')->textInput() ?>
    <?= $form->field($model, 'nomer')->textarea() ?>
    <?= $form->field($model, 'proezd')->textarea() ?>
    <?= $form->field($model, 'prostoy')->textInput() ?>
    <?=$form->field($model, 'locale')->dropDownList(ArrayHelper::map(spr_res::find()->all(), 'id', 'nazv')) ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'ОК' : 'OK', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>





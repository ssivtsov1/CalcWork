<?php
//namespace app\models;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\spr_res;
use app\models\status_sch;

?>
<br>
<div class="row">
    <div class="col-lg-5">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'enableAjaxValidation' => false,]); ?>

    <?=$form->field($model, 'status')->dropDownList(ArrayHelper::map(status_sch::find()->all(), 'id', 'nazv')) ?>

    <?= $form->field($model, 'okpo')->textInput() ?>
    <?= $form->field($model, 'inn')->textInput() ?>
    <?= $form->field($model, 'regsvid')->textInput() ?>
    <?= $form->field($model, 'nazv')->textarea() ?>
    <?= $form->field($model, 'priz_nds')->textInput() ?>

    <?= $form->field($model, 'addr')->textarea() ?>
    <?= $form->field($model, 'email')->textInput() ?>
    <?= $form->field($model, 'comment')->textarea() ?>
    <?= $form->field($model, 'schet')->textInput() ?>
    <?= $form->field($model, 'contract')->textInput() ?>
    <?= $form->field($model, 'usluga')->textarea() ?>

    <?= $form->field($model, 'summa')->textInput() ?>
    <?= $form->field($model, 'summa_beznds')->textInput() ?>
    <?= $form->field($model, 'summa_work')->textInput() ?>
    <?= $form->field($model, 'summa_delivery')->textInput() ?>
    <?= $form->field($model, 'summa_transport')->textInput() ?>
    <?= $form->field($model, 'adres')->textarea() ?>
    <?= $form->field($model, 'res')->textInput() ?>
<!--    --><?//= $form->field($model, 'date_z')->textInput() ?>
        <?= $form->field($model, 'date_z')->
        widget(\yii\jui\DatePicker::classname(), [
            'language' => 'uk'
        ]) ?>
    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'time')->textInput() ?>
    

    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'ОК' : 'OK', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a('Сформувати рахунок',['site/opl'], [
            'data' => [
                'method' => 'post',
                'params' => [
                    'sch' => $nazv,
                ],
            ],'class' => 'btn btn-info']); ?>
    </div>

    <?php ActiveForm::end(); ?>
    </div>
</div>





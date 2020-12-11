<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

$this->title = 'Завантаження файла виписки OTP банка';
$this->params['breadcrumbs'][] = $this->title;

?>


<div class="site-login">

    <h4><?= Html::encode($this->title) ?></h4>

    <!--    <p>Введіть реквізіти:</p>-->

    <div class="row row_reg">
        <div class="col-lg-6">
            <br>
            <br>
            <?php $form = ActiveForm::begin(['id' => 'inputdata',
                'options' => [
                    'class' => 'form-horizontal col-lg-25',
                    'enctype' => 'multipart/form-data',
                    //'enableClientValidation' => false,
                    'fieldConfig' => ['errorOptions' => ['encode' => false, 'class' => 'help-block']

                    ]]]); ?>

            <?= $form->field($model, 'file')->fileInput(); ?>

            <?php if(Yii::$app->session->hasFlash('success')):?>
                <span class="label label-success" ><?php echo Yii::$app->session->getFlash('success'); ?></span>
            <?php endif; ?>

            <?php if(Yii::$app->session->hasFlash('Error')):?>
                <span class="label label-danger" ><?php echo Yii::$app->session->getFlash('Error'); ?></span>

            <?php endif; ?>
            <?php if(!Yii::$app->session->hasFlash('Error')):?>
                <?php echo ' '; ?>

            <?php endif; ?>


            <div class="form-group">
                <?= Html::submitButton('OK', ['class' => 'btn btn-primary']); ?>

            </div>
            <?php
            ActiveForm::end();
            ?>
        </div>
    </div>
</div>








<?php
// Ввод основных данных для поиска данных

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
$this->title = 'Звіт по оплаті';
?>



<div class="site-login">
    <h3><?= Html::encode('Звіт по оплаті') ?></h3>
    <div class="row">

        <?php //debug(Yii::$app->user->identity); ?>

        <div>
            <?php $form = ActiveForm::begin(['id' => 'inputperiod',
                'options' => [
                    'class' => 'form-horizontal col-lg-25',
                    'enctype' => 'multipart/form-data'

                ]]); ?>
            <?php
            $session = Yii::$app->session;
            $session->open();
            if($session->has('user'))
                $user = $session->get('user');
            else
                $user = '';

            $flg=0;
            $pos=strpos($user,'РЕМ');
            if(!($pos===false))
                $flg=1;
            ?>



            <?= $form->field($model, 'date1')->
            widget(\yii\jui\DatePicker::classname(), [
                'language' => 'uk'
            ]) ?>
            <?= $form->field($model, 'date2')->
            widget(\yii\jui\DatePicker::classname(), [
                'language' => 'uk'
            ]) ?>


            <div class="form-group">
                <?= Html::submitButton('OK', ['class' => 'btn btn-primary','id' => 'btn_find','onclick'=>'dsave()']); ?>
                <!--                --><?//= Html::a('OK', ['/CalcWork/web'], ['class' => 'btn btn-success']) ?>
            </div>

            <?php


            ActiveForm::end(); ?>
        </div>
    </div>
</div>







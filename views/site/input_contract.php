<?php
// Ввод основных данных для поиска данных

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
$this->title = 'Формування договору';
$model->n_cnt= $model1[0]['contract'];
//debug($sch);
//return;
$model->sch=$sch;
$model->sch1=$sch1;
$model->mail=$mail;

?>


<div class="site-login">
    <h3><?= Html::encode('Формування договору') ?></h3>
    <div class="row">

        <?php //debug(Yii::$app->user->identity); ?>

        <div>
            <?php $form = ActiveForm::begin(['id' => 'input_contract',
                'options' => [
                    'class' => 'form-horizontal col-lg-6',
                    'enctype' => 'multipart/form-data'

                ]]); ?>
            <?php
            $session = Yii::$app->session;
            $session->open();
            if($session->has('user'))
                $user = $session->get('user');
            else
                $user = '';
            ?>

            <?= $form->field($model, 'n_cnt') ?>
            <?= $form->field($model, 'sch')->hiddenInput()->label(false)  ?>
            <?= $form->field($model, 'sch1')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'mail') ->hiddenInput()->label(false)?>

            <div class="form-group">
                <?= Html::submitButton('OK', ['class' => 'btn btn-primary','id' => 'btn_find','onclick'=>'dsave()']); ?>
                <!--                --><?//= Html::a('OK', ['/CalcWork/web'], ['class' => 'btn btn-success']) ?>
            </div>

            <?php


            ActiveForm::end(); ?>
        </div>
    </div>
</div>







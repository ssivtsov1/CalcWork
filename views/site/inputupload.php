<?php
// Ввод основных данных для поиска данных

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
$this->title = 'Звіт по оплаті';
?>



<div class="site-login">
    <?php if($parameter==1): ?>
        <h3><?= Html::encode('Вигрузка в САП') ?></h3>
    <?php else: ?>
        <h3><?= Html::encode('Звіт для  САП') ?></h3>
    <?php endif; ?>
    <div class="row">

        <?php //debug(Yii::$app->user->identity); ?>

        <div>
            <?php $form = ActiveForm::begin(['id' => 'inputperiod',
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

            <?=$form->field($model, 'usl')->
            dropDownList(ArrayHelper::map(
               app\models\spr_costwork::findbysql('Select min(id) as id,usluga from costwork where LENGTH(ltrim(rtrim(usluga)))<>0 group by usluga order by usluga')
                   ->all(), 'id', 'usluga'),
                    []) ?>

            <?= $form->field($model, 'id_sw')
                ->dropDownList([
                    '1' => 'Підключення',
                    '2' => 'Відключення',
                ],
                    [
                        'prompt' => 'Виберіть вид послуги (тільки для підключення або відключення)'
                    ]);?>

            <div class="form-group">
                <?= Html::submitButton('OK', ['class' => 'btn btn-primary','id' => 'btn_find','onclick'=>'dsave()']); ?>
                <!--                --><?//= Html::a('OK', ['/CalcWork/web'], ['class' => 'btn btn-success']) ?>
            </div>

            <?php


            ActiveForm::end(); ?>
        </div>
    </div>
</div>







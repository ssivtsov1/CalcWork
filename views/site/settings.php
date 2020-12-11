<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
$this->title = 'Настройки';
//$model->person = '1';
//$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login">

    <h4><?= Html::encode($this->title) ?></h4>

    <div class="row row_reg">
        <div class="col-lg-6">
            <?php $form = ActiveForm::begin(['id' => 'inputdata',
                'options' => [
                    'class' => 'form-horizontal col-lg-25',
                    'enctype' => 'multipart/form-data',
                    'fieldConfig' => ['errorOptions' => ['encode' => false, 'class' => 'help-block']
                    
                ]]]); ?>

            <br>
            <?= $form->field($model, 'contract_hap')->checkbox(); ?>
                
          
            <div class="form-group">
                <?= Html::submitButton('OK', ['class' => 'btn btn-primary']); ?>
            </div>
            <?php
                ActiveForm::end();
            ?>
        </div>
    </div>
</div>






   

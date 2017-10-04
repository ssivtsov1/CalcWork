<?php
//namespace app\models;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\spr_res;
use app\models\status_sch;
$role = Yii::$app->user->identity->role;
?>
<br>
<div class="row">
    <div class="col-lg-6">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'enableAjaxValidation' => false,]); ?>

    <?php
        // Установка статусов в соответствии с доступами
        switch($role) {
        case 3: // Полный доступ
            echo $form->field($model, 'status')->dropDownList(ArrayHelper::map(status_sch::find()->all(), 'id', 'nazv'));
            break;
        case 2:  // финансовый отдел
            echo $form->field($model, 'status')->dropDownList(
                ArrayHelper::map(status_sch::find()->where('id=:status1',[':status1' => 2])->
                orwhere('id=:status2',[':status2' => 3])->
                all(), 'id', 'nazv'));
                break;

        case 1:  // бухгалтерия
            echo $form->field($model, 'status')->dropDownList(
                ArrayHelper::map(status_sch::find()->where('id=:status1',[':status1' => 5])->
                orwhere('id=:status2',[':status2' => 7])->
                all(), 'id', 'nazv'));
            break;
        }
    ?>

    <?php
        if($model->status==5){
            if(!empty($model->act_work)) {
               echo $form->field($model, 'act_work')->textInput(['readonly' => true]);
               echo $form->field($model, 'date_akt')->textInput(['readonly' => true]);
           }
        }
            
    ?>

    <?php
    if($model->status==8){
            echo $form->field($model, 'why_refusal')->textInput();
    }
    ?>

    <?= $form->field($model, 'okpo')->textInput() ?>
    <?= $form->field($model, 'inn')->textInput() ?>
    <?= $form->field($model, 'regsvid')->textInput() ?>
    <?= $form->field($model, 'nazv')->textarea() ?>

    <?php if($model->priz_nds==0): $model->plat_yesno='ні';?>

        <?= $form->field($model, 'plat_yesno')->textInput(['readonly' => true]) ?>
    <?php endif; ?>
    <?php if($model->priz_nds==1): $model->plat_yesno='так';?>
        <?= $form->field($model, 'plat_yesno')->textInput(['readonly' => true]) ?>
    <?php endif; ?>

    <?= $form->field($model, 'addr')->textarea() ?>
    <?= $form->field($model, 'email')->textInput() ?>
    <?= $form->field($model, 'comment')->textarea() ?>
    <?= $form->field($model, 'schet')->textInput() ?>
    <?= $form->field($model, 'contract')->textInput() ?>
    <?= $form->field($model, 'usluga')->textarea(['rows' => 3, 'cols' => 25]) ?>

    <?= $form->field($model, 'summa')->textInput() ?>
    <?= $form->field($model, 'summa_beznds')->textInput() ?>
    <?= $form->field($model, 'summa_work')->textInput() ?>
    <?= $form->field($model, 'summa_delivery')->textInput() ?>
    <?= $form->field($model, 'summa_transport')->textInput() ?>
    <?= $form->field($model, 'adres')->textarea() ?>
    <?= $form->field($model, 'res')->textInput() ?>
<!--    --><?//= $form->field($model, 'date_z')->textInput() ?>

        <? if($model->status>1): ?>
            <?= $form->field($model, 'date_opl')->
            widget(\yii\jui\DatePicker::classname(), [
                'language' => 'uk'
            ]) ?>
        <? endif;?>

        <?= $form->field($model, 'date_z')->
        widget(\yii\jui\DatePicker::classname(), [
            'language' => 'uk'
        ]) ?>

        <? if($model->status>1): ?>
        <?= $form->field($model, 'date_exec')->
        widget(\yii\jui\DatePicker::classname(), [
            'language' => 'uk'
        ]) ?>
        <? endif;?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'time')->textInput() ?>

    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'ОК' : 'OK', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
         <?php
        if($model->status>1){ ?>
        <?= Html::a('Сформувати рахунок',['site/opl'], [
            'data' => [
                'method' => 'post',
                'params' => [
                    'sch' => $nazv,
                ],
            ],'class' => 'btn btn-info']); ?>
       <?php } ?> 
        
    <?php
        if($model->status==5){
            if(!empty($model->act_work)) { ?>    
    <?= Html::a('Акт виконаних робіт',['site/act_work'], [
            'data' => [
                'method' => 'post',
                'params' => [
                    'sch' => $nazv,
                ],
            ],'class' => 'btn btn-info']); ?>

                <?= Html::a('Договір',['site/contract'], [
                    'data' => [
                        'method' => 'post',
                        'params' => [
                            'sch' => $nazv,
                        ],
                    ],'class' => 'btn btn-info']); ?>
         
                <?= Html::a('Повідом.',['site/message'], [
                    'data' => [
                        'method' => 'post',
                        'params' => [
                            'sch' => $nazv,
                        ],
                    ],'class' => 'btn btn-info']); ?>
        <?php }} ?>
        
    </div>

    <?php ActiveForm::end(); ?>
    </div>
</div>





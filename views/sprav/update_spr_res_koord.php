<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\spr_res;
use yii\bootstrap\ActiveForm;

?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'enableAjaxValidation' => false,]); ?>
    
    <?= $form->field($model, 'name_koord',
             ['inputTemplate' => '<div class="input-group"><span class="input-group-btn">'.
            '<button class="btn btn-default"><span class="glyphicon glyphicon-wrench"></span></button></span>{input}</div>'])
                ->textInput(['onDblClick' => 'rmenu($(this).val(),"#spr_res_koord-name_koord")']) ?>
    
    <div class='rmenu' id='rmenu-spr_res_koord-name_koord'></div>
    <?= $form->field($model, 'tel_mobile',
        ['inputTemplate' => '<div class="input-group"><span class="input-group-addon">'
            . '<span class="glyphicon glyphicon-phone"></span></span>{input}</div>'])
                ->textInput(['onBlur' => 'norm_tel($(this).val())',]) ?>
    <?= $form->field($model, 'tel_town',
        ['inputTemplate' => '<div class="input-group"><span class="input-group-addon">'
            . '<span class="glyphicon glyphicon-phone-alt"></span></span>{input}</div>'])->textInput() ?>
    <?= $form->field($model, 'tel',['inputTemplate' => '<div class="input-group"><span class="input-group-addon">'
            . '<span class="glyphicon glyphicon-phone-alt"></span></span>{input}</div>'])->textInput() ?>
    <?= $form->field($model, 'tel_dop',['inputTemplate' => '<div class="input-group"><span class="input-group-addon">'
            . '<span class="glyphicon glyphicon-earphone"></span></span>{input}</div>'])->textInput(['onBlur' => 'norm_tel($(this).val())']) ?>
     <?=$form->field($model, 'id_res')->dropDownList(ArrayHelper::map(spr_res::find()->all(), 'id', 'nazv')) ?>
     <?= $form->field($model, 'email',['inputTemplate' => '<div class="input-group"><span class="input-group-addon">'
            . '<span class="glyphicon glyphicon-envelope"></span></span>{input}</div>'])->textInput() ?>
       
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'ОК' : 'OK', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>

<script>

function norm_tel(p){

var y,i,c,tel = '',kod,op,flag=0,rez='';
y = p.length;

for(i=0;i<y;i++)
{
c = p.substr(i,1);
kod=p.charCodeAt(i);
if(kod>47 && kod<58) tel+=c;
}
op = tel.substr(0,3);
y = tel.length;
if(y<10) {
return 1;
}
switch(op) {
    case '050':  flag = 1;
    break;
    case '096':  flag = 1;
    break;
    case '097':  flag = 1;
    break;
    case '098':  flag = 1;
    break;
    case '099':  flag = 1;
    break;

    case '091':  flag = 1;
    break;
    case '063':  flag = 1;
    break;
    case '073':  flag = 1;
    break;
    case '067':  flag = 1;
    break;
    case '066':  flag = 1;
    break;

    case '093':  flag = 1;
    break;
    case '095':  flag = 1;
    break;
    case '039':  flag = 1;
    break;
    case '068':  flag = 1;
    break;
    case '092':  flag = 1;
    break;
    case '094':  flag = 1;
    break;
}

var add = tel.substr(3,3);
rez+=add+'-';
add = tel.substr(6,2);
rez+=add+'-';
add = tel.substr(8);
rez+=add;

if(flag) {
rez = op+' '+rez;
}
else{
rez = '('+op+')'+' '+rez;
}
$('#spr_res_koord-tel_mobile').val(rez);
}

</script>

<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
//$model->transp_cek=1;
?>

<div class="user-form">
     <div class="col-lg-6">
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'enableAjaxValidation' => false,]); ?>
  
    <?= $form->field($model, 'work')->textarea() ?>
    <?= $form->field($model, 'cast_1')->textInput(['maxlength' => true,'onBlur' => 'norm_pole($(this).val(),"#spr_work-cast_1")']) ?>
    <?= $form->field($model, 'cast_2')->textInput(['maxlength' => true,'onBlur' => 'norm_pole($(this).val(),"#spr_work-cast_2")']) ?>
    <?= $form->field($model, 'cast_3')->textInput(['maxlength' => true,'onBlur' => 'norm_pole($(this).val(),"#spr_work-cast_3")']) ?>
    <?= $form->field($model, 'cast_4')->textInput(['maxlength' => true,'onBlur' => 'norm_pole($(this).val(),"#spr_work-cast_4")']) ?>
    <?= $form->field($model, 'cast_5')->textInput(['maxlength' => true,'onBlur' => 'norm_pole($(this).val(),"#spr_work-cast_5")']) ?> 
    <?= $form->field($model, 'cast_6')->textInput(['maxlength' => true,'onBlur' => 'norm_pole($(this).val(),"#spr_work-cast_6")']) ?> 
    <!--<?= $form->field($model, 'usluga')->textarea() ?>-->
    
    <!--<?=$form->field($model, 'usluga')->dropDownList(
                    ArrayHelper::map(app\models\spr_uslug::find()->all(), 'id', 'usluga'));?>-->
                    
     <?php
    $items1=ArrayHelper::map(app\models\spr_uslug::find()->all(), 'id', 'usluga');
    $key = array_search($model->usluga, $items1); 
    $param1 = ['options' =>[ $key => ['Selected' => true]]];
    ?>
    
    <?=$form->field($model, 'usluga')->dropDownList($items1, $param1);?>                   
    
    <!--<?= $form->field($model, 'brig')->textInput() ?>-->
    
    <?php
    $items=ArrayHelper::map(app\models\spr_brig::
                            findbysql('select id,trim(nazv) as nazv from spr_brig where nazv is not null')->all(), 'id', 'nazv');
    $key = array_search($model->brig, $items); 
    $param = ['options' =>[ $key => ['Selected' => true]]];
    ?>
    
    <?=$form->field($model, 'brig')->dropDownList($items, $param);?>
    
    <?= $form->field($model, 'stavka_grn')->textInput(['maxlength' => true,'onBlur' => 'norm_pole($(this).val(),"#spr_work-stavka_grn")']) ?>
    <?= $form->field($model, 'time_transp')->textInput(['maxlength' => true,'onBlur' => 'norm_pole($(this).val(),"#spr_work-time_transp")']) ?>
    <!--<?= $form->field($model, 'type_transp')->textInput() ?>-->
    <?= $form->field($model, 'type_transp')->dropDownList([
    'А' => 'Звичайний',
    'В' => 'Вишка',
    'Л'=>'Лабораторія'
    ],
                    [
            'onchange' => '$.get("' . Url::to('/CalcWork/web/sprav/gettransp?vid=') . 
             '"+$(this).val(),
                    function(data) {
                         var flag=0,fl=0;
                         $("#spr_work-t_ap").empty();
                         $("#spr_work-t_dn").empty();
                         $("#spr_work-t_vg").empty();
                         $("#spr_work-t_yv").empty();
                         $("#spr_work-t_krr").empty();
                         $("#spr_work-t_pvg").empty();
                         $("#spr_work-t_ing").empty();
                         $("#spr_work-t_gv").empty();
                         for(var ii = 0; ii<data.transp.length; ii++) {
                         var q = $.trim(data.transp[ii].tr);
                         var n = $.trim(data.transp[ii].res);
                         if(q==null) continue;
                         if(n==1)
                         $("#spr_work-t_ap").append("<option>"+q+"</option>");
                         if(n==2)
                         $("#spr_work-t_dn").append("<option>"+q+"</option>");
                         if(n==3)
                         $("#spr_work-t_vg").append("<option>"+q+"</option>");
                         if(n==4)
                         $("#spr_work-t_yv").append("<option>"+q+"</option>");
                         if(n==5)
                         $("#spr_work-t_krr").append("<option>"+q+"</option>");
                         if(n==6)
                         $("#spr_work-t_pvg").append("<option>"+q+"</option>");
                         if(n==7)
                         $("#spr_work-t_ing").append("<option>"+q+"</option>");
                         if(n==8)
                         $("#spr_work-t_gv").append("<option>"+q+"</option>");
                  }});']
                     
                    ) ?>
    
           
    <?= $form->field($model, 'transp_cek')->checkbox(); ?>
    
    
     <?=$form->field($model, 'T_Ap')->
            dropDownList(ArrayHelper::map(
            app\models\spr_work::findbysql(
                    'SELECT min(id) as id, T_Ap FROM costwork WHERE type_transp='."'" .$model->type_transp."'".
                    ' and type_transp is not null group by T_Ap'
                    )->all(),'T_Ap','T_Ap')); ?>
    
    <?=$form->field($model, 'T_Dn')->
            dropDownList(ArrayHelper::map(
            app\models\spr_work::findbysql(
                    'SELECT min(id) as id, T_Dn FROM costwork WHERE type_transp='."'" .$model->type_transp."'".
                    ' and type_transp is not null group by T_Dn'
                    )->all(),'T_Dn','T_Dn')); ?>
    
    <?=$form->field($model, 'T_Vg')->
            dropDownList(ArrayHelper::map(
            app\models\spr_work::findbysql(
                    'SELECT min(id) as id, T_Vg FROM costwork WHERE type_transp='."'" .$model->type_transp."'".
                    ' and type_transp is not null group by T_Vg'
                    )->all(),'T_Vg','T_Vg')); ?>
    
    <?=$form->field($model, 'T_Yv')->
            dropDownList(ArrayHelper::map(
            app\models\spr_work::findbysql(
                    'SELECT min(id) as id, T_Yv FROM costwork WHERE type_transp='."'" .$model->type_transp."'".
                    ' and type_transp is not null group by T_Yv'
                    )->all(),'T_Yv','T_Yv')); ?>
    
     <?=$form->field($model, 'T_Krr')->
            dropDownList(ArrayHelper::map(
            app\models\spr_work::findbysql(
                    'SELECT min(id) as id, T_Krr FROM costwork WHERE type_transp='."'" .$model->type_transp."'".
                    ' and type_transp is not null group by T_Krr'
                    )->all(),'T_Krr','T_Krr')); ?>
    
    <?=$form->field($model, 'T_Pvg')->
            dropDownList(ArrayHelper::map(
            app\models\spr_work::findbysql(
                    'SELECT min(id) as id, T_Pvg FROM costwork WHERE type_transp='."'" .$model->type_transp."'".
                    ' and type_transp is not null group by T_Pvg'
                    )->all(),'T_Pvg','T_Pvg')); ?>
    
    <?=$form->field($model, 'T_Ing')->
            dropDownList(ArrayHelper::map(
            app\models\spr_work::findbysql(
                    'SELECT min(id) as id, T_Ing FROM costwork WHERE type_transp='."'" .$model->type_transp."'".
                    ' and type_transp is not null group by T_Ing'
                    )->all(),'T_Pvg','T_Ing')); ?>
    
    <?=$form->field($model, 'T_Gv')->
            dropDownList(ArrayHelper::map(
            app\models\spr_work::findbysql(
                    'SELECT min(id) as id, T_Gv FROM costwork WHERE type_transp='."'" .$model->type_transp."'".
                    ' and type_transp is not null group by T_Gv'
                    )->all(),'T_Gv','T_Gv')); ?>
    
    
    
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'ОК' : 'OK', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
    </div>

<script>
 function norm_pole(p,pole){
        
        var y,i,c,kod,rez='';
        y = p.length;
        for(i=0;i<y;i++)
        {
            c = p.substr(i,1);
            kod=p.charCodeAt(i);
            if(kod==44) c='.';
            if((kod>47 && kod<58) || c=='.') rez+=c;
        }
       //alert(rez); 
        $(pole).val(rez);
    }
</script>
    

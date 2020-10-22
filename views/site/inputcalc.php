<?php
// Ввод основных данных для поиска данных

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
$this->title = 'Введення калькуляцій';
?>

<div class="site-login">
    <h3><?= Html::encode('Введення калькуляцій') ?></h3>
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

            <?=$form->field($model, 'usluga')->
            dropDownList(ArrayHelper::map(
               app\models\spr_costwork::findbysql('Select min(id) as id,usluga 
                from costwork where LENGTH(ltrim(rtrim(usluga)))<>0 group by usluga order by usluga')
                   ->all(), 'id', 'usluga'),
                    []) ?>

<!--            --><?//= $form->field($model, 'usluga1')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'work')->textInput(['maxlength' => true,'onBlur' =>
                '$.get("'  . Url::to('/CalcWork/web/site/get_check_work?name=') .
                    '"+$(this).val(),
                   function(data) {
                         if(data.work.length>0)
                                         alert("Така робота вже присутня в списку послуг. Введіть унікальну роботу");
                             });']) ?>

            <?= $form->field($model, 'n_work')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'expense_brig')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'cost_work')->textInput(['maxlength' => true])  ?>
<!--            --><?//= $form->field($model, 'work')->textInput(['maxlength' => true]) ?>
            <span class="span_brig"><? echo "Склад бригади для проїзду"; ?> </span>
<!--            <div class="clearfix"></div>-->

            <?=$form->field($model, 'id_brig1')->
            dropDownList(ArrayHelper::map(
                app\models\spr_costwork::findbysql('SELECT min(id) as id,brig 
                FROM costwork  group by brig order by 2')
                    ->all(), 'id', 'brig'),
                []) ?>
            <?= $form->field($model, 'stavka_brig1')->textInput(['maxlength' => true])  ?>
            <div class="clearfix"></div>

            <?=$form->field($model, 'id_brig2')->
            dropDownList(ArrayHelper::map(
                app\models\spr_costwork::findbysql('SELECT min(id) as id,brig 
                FROM costwork  group by brig order by 2')
                    ->all(), 'id', 'brig'),
                []) ?>
            <?= $form->field($model, 'stavka_brig2')->textInput(['maxlength' => true])  ?>
            <div class="clearfix"></div>

            <?=$form->field($model, 'id_brig3')->
            dropDownList(ArrayHelper::map(
                app\models\spr_costwork::findbysql('SELECT min(id) as id,brig 
                FROM costwork  group by brig order by 2')
                    ->all(), 'id', 'brig'),
                []) ?>
            <?= $form->field($model, 'stavka_brig3')->textInput(['maxlength' => true])  ?>
            <div class="clearfix"></div>

            <?=$form->field($model, 'id_brig4')->
            dropDownList(ArrayHelper::map(
                app\models\spr_costwork::findbysql('SELECT min(id) as id,brig 
                FROM costwork  group by brig order by 2')
                    ->all(), 'id', 'brig'),
                []) ?>
            <?= $form->field($model, 'stavka_brig4')->textInput(['maxlength' => true])  ?>
            <div class="clearfix"></div>

            <?=$form->field($model, 'id_brig5')->
            dropDownList(ArrayHelper::map(
                app\models\spr_costwork::findbysql('SELECT min(id) as id,brig 
                FROM costwork  group by brig order by 2')
                    ->all(), 'id', 'brig'),
                []) ?>
            <?= $form->field($model, 'stavka_brig5')->textInput(['maxlength' => true])  ?>
            <div class="clearfix"></div>

            <span class="span_tr"><? echo "Транспорт"; ?> </span>

            <?=$form->field($model, 'id_auto1')->dropDownList(
                    ArrayHelper::map( app\models\spr_costwork::findbysql(
                            'SELECT min(id) as id,T_Ap FROM costwork
                                    where id<>48
                                    group by 2 order by 2')->all(),  'id', 'T_Ap'),
            []); ?>
            <?=$form->field($model, 'id_auto2')->dropDownList(
                    ArrayHelper::map( app\models\spr_costwork::findbysql(
                            "select min(id) as id,T_Vg from (
                            SELECT min(id) as id,T_Vg FROM costwork
                                                                where id<>48
                                                                group by 2 
                            UNION
                            SELECT 300+min(id) as id,number as T_Vg FROM a_transport
                                                                where trim(place)='ВгРЕМ'
                                                                group by 2
                            ) e
                            where T_Vg is not null
                            group by 2")->all(),  'id', 'T_Vg'),
            []);
            ?>
            <div class="clearfix"></div>

            <?=$form->field($model, 'id_auto3')->dropDownList(
                ArrayHelper::map( app\models\spr_costwork::findbysql(
                    'SELECT min(id) as id,T_Gv FROM costwork
                                    where id<>48
                                    group by 2 order by 2')->all(),  'id', 'T_Gv'),
                []); ?>
<!--            --><?//=$form->field($model, 'id_auto4')->dropDownList(
//                ArrayHelper::map( app\models\spr_costwork::findbysql(
//                    'SELECT min(id) as id,T_Dn FROM costwork
//                                    where id<>48
//                                    group by 2 order by 2')->all(),  'id', 'T_Dn'),
//                []);

                 echo $form->field($model, 'id_auto4')->dropDownList(
                ArrayHelper::map( app\models\spr_costwork::findbysql(
                    "select min(id) as id,T_Dn from (
                            SELECT min(id) as id,T_Dn FROM costwork
                                                                where id<>48
                                                                group by 2 
                            UNION
                            SELECT 300+min(id) as id,number as T_Dn FROM a_transport
                                                                where trim(place)='ДнРЕМ'
                                                                group by 2
                            ) e
                            where T_Dn is not null
                            group by 2")->all(),  'id', 'T_Dn'),
                []);

            ?>
            <div class="clearfix"></div>

            <?=$form->field($model, 'id_auto5')->dropDownList(
                ArrayHelper::map( app\models\spr_costwork::findbysql(
                    'SELECT min(id) as id,T_Ing FROM costwork
                                    where id<>48
                                    group by 2 order by 2')->all(),  'id', 'T_Ing'),
                []); ?>
            <?=$form->field($model, 'id_auto6')->dropDownList(
                ArrayHelper::map( app\models\spr_costwork::findbysql(
                    'SELECT min(id) as id,T_Yv FROM costwork
                                    where id<>48
                                    group by 2 order by 2')->all(),  'id', 'T_Yv'),
                []);
            ?>
            <div class="clearfix"></div>

<!--            --><?//=$form->field($model, 'id_auto7')->dropDownList(
//                ArrayHelper::map( app\models\spr_costwork::findbysql(
//                    'SELECT min(id) as id,T_Krr FROM costwork
//                                    where id<>48
//                                    group by 2 order by 2')->all(),  'id', 'T_Krr'),
//                []);


            echo $form->field($model, 'id_auto7')->dropDownList(
            ArrayHelper::map( app\models\spr_costwork::findbysql(
            "select min(id) as id,T_Krr from (
            SELECT min(id) as id,T_Krr FROM costwork
            where id<>48
            group by 2
            UNION
            SELECT 300+min(id) as id,number as T_Krr FROM a_transport
            where trim(place)='КрРЕМ'
            group by 2
            ) e
            where T_Krr is not null
            group by 2")->all(),  'id', 'T_Krr'),
            []);
            ?>
            <div class="clearfix"></div>

           <?=$form->field($model, 'id_auto8')->dropDownList(
            ArrayHelper::map( app\models\spr_costwork::findbysql(
            "select min(id) as id,T_Pvg from (
            SELECT min(id) as id,T_Pvg FROM costwork
            where id<>48
            group by 2
            UNION
            SELECT 300+min(id) as id,number as T_Pvg FROM a_transport
            where trim(place)='ПвРЕМ'
            group by 2
            ) e
            where T_Pvg is not null
            group by 2")->all(),  'id', 'T_Pvg'),
            []);
            ?>

            <div class="clearfix"></div>

            <?=$form->field($model, 'id_auto9')->dropDownList(
                ArrayHelper::map( app\models\spr_costwork::findbysql(
                    'SELECT min(id) as id,Szoe FROM costwork
                                    where id<>48
                                    group by 2 order by 2')->all(),  'id', 'Szoe'),
                []); ?>
            <?=$form->field($model, 'id_auto10')->dropDownList(
                ArrayHelper::map( app\models\spr_costwork::findbysql(
                    'SELECT min(id) as id,Sdizp FROM costwork
                                    where id<>48
                                    group by 2 order by 2')->all(),  'id', 'Sdizp'),
                []);
            ?>
            <div class="clearfix"></div>

            <?=$form->field($model, 'id_auto11')->dropDownList(
                ArrayHelper::map( app\models\spr_costwork::findbysql(
                    'SELECT min(id) as id,T_Sp FROM costwork
                                    where id<>48
                                    group by 2 order by 2')->all(),  'id', 'T_Sp'),
                []); ?>
            <div class="clearfix"></div>

            <?= $form->field($model, 'time_prostoy')->textInput(['maxlength' => true])  ?>
            <?= $form->field($model, 'time_work')->textInput(['maxlength' => true])  ?>

            <span class="span_other"><? echo "Інші параметри"; ?> </span>

            <?= $form->field($model, 'norm_time')->textInput(['maxlength' => true])  ?>
            <?= $form->field($model, 'salary_brig')->textInput(['maxlength' => true])  ?>
            <div class="clearfix"></div>
            <?= $form->field($model, 'common_expense')->textInput(['maxlength' => true])  ?>
            <?= $form->field($model, 'tmc')->textInput(['maxlength' => true])  ?>
            <div class="clearfix"></div>
            <?= $form->field($model, 'other')->textInput(['maxlength' => true])  ?>
            <?= $form->field($model, 'poverka')->textInput(['maxlength' => true])  ?>
            <div class="clearfix"></div>

<!--            --><?//=$form->field($model, 'otv_contract')->dropDownList(
//                ArrayHelper::map( app\models\spr_uslug::findbysql(
//                    'SELECT  min(id) as id, exec_person  FROM spr_uslug
//                            group by exec_person')->all(),  'id', 'exec_person'),
//                []); ?>

            <div class="form-group">
                <?= Html::submitButton('OK', ['class' => 'btn btn-primary','id' => 'btn_find','onclick'=>'dsave()']); ?>
                <!--                --><?//= Html::a('OK', ['/CalcWork/web'], ['class' => 'btn btn-success']) ?>
            </div>

            <?php

            ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
function select_res($role)
{
    if (!($role > 0))
//            $r = "select id,concat(town,'  (',nazv,')') as nazv from spr_res where id<>12 and id<>13";
        if ($role == 13)
            $r = "select a.id,concat(a.town,'  (',a.nazv,')') as nazv,b.role,b.department from costwork.spr_res a
left join costwork.user b on trim(a.nazv) = trim(b.department) COLLATE utf8_unicode_ci
where a.id<>12 and a.id<>13
and a.id in (1,7,8)
union select 15 as id,'' as nazv,$role as role,0 as department
";
        else
            $r = "select a.id,concat(a.town,'  (',a.nazv,')') as nazv,b.role,b.department from costwork.spr_res a
left join costwork.user b on trim(a.nazv) = trim(b.department) COLLATE utf8_unicode_ci
where a.id<>12 and a.id<>13
and case when $role<6 then 1=1 else b.role=$role and b.department is not null end
union select 15 as id,'' as nazv,$role as role,0 as department
 ";
    else
//            $r = "select id,concat(town,'  (',nazv,')') as nazv from spr_res";
        if ($role == 13)
            $r = "select a.id,concat(a.town,'  (',a.nazv,')') as nazv,b.role,b.department from costwork.spr_res a
left join costwork.user b on trim(a.nazv) = trim(b.department) COLLATE utf8_unicode_ci
where a.id in (1,7,8)
union select 15 as id,'' as nazv,$role as role,0 as department
";
        else
            $r = "select * from
(select a.id,concat(a.town,'  (',a.nazv,')') as nazv,b.role,b.department from costwork.spr_res a
left join costwork.user b on trim(a.nazv) = trim(b.department) COLLATE utf8_unicode_ci
where case when $role<6 then 1=1 else b.role=$role and b.department is not null end 
union select 0 as id,'' as nazv,$role as role,0 as department) y order by 1
";
return $r;
}
?>
<script>
    function check_work(p){
       alert(p);
    }
</script>








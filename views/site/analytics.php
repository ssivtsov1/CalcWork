<?php

use app\models\Spr_res;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\status_sch;
use app\models\describe_fields;
use yii\bootstrap\Modal;
$this->title = 'Аналітика';

?>
<script>
    
   window.addEventListener('load', function(){
          localStorage.setItem("gr_status_sch", 'false'); 
          localStorage.setItem("gr_res", 'false');
          localStorage.setItem("gr_date", 'false'); 
          localStorage.setItem("gr_date_opl", 'false'); 
          localStorage.setItem("gr_usluga", 'false'); 
          localStorage.setItem("gr_usl", 'false'); 
    }); 
</script>    
<div class="site-login">

    <h3><?= Html::encode($this->title) ?></h3>

    <div class="row row_reg">
        <div class="col-lg-12">
            <?php $form = ActiveForm::begin(['id' => 'analytics',
                'options' => [
                    'class' => 'form-horizontal col-lg-6',
                    'enctype' => 'multipart/form-data',
                    'fieldConfig' => ['errorOptions' => ['encode' => false, 'class' => 'help-block']
                    
                ]]]); ?>
            <div class="analit-filtr">
            <h5 class="text-primary"><b><?= Html::encode("Фільтрація") ?></b></h5>
            <?= $form->field($model, 'res')->dropDownList(
                    ArrayHelper::map(app\models\spr_res::findbysql(
                    "select id,concat(town,'  (',nazv,')') as nazv from spr_res")->all(), 'id', 'nazv'),
                    ['prompt' => 'Виберіть РЕМ']); ?>
            
            <?= $form->field($model, 'status')->dropDownList(ArrayHelper::map(status_sch::find()->all(), 'id', 'nazv'),
                    ['prompt' => 'Виберіть статус']); ?>
            
            <?= $form->field($model, 'date1')->widget(\yii\jui\DatePicker::classname(), [
                'language' => 'uk',
            ]) ?>
            <?= $form->field($model, 'date2')->widget(\yii\jui\DatePicker::classname(), [
                'language' => 'uk',
            ]) ?>
            
            <?= $form->field($model, 'date_opl1')->widget(\yii\jui\DatePicker::classname(), [
                'language' => 'uk',
            ]) ?>
            <?= $form->field($model, 'date_opl2')->widget(\yii\jui\DatePicker::classname(), [
                'language' => 'uk',
            ]) ?>

                <?= $form->field($model, 'date_act1')->widget(\yii\jui\DatePicker::classname(), [
                    'language' => 'uk',
                ]) ?>
                <?= $form->field($model, 'date_act2')->widget(\yii\jui\DatePicker::classname(), [
                    'language' => 'uk',
                ]) ?>
            
             <?=$form->field($model, 'usluga')->
            dropDownList(ArrayHelper::map(
               app\models\spr_costwork::findbysql('Select min(id) as id,usluga from costwork where LENGTH(ltrim(rtrim(usluga)))<>0 group by usluga order by usluga')
                   ->all(), 'id', 'usluga'),
                    [
            'prompt' => 'Виберіть послугу',
            'onchange' => '$.get("' . Url::to('/CalcWork/web/site/getworks1?id=') . 
             '"+$(this).val()+"&res="+localStorage.getItem("id_res"),
                    function(data) {
                         var flag=0,fl=0;
                         var geo_marker = localStorage.getItem("geo_marker");
                         if(geo_marker!="")
                         {var tmp_work = $("#inputdataform-work").val();
                         }
                         localStorage.setItem("work", "");
                         localStorage.setItem("usluga", "");
                        
                         $("#analytics-work").empty();
                         for(var ii = 0; ii<data.works.length; ii++) {
                         var q = data.works[ii].work;
                         //alert(q);
                         if(q==null) continue;
                         var q1 = q.substr(3);
                         var n = q.substr(0,3);
                         //var pr_rab = q.substr(4,1);
                         //if(geo_marker=="") 
                         $("#analytics-work").append("<option value="+n+
                         " style="+String.fromCharCode(34)+"font-size: 10px;"+
                         String.fromCharCode(34)+">"+q1+"</option>");
                         if(+n>=166) flag=1; // Транспортні послуги
                         if(+n==90)  fl=1;
                         if((+n==88) || (+n==37)) fl=2;
                         
                        } 
                         
                  });',
                     ]
                    ) ?>
            
            <?=$form->field($model, 'work')->
            dropDownList(ArrayHelper::map(
            app\models\spr_costwork::findbysql('Select min(id) as id,work from costwork '
                    . ' where '
            . 'hide=:hide and work is not null group by work union 
            select 1000 as id,"    " as work from costwork
            order by work',[':hide' => 0])
            ->all(), 'id', 'work'),['prompt' => 'Виберіть послугу']
            ) ?>
            </div>
            <h5 class="text-primary"><b><?= Html::encode("Групові операції") ?></b></h5>
            
            <br>
            
            <div class="pole-gr">
                <h5><u><?= Html::encode("В розрізі:") ?></u></h5>
                <?= $form->field($model, 'gr_res')->checkbox(['onChange' => 'def_gr($(this).prop("checked"),"#analytics-gr_res")']); ?>
                <?= $form->field($model, 'gr_status_sch')->checkbox(['onChange' => 'def_gr($(this).prop("checked"),"#analytics-gr_status_sch")']); ?>
                <?= $form->field($model, 'gr_date')->checkbox(['onChange' => 'def_gr($(this).prop("checked"),"#analytics-gr_date")']); ?>
                <?= $form->field($model, 'gr_date_opl')->checkbox(['onChange' => 'def_gr($(this).prop("checked"),"#analytics-gr_date_opl")']); ?>
                <?= $form->field($model, 'gr_usluga')->checkbox(['onChange' => 'def_gr($(this).prop("checked"),"#analytics-gr_usluga")']); ?>
                <?= $form->field($model, 'gr_usl')->checkbox(['onChange' => 'def_gr($(this).prop("checked"),"#analytics-gr_usl")']); ?>
                <?= $form->field($model, 'ord')->textInput(); ?>
            </div>
            <div class="pole-gra">
                <h5><u><?= Html::encode("Поле групування:") ?></u></h5>
                <?= $form->field($model, 'gra_summa')->checkbox(); ?>
                <?= $form->field($model, 'gra_summa_beznds')->checkbox(); ?>
                <?= $form->field($model, 'gra_summa_work')->checkbox(); ?>
                <?= $form->field($model, 'gra_summa_transport')->checkbox(); ?>
                <?= $form->field($model, 'gra_summa_delivery')->checkbox(); ?>
                <?= $form->field($model, 'gra_kol')->checkbox(); ?>
            </div>
             <div class="pole-grf">
                <?= $form->field($model, 'gra_oper')->
                        dropDownList([1 => 'Сума',2 => 'Максимум',3 => 'Мінімум',4 => 'Середнє',5 => 'Кількість'],
                                ['onChange' => 'def_cnt($(this).val())']) ?>
            </div>
           
            <div class="pole-grh">
                <h5 class="text-primary"><b><?= Html::encode("Фільтрація по груповим операціям:") ?></b></h5>
                
                <?= $form->field($model, 'grh_having')->
                        dropDownList([1 => '=',2 => '>',3 => '>=',4 => '<',5 => '<=',6 => '<>'],['prompt'=>'Виберіть операцію']) ?>
                <?= $form->field($model, 'grh_value')->textInput(); ?>
                
            </div>
            <div class="pole-sort">
                <h5 class="text-primary"><b><?= Html::encode("Сортування результату:") ?></b></h5>
                
                <?= $form->field($model, 'grs_sort')->
                        dropDownList([1 => 'Підрозділ',2 => 'Статус заявки',3 => 'Дата заявки',4 => 'Дата оплати',
                            5 => 'Послуга',6 => 'Робота',7 => 'Сума з ПДВ',8 => 'Сума без ПДВ',
                            9 => 'Вартість робіт',10 => 'Транспорт всього',11 => 'Доставка бригади'],['prompt'=>'Виберіть поле']) ?>
                <?= $form->field($model, 'grs_dir')->
                        dropDownList([1 => 'За збільшенням',2 => 'За зменшенням'],['prompt'=>'Виберіть вид сортування']) ?>

                <?= $form->field($model, 'sql')->textarea(['rows' => 12, 'cols' => 85]); ?>

                
            </div>
                        
            <div class="form-group">
                <?= Html::button('SQL',
                    ['class' => 'btn btn-primary','onClick' => 'f_sql()']); ?>

                <? Modal::begin([
                'header' => '<h3>Опис полів</h3>',
                'toggleButton' => [
                'label' => 'Поля аналітики',
                'tag' => 'button',
                'class' => 'btn btn-success',
                ]
                ]);
                $desc=describe_fields::findbysql(
                    "select * from describe_fields where "
                    . 'name_table=:name_table' . ' order by field' ,[':name_table' => 'vw_analit'])->all();
                ?>
                <table width="600px" class="table table-bordered table-hover table-condensed ">
            <thead>
            <tr>
                <th width="30px">№ </th>
                <th width="150px">Назва таблиці</th>
                <th width="150px">Назва поля</th>
                <th width="150px">Опис поля</th>
            </tr>
            </thead>
            <tbody>
            <?
            $i=1;
            foreach ($desc as $v) {
                ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= $v['name_table'] ?></td>
                    <td><?= $v['field'] ?></td>
                    <td><?= $v['describe_f'] ?></td>
                </tr>
             <?
                $i++;
                }
            ?>

                </tbody>
                </table>
                <?php
                Modal::end();
                ?>

                <?= Html::submitButton('OK', ['onClick' => 'proc_ok()'],
                        ['class' => 'btn btn-primary']); ?>

            </div>
            
           
            
            <?php
                
            ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
    // Фомируем список полей для группировки (в порядке выбора)
    function def_gr(v,name){
        var s=name.substr(11),p1='',p2='',p3='',p4='',p5='',p6='',r;
        localStorage.setItem(s,v);
        r=localStorage.getItem("gr_res");   
        if(localStorage.getItem("gr_res")=='true'){
             p1=$('#analytics-ord').val();
             if (p1.indexOf("gr_res") == -1)
                $('#analytics-ord').val(p1+" gr_res"); 
        }       
        
        if(localStorage.getItem("gr_status_sch")=='true'){
             p2=$('#analytics-ord').val();
             if (p2.indexOf("gr_status_sch") == -1)
             $('#analytics-ord').val(p2+" gr_status_sch"); 
         }
         
         if(localStorage.getItem("gr_date")=='true'){
             p3=$('#analytics-ord').val();
             if (p3.indexOf("gr_date") == -1)
             $('#analytics-ord').val(p3+" gr_date"); 
         }
         if(localStorage.getItem("gr_date_opl")=='true'){
            p4=$('#analytics-ord').val();
            if (p4.indexOf("gr_date_opl") == -1) 
            $('#analytics-ord').val(p4+" gr_date_opl");
        }
        
        if(localStorage.getItem("gr_usluga")=='true'){
            p5=$('#analytics-ord').val();
            if (p5.indexOf("gr_usluga") == -1) {
                $('#analytics-ord').val(p5+" gr_usluga");
                }    
        }
        if(localStorage.getItem("gr_usl")=='true'){
             p6=$('#analytics-ord').val();
            if (p6.indexOf("gr_usl") == -1)
             $('#analytics-ord').val(p6+" gr_usl"); 
         }
         
    }
    
    // Убираем список полей агрегирования, если выбрана операция количества значений
    function def_cnt(v){
        if(v==5)
            $('.pole-gra').hide();
        else
            $('.pole-gra').show();
    }
    
    function proc_ok() {
        if ($('#analytics-gra_oper').val() == 5) return;
        if (localStorage.getItem("gr_status_sch") == 'true' ||
            localStorage.getItem("gr_date") == 'true' ||
            localStorage.getItem("gr_date_opl") == 'true' ||
            localStorage.getItem("gr_usluga") == 'true' ||
            localStorage.getItem("gr_usl") == 'true' ||
            localStorage.getItem("gr_res") == 'true') {

            if (!($('#analytics-gra_summa').prop("checked") ||
                $('#analytics-gra_summa_beznds').prop("checked") ||
                $('#analytics-gra_summa_work').prop("checked") ||
                $('#analytics-gra_summa_transport').prop("checked") ||
                $('#analytics-gra_summa_delivery').prop("checked"))) {
                alert('Введіть поле групування!');
                $('#analytics-ord').val("error");
            }
        }
    }
    function f_sql(){
        // Готовим аргументы для передачи в ajax запрос
        var res = $('#analytics-res').val();
        var status = $('#analytics-status').val();
        var date1 = $('#analytics-date1').val();
        var date2 = $('#analytics-date2').val();
        var date_opl1 = $('#analytics-date_opl1').val();
        var date_opl2 = $('#analytics-date_opl2').val();
        var date_act1 = $('#analytics-date_act1').val();
        var date_act2 = $('#analytics-date_act2').val();
        var usluga = $('#analytics-usluga').val();
        var work = $('#analytics-work').val();
        var gr_res = $('#analytics-gr_res').prop('checked');
        var gr_status_sch = $('#analytics-gr_status_sch').prop('checked');
        var gr_date = $('#analytics-gr_date').prop('checked');
        var gr_date_opl = $('#analytics-gr_date_opl').prop('checked');
        var gr_usluga = $('#analytics-gr_usluga').prop('checked');
        var gr_usl = $('#analytics-gr_usl').prop('checked');
        var gra_summa = $('#analytics-gra_summa').prop('checked');
        var gra_summa_beznds = $('#analytics-gra_summa_beznds').prop('checked');
        var gra_summa_work = $('#analytics-gra_summa_work').prop('checked');
        var gra_summa_transport = $('#analytics-gra_summa_transport').prop('checked');
        var gra_summa_delivery = $('#analytics-gra_summa_delivery').prop('checked');
        var gra_kol = $('#analytics-gra_kol').prop('checked');
        var gra_oper = $('#analytics-gra_oper').val();
        var grh_having = $('#analytics-grh_having').val();
        var grh_value = $('#analytics-grh_value').val();
        var grs_sort = $('#analytics-grs_sort').val();
        var grs_dir = $('#analytics-grs_dir').val();
        var ord = $('#analytics-ord').val();
        // alert(gr_usl);

        $.ajax({
            url: '/CalcWork/web/site/get_sql_analyt'
            // dataType: 'text',
          ,
            data: {res: res, status: status, date1: date1,
                date2: date2,date_opl1: date_opl1,
                date_opl2: date_opl2, date_act1: date_act1,date_act2: date_act2,
                usluga: usluga, work: work, gr_res: gr_res,gr_status_sch: gr_status_sch,
                gr_date: gr_date, gr_date_opl: gr_date_opl, gr_usluga: gr_usluga, gr_usl: gr_usl,
                gra_summa: gra_summa, gra_summa_beznds: gra_summa_beznds, gra_summa_work: gra_summa_work,
                gra_summa_transport: gra_summa_transport,gra_summa_delivery: gra_summa_delivery,
                gra_kol: gra_kol,gra_oper: gra_oper,  grh_having: grh_having,grh_value: grh_value,
                grs_sort: grs_sort,grs_dir: grs_dir,ord: ord
            },
            type: 'GET',

            success: function(result){
                // alert(res.work);
                $('#analytics-sql').val(result.sql);
                if(!result) alert('Данные не верны!');
            },
            error: function (data) {
                console.log('Error', data);
            },

        });

    setTimeout(function () {
        $('.hasFocus').focus();
    }, 300);

    }
    
  
</script>
    





   

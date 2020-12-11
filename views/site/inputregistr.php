<?php
/**
 * Created by PhpStorm.
 * User: ssivtsov
 * Date: 21.06.2017
 * Time: 9:43
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
$this->title = 'Замовлення послуги';
$model->person = '1';
if($role=3 || $role=5)
    $model->comment = 'ЦЕК';
if($role>0){
    $model->email = $email;
}
//$this->params['breadcrumbs'][] = $this->title;
?>

<script>
    window.onload=function(){
        localStorage.setItem("person", 1);
        localStorage.setItem("plat_nds",false);
    }
</script>
<div class="site-login">

    <h4><?= Html::encode($this->title) ?></h4>

<!--    <p>Введіть реквізіти:</p>-->

    <div class="row row_reg">
        <div class="col-lg-3">
            <?php $form = ActiveForm::begin(['id' => 'inputdata',
                'options' => [
                    'class' => 'form-horizontal col-lg-25',
                    'enctype' => 'multipart/form-data',
                    'fieldConfig' => ['errorOptions' => ['encode' => false, 'class' => 'help-block']
                    
                ]]]); ?>

            <?= $form->field($model, 'person')->radioList(['1' => 'Фізична особа', '2' => 'Юридична особа'],
                ['onchange' => 'showfields_person($(this).find("input:checked").val());']) ?>

            <?= $form->field($model, 'priz_nds')->checkbox([
                'onchange' => 'showfields(this.checked);',
                'label' => 'Платник ПДВ',
                'labelOptions' => [
                    'style' => 'padding-left:20px;'
                ],
                'disabled' => false
            ]); ?>


            <?= $form->field($model, 'inn')->textInput(
                ['onblur' => '$.get("' . Url::to('/CalcWork/web/site/getklient?inn=') .
                    '"+$(this).val(),
                    function(data) {
                     //alert(data.nazv); 
                    if(data.nazv=="")
                    { //alert(11);  
                      $(".nazv_kl").hide();
                      $(".btn-primary").removeClass("disabled");
                      $("#klient-nazv").text("");
                      $("#klient-addr").text("");
                      $("#klient-fio_dir").text("");
                      $("#klient-contact_person").text("");
                      $("#klient-tel").val("");
                      //$("#klient-okpo").val($("#klient-inn").val());
                      $("#klient-email").val("");
                      $("#klient-regsvid").val("");
//                      $("#klient-priz_nds").val("");
                      
                      var pnds = localStorage.getItem("plat_nds");
                      
                      if(pnds)
                         $("#klient-priz_nds").attr("checked", true);
                      else
                         $("#klient-priz_nds").attr("checked", false);
                      
                     //alert($("#klient-priz_nds").is(":checked"));
                     
                     // $("#klient-person").val("");
                    }
                    else
                    { //$(".nazv_kl").text("Такий № ІНН вже існує, збереження інформації не можливе.");
                      //$(".nazv_kl").show();
                      //$(".btn-primary").addClass("disabled");
                      $("#klient-nazv").text(data.nazv);
                      $("#klient-addr").text(data.addr);
                      $("#klient-tel").val(data.tel);
                      $("#klient-regsvid").val(data.regsvid);
                      //$("#klient-okpo").val($("#klient-inn").val());
                      $("#klient-email").val(data.email);
                      $("#klient-fio_dir").text(data.fio_dir);
                      $("#klient-contact_person").text(data.contact_person);
//                      $("#klient-priz_nds").attr("checked", true);
//                      if(data.priz_nds==1){
//                       
//                        $("#klient-priz_nds").attr("checked", true);
//                         $("#klient-priz_nds").change();
//                          $("#klient-priz_nds").attr("checked", true);
//                       }
//                      else
//                       { $("#klient-priz_nds").attr("checked", false);
//                         $("#klient-priz_nds").change();
//                          $("#klient-priz_nds").attr("checked", false);}
//                        
//                           var myradio = $(".input:Klient[person]");
//                           alert(myradio);
//input:radio[name=Klient[person]]
//                           $("input:radio[name=Klient[person]]").filter([value=data.person]).attr("checked", true);
//                    }
                    }   
                });',
                ]) ?>

            <?= $form->field($model, 'woinn')->checkbox([
                'onchange' => 'woinn(this.checked);',
                'label' => 'Індивідуальний податковий № відсутній',
                'labelOptions' => [
                    'style' => 'padding-left:20px;'
                ],
                'disabled' => false
            ]); ?>


            <span class="nazv_kl"></span>

            <?= $form->field($model, 'okpo')->textInput(
                ['maxlength' => true,'onblur' => '$.get("' . Url::to('/CalcWork/web/site/getklient?inn=') .
                    '"+$(this).val(),
                    function(data) {
                     //alert(data.email); 
                    if(data.nazv=="")
                    {   $(".nazv_kl").hide();
                        $(".btn-primary").removeClass("disabled");
                      $("#klient-nazv").text("");
                      $("#klient-addr").text("");
                      $("#klient-fio_dir").text("");
                      $("#klient-contact_person").text("");
                      $("#klient-regsvid").val("");
                      $("#klient-tel").val("");
                     // $("#klient-okpo").val($("#klient-inn").val());
//                      $("#klient-inn").val($("#klient-okpo").val());
                      $("#klient-email").val("");
                      
                      var pnds = localStorage.getItem("plat_nds");
                      
                      if(pnds)
                         $("#klient-priz_nds").attr("checked", true);
                      else{
                         $("#klient-priz_nds").attr("checked", false);
                         //$("#klient-inn").val($("#klient-okpo").val());
                         }
                         
                         //alert($("#klient-inn").val());
                         
//                      $("#klient-priz_nds").val("");
//                      $("#klient-priz_nds").attr("checked", false);
                     // $("#klient-person").val("");
                    }
                    else
                    { //$(".nazv_kl").text("Такий № ІНН вже існує, збереження інформації не можливе.");
                      //$(".nazv_kl").show();
                      //$(".btn-primary").addClass("disabled");
                      $("#klient-nazv").text(data.nazv);
                      $("#klient-addr").text(data.addr);
                      $("#klient-fio_dir").text(data.fio_dir);
                      $("#klient-contact_person").text(data.contact_person);
                      $("#klient-regsvid").val(data.regsvid);
                      $("#klient-tel").val(data.tel);
                      //$("#klient-okpo").val($("#klient-inn").val());
//                      $("#klient-inn").val($("#klient-okpo").val());
                      $("#klient-email").val(data.email);
//                      $("#klient-priz_nds").attr("checked", true);
//                      if(data.priz_nds==1){
//                       
//                        $("#klient-priz_nds").attr("checked", true);
//                         $("#klient-priz_nds").change();
//                          $("#klient-priz_nds").attr("checked", true);
//                       }
//                      else
//                       { $("#klient-priz_nds").attr("checked", false);
//                         $("#klient-priz_nds").change();
//                          $("#klient-priz_nds").attr("checked", false);}
//                        
//                           var myradio = $(".input:Klient[person]");
//                           alert(myradio);
//input:radio[name=Klient[person]]
//                           $("input:radio[name=Klient[person]]").filter([value=data.person]).attr("checked", true);
//                    }
                       
                });',
                ]) ?>
            <?= $form->field($model, 'regsvid') ?>
            <?= $form->field($model, 'nazv')->textarea(['rows' => 3, 'cols' => 25,
                'onDblClick' => 'rmenu($(this).val(),"#klient-nazv")']) ?>
             
            
            <div class='rmenu' id='rmenu-klient-nazv'></div>
            
            <?= $form->field($model, 'addr')->textarea(['rows' => 3, 'cols' => 25,
                'onDblClick' => 'rmenu($(this).val(),"#klient-addr")']) ?>
             <div class='rmenu' id='rmenu-klient-addr'></div>  
             
            <!--<?= $form->field($model, 'fio_dir')->textarea(['rows' => 1, 'cols' => 25]) ?>-->
             
            <?= $form->field($model, 'post_dir')->textarea(['rows' => 1, 'cols' => 25]) ?>
            <?= $form->field($model, 'pib_dir')->textarea(['rows' => 1, 'cols' => 25]) ?>
            <?= $form->field($model, 'contact_person')->textarea(['rows' => 1, 'cols' => 25]) ?>
            <?= $form->field($model, 'tel',['inputTemplate' => '<div class="input-group"><span class="input-group-addon">'
            . '<span class="glyphicon glyphicon-phone"></span></span>{input}</div>'])->textInput(
                ['maxlength' => true,'onBlur' => 'norm_tel($(this).val())']) ?>


            <?= $form->field($model, 'email',['inputTemplate' => '<div class="input-group"><span class="input-group-addon">'
            . '<span class="glyphicon glyphicon-envelope"></span></span>{input}</div>']) ?>


            <?= $form->field($model, 'date_z')->widget(\yii\jui\DatePicker::classname(), [
                'language' => 'uk',
            ]) ?>
            
            <?= $form->field($model, 'object')->textarea(['rows' => 1, 'cols' => 25,
                'onDblClick' => 'rmenu($(this).val(),"#klient-object")']) ?>
             <div class='rmenu' id='rmenu-klient-object'></div>
             
            <?= $form->field($model, 'adr_work')->textarea(['rows' => 3, 'cols' => 25,
                'onDblClick' => 'rmenu($(this).val(),"#adr_work")']) ?>
             <div class='rmenu' id='rmenu-adr_work'></div>
            <?= $form->field($model, 'comment')->textarea(['rows' => 3, 'cols' => 25,
                'onDblClick' => 'rmenu($(this).val(),"#klient-comment")']) ?>
             <div class='rmenu' id='rmenu-klient-comment'></div>
            <p class="text-warning">Увага! Перевірте правильність заповнення реєстраційних данних.</p>

            <div class="form-group">
                <?= Html::submitButton('OK', ['class' => 'btn btn-primary']); ?>

            </div>
            <?php
            ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
    function f_inn(p){
        $("#klient-inn").val(p);
    }

    function woinn(p){
        if (p == 1) {
            v=8000000000+Math.floor(Math.random()*(1000000000-1));;
            $("#klient-inn").val(v);
        }
        else
            $("#klient-inn").val('');
    }

    function showfields(p){
        //alert(p);
        var tip = localStorage.getItem("person");
        localStorage.setItem("plat_nds",p);
        if(tip==1) {
            if (p == 1) {
                // Физ. лицо плат НДС
                //alert('aaa');
                $('.field-klient-okpo').show();
                $('.field-klient-regsvid').hide();
                $('.control-label[for=klient-nazv]').text("Прізвище, ім’я та по батькові:");
            }
            else {
                // Физ. лицо не плат НДС
                $('.field-klient-okpo').hide();
                $('.field-klient-regsvid').hide();
                $('.control-label[for=klient-nazv]').text("Прізвище, ім’я та по батькові:");
            }
        }
        if(tip==2) {
            if (p == 1) {
               // Юр. лицо плат НДС
                //alert('aaa');
                $('.field-klient-okpo').show();
                $('.field-klient-regsvid').show();
                $('.field-klient-inn').show();
                $('.control-label[for=klient-nazv]').text("Повна назва юридичної особи:");
            }
            else {
                // Юр. лицо не плат НДС
                $('.field-klient-okpo').show();
                $('.field-klient-regsvid').show();
                $('.field-klient-inn').hide();
                $('.control-label[for=klient-nazv]').text("Повна назва юридичної особи:");
                $('#klient-inn').val(Math.floor(990000000000+Math.random()*1000000)); // левый ИНН
                //alert(111);
            }
        }
    }
// Срабатывает при изменении значения радиокнопок
    function showfields_person(p){
        var nds = localStorage.getItem("plat_nds");
        //alert(nds);
        if(p==2)  {$('.field-klient-priz_nds').show();
                   $('#klient-priz_nds').get(0).checked = true;
                   //localStorage.setItem("plat_nds",true);
                   //nds = true;
                   }
        if(p==1)  $('.field-klient-priz_nds').hide();
        if(nds=='true') {
//            Плательщик НДС
            if (p == 1) {
//                alert('Плательщик НДС физ');
                localStorage.setItem("person", 1);
                $('.field-klient-okpo').show();
                $('.field-klient-regsvid').hide();
                $('.field-klient-inn').show();
                $('.field-klient-fio_dir').hide();
                $('.field-klient-pib_dir').hide();
                $('.field-klient-post_dir').hide();
                $('.field-klient-contact_person').hide();
                $('.control-label[for=klient-addr]').text("Адреса проживання:");
                $('.control-label[for=klient-nazv]').text("Прізвище, ім’я та по батькові:");
                $('.control-label[for=klient-inn]').text("Індивід. податковий №:");
                //$('.control-label[for=klient-nazv]').text("Назва:");
            }
            else {
//                alert('Плательщик НДС юр');
                localStorage.setItem("person", 2);
                $('.field-klient-okpo').show();
                $('.field-klient-fio_dir').show();
                $('.field-klient-pib_dir').show();
                $('.field-klient-post_dir').show();
                $('.field-klient-contact_person').show();
                $('.field-klient-regsvid').show();
                $('.field-klient-inn').show();
                $('.control-label[for=klient-addr]').text("Юридична адреса:");
                $('.control-label[for=klient-nazv]').text("Повна назва юридичної особи:");
                //$('.control-label[for=klient-inn]').text("ЄДРПОУ:");
            }
        }
        if(nds=='false') {
//          не плательщик НДС
            if (p == 1) {
//                alert('не Плательщик НДС физ');
                localStorage.setItem("person", 1);
                $('.field-klient-okpo').hide();
                $('.field-klient-regsvid').hide();
                $('.field-klient-fio_dir').hide();
                $('.field-klient-contact_person').hide();
                $('.field-klient-inn').show();
                $('.control-label[for=klient-addr]').text("Адреса проживання:");
                $('.control-label[for=klient-nazv]').text("Прізвище, ім’я та по батькові:");
                $('.control-label[for=klient-inn]').text("Індивід. податковий №:");
                //$('.control-label[for=klient-nazv]').text("Назва:");
            }
            else {
//                alert('не Плательщик НДС юр');
                localStorage.setItem("person", 2);
                $('.field-klient-okpo').show();
                $('.field-klient-regsvid').show();
                $('.field-klient-fio_dir').show();
                $('.field-klient-contact_person').show();

               // $('.field-klient-inn').hide();
                $('.control-label[for=klient-addr]').text("Юридична адреса:");
                $('.control-label[for=klient-nazv]').text("Повна назва юридичної особи:");

                //$('.control-label[for=klient-inn]').text("ЄДРПОУ:");
            }
        }
    }
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
        $('#klient-tel').val(rez);
    }
</script>
    





   

<?php
//namespace app\models;
use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\spr_res;
use app\models\status_sch;
$role = Yii::$app->user->identity->role;
?>
<script>

    
   window.addEventListener('load', function(){

       $('.scrolldown').click(function() {
           // переместиться в верхнюю часть страницы

           $("html, body").animate({
               scrollTop:2000
           },400);
       })

       $('.scrollup').click(function() {
           // переместиться в верхнюю часть страницы

           $("html, body").animate({
               scrollTop:0
           },1000);
       })

       // при прокрутке окна (window)
       $(window).scroll(function() {
           // если пользователь прокрутил страницу более чем на 200px
           if ($(this).scrollTop()>200) {
               // то сделать кнопку scrollup видимой
               $('.scrollup').fadeIn();
           }
           // иначе скрыть кнопку scrollup
           else {
               $('.scrollup').fadeOut();
           }
       });

         var stat = $("#viewschet-status").val();   
         //alert(stat);
         localStorage.setItem("status", stat); 
    }); 
    
   window.onload=function(){

    $(document).click(function(e){

	  if ($(e.target).closest("#recode-menu").length) return;

	   $("#rmenu").hide();

	  e.stopPropagation();

	  });


   }


    // Ограничение для установки статусов
    // оплата и выполнено
    function restrict(p){  
       
        if(p==3 || p==7){
           alert("У Вас немає прав для установки цього статусу.") 
           $("#viewschet-status").val(localStorage.getItem("status"));
       }
    }




</script>

<br>
<div class="row">
    <div class="col-lg-7">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'enableAjaxValidation' => false,]); ?>

        <div class="scrolldown">
            <!-- Иконка fa-chevron-up (Font Awesome) -->
            <i class="fa fa-chevron-down"></i>
            <!--            <i class="glyphicon glyphicon-eject"></i>-->
        </div>

    <?php
        // Установка статусов в соответствии с доступами
        $buh=0;
        $fin=0;
        switch($role) {
        case 16: // Полный доступ Желтые Воды
                echo $form->field($model, 'read_z')->checkbox();
                echo $form->field($model, 'status')->dropDownList(ArrayHelper::map(status_sch::find()->all(), 'id', 'nazv'));
                break;
        case 15: // Полный доступ Вольногорск
                echo $form->field($model, 'read_z')->checkbox();
                echo $form->field($model, 'status')->dropDownList(ArrayHelper::map(status_sch::find()->all(), 'id', 'nazv'));
                break;
        case 14: // Полный доступ Павлоград
                echo $form->field($model, 'read_z')->checkbox();
                echo $form->field($model, 'status')->dropDownList(ArrayHelper::map(status_sch::find()->all(), 'id', 'nazv'));
                break;
        case 13: // Полный доступ Кр Рог
                echo $form->field($model, 'read_z')->checkbox();
                echo $form->field($model, 'status')->dropDownList(ArrayHelper::map(status_sch::find()->all(), 'id', 'nazv'));
                break;
        case 12: // Полный доступ Гвардейское
                echo $form->field($model, 'read_z')->checkbox();
                echo $form->field($model, 'status')->dropDownList(ArrayHelper::map(status_sch::find()->all(), 'id', 'nazv'));
                break;
        case 11: // Полный доступ Днепр
                echo $form->field($model, 'read_z')->checkbox();
                echo $form->field($model, 'status')->dropDownList(ArrayHelper::map(status_sch::find()->all(), 'id', 'nazv'));
                break;
        case 5: // Полный доступ
            echo $form->field($model, 'read_z')->checkbox(); 
            echo $form->field($model, 'status')->dropDownList(ArrayHelper::map(status_sch::find()->all(), 'id', 'nazv'));
            break;
        case 3: // Полный доступ
            echo $form->field($model, 'read_z')->checkbox(); 
            echo $form->field($model, 'status')->dropDownList(ArrayHelper::map(status_sch::find()->all(), 'id', 'nazv'),
                    ['onChange' => 'restrict($(this).val())']);
                    
            break;
        case 2:  // финансовый отдел
            echo $form->field($model, 'status')->dropDownList(
                ArrayHelper::map(status_sch::find()->where('id=:status1',[':status1' => 2])->
                orwhere('id=:status2',[':status2' => 3])->
                all(), 'id', 'nazv'));
                $fin=1;
                break;

        case 1:  // бухгалтерия
            echo $form->field($model, 'status')->dropDownList(
                ArrayHelper::map(status_sch::find()->where('id=:status1',[':status1' => 5])->
                orwhere('id=:status2',[':status2' => 7])->
                all(), 'id', 'nazv'));
            $buh=1;
            break;
        }
    ?>

    <?php
        if(($model->status==5 && $buh==1) || ($model->status==7 && $buh==1)){
            
               echo $form->field($model, 'act_work')->textInput();
               echo $form->field($model, 'date_akt')->
                widget(\yii\jui\DatePicker::classname(), [
                    'language' => 'uk'
                ]); 
           
        }
            
    ?>

    <?php
    if($model->status==8){
            echo $form->field($model, 'why_refusal')->textInput();
    }
    ?>

        <table width="600px" class="table table-bordered ">
    <tr>
        <th width="25%">
             <?= Html::encode("Виконавча служба") ?>
        </th> 
        <th width="25%">
             <?= Html::encode("Відповідальна особа") ?>
        </th> 
        <th width="10%">
             <?= Html::encode("Моб. телефон") ?>
        </th> 
        <th width="10%">
             <?= Html::encode("Міський телефон") ?>
        </th>
        <th width="10%">
             <?= Html::encode("Внутр. телефон") ?>
        </th>
        <th width="10%">
             <?= Html::encode("Доп. телефон") ?>
        </th>
        <th width="10%">
             <?= Html::encode("Адреса пошти") ?>
        </th>
     </tr> 
     <?php
        $y = count($data_koord);
        for($i=0;$i<$y;$i++) {
     ?>
     <tr>
    
     <td>
         <?= Html::encode($data_koord[$i]->nazv) ?>
     </td>
     <td>
         <?= Html::encode($data_koord[$i]->name_koord) ?>
     </td>
     <td>
         <?= Html::encode($data_koord[$i]->tel_mobile) ?>
     </td>
     <td>
         <?= Html::encode($data_koord[$i]->tel_town) ?>
     </td>
     <td>
         <?= Html::encode($data_koord[$i]->tel) ?>
     </td>
     <td>
         <?= Html::encode($data_koord[$i]->tel_dop) ?>
     </td>
     <td>
         <?= Html::encode($data_koord[$i]->email) ?>
     </td>
     </tr>
     <?php
        }
     ?>
    </table>

    <?php if(substr($model->inn,0,2)=='99') $model->inn='';?>

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

    <?= $form->field($model, 'tel',
            ['inputTemplate' => '<div class="input-group"><span class="input-group-addon">'
            . '<span class="glyphicon glyphicon-phone"></span></span>{input}</div>'] )->textInput() ?>
        
        
    <?= $form->field($model, 'addr')->textarea() ?>
    <?= $form->field($model, 'email',
            ['inputTemplate' => '<div class="input-group"><span class="input-group-addon">'
            . '<span class="glyphicon glyphicon-envelope"></span></span>{input}</div>'])->textInput() ?>
    <?= $form->field($model, 'comment')->textarea() ?>
    <?= $form->field($model, 'schet')->textInput() ?>
    <?= $form->field($model, 'contract')->textInput() ?>
    <?= $form->field($model, 'usluga')->textarea(['rows' => 3, 'cols' => 25]) ?>

    <?= $form->field($model, 'summa')->textInput() ?>
    <?= $form->field($model, 'summa_beznds')->textInput() ?>
    <?= $form->field($model, 'summa_work')->textInput() ?>
    <?= $form->field($model, 'summa_tmc')->textInput() ?>
    <?= $form->field($model, 'summa_delivery')->textInput() ?>
    <?= $form->field($model, 'summa_transport')->textInput() ?>
    <?= $form->field($model, 'adres')->textarea(['onDblClick' => 'rmenu($(this).val(),"#viewschet-adres")']) ?>
        <?= $form->field($model, 'time_t')->textInput() ?>
        <p>  <? echo($nomer);  ?> </p>
           <div class='rmenu' id='rmenu-viewschet-adres'></div>
 <!--    -->   <?//= $form->field($model, 'res')->textInput() ?>
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

    <?= $form->field($model, 'date'
                    )->textInput() ?>

    <?= $form->field($model, 'time')->textInput() ?>
    <?= $form->field($model, 'kol')->textInput() ?>


        <div class="scrollup">
            <!-- Иконка fa-chevron-up (Font Awesome) -->
                        <i class="fa fa-chevron-up"></i>
<!--            <i class="glyphicon glyphicon-eject"></i>-->
        </div>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'ОК' : 'OK', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
         <?php
        if($model->status>1 ) {
            ?>


        <?= Html::a('Сформувати рахунок',['site/opl'], [
            'data' => [
                'method' => 'post',
                'params' => [
                    'sch' => $nazv,
                    'sch1' => $model->union_sch,
                ],
            ],'class' => 'btn btn-info']); ?>
        
         <?= Html::a('Договір',['site/contract'], [
                    'data' => [
                        'method' => 'post',
                        'params' => [
                            'sch' => $nazv,
                            'sch1' => $model->union_sch,
                            'mail'=> $mail
                        ],
                    ],'class' => 'btn btn-info']); ?>

            <?php
            if($model->status==3 || $model->status==7 || $model->status==5) {
            ?>
            <?= Html::a('Акт виконаних робіт',['site/act_work'], [
                'data' => [
                    'method' => 'post',
                    'params' => [
                        'sch' => $nazv,
                        'sch1' => $model->union_sch,
                        'mail'=> $mail
                    ],
                ],'class' => 'btn btn-info']); ?>
       <?php } ?>

        <?php
        if($model->status>1){
        ?>
        <?= Html::a('Експорт в САП',['site/z2sap'], [
            'data' => [
                'method' => 'post',
                'params' => [
                    'sch' => $nazv,
                ],
            ],'class' => 'btn btn-info']); ?>
        <?php } ?>

        <?php if(Yii::$app->session->hasFlash('success')):?>
            <span class="label label-success" ><?php echo Yii::$app->session->getFlash('success'); ?></span>
        <?php endif; ?>
        
    <?php
        if($model->status==1){
            echo Html::a('Інформація для виконавця',['site/info_exec'], [
                    'data' => [
                        'method' => 'post',
                        'params' => [
                            'sch' => $nazv,
                            'mail'=> $mail
                        ],
                    ],'class' => 'btn btn-info']);
        }
    
        if($model->status==7) {
            if(!empty($model->act_work)) { ?>
<!--    --><?//= Html::a('Акт виконаних робіт',['site/act_work'], [
//            'data' => [
//                'method' => 'post',
//                'params' => [
//                    'sch' => $nazv,
//                    'sch1' => $model->union_sch,
//                    'mail'=> $mail
//                ],
//            ],'class' => 'btn btn-info']); ?>
<!---->
<!--                --><?//= Html::a('Договір',['site/contract'], [
//                    'data' => [
//                        'method' => 'post',
//                        'params' => [
//                            'sch' => $nazv,
//                            'sch1' => $model->union_sch,
//                            'mail'=> $mail
//                        ],
//                    ],'class' => 'btn btn-info']); ?>
<!--         -->
<!--                --><?//= Html::a('Повідом.',['site/message'], [
//                    'data' => [
//                        'method' => 'post',
//                        'params' => [
//                            'sch' => $nazv,
//                            'sch1' => $model->union_sch,
//                            'mail'=> $mail
//                        ],
//                    ],'class' => 'btn btn-info']); ?>



        <?php }}} ?>
        
    </div>



    <?php ActiveForm::end(); ?>
    </div>
</div>




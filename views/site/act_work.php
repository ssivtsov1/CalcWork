<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = "Акт виконаних робіт";
$this->params['breadcrumbs'][] = $this->title;
$rr='26007000030100';
$mfo='322313';
$okpo='31793056';
?>
<!--<div class="site-about">-->
    <div class=<?= $style_title ?> >
         <h3><?= Html::encode($this->title) ?></h3>
    </div>

<table width="600px" class="table table-bordered ">
    <tr>
        <th width="180px">
            <div class="act_left">
            <span class="span_single">ЗАТВЕРДЖУЮ </span>
            <br>
            <br>
                <?= Html::encode("Начальник ". $model->parrent_nazv ." РЕМ: ") ?>
            <br>
            
                <?= Html::encode("ПРИВАТНЕ АКЦІОНЕРНЕ ТОВАРИСТВО ".'"'.
                        "ПІДПРИЄМСТВО З ЕКСПЛУАТАЦІЇ ЕЛЕКТРИЧНИХ МЕРЕЖ ".'"'."ЦЕНТРАЛЬНА ЕНЕРГЕТИЧНА КОМПАНІЯ".'"') ?>
            <br>
            <br>
            <div class="act_hr">
                <hr class="act_line">
            </div>
            
            <?= Html::encode($model->Director) ?>
            
            </div>
        </th>
        <th width="420px" class="th_r">
            <div class="act_left">
                <span class="span_single">
                    <?= Html::encode("ЗАТВЕРДЖУЮ") ?>
                </span>
                <br>
                <br>
                <br>
                <?= Html::encode($model->nazv) ?>
                <br>
                 <br>
                <br>
                <br>
                <div class="act_hr">
                 <hr class="act_line">
                </div>
            </div>
        </th>
    </tr>
</table>
<div class="act_center">
<?= Html::encode("АКТ виконання робіт (надання послуг)") ?>
<br>
<?= Html::encode("№".$model->act_work.' від '.changeDateFormat($model->date_akt, 'd.m.Y')) ?>
<hr class="act_line_main">

</div>
<div class="act_center_text">
    <?= Html::encode("Ми, що нижче підписалися, представники Замовника ".$model->nazv.' _________________'.
            ", з одного боку, і представник Виконавця ПРИВАТНЕ АКЦІОНЕРНЕ ТОВАРИСТВО ".
            '"'."ПІДПРИЄМСТВО З ЕКСПЛУАТАЦІЇ ЕЛЕКТРИЧНИХ МЕРЕЖ ".'"'.
            "ЦЕНТРАЛЬНА ЕНЕРГЕТИЧНА КОМПАНІЯ".'"'.
            " Начальник ". $model->parrent_nazv ." РЕМ, з іншого боку, склали дійсний акт про те,
                що на підставі наступних документів:");?>
    <?= Html::encode("Договір:  №".$model->contract) ?>
    <br>
    <?= Html::encode("виконавцем були проведені наступні роботи (виконані такі послуги):") ?>
    
</div>   
<table width="600px" class="table table-bordered ">
    <tr>
        <th width="5%">
             <?= Html::encode("№") ?>
        </th> 
        <th width="67%">
             <?= Html::encode("Послуга") ?>
        </th> 
        <th width="8%">
             <?= Html::encode("Кільк.од.") ?>
        </th> 
        <th width="10%">
             <?= Html::encode("Ціна без ПДВ, грн.") ?>
        </th>
        <th width="10%">
             <?= Html::encode("Сума без ПДВ, грн.") ?>
        </th>
     </tr> 
     <tr>
     <td>
         <?= Html::encode("1") ?>
     </td>
     <td>
         <?= Html::encode($model->usluga) ?>
     </td>
     <td>
         <?= Html::encode($model->kol) ?>
     </td>
     <td>
         <?= Html::encode(round($model->summa_beznds/$model->kol,2)) ?>
     </td>
     <td>
         <?= Html::encode($model->summa_beznds) ?>
     </td>
     </tr>
    </table>  
    
<!--    <div class="act_center_itog">-->
<!--        --><?//= Html::encode("Разом:          ");?><!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; --><?//= Html::encode($model->summa_beznds) ?>
<!--        <br>-->
<!--         --><?//= Html::encode("Сума ПДВ:      ");?><!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; --><?//= Html::encode((($model->summa)-($model->summa_beznds))) ?>
<!--        <br>-->
<!--         --><?//= Html::encode("Усього з ПДВ:  ");?><!-- &nbsp; --><?//= Html::encode($model->summa) ?>
<!--    </div>    -->

<div class="act_center_itog">
    <table width="200px" class="table table-bordered ">
        <tr>
            <td>
                <?= Html::encode("Разом:") ?>
            </td>
            <td>
                <?= Html::encode("$model->summa_beznds"." грн.") ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= Html::encode("Сума ПДВ:") ?>
            </td>
            <td>
                <?= Html::encode((($model->summa)-($model->summa_beznds))." грн.") ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= Html::encode("Усього з ПДВ:") ?>
            </td>
            <td>
                <?= Html::encode($model->summa." грн.") ?>
            </td>
        </tr>
    </table>
</div>

    <br>
    <div class="act_center_end">
        <?= Html::encode("Загальна вартість робіт (послуг) без ПДВ склала ". num2text_ua($model->summa_beznds).
                ",ПДВ ". num2text_ua((($model->summa)-($model->summa_beznds))).
                ", загальна вартість робіт (послуг) з ПДВ ".num2text_ua($model->summa)) ?>
        <br> 
        <?= Html::encode(" Сторони претензій одна до одної не мають.");?>
      
    </div>    
     <br> 
     <div class="act_footer_top">
        <?= Html::encode("Місце складання: м. Дніпропетровськ");?>
     </div>
     
     <hr class="act_line_main">
     <table width="600px" class="table table-bordered ">
    <tr>
        <th width="180px">
            <div class="act_left">
            <span class="span_single"> 
                <?= Html::encode("Від Виконавця*") ?>
            </span>
            <br>
           
            <br>
            <br>
            <div class="act_hr">
                <hr class="act_line">
            </div>
             <br>
              <?= Html::encode("* Відповідальний за здійснення господарської операції і правильність її оформлення") ?>
             <br>
             <span class="span_single"> 
                <?= Html::encode(changeDateFormat($model->date_akt, 'd.m.Y')) ?>
            </span>
             <br>
             <?= Html::encode("ПРИВАТНЕ АКЦІОНЕРНЕ ТОВАРИСТВО ".'"'.
                        "ПІДПРИЄМСТВО З ЕКСПЛУАТАЦІЇ ЕЛЕКТРИЧНИХ МЕРЕЖ ".'"'."ЦЕНТРАЛЬНА ЕНЕРГЕТИЧНА КОМПАНІЯ".'",') ?>
             <br>
             <?= Html::encode("код за ЄДРПОУ 31793056, тел.: (0562) 31-03-93,38-64-62,
                    ІПН 317930504629, № свід.  100339376,
                    п/р 26007000030100, у банку Акціонерне товариство ".
                     '"'."Державний експортно-імпортний банк України".'"'.
                     ", МФО 322313   ,
                    юр. адреса: 49008, Дніпропетровська  обл., місто Дніпро, вулиця Дмитра Кедріна, Будинок № 28,
                     Є платником податку на прибуток на загальних підставах") ?>
             <br>
            </div>
        </th>
        <th width="420px" class="th_r">
            <div class="act_left">
                <span class="span_single">
                    <?= Html::encode("Від Замовника") ?>
                </span>
                <br>
                <br>
                <br>
                <div class="act_hr">
                 <hr class="act_line">
                </div>
                <br>
                <br>
                <br>
                <span class="span_single"> 
                    <?= Html::encode(changeDateFormat($model->date_akt, 'd.m.Y')) ?>
                </span>
                 <br>
                  <?= Html::encode($model->nazv) ?>
                 <br>
            </div>
        </th>
    </tr>
</table>
     
    <?= Html::a('Роздрукувати',['site/act_print'],

        [
            'data' => [
                'method' => 'post',
                'params' => [
                'sch' => $model->schet,
         ],],
            'class' => 'btn btn-primary','target'=>'_blank', ]); ?>

    <?= Html::a('Відправити по Email',['site/act_email'],

    [
        'data' => [
            'method' => 'post',
            'params' => [
                'sch' => $model->schet,
                'email' => $mail,
            ],],
        'class' => 'btn btn-primary']); ?>


    <code><?//= __FILE__ ?></code>

<!--</div>-->

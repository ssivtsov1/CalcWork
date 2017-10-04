<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = "Договір";
$this->params['breadcrumbs'][] = $this->title;
$year = date('Y');

?>
<!--<div class="site-about">-->
    <div class=<?= $style_title ?> >
         <h3><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="contract_center">
    <span class="span_single">
        <?= Html::encode(" ДОГОВІР №".$model->contract) ?>

    </span>
    </div>
    <table width="600px" class="table table-hover table-hap">
        <tr>
            <th width="10%">
            <div class="act_center_text">
                <?= Html::encode("м.Дніпро") ?>
            </div>
            </th>
            <th width="20%">
                <div class="act_center_text_right">
                    <?= Html::encode("_____________$year р.") ?>
                </div>
            </th>
        </tr>
    </table>



<span class="contract_center_text_bold" >
    <?= Html::encode("Приватне акціонерне товариство ".'"'.
        "Підприємство з експлуатації електричних мереж  ".'"'."Центральна енергетична компанія".'"');?>
</span>
<span class="contract_center_text" >
    <?= Html::encode(",іменоване надалі ".'"'." ВИКОНАВЕЦЬ".'"'.
    ", в особі $model->exec_post_pp $model->exec_person_pp, що діє на підставі довіреності, з одного боку, і ".
        $model->nazv.", іменований надалі ".'"'." ЗАМОВНИК".'"'.", в особі ".
        (empty($model->fio_dir) ? "_______________________________________________" : $model->fio_dir) .
    " , що діє на підставі ____________________________________________, з іншого боку, надалі ".'"'." Сторони".'"'.
    ", домовилися про нижченаведене:");?>
</span>
<br>
<br>
<div class="contract_center">
    <span class="span_single">
        <?= Html::encode("1. ПРЕДМЕТ ДОГОВОРУ") ?>
    </span>
</div>
<br>
<span class="contract_center_text" >
    <?= Html::encode("1.1. ".'"'." ЗАМОВНИК".'"'." доручає, а ".'"'." ВИКОНАВЕЦЬ".'"'.
        " переймає на себе обов'язки по наданню послуг - " . $model->usluga . " на наступному об'єкті:");?>
</span>
<br>
<span class="contract_center_text" >
    <?= Html::encode("1.1.1. Найменування об'єкту: ".$model->nazv."." );?>
</span>
<br>
<span class="contract_center_text" >
    <?= Html::encode("1.1.2. Місце розташування: ".$model->addr."." );?>
</span>
<br>
<br>
<div class="contract_center">
    <span class="span_single">
        <?= Html::encode("2. ВАРТІСТЬ І ПОРЯДОК РОЗРАХУНКІВ  ЗА ДОГОВОРОМ") ?>
    </span>
</div>
<br>
<span class="contract_center_text" >
    <?= Html::encode("2.1. Вартість послуг, що виконуються, складає ".$model->summa_beznds.
    " грн. без ПДВ, ПДВ 20% ".(($model->summa)-($model->summa_beznds))." грн., всього з ПДВ ".
        $model->summa." грн.(".num2text_ua($model->summa).").");?>
</span>
<br>
<span class="contract_center_text" >
    <?= Html::encode("2.2. Розмір плати за послуги, вказані в п. 1.1. Договору, визначений прикладеною до договору калькуляцією.");?>
</span>
<br>
<span class="contract_center_text" >
    <?= Html::encode("2.3. ".'"'."ЗАМОВНИК".'"'." оплачує виконання послуги шляхом перерахування грошової суми,
        вказаної в п. 2.1. цього Договору, впродовж 5 (п'яти) банківських днів з моменту підписання Договору.");?>
</span>
<br>
<br>
<div class="contract_center">
    <span class="span_single">
        <?= Html::encode("3. ОБОВ'ЯЗКИ  СТОРІН") ?>
    </span>
</div>
<br>
<span class="contract_center_text" >
    <?= Html::encode("3.1. ".'"'."ВИКОНАВЕЦЬ".'"'.
            " зобов'язаний зробити послуги впродовж 10 (десяти) робочих днів з моменту
                отримання грошових коштів, згідно п.2.3. цього Договору. ");?>
</span>
<br>
<span class="contract_center_text" >
    <?= Html::encode("3.2. ".'"'."ЗАМОВНИК".'"'.
            " зобов'язаний в повному об'ємі і в зазначені терміни сплатити зроблені ".
            '"'."ВИКОНАВЦЕМ".'" послуги.');?>
</span>
<br>
<br>
<div class="contract_center">
    <span class="span_single">
        <?= Html::encode("4. ПОРЯДОК СДАЧІ-ПРИЙНЯТТЯ ПОСЛУГ") ?>
    </span>
</div>
<br>
<span class="contract_center_text" >
    <?= Html::encode("4.1. Здача-прийняття зроблених послуг здійснюється Сторонами по акту прийняття зроблених послуг.");?>
</span>
<br>
<span class="contract_center_text" >
    <?= Html::encode("4.2. Після закінчення надання послуг" .'"'."ВИКОНАВЕЦЬ".'"'.
            " зобов'язаний впродовж 3-х робочих днів підготувати і надати ".
            '"'."ЗАМОВНИКОВІ".'"'." для підписання акт прийняття зроблених послуг.");?>
</span>
<br>
<span class="contract_center_text" >
    <?= Html::encode("4.3. ".'"'."ЗАМОВНИК".'"'.
            " зобов'язаний впродовж 3-х робочих днів підписати акт прийняття зроблених"
            . " послуг або надати мотивовану відмову від підписання вказаного акту."
            . " Якщо в зазначений триденний термін ".'"'."ЗАМОВНИК".'"'.
            "не повернув підписаний акт або не надав мотивовану відмову, акт вважається"
            . " підписаним, а послуги прийнятими без зауважень.");?>
</span>
<br>
<br>
<div class="contract_center">
    <span class="span_single">
        <?= Html::encode("5. ВІДПОВІДАЛЬНІСТЬ СТОРІН") ?>
    </span>
</div>
<br>
<span class="contract_center_text" >
    <?= Html::encode("5.1. Сторони несуть відповідальність за невиконання або"
            . " неналежне виконання своїх зобов'язань за даною угодою відповідно до чинного законодавства України.");?>
</span>
<br>
<br>
<div class="contract_center">
    <span class="span_single">
        <?= Html::encode("6. ВИРІШЕННЯ СУПЕРЕЧОК") ?>
    </span>
</div>
<br>
<span class="contract_center_text" >
    <?= Html::encode("6.1. Усі спори і розбіжності, які можуть виникнути з Договору"
            . " або у зв'язку з його виконанням, по можливості вирішуватимуться шляхом"
            . " переговорів уповноважених представників сторін.");?>
</span>
<br>
<span class="contract_center_text" >
    <?= Html::encode("6.2. Будь-яка суперечка, неврегульована сторонами,"
            . " підлягає передачі на розглядання і вирішення до суду, згідно із законодавством України.");?>
</span>
<br>
<br>

<div class="contract_center" style = "page-break-before: always;">
    <span class="span_single">
        <?= Html::encode("7. ІНШІ УМОВИ") ?>
    </span>
</div>
<br>
<span class="contract_center_text" >
    <?= Html::encode("7.1. Договір набуває чинності з моменту його підписання"
            . " і діє до повного виконання сторонами своїх зобов'язань, але не більше ніж до 31.12.$year р.");?>
</span>
<br>
<span class="contract_center_text" >
    <?= Html::encode("7.2. ".'"'."ВИКОНАВЕЦЬ".'"'." є платником податку на прибуток на загальних підставах, згідно з Податковим кодексом України.");?>
</span>
<br>
<span class="contract_center_text" >
    <? if($model->priz_nds==0): ?>
         <?= Html::encode("7.3. ".'"'."ЗАМОВНИК".'"'." не є платником податку.");?>
    <? endif; ?>
    <? if($model->priz_nds==1): ?>
        <?= Html::encode("7.3. ".'"'."ЗАМОВНИК".'"'." є платником податку на прибуток
         _____________________________________________________________________________________.");?>
    <? endif; ?>
    </span>
<br>
<br>
<div class="contract_center">
    <span class="span_single">
        <?= Html::encode("8. ЮРИДИЧНІ АДРЕСИ, БАНКІВСЬКІ РЕКВІЗИТИ І ПІДПИСИ СТОРІН") ?>
    </span>
</div>
    
     <table width="600px" class="table table-bordered ">
    <tr>
        <th width="300px">
            <div class="contract_center">
            <span class="span_single"> 
                &laquo; <?= Html::encode("ВИКОНАВЕЦЬ") ?>&raquo; 
            </span>
            </div>
           
            <br>
            <div class="contract_center">
            <span class="span_single"> 
                 <?= Html::encode("ПрАТ ") ?> &laquo; <?= Html::encode("ПЕЕМ ") ?>&laquo;<?= Html::encode("ЦЕК") ?>&raquo;
            </span>
            </div>
                
             <br>
             <span class="contract_text_footer"> 
                <?= Html::encode("49008 м. Дніпро, вул. Дмитра Кедріна, 28 
                    р/р № 26007000030100 у ПАТ ") ?> &laquo;
                 <?= Html::encode("Державний експортно-імпортний банк України") ?> &raquo;  
                 <?= Html::encode("МФО 322313
                         Св. № 100339376 ІПН 317930504629 
                         Код ЄДРПОУ 31793056 
                            тел. (056) 31-03-84 ") ?>
             </span>
             
             <br>
             <br>
             <span class="contract_text_footer"> 
                <?= Html::encode(mb_ucfirst($model->exec_post, 'UTF-8')) ?> 
             </span>
             <br>
             <br>
             <span class="contract_text_footer"> 
                <?= Html::encode("________________________/$model->exec_person / ") ?> 
             </span>
             
            </div>
        </th>
        <th width="300px">
            <div class="contract_center">
                <span class="span_single">
                    &laquo;<?= Html::encode("ЗАМОВНИК") ?>&raquo;
                </span>
                <br>
                <br>
              </div>  
             <div class="contract_center">
               <span class="span_single"> 
             
                 <?= Html::encode($model->nazv) ?>
               </span>
             </div>
                
                 <br>
                <span class="contract_text_footer"> 
                  <?= Html::encode($model->addr) ?> 
                </span>
                 <br>
                 <? if(!empty($model->okpo))
                        {$okpo = $model->okpo;
                        $s = 'Код ЄДРПОУ ';}
                    else    
                       { $okpo = $model->inn;
                         $s = 'ІНН ';
                       }
                 ?>
                 <span class="contract_text_footer"> 
                    <?= Html::encode($s.$okpo) ?>
                 </span>
                 <br>
                 <span class="contract_text_footer"> 
                    <?= Html::encode("тел. ".$model->tel) ?>
                 </span>
                 <br>
                <br>
             <span class="contract_text_footer"> 
                <?= Html::encode("Директор ") ?> 
             </span>
              <br>
             <br>
             <span class="contract_text_footer"> 
                <?= Html::encode("________________________ ") ?> 
             </span>  
             <br>    
             <br>
            </div>
        </th>
    </tr>
</table>
     
    <?= Html::a('Роздрукувати',['site/contract_print'],

        [
            'data' => [
                'method' => 'post',
                'params' => [
                'sch' => $model->schet,
         ],],
            'class' => 'btn btn-primary','target'=>'_blank', ]); ?>

    <?= Html::a('Відправити по Email',['site/contract_email'],

    [
        'data' => [
            'method' => 'post',
            'params' => [
                'sch' => $model->schet,
                'email' => $model->mail,
            ],],
        'class' => 'btn btn-primary']); ?>


    <code><?//= __FILE__ ?></code>

<!--</div>-->

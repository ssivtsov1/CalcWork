<?php
use yii\helpers\Html;

$this->title = "Типовий договір";
$this->params['breadcrumbs'][] = $this->title;
$year = date('Y');

?>
    <div class=<?= Html::encode('d9') ?> >
         <h3><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="contract_center">
    <span class="span_single">
        <?= Html::encode(" ДОГОВІР №__________") ?>

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
    ", в особі _________________________________________, що діє на підставі довіреності від ________ № _______,". " з одного боку, і ".
       "_______________________________________".", іменований надалі ".'"'." ЗАМОВНИК".'"'.", в особі ".
        "_______________________________________________" .
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
        " переймає на себе обов'язки по наданню послуг - " .
        "__________________________________" . " на наступному об'єкті:");?>
</span>
<br>
<span class="contract_center_text" >
    <?= Html::encode("1.1.1. Найменування об'єкту: "."__________________________________"."." );?>
</span>
<br>
<span class="contract_center_text" >
    <?= Html::encode("1.1.2. Місце розташування: "."__________________________________"."." );?>
</span>
<br>
<br>
<div class="contract_center">
    <span class="span_single">
        <?= Html::encode("2. ВАРТІСТЬ І ПОРЯДОК РОЗРАХУНКІВ  ЗА ДОГОВОРОМ") ?>
    </span>
</div>
<br>
<div class="contract_center_text1" >
    <?= Html::encode("2.1. Вартість послуг, що виконуються, складає "."__________________".
    " грн. без ПДВ, в тому числі робота _______ грн, проїзд ________ грн, доставка бригади _______ грн,"
            . " ПДВ 20% "."__________________"." грн., всього з ПДВ ".
        "__________________"." грн.") ?>
</div>

<span class="contract_center_text" >
    <?= Html::encode("2.2. ".'"'."ЗАМОВНИК".'"'." оплачує виконання послуги шляхом перерахування грошової суми,
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
                отримання грошових коштів, згідно п.2.2. цього Договору. ");?>
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
<!--style = "page-break-before: always;"-->
<div class="contract_center" style = "page-break-before: always;" >
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

        <?= Html::encode("Якщо ЗАМОВНИК платник податків, то:"); ?>
</span>    
    <br>
    <span class="contract_center_text" >
        <?= Html::encode("7.3. ".'"'."ЗАМОВНИК".'"'." є платником податку на прибуток та ПДВ згідно з Податковим кодексом України
         _____________________________________________________________________________________.");?>
    </span>    
    <br>
    <span class="contract_center_text" > 
        <?= Html::encode("Якщо ЗАМОВНИК не являється платником ПДВ, тоді:"); ?>
    </span>    
      <br>
   <span class="contract_center_text" > 
        <?= Html::encode("7.3. ".'"'."ЗАМОВНИК".'"'." не є платником податків.");?>

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
                <?= Html::encode(" ") ?>
             </span>
             <br>
             <br>
             <span class="contract_text_footer"> 
                <?= Html::encode("________________________/П.І.Б. / ") ?>
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
             
                 <?= Html::encode("Назва___________________________________") ?>
               </span>
             </div>
                
                 <br>
                <span class="contract_text_footer"> 
                  <?= Html::encode("Адреса__________________________________________________") ?>
                </span>
                 <br>

                 <span class="contract_text_footer"> 
                    <?= Html::encode('ІНН або код ЄДРПОУ') ?>
                 </span>
                 <br>
                 <span class="contract_text_footer"> 
                    <?= Html::encode("тел. ____________________") ?>
                 </span>
                 <br>
                <br>
             <span class="contract_text_footer"> 
                <?= Html::encode(" ") ?> 
             </span>
              <br>
             <br>
             <span class="contract_text_footer"> 
                 <?= Html::encode("________________________/П.І.Б. / ") ?>
             </span>  
             <br>    
             <br>
            </div>
        </th>
    </tr>
</table>

 
    <code><?//= __FILE__ ?></code>



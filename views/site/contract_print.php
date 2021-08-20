<?php
use yii\helpers\Html;

$this->title = "";
$this->params['breadcrumbs'][] = $this->title;
$year = date('Y');
$pos = strpos($model[0]['res'], 'РЕМ');
if ($pos === false) {
    $res=0;
} else {
    $res=1;
}

?>
<!--<div class="site-about">-->
    <div class=<?= $style_title ?> >
         <h3><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="contract_center">
    <span class="span_single">
<!--        --><?//= Html::encode(" ДОГОВІР №".$model[0]['contract']) ?>
        <? if($model[0]['budget_org']<>1): ?>
            <?= Html::encode(" ДОГОВІР №".$n_cnt) ?>
        <? else: ?>
            <?= Html::encode(" ДОГОВІР") ?>
        <? endif; ?>

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
    <?php
     $session = Yii::$app->session;
    if($session->has('contract_hap')) {
        if($session->get('contract_hap')==1)
            if($res==0) {
                echo Html::encode(",іменоване надалі " . '"' . " ВИКОНАВЕЦЬ" . '"' .
                    ", в особі " . $model[0]['exec_post_pp'] . ' ' . $model[0]['exec_person_pp'] . ", що діє на підставі доручення №" .
                    $model[0]['assignment'] . ' від ' . $model[0]['date_assignment'] . ' р.' . ", з одного боку, і " .
                    $model[0]['nazv'] . ", іменований надалі " . '"' . " ЗАМОВНИК" . '"' . ", в особі " .
                    (empty($model[0]['fio_dir']) ? "_______________________________________________" : $model[0]['fio_dir']) .
                    " , що діє на підставі ____________________________________________, з іншого боку, надалі " . '"' . " Сторони" . '"' .
                    ", домовилися про нижченаведене:");
            }
            else
                echo Html::encode(",іменоване надалі ".'"'." ВИКОНАВЕЦЬ".'"'.
                    ", в особі ".'начальника РЕМ'.' '. $model[0]['chief'].", що діє на підставі доручення №".
                    $model[0]['n_dov'].' від '.$model[0]['d_dov'].' р.'.", з одного боку, і ".
                    $model[0]['nazv'].", іменований надалі ".'"'." ЗАМОВНИК".'"'.", в особі ".
                    (empty($model[0]['fio_dir']) ? "_______________________________________________" : $model[0]['fio_dir']) .
                    " , що діє на підставі ____________________________________________, з іншого боку, надалі ".'"'." Сторони".'"'.
                    ", домовилися про нижченаведене:");
        else
            echo Html::encode(",іменоване надалі ".'"'." ВИКОНАВЕЦЬ".'"'.
                ", в особі "."_______________________________________________________________".", що діє на підставі ".
                "_______________________________________________________________".", з одного боку, і ".
                $model[0]['nazv'].", іменований надалі ".'"'." ЗАМОВНИК".'"'.", в особі ".
                (empty($model[0]['fio_dir']) ? "_______________________________________________" : $model[0]['fio_dir']) .
                " , що діє на підставі ____________________________________________, з іншого боку, надалі ".'"'." Сторони".'"'.
                ", домовилися про нижченаведене:");

    }
    else
        if($res==0) {
            echo Html::encode(",іменоване надалі " . '"' . " ВИКОНАВЕЦЬ" . '"' .
                ", в особі " . $model[0]['exec_post_pp'] . ' ' . $model[0]['exec_person_pp'] . ", що діє на підставі доручення №" .
                $model[0]['assignment'] . ' від ' . $model[0]['date_assignment'] . ' р.' . ", з одного боку, і " .
                $model[0]['nazv'] . ", іменований надалі " . '"' . " ЗАМОВНИК" . '"' . ", в особі " .
                (empty($model[0]['fio_dir']) ? "_______________________________________________" : $model[0]['fio_dir']) .
                " , що діє на підставі ____________________________________________, з іншого боку, надалі " . '"' . " Сторони" . '"' .
                ", домовилися про нижченаведене:");
        }
        else
            echo Html::encode(",іменоване надалі " . '"' . " ВИКОНАВЕЦЬ" . '"' .
                ", в особі " . 'начальника РЕМ' . ' ' . $model[0]['chief'] . ", що діє на підставі доручення №" .
                $model[0]['n_dov'] . ' від ' . $model[0]['d_dov'] . ' р.' . ", з одного боку, і " .
                $model[0]['nazv'] . ", іменований надалі " . '"' . " ЗАМОВНИК" . '"' . ", в особі " .
                (empty($model[0]['fio_dir']) ? "_______________________________________________" : $model[0]['fio_dir']) .
                " , що діє на підставі ____________________________________________, з іншого боку, надалі " . '"' . " Сторони" . '"' .
                ", домовилися про нижченаведене:");
     ?>   
</span>
<br>
<br>
<div class="contract_center">
    <span class="span_single">
        <?= Html::encode("1. ПРЕДМЕТ ДОГОВОРУ") ?>
    </span>
</div>
<br>

    <?php if($q==1): ?>
    <div class="contract_center_text1" >
        <?= Html::encode("1.1. ".'"'." ЗАМОВНИК".'"'." доручає, а ".'"'." ВИКОНАВЕЦЬ".'"'.
            " переймає на себе обов'язки по наданню послуг - " . $model[0]['usluga'] . " на наступному об'єкті:");?>
    <?php endif; ?>
    <?php if($q>1): ?>
      <div class="contract_center_text1" >  
      <?php 
        $str_u='';
        for ($i = 0; $i < $q; $i++) { 
            $str_u.=mb_strtolower($model[$i]['usluga'],'utf-8').', ';
        } 
        $str_u=mb_substr($str_u,0,mb_strlen($str_u,'utf-8')-2,'utf-8');
        ?>
        <?= Html::encode("1.1. ".'"'." ЗАМОВНИК".'"'." доручає, а ".'"'." ВИКОНАВЕЦЬ".'"'.
            " переймає на себе обов'язки по наданню послуг - (" . $str_u . ") на наступному об'єкті:");?>
    <?php endif; ?>
</div>

<span class="contract_center_text" >
    <?php
        if(empty($model[0]['object']))
            $obj = $model[0]['nazv'];
        else
            $obj = $model[0]['object'];
     ?>
    <?= Html::encode("1.1.1. Найменування об'єкту: ".$obj."." );?>
</span>
<br>
<span class="contract_center_text" >
    <?= Html::encode("1.1.2. Місце розташування: ".$model[0]['adres']."." );?>
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
    <?php if($q>1): ?>
        <!--        --><?//= Html::encode("2.1. Вартість послуг, що виконуються, складає ".$total_beznds.
//        " грн. без ПДВ, ПДВ 20% ".($total-$total_beznds)." грн., всього з ПДВ ".
//            $total." грн.(".num2text_ua($total).")., в тому числі робота ".$model[0]['summa_work'].
//             " грн., матеріали (".$model[0]['tmc_name'].") ".$model[0]['summa_tmc']." грн., проїзд ".$model[0]['summa_transport']." грн., доставка бригади ".$model[0]['summa_delivery']." грн.");?>

        <?php if($model[0]['usl']<>'Транспортні послуги'): ?>

            <?php if($model[0]['cost_auto_work']==0): ?>
                <?= Html::encode("2.1. Вартість послуг, що виконуються, складає ".$total_beznds.
                    " грн. без ПДВ, ПДВ 20% ".($total-$total_beznds)." грн., всього з ПДВ ".
                    $total." грн.(".num2text_ua($total).")., в тому числі робота ".$model[0]['summa_work'].
                    " грн., проїзд ".$model[0]['summa_transport']." грн., доставка бригади ".$model[0]['summa_delivery']." грн.");?>
            <?php endif; ?>
            <?php if($model[0]['cost_auto_work']>0): ?>
                <?= Html::encode("2.1. Вартість послуг, що виконуються, складає ".$total_beznds.
                    " грн. без ПДВ, ПДВ 20% ".($total-$total_beznds)." грн., всього з ПДВ ".
                    $total." грн.(".num2text_ua($total).")., в тому числі робота ".$model[0]['summa_work']." грн.,".
                    "  транспортні послуги ".($model[0]['summa_transport'] + $model[0]['cost_auto_work']) .
                    " грн., проїзд ".$model[0]['summa_transport']." грн., доставка бригади ".$model[0]['summa_delivery']." грн.");?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if($model[0]['usl']=='Транспортні послуги'): ?>
            <?= Html::encode("2.1. Вартість послуг, що виконуються, складає ".$total_beznds.
                " грн. без ПДВ, ПДВ 20% ".($total-$total_beznds)." грн., всього з ПДВ ".
                $total." грн.(".num2text_ua($total).")");?>
        <?php endif; ?>

    <?php endif; ?>
    <?php if($q==1): ?>
        <!--        --><?//= Html::encode("2.1. Вартість послуг, що виконуються, складає ".$model[0]['summa_beznds'].
//        " грн. без ПДВ,"." в тому числі робота ".$model[0]['summa_work'].
//             " грн., матеріали (".$model[0]['tmc_name'].") ".$model[0]['summa_tmc']." грн., проїзд ".$model[0]['summa_transport']." грн., доставка бригади ".$model[0]['summa_delivery']." грн.".
//                " ПДВ 20% ".(($model[0]['summa'])-($model[0]['summa_beznds']))." грн., всього з ПДВ ".
//            $model[0]['summa']." грн.(".num2text_ua($model[0]['summa']).").");?>

        <?php if($model[0]['usl']<>'Транспортні послуги'): ?>
            <?php if($model[0]['cost_auto_work']==0): ?>
                <?= Html::encode("2.1. Вартість послуг, що виконуються, складає ".$model[0]['summa_beznds'].
                    " грн. без ПДВ,"." в тому числі робота ".$model[0]['summa_work'].
                    " грн., проїзд ".$model[0]['summa_transport']." грн., доставка бригади ".$model[0]['summa_delivery']." грн.".
                    " ПДВ 20% ".(($model[0]['summa'])-($model[0]['summa_beznds']))." грн., всього з ПДВ ".
                    $model[0]['summa']." грн.(".num2text_ua($model[0]['summa']).").");?>
            <?php endif; ?>
            <?php if($model[0]['cost_auto_work']>0): ?>
                <?= Html::encode("2.1. Вартість послуг, що виконуються, складає ".$model[0]['summa_beznds'].
                    " грн. без ПДВ,"." в тому числі робота ".$model[0]['summa_work'].
                    " грн., транспортні послуги ".($model[0]['summa_transport'] + $model[0]['cost_auto_work']) ." грн., доставка бригади ".$model[0]['summa_delivery']." грн.".
                    " ПДВ 20% ".(($model[0]['summa'])-($model[0]['summa_beznds']))." грн., всього з ПДВ ".
                    $model[0]['summa']." грн.(".num2text_ua($model[0]['summa']).").");?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if($model[0]['usl']=='Транспортні послуги'): ?>
            <?= Html::encode("2.1. Вартість послуг, що виконуються, складає ".$model[0]['summa_beznds'].
                " грн. без ПДВ,".
                " ПДВ 20% ".(($model[0]['summa'])-($model[0]['summa_beznds']))." грн., всього з ПДВ ".
                $model[0]['summa']." грн.(".num2text_ua($model[0]['summa']).").");?>
        <?php endif; ?>


    <?php endif; ?>
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
        <?= Html::encode("4. ПОРЯДОК ЗДАЧІ-ПРИЙНЯТТЯ ПОСЛУГ") ?>
    </span>
</div>
<br>
<span class="contract_center_text" >
    <?= Html::encode("4.1. Здача-прийняття виконаних послуг здійснюється Сторонами по акту прийняття виконаних послуг.");?>
</span>
<br>
<span class="contract_center_text" >
    <?= Html::encode("4.2. Після закінчення надання послуг" .'"'."ВИКОНАВЕЦЬ".'"'.
            " зобов'язаний впродовж 3-х робочих днів підготувати і надати ".
            '"'."ЗАМОВНИКОВІ".'"'." для підписання акт прийняття виконаних послуг.");?>
</span>
<br>
<span class="contract_center_text" >
    <?= Html::encode("4.3. ".'"'."ЗАМОВНИК".'"'.
            " зобов'язаний впродовж 3-х робочих днів підписати акт прийняття виконаних"
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
    <?php if($model[0]['priz_nds']==0): ?>
         <?= Html::encode("7.3. ".'"'."ЗАМОВНИК".'"'." не є платником податку.");?>
    <?php endif; ?>
    <?php if($model[0]['priz_nds']==1): ?>
        <?= Html::encode("7.3. ".'"'."ЗАМОВНИК".'"'." є платником податку на прибуток
         _____________________________________________________________________________________.");?>
    <?php endif; ?>
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
                <?= Html::encode("49008 м. Дніпро, вул. Дмитра Кедріна, 28,  
                    р/р UA483005280000026004455048529 у АТ ") ?> &laquo;
                 <?= Html::encode("ОТП Банк , ") ?> &raquo;
                  <?= Html::encode(", МФО 300528,
                         Код ЄДРПОУ 31793056,
                         ІПН 317930504629, 
                            тел. (0562) 31-03-84, 
                            тел. (0800) 30-00-15, 
                            e-mail: kanc@cek.dp.ua,
                            http:cek.dp.ua") ?>
             </span>
             
             <br>
             <br>
             <?php
             if($session->has('contract_hap')) {
              if($session->get('contract_hap')==1):
                  
             ?>     
             <span class="contract_text_footer"> 
                <?= Html::encode(mb_ucfirst($model[0]['exec_post'], 'UTF-8')) ?> 
             </span>
             <br>
             <br>
             <span class="contract_text_footer"> 
                <?= Html::encode("________________________/".$model[0]['exec_person']." / ") ?> 
             </span>
             <?php endif; ?>
             <?php 
              if($session->get('contract_hap')==0):
             ?>
               <span class="contract_text_footer"> 
                <?= Html::encode('_______________') ?> 
               </span>
            
             <br>
             <br>
             <span class="contract_text_footer"> 
                <?= Html::encode("___________________________") ?> 
             </span>  
             <?php endif; ?>
             <?php }
             if(!$session->has('contract_hap')):?>
                 <span class="contract_text_footer">
                <?php if($res==0): ?>
                    <?= Html::encode(mb_ucfirst($model[0]['exec_post'], 'UTF-8')) ?>
                <?php endif; ?>
                <?php if($res==1): ?>
                    <?= Html::encode(mb_ucfirst('Начальник РЕМ', 'UTF-8')) ?>
                <?php endif; ?>
             </span>
                 <br>
                 <br>
                 <span class="contract_text_footer">
                 <?php if($res==0): ?>
                     <?= Html::encode("________________________/".$model[0]['exec_person']." / ") ?>
                 <?php endif; ?>
                 <?php if($res==1): ?>
                     <?= Html::encode("________________________/".$model[0]['chief']." / ") ?>
                 <?php endif; ?>
             </span>
             <?php endif; ?>
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
             
                 <?= Html::encode($model[0]['nazv']) ?>
               </span>
             </div>
                
                 <br>
                <span class="contract_text_footer"> 
                  <?= Html::encode($model[0]['addr']) ?> 
                </span>
                 <br>
                  <?php if(!empty($model[0]['okpo']))
                        {$okpo = $model[0]['okpo'];
                         $inn = $model[0]['inn'];
                         $s = 'Код ЄДРПОУ ';
                            if(mb_substr($inn,0,2,"UTF-8")=='99') $inn = '';
                         $s1 = 'ІПН '.$inn; 
                        }
                    else    
                       { $okpo = $model[0]['inn'];
                         $s = 'ІПН ';
                         $s1 = '';
                         $inn ='';
                       }
                 ?>
                 
                 <span class="contract_text_footer"> 
                  <?php
                  if(!empty($model[0]['okpo'])) { 
                        echo Html::encode("р/р _______________________________________________"); 
                        echo '<br>';
                        echo Html::encode("МФО ____________");
                        echo '<br>';
                        echo Html::encode($s.$okpo);
                        echo '<br>';
                        echo Html::encode($s1);
                      }
                  if(empty($model[0]['okpo'])) {   
                        echo Html::encode($s.$okpo);
                        
                      }    
                  ?>
                 </span>
                 <br>
                 <span class="contract_text_footer"> 
                    <?= Html::encode("тел. ".$model[0]['tel']) ?>
                 </span>
                 <br>
                <br>
             <span class="contract_text_footer"> 
                <?php
                if(!empty($model[0]['okpo']))   
                    echo Html::encode($model[0]['post_dir']);
                else
                     echo Html::encode("Фізична особа");
                
                ?>
             </span>
              <br>
             <br>
             <span class="contract_text_footer"> 
                <?php
                if(empty($model[0]['okpo']))  
                    echo Html::encode("________________________ ".$model[0]['nazv']);
                else
                {
                    echo Html::encode("________________________ ".$model[0]['pib_dir']);
                }
                ?>
             </span>  
             <br>    
             <br>
            </div>
        </th>
    </tr>
</table>
     
   
    <code><?//= __FILE__ ?></code>

<!--</div>-->

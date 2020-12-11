<?php
/**
 * Created by PhpStorm.
 * User: ssivtsov
 * Date: 21.06.2017
 * Time: 9:58
 * Программа расчета всех показателей
 */
//use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\models\forExcel;


$time_t = round($distance / 45,2);

$time_work = str_replace(',','.',$time_work);
$time_prostoy = str_replace(',','.',$time_prostoy);
$flag =1;
$role=0;
$cost_auto_work=0;
if(!is_null($tmc_price) && $tmc_price>0)
    $tmc_price_=$tmc_price;
else
    $tmc_price_=0;

if(!isset(Yii::$app->user->identity->role))
{      $flag=0;}
else{
    $role=Yii::$app->user->identity->role;
}
?>

<div class="site-login">
    <?php if($flag): ?>

    <h4><?= Html::encode("Результат розрахунку для:  ".$name_res[0]->nazv.', споживач: '.$nazv.' (ІНН: '.$potrebitel.').') ?></h4>
    <br>
    <?php
    if($model1[0]->usluga!="Транспортні послуги") {

    ?>
    <div class="main_pokaz">
        <h4><?= Html::encode('Розрахунок вартості робіт:  '.$kol*$model1[0]->cost ) ?></h4>
    </div>
    <br>
    <h4><?= Html::encode($model1[0]->work) ?></h4>
    <br>
    <h4><?= Html::encode("Вартість роботи на 1 калькуляційну одиницю: ".$model1[0]->cost.' грн.') ?></h4>
    <h4><?= Html::encode("Кількість калькуляційних одиниць: ".$kol) ?></h4>
    <h4><?= Html::encode("Сумарна вартість роботи грн. без ПДВ: ".$kol*$model1[0]->cost) ?></h4>
    <?php if(!is_null($tmc_price) && $tmc_price>0): ?>
        <h4><?= Html::encode("Вартість матеріалів та устаткування без ПДВ: ".$tmc_price.' грн.') ?></h4>
    <?php endif; ?>
    <br>
    <div class="main_pokaz">
        <h4><?= Html::encode("Розрахунок доставки бригади:  ".round($time_t*$model2[0]->stavka_grn,2).' грн.') ?></h4>
    </div>
    <br>
    <h4><?= Html::encode("Часова тарифна ставка виконавців робіт: ".$model2[0]->stavka_grn.' грн./год.') ?></h4>
    <h4><?= Html::encode("Відстань від виробничої бази до місця проведення робіт: ".$distance.' км.') ?></h4>
    <h4><?= Html::encode("Термін проїзду до місця робіт: ".$time_t.' год.') ?></h4>
    <h4><?= Html::encode("Вартість доставки бригади: ".round($time_t*$model2[0]->stavka_grn,2).' грн.') ?></h4>
    <br>
    <? } ?>
    <div class="main_pokaz">
    <h4><?= Html::encode("Транспортні послуги: ".$model1[0]->transport.' ,Номер '.$model1[0]->nom_tr) ?></h4>

    <?php if($model1[0]->usluga!="Транспортні послуги"): ?>
    <h4><?= Html::encode("Всього: ".(round($model1[0]->proezd*$time_t,2)+
                round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
                round($model1[0]->rabota*$kol,2)).' грн.') ?></h4>
    <?php endif; ?>

    <?php if($model1[0]->usluga=="Транспортні послуги"): ?>
        <h4><?= Html::encode("Всього: ".(round($model1[0]->proezd*$time_t,2)+
                    round($model1[0]->prostoy*$time_prostoy*$kol,2) +
                    round($model1[0]->rabota*$time_work,2)).' грн.') ?></h4>
        <br>
    <?php endif; ?>

    <h4><?= Html::encode(" Проїзд: ".round($model1[0]->proezd*$time_t,2).' грн.') ?></h4>
    </div>
    <?php if($model1[0]->usluga!="Транспортні послуги"): ?>
        <br>
    <?php endif; ?>
    <h4><?= Html::encode("Термін проїзду до місця робіт: ".$time_t.' год.') ?></h4>
    <h4><?= Html::encode("Вартість транспорту (проїзд): ".$model1[0]->proezd.' грн./година') ?></h4>

    <br>
    <?php if($model1[0]->usluga=="Транспортні послуги"): ?>
        <?php if($time_prostoy<>0): ?>
            <div class="main_pokaz">
                <h4><?= Html::encode(" Простой: ".round($model1[0]->prostoy*$time_prostoy,2).' грн.') ?></h4>
            </div>
            <h4><?= Html::encode(" Термін простою: ".$time_prostoy.' год.') ?></h4>
            <h4><?= Html::encode(" Вартість транспорту (простой): ".$model1[0]->prostoy.' грн/година.') ?></h4>

            <br>
        <?php endif; ?>
        <?php if($time_work<>0 && $model1[0]->rabota<>0): ?>
            <div class="main_pokaz">
                <h4><?= Html::encode(" Робота: ".round($model1[0]->rabota*$time_work,2).' грн.') ?></h4>
            </div>

            <h4><?= Html::encode(" Термін роботи: ".$time_work.' год.') ?></h4>
            <h4><?= Html::encode(" Вартість транспорту (робота): ".$model1[0]->rabota.' грн/година.') ?></h4>
        <?php endif; ?>
    <?php endif; ?>

    <br>

    <?php if($model1[0]->usluga!="Транспортні послуги"): ?>
    <div class="main_pokaz">
<!--        $model1[0]->prostoy*$model1[0]->time_transp*$kol-->
            <h4><?= Html::encode(" Простой: ".round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2).' грн.') ?></h4>
    </div>
    <br>
    <h4><?= Html::encode("Термін простою на 1 калькул. один.: ".$model1[0]->time_transp.' год.') ?></h4>
    <h4><?= Html::encode("Кількість калькуляційних одиниць: ".$kol) ?></h4>
    <h4><?= Html::encode("Вартість транспорту (простой): ".$model1[0]->prostoy.' грн.') ?></h4>
    <h4><?= Html::encode("Вартість транспорту (робота): ".round($model1[0]->rabota,2).' грн') ?></h4>
    <?php endif; ?>
    <?php endif; ?>
    <br>
    <div class="main_schet">
        <h3><?= Html::encode(" Результат розрахунку: ") ?></h3>
    </div>
    <br>
<!--    <h4>--><?//= Html::encode("Координати: ".$geo) ?><!--</h4>-->

<!--    <h4>--><?//= Html::encode("Результат розрахунку для:  ".$name_res[0]->nazv.', споживач: '.$nazv.' (ІНН: '.$potrebitel.').') ?><!--</h4>-->
<!--    --><?php //if($model1[0]->usluga!="Транспортні послуги"): ?>
<!--    <h4>--><?//= Html::encode($model1[0]->work.': '.$kol*$model1[0]->cost.' грн.') ?><!--</h4>-->
<!--    <h4>--><?//= Html::encode("Доставка бригади:  ".round($time_t*$model2[0]->stavka_grn,2).' грн.') ?><!--</h4>-->

<!--    <h4>--><?//= Html::encode("Транспортні послуги: ".(round($model1[0]->proezd*$time_t,2)+
//                round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
//                round($time_t*$model2[0]->stavka_grn,2)).' грн.')  ?><!-- </h4>-->
<!--    --><?php //endif; ?>
<!---->
<!--    --><?php //if($model1[0]->usluga=="Транспортні послуги"): ?>
<!--        <h4>--><?//= Html::encode("Транспортні послуги: ".((round($model1[0]->proezd*$time_t,2)+
//                        round($model1[0]->prostoy*$time_prostoy*$kol,2) +
//                        round($model1[0]->rabota*$time_work,2)).' грн.')) ?><!--</h4>-->
<!--    --><?php //endif; ?>
<!---->
<!--    --><?php //if($model1[0]->usluga!="Транспортні послуги"): ?>
<!--    <h4>--><?//= Html::encode("Всього: ".(round($model1[0]->proezd*$time_t,2)+
//                round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
//                (round($time_t*$model2[0]->stavka_grn,2))+$kol*$model1[0]->cost).' грн.') ?><!--</h4>-->
<!---->
<!--    <h4>--><?//= Html::encode("ПДВ: ".round((round($model1[0]->proezd*$time_t,2)+
//                round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
//                (round($time_t*$model2[0]->stavka_grn,2))+$kol*$model1[0]->cost)*0.2,2).' грн.') ?><!--</h4>-->
<!---->
<!--    <h4>--><?//= Html::encode("Разом з ПДВ: ".(round((round($model1[0]->proezd*$time_t,2)+
//                    round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
//                    (round($time_t*$model2[0]->stavka_grn,2))+$kol*$model1[0]->cost)*0.2,2)+
//                (round($model1[0]->proezd*$time_t,2)+
//                round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
//                (round($time_t*$model2[0]->stavka_grn,2))+$kol*$model1[0]->cost))
//            .' грн.') ?><!--</h4>-->
<!---->
<!--    --><?php //endif; ?>
<!---->
<!--    --><?php //if($model1[0]->usluga=="Транспортні послуги"): ?>
<!--        <h4>--><?//= Html::encode("Всього: ".((round($model1[0]->proezd*$time_t,2)+
//                        round($model1[0]->prostoy*$time_prostoy*$kol,2) +
//                        round($model1[0]->rabota*$time_work,2)).' грн.')) ?><!--</h4>-->
<!---->
<!--        <h4>--><?//= Html::encode("ПДВ: ".round((round($model1[0]->proezd*$time_t,2)+
//                        round($model1[0]->prostoy*$time_prostoy*$kol,2) +
//                        round($model1[0]->rabota*$time_work,2))*0.2,2).' грн.') ?><!--</h4>-->
<!---->
<!--        <h4>--><?//= Html::encode("Разом з ПДВ: ".(round(round((round($model1[0]->proezd*$time_t,2)+
//                            round($model1[0]->prostoy*$time_prostoy*$kol,2) +
//                            round($model1[0]->rabota*$time_work,2))*0.2,2)+
//                    (round($model1[0]->proezd*$time_t,2)+
//                        round($model1[0]->prostoy*$time_prostoy*$kol,2) +
//                        round($model1[0]->rabota*$time_work,2)),2)
//                .' грн.')) ?><!--</h4>-->
<!---->
<!--    --><?php //endif; ?>
    <br/>
    <div class="main_pokaz">
     <h4><?= Html::encode("Увага! Остаточна вартість визначається після зв’язку з оператором.") ?></h4>
    </div>
    <?php
    // Формирование csv файла

    array_map('unlink', glob("Calc_*.csv"));
    $file_xls = 'Calc_'.date('d').date('m').date('y').'_'.date('H').date('i').date('s').'.csv';
    $f = fopen($file_xls,'w');

    fputs($f," РАХУНОК: ");
    fputs($f,"\r\n");
    fputs($f,$model1[0]->work.': '.$kol*$model1[0]->cost.' грн.'."\r\n");
    fputs($f,"Доставка бригади:  ".round($time_t*$model2[0]->stavka_grn,2).' грн.'."\r\n");
    fputs($f,"Транспортні послуги: ".(round($model1[0]->proezd*$time_t,2)+
                round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)).' грн.'."\r\n");
    fputs($f,"Всього: ".(round($model1[0]->proezd*$time_t,2)+
                round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
                (round($time_t*$model2[0]->stavka_grn,2))+$kol*$model1[0]->cost).' грн.'."\r\n");
    
    fputs($f,"ПДВ: ".round((round($model1[0]->proezd*$time_t,2)+
                round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
                round($model1[0]->rabota*$kol,2)+
                (round($time_t*$model2[0]->stavka_grn,2))+$kol*$model1[0]->cost)*0.2,2).' грн.'."\r\n");
    if($model1[0]->usluga!="Транспортні послуги") {
        $all_grn = round((round($model1[0]->proezd * $time_t, 2) +
                    round($model1[0]->prostoy * $model1[0]->time_transp * $kol, 2) +
                    round($model1[0]->rabota * $kol, 2) +
                    (round($time_t * $model2[0]->stavka_grn, 2)) + $kol * $model1[0]->cost + $tmc_price_) * 0.2, 2) +
            (round($model1[0]->proezd * $time_t, 2) +
                round($model1[0]->prostoy * $model1[0]->time_transp * $kol, 2) +
                round($model1[0]->rabota * $kol, 2) +
                (round($time_t * $model2[0]->stavka_grn, 2)) + $kol * $model1[0]->cost + $tmc_price_);

               $cost_auto_work=round($model1[0]->rabota * $kol, 2);
    }
    else {
        $all_grn = round(round((round($model1[0]->proezd * $time_t, 2) +
                    round($model1[0]->prostoy * $time_prostoy * $kol, 2) +
                    round($model1[0]->rabota * $time_work, 2)) * 0.2, 2) +
            (round($model1[0]->proezd * $time_t, 2) +
                round($model1[0]->prostoy * $time_prostoy * $kol, 2) +
                round($model1[0]->rabota * $time_work, 2)), 2);
    }



    fputs($f,"Разом з ПДВ: ".(round((round($model1[0]->proezd*$time_t,2)+
                    round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
                    (round($time_t*$model2[0]->stavka_grn,2))+$kol*$model1[0]->cost)*0.2,2)+
                (round($model1[0]->proezd*$time_t,2)+
                round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
                (round($time_t*$model2[0]->stavka_grn,2))+$kol*$model1[0]->cost))
            .' грн.'.',,,,,');
    fclose($f);

    // Подготовка данных для сброса в Excel и дальнейшей работы с данными
    $model = new forExcel();
    if($model1[0]->usluga!="Транспортні послуги") {
        $model->nazv = $model1[0]->work;
        $model->rabota = $kol*$model1[0]->cost;
        $model->delivery = round($time_t * $model2[0]->stavka_grn, 2);
        $model->transp = round($model1[0]->proezd*$time_t,2)+
            round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2);
        $model->all = round($model1[0]->proezd*$time_t,2)+
            round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
            round($model1[0]->rabota*$kol,2)+
            round($time_t*$model2[0]->stavka_grn,2)+$kol*$model1[0]->cost+$tmc_price_;
        $model->nds = round((round($model1[0]->proezd*$time_t,2)+
                round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
                round($model1[0]->rabota*$kol,2)+
                (round($time_t*$model2[0]->stavka_grn,2))+$kol*$model1[0]->cost+$tmc_price_)*0.2,2);
        $model->all_nds = (round((round($model1[0]->proezd*$time_t,2)+
                    round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
                    round($model1[0]->rabota*$kol,2)+
                    (round($time_t*$model2[0]->stavka_grn,2))+$kol*$model1[0]->cost+$tmc_price_)*0.2,2)+
            (round($model1[0]->proezd*$time_t,2)+
                round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
                round($model1[0]->rabota*$kol,2)+
                (round($time_t*$model2[0]->stavka_grn,2))+$kol*$model1[0]->cost+$tmc_price_));

    }
    if($model1[0]->usluga=="Транспортні послуги") {
        $model->nazv = "Транспортні послуги: ".$model1[0]->work;
        $model->rabota = -1;     // Не участвует в отображении
        $model->delivery = -1;  // Не участвует в отображении
        $model->transp = -1;    // Не участвует в отображении
        $model->all = round($model1[0]->proezd*$time_t,2)+
                round($model1[0]->prostoy*$time_prostoy*$kol,2) +
                round($model1[0]->rabota*$time_work,2);
        $model->nds = round($model->all*0.2,2);
        $model->all_nds = $model->all + $model->nds;
    }
    $model->adr_work = str_replace('Адреса виконання робіт:','',$adr_work);
    $model->adr_work = str_replace('Украина','Україна',$adr_work);

//    debug($model->all_nds);
//    return;
    
    ?>

    <?php if($model1[0]->usluga!="Транспортні послуги"): ?>
    <table class="table table-bordered table-hover table-condensed">
        <thead>
        <tr>
            <th width="400px">Послуга </th>
            <th width="150px">Сума, грн.</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?= $model->nazv ?></td>
            <td><?= $model->rabota ?></td>
        </tr>

        <?php if(!is_null($tmc_price) && $tmc_price>0): ?>
        <tr>
                <td><?= "Вартість матеріалів та устаткування: " ?></td>
                <td><?= $tmc_price ?></td>
        </tr>
        <?php endif; ?>

        <tr>
            <td><?= "Транспортні послуги: " ?></td>
            <td><?= (round($model1[0]->proezd*$time_t,2)+
                    round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
                    round($model1[0]->rabota*$kol,2)+
                    round($time_t*$model2[0]->stavka_grn,2)) ?></td>
        </tr>
        <tr>
            <td>Всього: </td>
            <td><?= (round($model1[0]->proezd*$time_t,2)+
                round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
                round($model1[0]->rabota*$kol,2)+
                (round($time_t*$model2[0]->stavka_grn,2))+$kol*$model1[0]->cost+$tmc_price_) ?></td>

        </tr>
        <tr>
            <td>ПДВ: </td>
            <td><?= round((round($model1[0]->proezd*$time_t,2)+
                        round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
                        round($model1[0]->rabota*$kol,2)+
                        (round($time_t*$model2[0]->stavka_grn,2))+$kol*$model1[0]->cost+$tmc_price_)*0.2,2) ?></td>

        </tr>
        <tr>
            <td>Разом з ПДВ: </td>
            <td class="itogo_s_nds"><?= (round((round($model1[0]->proezd*$time_t,2)+
                            round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
                            round($model1[0]->rabota*$kol,2)+
                            (round($time_t*$model2[0]->stavka_grn,2))+$kol*$model1[0]->cost+$tmc_price_)*0.2,2)+
                    (round($model1[0]->proezd*$time_t*$kol,2)+
                        round($model1[0]->rabota*$kol,2)+
                        round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
                        (round($time_t*$model2[0]->stavka_grn,2))+$kol*$model1[0]->cost+$tmc_price_))  ?></td>

        </tr>
        </tbody>
    </table>
    <?php endif; ?>

    <?php if($model1[0]->usluga=="Транспортні послуги"): ?>
        <table width="600px" class="table table-bordered table-hover table-condensed ">
            <thead>
            <tr>
                <th width="300px">Послуга </th>
                <th width="300px">Сума, грн.</th>
            </tr>
            </thead>
            <tbody>

            <tr>
                <td><?= "Транспортні послуги: " ?></td>
                <td><?= (round($model1[0]->proezd*$time_t,2)+
                        round($model1[0]->prostoy*$time_prostoy*$kol,2) +
                        round($model1[0]->rabota*$time_work,2)) ?></td>
            </tr>
            <tr>
                <td>Всього: </td>
                <td><?= ((round($model1[0]->proezd*$time_t,2)+
                            round($model1[0]->prostoy*$time_prostoy*$kol,2) +
                            round($model1[0]->rabota*$time_work,2))) ?></td>

            </tr>
            <tr>
                <td>ПДВ: </td>
                <td><?= round((round($model1[0]->proezd*$time_t,2)+
                            round($model1[0]->prostoy*$time_prostoy*$kol,2) +
                            round($model1[0]->rabota*$time_work,2))*0.2,2) ?></td>

            </tr>
            <tr>
                <td>Разом з ПДВ: </td>
                <td class="itogo_s_nds"><?= round(round((round($model1[0]->proezd*$time_t,2)+
                                round($model1[0]->prostoy*$time_prostoy*$kol,2) +
                                round($model1[0]->rabota*$time_work,2))*0.2,2)+
                        (round($model1[0]->proezd*$time_t,2)+
                            round($model1[0]->prostoy*$time_prostoy*$kol,2) +
                            round($model1[0]->rabota*$time_work,2)),2);

                    ?></td>

            </tr>
            </tbody>
        </table>
    <?php endif; ?>

    <?php
    if($model1[0]->usluga!="Транспортні послуги")
       $all_grn=(round((round($model1[0]->proezd*$time_t,2)+
            round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
            round($model1[0]->rabota*$kol,2)+
            (round($time_t*$model2[0]->stavka_grn,2))+$kol*$model1[0]->cost+$tmc_price_)*0.2,2)+
           (round($model1[0]->proezd*$time_t*$kol,2)+
          round($model1[0]->rabota*$kol,2)+
         round($model1[0]->prostoy*$model1[0]->time_transp*$kol,2)+
         (round($time_t*$model2[0]->stavka_grn,2))+$kol*$model1[0]->cost+$tmc_price_));
    ?>

    <div class="form-group">
        <?php if($model1[0]->usluga=="Транспортні послуги"): ?>
            <?php if(!$refresh): ?>
                 <?= Html::a('Відмовитись',["cancel?&nazv=$model->nazv&summa=$model->all_nds&res=".$name_res[0]->nazv.
                "&adr_work=$adr_work"], ['class' => 'btn btn-primary']); ?>
            <?php endif; ?>
         <?php if($flag): ?>
             <?= Html::a('Сброс в Excel', ["site/excel?&kind=2&nazv=$model->nazv&rabota=$model->rabota&
            delivery=$model->delivery&transp=$model->transp&
            all=$model->all&nds=$model->nds&all_nds=$model->all_nds"],
                ['class' => 'btn btn-info'])  ?>
         <?php endif; ?>
        <?php endif; ?>

        <?php if($model1[0]->usluga!="Транспортні послуги"): ?>
        <?php if(!($role==1||$role==2)): ?>    
            <?php if(!$refresh): ?>
                <?= Html::a('Відмовитись',["cancel?&nazv=$model->nazv&summa=$model->all_nds&res=".$name_res[0]->nazv.
                    "&adr_work=$adr_work"], ['class' => 'btn btn-primary']); ?>
            <?php endif; ?>
        <?php endif; ?>
            <?php if($flag): ?>
              <?= Html::a('Сброс в Excel', ["site/excel?&kind=1&nazv=$model->nazv&rabota=$model->rabota&
             delivery=$model->delivery&transp=$model->transp&
                 all=$model->all&nds=$model->nds&all_nds=$model->all_nds&nazv1=$nazv1"],
                ['class' => 'btn btn-info'])  ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if(!($role==1||$role==2)): ?>
        <?= Html::a(($refresh==0) ? 'Замовити послугу' : 'Зберегти',['proposal?rabota='.$model->rabota.'&delivery='.$model->delivery.
            '&transp='.$model->transp.'&all='.$model->all.'&g=' .$all_grn.
            '&u='.$model1[0]->work.'&res='.$name_res[0]->nazv.
            '&adr='.$model->adr_work.'&geo='.$geo.'&kol='.$kol.'&refresh='.$refresh.
            '&schet='.$schet.'&tmc='.$tmc_price_.'&tmc_name='.$tmc_name.
            '&time_t='.$time_t.'&mvp='.$mvp.'&time_prostoy='.$time_prostoy.'&time_work='.$time_work.
            '&cost_auto_work='.$cost_auto_work],
            ['class' => 'btn btn-primary']); ?>

        <?php endif; ?>
    </div>
   
</div>


<?php
/**
 Оперативно-технічне обслуговування
*/

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\models\forExcel;

$time_t = round($distance / 45,2);
//debug($time_t);
//return;

$time_work = str_replace(',','.',$time_work);
$time_prostoy = str_replace(',','.',$time_prostoy);
$flag =1;
$role=0;
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

    ?>
    <div class="main_pokaz">
        <h4><?= Html::encode('Розрахунок вартості робіт:  '.$kol*$model1[0]['cast_1'] ) ?></h4>
    </div>
    <br>
    <h4><?= Html::encode($model1[0]['work']) ?></h4>
    <br>
    <h4><?= Html::encode("Вартість роботи на 1 калькуляційну одиницю: ".$model1[0]['cast_1'].' грн.') ?></h4>
    <h4><?= Html::encode("Кількість калькуляційних одиниць: ".$kol) ?></h4>
    <h4><?= Html::encode("Сумарна вартість роботи грн. без ПДВ: ".$kol*$model1[0]['cast_1']) ?></h4>
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

    <div class="main_pokaz">
    <h4><?= Html::encode("Транспортні послуги: ".$model1[0]['work']) ?></h4>


    <h4><?= Html::encode("Всього: ".($model1[0]['all_move']+
                round($model1[0]['all_p']*$model1[0]['time_transp']*$kol,2)).' грн.') ?></h4>
    <?php endif; ?>


    <h4><?= Html::encode("Термін проїзду до місця робіт: ".$time_t.' год.') ?></h4>
    <h4><?= Html::encode("Вартість транспорту (проїзд): ".$model1[0]['all_move'].' грн./година') ?></h4>
    <br>
    <br>

    <div class="main_pokaz">
            <h4><?= Html::encode(" Простой: ".round($model1[0]['all_p']*$model1[0]['time_transp']*$kol,2).' грн.') ?></h4>
    </div>
    <br>
    <h4><?= Html::encode("Термін простою на 1 калькул. один.: ".$model1[0]['time_transp'].' год.') ?></h4>
    <h4><?= Html::encode("Кількість калькуляційних одиниць: ".$kol) ?></h4>
    <h4><?= Html::encode("Вартість транспорту (простой): ".$model1[0]['all_p'].' грн.') ?></h4>

    <br>
    <div class="main_schet">
        <h3><?= Html::encode(" Результат розрахунку: ") ?></h3>
    </div>
    <br>

    <br/>
    <div class="main_pokaz">
     <h4><?= Html::encode("Увага! Остаточна вартість визначається після зв’язку з оператором.") ?></h4>
    </div>

    <table class="table table-bordered table-hover table-condensed">
        <thead>
        <tr>
            <th width="400px">Послуга </th>
            <th width="150px">Сума, грн.</th>
        </tr>
        </thead>
        <tbody>
        <tr>
                      
            <td><?= $model1[0]['work'] ?></td>
            <td><?= $kol*$model1[0]['cast_1'] ?></td>
        </tr>



        <tr>
            <td>Всього: </td>
            <td><?= $kol*$model1[0]['cast_1'] ?></td>

        </tr>
        <tr>
            <td>ПДВ: </td>
            <td><?= round($kol*$model1[0]['cast_1']*0.2,2) ?></td>

        </tr>
        <tr>
            <td>Разом з ПДВ: </td>
            <td class="itogo_s_nds"><?= $kol*$model1[0]['cast_1']+
                round($kol*$model1[0]['cast_1']*0.2,2) ?>
            </td>

        </tr>
        </tbody>
    </table>

    <div class="form-group">

        <?php
        $model = new forExcel();
        $model->nazv = $model1[0]['work'];
        $model->rabota = $kol*$model1[0]['cast_1'];
        $model->delivery = round($time_t * $model2[0]['stavka_grn'], 2);
        $model->transp = round($model1[0]['all_move']*$time_t,2)+
            round($model1[0]['all_p']*$model1[0]['time_transp']*$kol,2);
        $model->all = round($model1[0]['all_move']*$time_t,2)+
            round($model1[0]['all_p']*$model1[0]['time_transp']*$kol,2)+
            round($time_t*$model2[0]['stavka_grn'],2)+$kol*$model1[0]['cast_1']+$tmc_price_;
        $model->nds = round((round($model1[0]['all_move']*$time_t,2)+
                round($model1[0]['all_p']*$model1[0]['time_transp']*$kol,2)+
                (round($time_t*$model2[0]['stavka_grn'],2))+$kol*$model1[0]['cast_1']+$tmc_price_)*0.2,2);
        $model->all_nds = (round((round($model1[0]['all_move']*$time_t,2)+
                    round($model1[0]['all_p']*$model1[0]['time_transp']*$kol,2)+
                    (round($time_t*$model2[0]['stavka_grn'],2))+$kol*$model1[0]['cast_1']+$tmc_price_)*0.2,2)+
            (round($model1[0]['all_move']*$time_t,2)+
                round($model1[0]['all_p']*$model1[0]['time_transp']*$kol,2)+
                (round($time_t*$model2[0]['stavka_grn'],2))+$kol*$model1[0]['cast_1']+$tmc_price_));

        if(empty($adr_work));
            $adr_work='';
        $n_res=$name_res[0]->nazv;
//        $all_grn = round((round($model1[0]['all_move']*$time_t,2)+
//                    round($model1[0]['all_p']*$model1[0]['time_transp']*$kol,2)+
//                    (round($time_t*$model2[0]['stavka_grn'],2))+$kol*$model1[0]['cast_1']+$tmc_price_)*0.2,2)+
//            (round($model1[0]['all_move']*$time_t,2)+
//                round($model1[0]['all_p']*$model1[0]['time_transp']*$kol,2)+
//                (round($time_t*$model2[0]['stavka_grn'],2))+$kol*$model1[0]['cast_1']+$tmc_price_);

        $all_grn = $kol*$model1[0]['cast_1']+round($kol*$model1[0]['cast_1']*0.2,2);

        $model->all = $kol*$model1[0]['cast_1'];
        $model->nds = round($kol*$model1[0]['cast_1']*0.2,2);


        if(!($role==1||$role==2)): ?>
            <?php if(!$refresh): ?>
                <?= Html::a('Відмовитись',["cancel?&nazv=$model->nazv&summa=$model->all_nds&res=".$n_res.
                    "&adr_work=$adr_work"], ['class' => 'btn btn-primary']); ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if(!($role==1||$role==2)): ?>
        <?= Html::a(($refresh==0) ? 'Замовити послугу' : 'Зберегти',['proposal?rabota='.$model->rabota.'&delivery='.$model->delivery.
            '&transp='.$model->transp.'&all='.$model->all.'&g=' .$all_grn.
            '&u='.$model->nazv.'&res='.$name_res[0]->nazv.
            '&adr='.$model->adr_work.'&geo='.$geo.'&kol='.$kol.'&refresh='.$refresh.
            '&schet='.$schet.'&tmc='.$tmc_price_.'&tmc_name='.$tmc_name.
            '&time_t='.$time_t.'&mvp='.$mvp.'&time_prostoy='.$time_prostoy.'&time_work='.$time_work],
            ['class' => 'btn btn-primary']); ?>

        <?php endif; ?>
    </div>
</div>


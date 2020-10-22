<?php

use yii\helpers\Html;
include "phpqrcode/qrlib.php";

/* @var $this yii\web\View */
$this->title = "Рахунок для оплати";
$this->params['breadcrumbs'][] = $this->title;
$rr = 'UA483005280000026004455048529';
//26004455048529
$mfo = 'МФО: 300528 в АТ "ОТП БАНК"';
//300528
$okpo = '31793056';

?>
<!--<div class="site-about">-->
<div class=<?= $style_title ?> >
    <h3><?= Html::encode($this->title) ?></h3>
</div>

<table width="600px" class="table table-bordered ">

    <tr>
        <th width="250px">
            <div class="opl_left">
                <span class="span_single">Повідомлення про оплату за послугу по рахунку №
                    <?php
                    echo $model[0]['schet'];
                    ?>
                </span>
                <br>
                <br>
                Одержувач:
                <br>
                <?= Html::encode("ПрАТ «ПЕЕМ «ЦЕК»") ?>
                <br>
                <?= Html::encode("р/р: $rr $mfo") ?>

                <br>
                <?= Html::encode("ЄДРПОУ: $okpo") ?>

                <br>
                <br>
                <?= Html::encode("Платник:") ?>

                <br>
                <?= Html::encode($model[0]['nazv']) ?>

                <br>
                <?= Html::encode($model[0]['addr']) ?>
                <br>
                <br>
                <br>
                <span class="span_single">
                    <?= Html::encode("Сплачено:") ?>

                </span> <span class="span_ramka"> <?= Html::encode($total . ' грн.') ?> </span>
                <br>
                <br>
                <br>
                <?= Html::encode("Підпис") ?>

                <br>
                <br>
            </div>
        </th>
        <th width="350px" class="th_r">
            <div class="opl_left">
                <span class="span_single"><?= Html::encode("Рахунок за послугу №") ?>

                    <? if((int) $model[0]['schet']==9031 || (int) $model[0]['schet']==9030 || (int) $model[0]['schet']==9029): ?>
                        <?= Html::encode($model[0]['schet'] ) ?>
                    <? else: ?>
                        <?= Html::encode($model[0]['schet'] . ' від ' . date("d.m.Y", strtotime($model[0]['date']))) ?>
                    <? endif; ?>

                    <?= Html::encode(' по договору ' . $model[0]['contract']) ?>
                </span>
                <br>
                <br>
                <?= Html::encode("Платник:") ?>
                <br>
                <?= Html::encode($model[0]['nazv']) ?>
                <br>
                <?= Html::encode($model[0]['addr']) ?>
                <br>
                <br>
                <?php if ($q == 1): ?>
                    <?= Html::encode("Послуга (призначення платежу):") ?>

                    <br>
                    <?= Html::encode(del_brackets($model[0]['usluga'])) ?>
                    <br>
                    <?= Html::encode("Кiлькiсть калькуляцiйних одиниць: ".$model[0]['kol']) ?>

                    <br>
                    <br>
                    <!--                    <br>-->

                    <?= Html::encode("Сума без ПДВ:") ?>
                    <?= Html::encode($model[0]['summa_beznds']. ' грн.') ?>
                    <br>
                    <?= Html::encode("ПДВ:") ?>
                    <?= Html::encode($model[0]['summa']-$model[0]['summa_beznds']. ' грн.') ?>
                    <br>
                    <br>
                    <span class="span_single">
                        Всього до сплати:
                    </span> <span class="span_ramka">

                        <?= Html::encode($model[0]['summa'] . ' грн.') ?>
                    </span>
                <?php endif; ?>
                <?php if ($q > 1): ?>

                    <table width="350px" class="table table-bordered ">

                        <tr>
                            <th class="th_center" width="85%">
                                <?= Html::encode("Послуга") ?>
                            </th>
                            <th width="15%">
                                <?= Html::encode("Сума, грн.") ?>
                            </th>
                        </tr>
                        <?php for ($i = 0; $i < $q; $i++) { ?>
                            <tr>
                                <td>
                                    <?= Html::encode($model[$i]['usluga']) ?>
                                </td>
                                <td>
                                    <?= Html::encode($model[$i]['summa']) ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                    <br>
                    <br>
                    <br>
                    <span class="span_single">
                        Всього до сплати:
                    </span> <span class="span_ramka">
                        <?= Html::encode($total . ' грн.') ?>
                    </span>
                <?php endif; ?>

                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <?= Html::encode("Телефон для довідок:    0 800 300 015 (безкоштовно цілодобово)") ?>
                <div class="single_red">
                    <?= Html::encode("Рахунок дійсний протягом однієї доби !") ?>
                    <?= Html::encode("В призначенні платежу обов'язково указуйте № рахунку або договору!") ?>
                </div>
                <br>
                <br>

            </div>
        </th>
    </tr>


</table>
<?php
// Данные для QR-кода
$qr = "Рах.№".$model[0]['schet'] . ' від ' . date("d.m.Y", strtotime($model[0]['date'])).
    "|".'№ дог.' . $model[0]['contract']."|"."Платник:".$model[0]['nazv'].
    "|"."Призначення платежу:".$model[0]['usluga'].
    "|"."До сплати:".$model[0]['summa']. ' грн.';

$k=rand(1000000,9999999);
$qr_file="qrlib".$k.".png";
QRcode::png($qr, $qr_file, "H", 8, 5);
?>
<img class="qr_code" src="<?php echo $qr_file?>" alt="QR">

<code><?//= __FILE__ ?></code>


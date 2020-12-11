<?php
// Вывод результата рассчета стоимости подключения

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\models\forExcel;

use yii\bootstrap\Modal;
//debug($result);
//return;
$j = count($result['summa']);
//debug($warn);
?>

<div class="site-login">
    <h4><?= Html::encode("Результат імпорту з виписки OTP банка:") ?></h4>
    <br>
    <h4>Дата оплати: <?php echo $date; ?> </h4>
    <h4>Кількість оплат: <?php echo $j; ?> </h4>
    <br>
    <table width="600px" class="table table-bordered table-hover table-condensed ">
        <thead>
        <tr>
            <th width="150px">РЕМ</th>
            <th width="150px">№ рахунку</th>
            <th width="150px">Сума, грн.</th>
            <th width="150px">Примітки</th>

        </tr>
        </thead>
        <tbody>
      <?php
        for($i=0;$i<$j;$i++){
                $cnt = $result['contract'][$i];
                $sum = $result['summa'][$i];
                $rem = $result['res'][$i];
                $note = $result['note'][$i];
        ?>
        <tr>
            <td><?= $rem ?></td>
            <td><?= $cnt ?></td>
            <td><?= $sum ?></td>
            <td><?= $note ?></td>

        </tr>

        <?php }

        ?>

        </tbody>
    </table>
    <br>
    <br>
    <?php
    if(count($prop)>0) {
    $s='';
    foreach ($prop as $v)
    $s.='№'.($v+1).', ';
    $y=mb_strlen($s);
    $s=mb_substr($s,0,$y-2,'UTF-8');
    ?>
        <h4> <span class="label label-danger"><?= Html::encode("Неімпортовані номера записів:  ".$s); ?></span></h4>

        <?php
    }
    ?>
    <br>
    <br>
    <?php
    //$j=count($warn);
    $j = $kol_warn;
//    debug($warn);
//    return;
    if($j>0) {
        ?>
        <h4> <span class="label label-danger"><?= Html::encode("Зверніть увагу на неімпортовані записи:  "); ?></span></h4>


     <table width="600px" class="table table-bordered ">
    <tr>
        <th width="25%">
             <?= Html::encode("Сума, грн.") ?>
    </th>
    <th width="75%">
        <?= Html::encode("Призначення платежу") ?>
    </th>

    </tr>
    <?php

    for($i=0;$i<$j;$i++){
        $sum = $warn['summa'][$i];
        $note = '...'.str_replace('</td>','',$warn['note'][$i]);

    ?>
    <tr>

        <td>
            <?= Html::encode($sum) ?>
        </td>
        <td>
            <?= Html::encode($note) ?>
        </td>

    </tr>
    <?php
    }}
    ?>
    </table>



</div>


</br>
</br>




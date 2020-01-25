<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\SerialColumn;

$this->title = 'Результат аналітики';

?>
<div class="site-spr1">

    <h3><?= Html::encode($this->title) ?></h3>
Всього: 451<?= GridView::widget([
            'dataProvider' => $dataProvider,'tableOptions' => ['class' => 'table table-striped table-bordered table-condensed'],
            'summary' => false,
            'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'union_sch','read_z','id','kol','contract','inn','schet','date','date_opl','usluga','summa','summa_work','summa_transport','summa_delivery','summa_beznds','adres','geo','res','comment','time','surely','status','date_z','date_exec','act_work','date_akt','why_refusal','summa_tmc','tmc_name','nazv','addr','priz_nds','okpo','regsvid','tel','email','fio_dir','status_sch','usl',] ]); ?> </div><?php echo Html::a('Сброс в Excel', ['site/analytics_excel'],
                ['class' => 'btn btn-info']); ?>
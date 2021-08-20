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
Всього: 13<?= GridView::widget([
            'dataProvider' => $dataProvider,'tableOptions' => ['class' => 'table table-striped table-bordered table-condensed'],
            'summary' => false,
            'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'usluga','summa','kol',] ]); ?> </div><?php echo Html::a('Сброс в Excel', ['site/analytics_excel'],
                ['class' => 'btn btn-info']); ?>
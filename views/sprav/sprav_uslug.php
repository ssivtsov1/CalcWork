<?php


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\SerialColumn;

$this->title = 'Довідник послуг';
$this->params['breadcrumbs'][] = $this->title;

?>
<?//= Html::a('Добавити', ['createres'], ['class' => 'btn btn-success']) ?>
<div class="site-spr">
    <h3><?= Html::encode($this->title) ?></h3>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered table-condensed'],
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            //'id',
            'kod',
            'usluga',
            'exec_office'

        ],
    ]); ?>


    
</div>



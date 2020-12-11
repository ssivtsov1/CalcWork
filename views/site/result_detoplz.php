<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\ArrayHelper;

$this->title = "Звіт по оплаті (деталізація) з $date1 по $date2";
//$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="site-spr">

        <h4><?php

            echo Html::encode($this->title);
            ?></h4>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'emptyText' => 'Нічого не знайдено',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'res',
                'usluga',
                'nazv',
                'tel',
                [
                    'attribute' => 'date_opl',
                    'format' =>  ['date', 'dd.MM.Y'],

                ],
                'summa_beznds',
                'summa',
                'status_sch',
            ],
        ]);?>


    </div>

<?php

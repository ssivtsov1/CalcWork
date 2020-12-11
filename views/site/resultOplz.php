<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\ArrayHelper;

$this->title = "Звіт по оплаті з $date1 по $date2";
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
                [
                    'format' => 'raw',
                    'header' => '...',
                    'value' => function ($model) use($date1,$date2){
                        return \yii\helpers\Html::a('<span class="glyphicon glyphicon-list"></span>',
                            ['site/det_opl?usl=' . $model->usluga .
                                '&res=' . $model->res.'&date1=' . $date1.'&date2=' . $date2
                            ],
                            ['title' => Yii::t('yii', 'Відобразити детально'), 'data-pjax' => '0']
                        );
                    }
                ],

                'res',
                'usluga',
                'direct',

                ['attribute' =>'kol',
                    'label' => 'Кількість',
                    'encodeLabel' => false
                ],
                ['attribute' =>'summa',
                    'label' => 'Сума з ПДВ, <br /> грн:',
                    'encodeLabel' => false
                ],


//                ['attribute' =>'kol_zak',
//                    'label' => 'Кількість <br /> замовлено',
//                    'encodeLabel' => false
//                ],
//                //'kol_zak',
//                ['attribute' =>'kol_give',
//                    'label' => 'Кількість <br /> видано',
//                    'encodeLabel' => false
//                ],
//                //'kol_give',
//                ['attribute' =>'kol_zakup',
//                    'label' => 'Кількість <br /> залишилось купити',
//                    'encodeLabel' => false
//                ],
//                // 'kol_zakup',
//                ['attribute' =>'ost_res',
//                    'label' => 'Отримано РЕМ <br /> (кількість)',
//                    'encodeLabel' => false
//                ],
//                //'ost_res',
//                ['attribute' =>'ostp_res',
//                    'label' => 'Отримано РЕМ <br /> (вартість тис.грн.)',
//                    'encodeLabel' => false
//                ],
//                //'ostp_res',
//                ['attribute' =>'isp_res',
//                    'label' => 'Списано РЕМ <br /> (кількість)',
//                    'encodeLabel' => false
//                ],
//                //'isp_res',
//                ['attribute' =>'ostn_res',
//                    'label' => 'Не викор. РЕМ <br /> (кількість)',
//                    'encodeLabel' => false
//                ],
//                ['attribute' =>'ostz_res',
//                    'label' => 'Не отрим. РЕМ <br /> (кількість)',
//                    'encodeLabel' => false
//                ],
            ],
        ]);?>


    </div>

<?php

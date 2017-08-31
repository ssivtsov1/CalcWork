<?php


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\SerialColumn;
use yii\helpers\Url;

$this->title = 'Перегляд замовлень';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-spr">
    <h3><?= Html::encode($this->title) ?></h3>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'emptyText' => 'Нічого не знайдено',
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
//            'okpo',
//            'inn',
            ['attribute' =>'status_sch',
                'value' => function ($model){
                    if($model->status == 1)
                        return "<span class='text-danger'> $model->status_sch </span>";
                    elseif($model->status == 5)
                        return "<span class='text-success fontbld'> $model->status_sch </span>";
                    else
                        return $model->status_sch;
                },
                'format' => 'raw'
            ],
            'nazv',
//            'addr',
//            ['attribute' =>'priz_nds',
//                'value' => function ($model){
//                    if($model->priz_nds == 0)
//                        return 'ні';
//                    else
//                        return 'так';
//
//                },
//            ],
//            'okpo',
//            'regsvid',
            'tel',
            //'email',
            //'comment',
//            ['attribute' =>'surely',
//                'value' => function ($model){
//                    if($model->surely == 0)
//                        return '';
//                    else
//                        return $model->surely;
//
//                },
//            ],
//            'schet',
            'usluga',
            'summa',
            //'summa_beznds',
            //'summa_work',
           // 'summa_delivery',
           // 'summa_transport',
           // 'contract',
            'adres',
            'res',
            [
                'attribute' => 'date_z',
                'format' =>  ['DateTime','php:d.m.Y'],
                'label' => 'Бажана дата отрим. <br /> послуги:',
                'encodeLabel' => false,
            ],
            [
                'attribute' => 'date',
                'label' => 'Дата <br />заявки:',
                'format' =>  ['date', 'php:d.m.Y'],
                'encodeLabel' => false,
            ],
            'time',

            [
                'format' => 'raw',
                'header' => 'Форм. <br /> рах.',
                'value' => function($model) {
                    return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-book"></span>', ['site/opl'],[
                        'data' => [
                            'method' => 'post',
                            'params' => [
                                'sch' => $model->schet,
                            ]]],
                            ['title' => Yii::t('yii', 'Сформувати рахунок'), 'data-pjax' => '0']
                        );
                }
            ],
            [
                'format' => 'raw',
                'header' => 'Перерах.',
                'value' => function($model) {
                    return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-refresh"></span>',
                        ['site/refresh?work='.$model->usluga.
                            '&res='.$model->res.'&geo='.$model->geo.
                            '&kol='.$model->kol.
                            '&schet='.$model->schet],
                        ['title' => Yii::t('yii', 'Перерахувати заявку'), 'data-pjax' => '0']
                    );
                }
            ],

            [
                /**
                 * Указываем класс колонки
                 */
            'class' => \yii\grid\ActionColumn::class,
            'buttons'=>[

                'update'=>function ($url, $model) {
                    $customurl=Yii::$app->getUrlManager()->createUrl(['/site/upd','id'=>$model['id'],'mod'=>'schet']); //$model->id для AR
                    return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-pencil"></span>', $customurl,
                        ['title' => Yii::t('yii', 'Редагувати'), 'data-pjax' => '0']);
                }
            ],
            /**
             * Определяем набор кнопочек. По умолчанию {view} {update} {delete}
             */
            'template' => '{update}',
        ],
        ],
    ]); ?>

    <?= Html::a('Сброс в Excel', ['site/viewschet?'.
        'item=Excel'],
        ['class' => 'btn btn-info'])  ?>

</div>




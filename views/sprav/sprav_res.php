<?php


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\SerialColumn;

$this->title = 'Довідник РЕМів';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-spr1">
<?= Html::a('Добавити', ['createres'], ['class' => 'btn btn-success']) ?>

    <h3><?= Html::encode($this->title) ?></h3>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered table-condensed'],
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                /**
                 * Указываем класс колонки
                 */
                'class' => 'yii\grid\ActionColumn',
                 'header' => 'Кн. <br /> Управ.',
                 'contentOptions'=>['style'=>'white-space: normal;'] ,
                 'buttons'=>[
                  'delete'=>function ($url, $model) {
                        $customurl=Yii::$app->getUrlManager()->createUrl(['/sprav/delete','id'=>$model['id'],'mod'=>'spr_res']); //$model->id для AR
                        return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-remove-circle"></span>', $customurl,
                                                ['title' => Yii::t('yii', 'Видалити'),'data' => [
                                                    'confirm' => 'Ви впевнені, що хочете видалити цей запис ?',

                                                ], 'data-pjax' => '0']);
                  },
                  
                  'update'=>function ($url, $model) {
                        $customurl=Yii::$app->getUrlManager()->createUrl(['/sprav/update','id'=>$model['id'],'mod'=>'spr_res']); //$model->id для AR
                        return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-pencil"></span>', $customurl,
                                                ['title' => Yii::t('yii', 'Редагувати'), 'data-pjax' => '0']);
                  }
                ],
                 
                /**
                 * Определяем набор кнопочек. По умолчанию {view} {update} {delete}
                 */
                'template' => '{update} {delete}',
                 'controller' => '/sprav',
            ],
            'id',
            'nazv',
            'addr',
            'mail',
            'Director',
            [
                'label' => 'Назва в<br />родовому відмінку',
                'attribute' => 'parrent_nazv',
                'encodeLabel' => false,

            ],
            'geo_koord',
            [
                'label' => 'Гео-коорд. звідки <br /> їде машина (лабор.)',
                'attribute' => 'geo_fromwhere_sd',
                'encodeLabel' => false,

            ],
            [
                'label' => 'Гео-коорд. звідки <br /> їде машина (метрологи)',
                'attribute' => 'geo_fromwhere_sz',
                'encodeLabel' => false,

            ],
            [
                'label' => 'Місто звідки <br /> їде машина (лабор.)',
                'attribute' => 'town_fromwhere_sd',
                'encodeLabel' => false,

            ],
            [
                'label' => 'Місто звідки <br /> їде машина (метрологи)',
                'attribute' => 'town_fromwhere_sz',
                'encodeLabel' => false,

            ],
           
            'tel',
            'relat',
             
        ],
    ]); ?>


    
</div>



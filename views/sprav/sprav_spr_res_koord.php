<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\SerialColumn;

$this->title = 'Довідник відповідальних осіб по РЕМам';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::a('Добавити', ['createkoord'], ['class' => 'btn btn-success']) ?>
<div class="site-spr">
    <h3><?= Html::encode($this->title) ?></h3>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
       
        'summary' => false,
        'emptyText' => 'Нічого не знайдено',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
 
            'id',
            'nazv',
            'name_koord',
            'tel_mobile',
            'tel_town',
            'tel',
            'tel_dop',
            'email',
            
             [
                /**
                 * Указываем класс колонки
                 */
                'class' => \yii\grid\ActionColumn::class,
                 'buttons'=>[
                  'delete'=>function ($url, $model) {
                        $customurl=Yii::$app->getUrlManager()->createUrl(['/sprav/delete','id'=>$model['id'],'mod'=>'spr_res_koord']); //$model->id для AR
                        return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-remove-circle"></span>', $customurl,
                                                ['title' => Yii::t('yii', 'Видалити'),'data' => [
                                                'confirm' => 'Ви впевнені, що хочете видалити цей запис ?',
                                                ], 'data-pjax' => '0']);
                  },
                  
                  'update'=>function ($url, $model) {
                        $customurl=Yii::$app->getUrlManager()->createUrl(['/sprav/update','id'=>$model['id'],'mod'=>'spr_res_koord']); //$model->id для AR
                        return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-pencil"></span>', $customurl,
                                                ['title' => Yii::t('yii', 'Редагувати'), 'data-pjax' => '0']);
                  }
                ],
                /**
                 * Определяем набор кнопочек. По умолчанию {view} {update} {delete}
                 */
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>


    
</div>




<?php


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\SerialColumn;
use yii\helpers\Url;
if(empty($last))
    $this->title = 'Перегляд замовлень';
else
    $this->title = 'Перегляд замовлень [остання дата редагування: '.date("d.m.Y", strtotime($last)).']';
//$this->params['breadcrumbs'][] = $this->title;
//echo Yii::$app->user->identity->role;
?>

<div class="site-spr1">
<!--    --><?php //if(empty($last)): ?>
        <h4><?= Html::encode($this->title) ?></h4>
<!--    --><?php //endif; ?>
<!--    --><?php //if(!empty($last)): ?>
<!--        <h3>--><?//= Html::encode($this->title) ?><!--</h3>-->
<!--        <span>--><?//= Html::encode('[остання дата редагування: '.date("d.m.Y", strtotime($last)).']'); ?><!--</span>-->
<!--    --><?php //endif; ?>
<!--    --><?php
        if($role==2 || $role==3 || $role==5){
            echo Html::a('Імпорт з виписки', ['import_otp_new'], ['class' => 'btn btn-success']);
            echo '<br>';
            echo '<br>';
        }
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'emptyText' => 'Нічого не знайдено',
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
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
             [
                'format' => 'raw',
                'header' => 'Форм. <br /> рах.',
                'value' => function($model) {
                if($model->status>1)    
                    return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-book"></span>', ['site/opl'],[
                        'data' => [
                            'method' => 'post',
                            'params' => [
                                'sch' => $model->schet,
                            ]]],
                            ['title' => Yii::t('yii', 'Сформувати рахунок'), 'data-pjax' => '0']
                        );
                     else
                    return '';
                }
            ],
//            [
//                'format' => 'raw',
//                'header' => 'Форм. <br /> док.',
//                'value' => function($model) {
//                    if($model->status==5)    
//                    return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-briefcase"></span>', ['site/doc_email'],[
//                        'data' => [
//                            'method' => 'post',
//                            'params' => [
//                                'sch' => $model->schet,
//                                //'mail'=> $mail
//                            ]]],
//                        ['title' => Yii::t('yii', 'Формування документів'), 'data-pjax' => '0']
//                    );
//                    else
//                    return '';    
//                }
//            ],
                    
             [
                'format' => 'raw',
                'header' => 'Прочит.',
                'value' => function($model) {
                    if($model->read_z==0)    
                    return 'Ні';
                    else
                    return 'Так';    
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
                            '&schet='.$model->schet.
                            '&adr='.$model->adres],
                        ['title' => Yii::t('yii', 'Перерахувати заявку'), 'data-pjax' => '0']
                    );
                }
            ],

           // 'id',
            
            ['attribute' =>'main_u',
                'value' => function ($model){
                    $q = $model->union_sch;
                    if(!empty($q))
                        return '1';
                    else
                        return '';
                },
                'format' => 'raw'
            ],
            ['attribute' =>'schet',
                'value' => function ($model){
                    $q = $model->status;
                    switch($q){
                        case 1:
                            return "<span class='text-info'> $model->schet </span>";
                        case 2:
                            return "<span class='text-proc'> $model->schet </span>";
                        case 3:
                            return "<span class='text-opl'> $model->schet </span>";
                        case -1:
                            return "<span class='text-info fontbld'> $model->schet </span>";
                        case 5:
                            return "<span class='text-success fontbld'> $model->schet </span>";
                        case 8:
                            return "<span class='text-bad fontbld'> $model->schet </span>";
                        default:
                            return $model->schet;}
                },
                'format' => 'raw'
            ],
//            'okpo',
//            'inn',
            ['attribute' =>'status_sch',
                'label' => 'Статус <br /> заявки:',
                'encodeLabel' => false,
                'value' => function ($model){
                    $q = $model->status;
                    switch($q){
                         case 1:
                         return "<span class='text-info'> $model->status_sch </span>";
                         case 2:
                         return "<span class='text-proc'> $model->status_sch </span>";
                         case 3:
                            return "<span class='text-opl'> $model->status_sch </span>";
                         case -1:
                         return "<span class='text-info fontbld'> $model->status_sch </span>";
                         case 5:
                        return "<span class='text-success fontbld'> $model->status_sch </span>";
                         case 8:
                         return "<span class='text-bad fontbld'> $model->status_sch </span>";
                         default:
                    return $model->status_sch;}
                },
                'filter'=>array('Нова перерахована' => 'Нова перерахована',
                    'Нова' => 'Нова',
                    'Узгоджена' => 'Узгоджена',
                    'Оплачена' => 'Оплачена',
                    'В роботі' => 'В роботі',
                    'Виконана' => 'Виконана',
                    'Відмова' => 'Відмова',

                ),
                'format' => 'raw'
            ],
            
            //'nazv',
            ['attribute' =>'n_work',
                'value' => function ($model){
                    $q = $model->status;
                    switch($q){
                        case 1:
                            return "<span class='text-info'> $model->n_work </span>";
                        case 2:
                            return "<span class='text-proc'> $model->n_work </span>";
                        case 3:
                            return "<span class='text-opl'> $model->n_work </span>";
                        case -1:
                            return "<span class='text-info fontbld'> $model->n_work </span>";
                        case 5:
                            return "<span class='text-success fontbld'> $model->n_work </span>";
                        case 8:
                            return "<span class='text-bad fontbld'> $model->n_work </span>";
                        default:
                            return $model->n_work;}
                },
                'format' => 'raw'
            ],
                ['attribute' =>'nazv',
                'value' => function ($model){
                    $q = $model->status;
                    switch($q){
                         case 1:
                         return "<span class='text-info'> $model->nazv </span>";
                         case 2:
                         return "<span class='text-proc'> $model->nazv </span>";
                         case 3:
                            return "<span class='text-opl'> $model->nazv </span>";
                         case -1:
                         return "<span class='text-info fontbld'> $model->nazv </span>";
                         case 5:
                        return "<span class='text-success fontbld'> $model->nazv </span>";
                         case 8:
                         return "<span class='text-bad fontbld'> $model->nazv </span>";
                         default:
                    return $model->nazv;}
                },
                'format' => 'raw'
            ],
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
            //'tel',
            ['attribute' =>'tel',
                'value' => function ($model){
                    $q = $model->status;
                    switch($q){
                        case 1:
                            return "<span class='text-info'> $model->tel </span>";
                        case 2:
                            return "<span class='text-proc'> $model->tel </span>";
                        case 3:
                            return "<span class='text-opl'> $model->tel </span>";
                        case -1:
                            return "<span class='text-info fontbld'> $model->tel </span>";
                        case 5:
                            return "<span class='text-success fontbld'> $model->tel </span>";
                        case 8:
                         return "<span class='text-bad fontbld'> $model->tel </span>";    
                        default:
                            return $model->tel;}
                },
                'format' => 'raw'
            ],
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
            //'usluga',
            ['attribute' =>'usluga',
                'value' => function ($model){
                    $q = $model->status;   
                    switch($q){
                        case 1:
                            return "<span class='text-info'> $model->usluga </span>";
                        case 2:
                            return "<span class='text-proc'> $model->usluga </span>";
                        case 3:
                            return "<span class='text-opl'> $model->usluga </span>";
                        case -1:
                            return "<span class='text-info fontbld'> $model->usluga </span>";
                        case 5:
                            return "<span class='text-success fontbld'> $model->usluga </span>";
                        case 8:
                         return "<span class='text-bad fontbld'> $model->usluga </span>";    
                        default:
                            return $model->usluga;}
                },
                'format' => 'raw'
            ],
            //'summa',
            ['attribute' =>'summa',
                'label' => 'Сума <br /> з ПДВ,грн:',
                'encodeLabel' => false,
                'value' => function ($model){
                    $q = $model->status;
                    switch($q){
                        case 1:
                            return "<span class='text-info'> $model->summa </span>";
                        case 2:
                            return "<span class='text-proc'> $model->summa </span>";
                        case 3:
                            return "<span class='text-opl'> $model->summa </span>";
                        case -1:
                            return "<span class='text-info fontbld'> $model->summa </span>";
                        case 5:
                            return "<span class='text-success fontbld'> $model->summa </span>";
                        case 8:
                         return "<span class='text-bad fontbld'> $model->summa </span>";    
                        default:
                            return $model->summa;}
                },
                'format' => 'raw'
            ],
            //'summa_beznds',
            //'summa_work',
           // 'summa_delivery',
           // 'summa_transport',
           // 'contract',
            //'adres',
            ['attribute' =>'adres',
                'value' => function ($model){
                    $q = $model->status;
                    switch($q){
                        case 1:
                            return "<span class='text-info'> $model->adres </span>";
                        case 2:
                            return "<span class='text-proc'> $model->adres </span>";
                        case 3:
                            return "<span class='text-opl'> $model->adres </span>";
                        case -1:
                            return "<span class='text-info fontbld'> $model->adres </span>";
                        case 5:
                            return "<span class='text-success fontbld'> $model->adres </span>";
                        case 8:
                         return "<span class='text-bad fontbld'> $model->adres </span>";    
                        default:
                            return $model->adres;}
                },
                'format' => 'raw'
            ],
            //'res',
            ['attribute' =>'res',
                'value' => function ($model){
                    $q = $model->status;
                    switch($q){
                        case 1:
                            return "<span class='text-info'> $model->res </span>";
                        case 2:
                            return "<span class='text-proc'> $model->res </span>";
                        case 3:
                            return "<span class='text-opl'> $model->res </span>";
                        case -1:
                            return "<span class='text-info fontbld'> $model->res </span>";
                        case 5:
                            return "<span class='text-success fontbld'> $model->res </span>";
                        case 8:
                            return "<span class='text-bad fontbld'> $model->res </span>";    
                        default:
                            return $model->res;}
                },
                        
                'filter'=>array('Жовтоводські РЕМ' => 'Жовтоводські РЕМ',
                        'Дніпропетровські РЕМ' => 'Дніпропетровські РЕМ',
                        'Гвардійська дільниця' => 'Гвардійська дільниця',
                        'Криворізькі РЕМ' => 'Криворізькі РЕМ',
                        'Павлоградські РЕМ' => 'Павлоградські РЕМ',
                        'Вільногірські РЕМ' => 'Вільногірські РЕМ',
                        'Інгулецька дільниця' => 'Інгулецька дільниця',
                        'Апостолівська дільниця' => 'Апостолівська дільниця',
                        'СПС' => 'СПС',
                        'СЗОЕ' => 'СЗОЕ',
                        'СДІЗП' => 'СДІЗП',
                        'СЦ' => 'СЦ',
                        
                       ),        
                 'format' => 'raw'
            ],
                         [
                'attribute' => 'date_opl',
                //'format' =>  ['DateTime','php:d.m.Y'],
                'label' => 'Дата  <br /> оплати:',
                'encodeLabel' => false,
                'value' => function ($model){
                    $q = $model->status;
                    switch($q){
                        case 1:
                            return "<span class='text-info'> $model->date_opl </span>";
                        case 2:
                            return "<span class='text-proc'> $model->date_opl </span>";
                        case 3:
                            return "<span class='text-opl'> $model->date_opl </span>";
                        case -1:
                            return "<span class='text-info fontbld'> $model->date_opl </span>";
                        case 5:
                            return "<span class='text-success fontbld'> $model->date_opl </span>";
                        case 8:
                            return "<span class='text-bad fontbld'> $model->date_opl </span>";    
                        default:
                            return $model->date_opl;}
                },
                             'filter' => \yii\jui\DatePicker::widget([
                                 'model'=>$searchModel,
                                 'attribute'=>'date_opl',
                                 'language' => 'uk',
                                 //'dateFormat' => 'yy-mm-dd',
                             ]),
                             //'format' => 'html',

                           'format' => 'raw'

            ],
            [
                'attribute' => 'date_z',
                'format' =>  ['DateTime','php:d.m.Y'],
                'label' => 'Бажана <br /> дата отрим. <br /> послуги:',
                'encodeLabel' => false,
                'value' => function ($model){
                    $q = $model->status;
                    switch($q){
                        case 1:
                            return "<span class='text-info'> $model->date_z </span>";
                        case 2:
                            return "<span class='text-proc'> $model->date_z </span>";
                        case 3:
                            return "<span class='text-opl'> $model->date_z </span>";
                        case -1:
                            return "<span class='text-info fontbld'> $model->date_z </span>";
                        case 5:
                            return "<span class='text-success fontbld'> $model->date_z </span>";
                        case 8:
                            return "<span class='text-bad fontbld'> $model->date_z </span>";    
                        default:
                            return $model->date_z;}
                },
                'format' => 'raw'

            ],
            [
                'attribute' => 'date',
                'label' => 'Дата <br />заявки:',
                'format' =>  ['date', 'php:d.m.Y'],
                'encodeLabel' => false,
                'value' => function ($model){
                    $q = $model->status;
                    switch($q){
                        case 1:
                            return "<span class='text-info'> $model->date </span>";
                        case 2:
                            return "<span class='text-proc'> $model->date </span>";
                        case 3:
                            return "<span class='text-opl'> $model->date </span>";
                        case -1:
                            return "<span class='text-info fontbld'> $model->date </span>";
                        case 5:
                            return "<span class='text-success fontbld'> $model->date </span>";
                        case 8:
                            return "<span class='text-bad fontbld'> $model->date </span>";     
                        default:
                            return $model->date;}
                },
                'filter' => \yii\jui\DatePicker::widget([
                    'model'=>$searchModel,
                    'attribute'=>'date',
                    'language' => 'uk',
                    //'dateFormat' => 'yy-mm-dd',
                ]),
                'format' => 'raw'
            ],
            //'time',
            ['attribute' =>'time',
                'value' => function ($model){
                    $q = $model->status;
                    switch($q){
                        case 1:
                            return "<span class='text-info'> $model->time </span>";
                        case 2:
                            return "<span class='text-proc'> $model->time </span>";
                        case 3:
                            return "<span class='text-opl'> $model->time </span>";
                        case -1:
                            return "<span class='text-info fontbld'> $model->time </span>";
                        case 5:
                            return "<span class='text-success fontbld'> $model->time </span>";
                        case 8:
                            return "<span class='text-bad fontbld'> $model->time </span>"; 
                        default:
                            return $model->time;}
                },
                'format' => 'raw'
            ],
        ],
    ]); ?>

    <?= Html::a('Сброс в Excel', ['site/viewschet?'.
        'item=Excel'],
        ['class' => 'btn btn-info']);  
    echo Html::encode('  ');  
            
    echo Html::a('Аналітика', ['site/analytics'],
        ['class' => 'btn btn-success']);  
    ?>

</div>




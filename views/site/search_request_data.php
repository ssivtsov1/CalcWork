<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii \ helpers \ ArrayHelper;

$arr1 = ['- Виберіть підрозділ *-','Дніпропетровські РЕМ','Вільногірські РЕМ','Павлоградські РЕМ','Гвардійська дільниця',
    'Жовтоводські РЕМ','Криворізькі РЕМ','Апостолівська дільниця','Інгулецька дільниця'];
$arr2 = ['- Виберіть статус *-', 'Узгоджена', 'Оплачена', 'В роботі', 'Виконана', 'Відмова'];
$arr3 = ['- Оберіть поле *-', '№ заяки', 'Дата оплати', 'Сума'];
$arr4 = ['- Оберіть сортування *-', 'збільшенням', 'зменьшенням'];
?>
<div class = 'test col-xs-3' >
    <h3>Пошук заявок</h3>
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'name')->label('Заявка') -> textInput()?>

    <?= $form->field($model, 'text')->label('Споживач') -> textInput()?>

    <?=$form->field($model, 'usl')->
       dropDownList(ArrayHelper::map(
        app\models\spr_costwork::findbysql('
       select 0 as id," " as usluga  union
       Select min(id) as id,usluga from costwork 
        where LENGTH(ltrim(rtrim(usluga)))<>0 group by usluga order by usluga')
            ->all(), 'id', 'usluga'),
        []) ?>

    <?= $form->field($model, 'tel')->label('Телефон') ?>

    <?= $form->field($model, 'date1')->label('Дата оплати з ')-> widget(\yii\jui\DatePicker::classname(), ['language' => 'uk']) ?>

    <?= $form->field($model, 'date2')->label('Дата оплати по ')-> widget(\yii\jui\DatePicker::classname(), ['language' => 'uk']) ?>

    <?= $form->field($model, 'summ1')->label('Сума з ПДВ, грн з')  ?>

    <?= $form->field($model, 'summ2')->label('Сума з ПДВ, грн по') ?>

    <?= $form->field($model, 'summ_pdv_1')->label('Сума з ПДВ, грн (точно)')  ?>

    <?= $form->field($model, 'summ_pdv')->label('Сума з ПДВ, грн (приблизно)')  ?>

    <?= $form->field($model, 'summ_pdv_1')->label('Сума з ПДВ, грн (точно)')  ?>

    <?= $form->field($model, 'summ_c')  ?>

    <?= $form->field($model, 'pidrozdil')->label('Підрозділ')  -> dropDownList ( $arr1 ) ?>

    <?= $form->field($model, 'status')->label('Статус заявки')  -> dropDownList ( $arr2 ) ?>

    <?= $form->field($model, 'result')->label('Сортувати результат по полю')  -> dropDownList ( $arr3 ) ?>

    <?= $form->field($model, 'sorting')->label('Сортування за')  -> dropDownList ( $arr4 ) ?>


    <?= Html::submitButton('ОК',['class' => 'btn btn-success']) ?>
    <?= Html::a('SQL',["sql_search"], ['class' => 'btn btn-info']); ?>

    <?php ActiveForm::end() ?>
</div>

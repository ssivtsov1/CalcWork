<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Формування заявки';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
<!--    <h3>--><?//= Html::encode($this->title) ?><!--</h3>-->

    <p>
    <?php if($is==0): ?>
         <h4><?= Html::encode("Заявку сформовано. Дякуємо за звернення до ПрАТ «ПЕЕМ «ЦЕК».") ?></h4>
          <h4><?= Html::encode("Чекайте на зворотній зв’язок з оператором.") ?></h4>
    <?php endif; ?>
    <?php if($is==1): ?>
        <h3><?= Html::encode("Така заявка вже є") ?></h3>
    <?php endif; ?>
    <?php if($is==3): ?>
        <h3><?= Html::encode("Заявку № $nazv перераховано.") ?></h3>
    <?php endif; ?>

    <?php if($is==2): ?>
        <h4><?= Html::encode("Ваша заявка в черзі на обробку. Якнайшвидше з Вами з’єднається оператор.") ?></h4>
        <h4><?= Html::encode("Чекайте на зворотній зв’язок з оператором.") ?></h4>
    <?php endif; ?>

    </p>

    <?php if($is==-1): ?>
        <?= Html::a("З’єднатись з оператором",['relat?sch='.$nazv], ['class' => 'btn btn-primary']); ?>

        <?
           // Подключение стороннего сервиса по оплате
            echo(Html::a('Сплатити карткою',\yii\helpers\Url::to('https://secure.mega-billing.com/byt/'),['target' => '_blank',
           'class' => 'btn btn-primary']));
        ?>

        <?
            echo(Html::a('Сплатити в відділенні банку',['site/opl'], [
                'data' => [
                    'method' => 'post',
                    'params' => [
                        'sch' => $nazv,
                    ],
                ],'class' => 'btn btn-primary']));
        ?>
  

    <?php endif; ?>
    <code><?//= __FILE__ ?></code>
</div>

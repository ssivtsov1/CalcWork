<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Про программу';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h2><?= Html::encode($this->title) ?></h2>

    <p>
        Ця програма здійснює розрахунок робіт відповідно вибраному виду роботи, а також транспортні витрати.
    </p>

    <code><?//= __FILE__ ?></code>
</div>

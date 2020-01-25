<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = "Інформаційне повідомлення";
$this->params['breadcrumbs'][] = $this->title;
?>
<!--<div class="site-about">-->
    <div class=<?= $style_title ?> >
         <h3><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="contract_center">
    <span class="span_single">
        <?= Html::encode("Інформаційне повідомлення для надання вашим підрозділом послуги
                        фізичній особі (або сторонній організації)") ?>

    </span>
    </div>

<br>
<br>
<span class="contract_center_text_bold" >
    <?= Html::encode("Споживач послуги (контрагент):");?>
</span>
<br>
<span class="contract_center_text" >
    <?= Html::encode($model[0]['nazv']);?>
</span>
<br>

<hr class="inf_line_main">

<span class="contract_center_text_bold" >
    <?= Html::encode("Назва послуги:");?>
</span>
<br>
<?php if($q==1): ?>
    <span class="contract_center_text" >
        <?= Html::encode($model[0]['usluga']);?>
    </span>
<?php endif; ?>

<?php if($q>1): ?>
<?php 
        $str_u='';
        for ($i = 0; $i < $q; $i++) { 
            $str_u.=mb_strtolower($model[$i]['usluga'],'utf-8').', ';
        } 
        $str_u=mb_substr($str_u,0,mb_strlen($str_u,'utf-8')-2,'utf-8');
?>        
    <div class="contract_center_text1" >  
        <?= Html::encode($str_u);?>
    </div>
<?php endif; ?>
<br>

<hr class="inf_line_main">

<br>
<span class="contract_center_text_bold" >
    <?= Html::encode("Вартість послуги:");?>
</span>
<br>
<?php if($q==1): ?>
    <span class="contract_center_text" >
        <?= Html::encode($model[0]['summa']).' грн.';?>
    </span>
<?php endif; ?>
<?php if($q>1): ?>
    <span class="contract_center_text" >
        <?= Html::encode($total.' грн.');?>
    </span>
<?php endif; ?>
<br>
<hr class="inf_line_main">
<br>
<span class="contract_center_text_bold" >
    <?= Html::encode("Адреса виконання робіт:");?>
</span>
<br>
<span class="contract_center_text" >
    <?= Html::encode($model[0]['adres']);?>
</span>
<br>
<hr class="inf_line_main">
<br>
<span class="contract_center_text_bold" >
    <?= Html::encode("Дата виконання роботи:");?>
</span>
<br>
<span class="contract_center_text" >
    <?= Html::encode(changeDateFormat($model[0]['date_exec'], 'd.m.Y'));?>
</span>
<br>
<hr class="inf_line_main">
<br>
<br>
<br>

<?= Html::encode("Статус оплати за роботу, яка буде виконуватись:");?>
<br>
<br>
<?= Html::encode("ОПЛАЧЕНО");?>
<br>
<br>
<?= Html::encode("Додатки:");?>
<br>
<?= Html::encode("1. Договір.");?>
<br>
<?= Html::encode("2. Акт виконаних робіт.");?>
<br>
<?= Html::encode("3. Рахунок.");?>
<br>
<br>
<?= Html::encode("Договір та акт виконаних робіт роздрукувати в двох екземплярах.");?>
<br>
<br>

   
    <code><?//= __FILE__ ?></code>

<!--</div>-->

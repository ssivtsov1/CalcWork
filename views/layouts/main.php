<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\web\Request;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php

        $flag=1;
         if(!isset(Yii::$app->user->identity->role))
                $flag=0;

//        debug(Yii::$app->user->identity->role);
//        debug($flag);

            NavBar::begin([
                'brandLabel' => 'Розрахунок вартості робіт',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
        if($flag)
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => 'Головна', 'url' => ['/site/index']],

                    ['label' => 'Довідники', 'url' => ['/site/index'],
                        'options' => ['id' => 'down_menu'],			
                     'items' => [
                                ['label' => 'Довідник РЕМів', 'url' => ['/sprav/sprav_res']],
                                ['label' => 'Довідник вартості робіт', 'url' => ['/sprav/sprav_work']], 
                                ['label' => 'Довідник транспорту', 'url' => ['/sprav/sprav_transp']],
                                ['label' => 'Довідник контрагентів', 'url' => ['/sprav/sprav_klient']],
                                ['label' => 'Довідник послуг', 'url' => ['/sprav/sprav_uslug']],
                                ['label' => 'Статуси заявки', 'url' => ['/sprav/status_sch']]
                        ]],

                    ['label' => 'Сервіс', 'url' => ['/site/index'],
                        'options' => ['id' => 'down_menu'],
                        'items' => [
                            ['label' => 'Перегляд заявок', 'url' => ['/site/viewschet']],
                            ['label' => 'Відмови', 'url' => ['/site/viewcancel']],

                        ]],

                   // ['label' => 'Реєстрація', 'url' => ['/site/registr']],
                    ['label' => 'Про программу', 'url' => ['/site/about']],
                    ['label' => 'Контакти', 'url' => ['/site/contact']],
                    ['label' => 'Вийти', 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']],
                    /*
                    Yii::$app->user->isGuest ?
                        ['label' => 'Login', 'url' => ['/site/login']] :
                        ['label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                            'url' => ['/site/logout'],
                            'linkOptions' => ['data-method' => 'post']],
                     * 
                     */
                ],
            ]);
        else
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => 'Головна', 'url' => ['/site/index']],

                   // ['label' => 'Реєстрація', 'url' => ['/site/registr']],
                    ['label' => 'Про программу', 'url' => ['/site/about']],
                    ['label' => 'Контакти', 'url' => ['/site/contact']],
                   // ['label' => 'Вийти', 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']],
                    /*
                    Yii::$app->user->isGuest ?
                        ['label' => 'Login', 'url' => ['/site/login']] :
                        ['label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                            'url' => ['/site/logout'],
                            'linkOptions' => ['data-method' => 'post']],
                     *
                     */
                ],
            ]);
            NavBar::end();
        ?>


        <!--Вывод логотипа-->
        <? if(!strpos(Yii::$app->request->url,'/cek')): ?>
        <? if(strlen(Yii::$app->request->url)==10): ?>
        <img class="logo_site" src="web/Logo.png" alt="ЦЕК" />
        <? endif; ?>

        <? if(strlen(Yii::$app->request->url)<>10): ?>
            <img class="logo_site" src="../Logo.png" alt="ЦЕК" />
        <? endif; ?>
        <? endif; ?>

        <? if(strpos(Yii::$app->request->url,'/cek')): ?>
            <? if(strlen(Yii::$app->request->url)==13): ?>
                <img class="logo_site" src="web/Logo.png" alt="ЦЕК" />
            <? endif; ?>

            <? if(strlen(Yii::$app->request->url)<>13): ?>
                <img class="logo_site" src="../Logo.png" alt="ЦЕК" />
            <? endif; ?>
        <? endif; ?>


        <div class="container">
            <div class="page-header">
                <small class="text-info">Кол-центр: <mark>0 800 300 015</mark> безкоштовно цілодобово</small></h1>
            </div>
            <?= Breadcrumbs::widget([
                'homeLink' => ['label' => 'Головна', 'url' => '/CalcWork'],
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= $content ?>
        </div>
    </div>
   

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; ЦЕК <?= date('Y') ?></p>
            <p class="pull-right"><?//= Yii::powered() ?></p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

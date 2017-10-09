<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\web\Request;
use app\models\schet;
use app\models\max_schet;

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
    <?php
    $flag=1;
    $role=0;
    $department = '';
    if(!isset(Yii::$app->user->identity->role))
    {      $flag=0;}
    else{
    $role=Yii::$app->user->identity->role;
    $department=Yii::$app->user->identity->department;

    }

    if($flag==1 && $role==3) { ?>
    <script>
        function fresh() {
        location.reload();
        }
        setInterval("fresh()",600000);
    </script>
    <?php } $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php


//        debug(Yii::$app->user->identity->role);
//        debug($flag);

            NavBar::begin([
                'brandLabel' => 'Розрахунок вартості робіт',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);

        if($flag){
            if($role==3)
            {echo Nav::widget([
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
                    ['label' => 'Про программу', 'url' => ['/site/about']],
                    ['label' => 'Контакти', 'url' => ['/site/contact']],
                    ['label' => 'Вийти', 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']],
                ],
            ]);}
        else
            {echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => 'Головна', 'url' => ['/site/index']],
                    ['label' => 'Сервіс', 'url' => ['/site/index'],
                        'options' => ['id' => 'down_menu'],
                        'items' => [
                            ['label' => 'Перегляд заявок', 'url' => ['/site/viewschet']],
                            //['label' => 'Відмови', 'url' => ['/site/viewcancel']],
                        ]],
                    ['label' => 'Про программу', 'url' => ['/site/about']],
                    ['label' => 'Контакти', 'url' => ['/site/contact']],
                    ['label' => 'Вийти', 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']],

                ],
            ]);
        }}
        else
        {echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => 'Головна', 'url' => ['/site/index']],
                    ['label' => 'Про программу', 'url' => ['/site/about']],
                    ['label' => 'Контакти', 'url' => ['/site/contact']],
                ],
            ]);}
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
            <?php if(!$flag): ?>
            <div class="page-header">
                <small class="text-info">Кол-центр: <mark>0 800 300 015</mark> безкоштовно цілодобово</small></h1>
            </div>
            <?php endif; ?>

            <?php if($flag): ?>
                <div class="page-header">
                    <small class="text-info">Ви зайшли як: <mark><?php echo $department; ?></mark> </small></h1>
                </div>
            <?php endif; ?>
            
             <?php
             if($flag==1 && $role==3) {
//                $filename = 'cron_schet';
//                if (file_exists($filename)) {
//                $f = fopen($filename,'r');
//                $s = fgets($f);
                  $model = new schet();
                  $sql = 'select max(cast(id as unsigned)) as id from schet';
                  $sch = schet::findBySql($sql)->one();
                  $max_id = $sch->id;
                  $sch_last = max_schet::find()->one();
                  $max_value = $sch_last->value;
                if($max_id>$max_value){
                $kol =  $max_id-$max_value;   
                $music = "http://localhost/CalcWork/web/zvukovye-effekty-korotkie-fanfary.mp3";

                $audio = "<embed src='".$music."'>";
                ?>
                 <? if($kol==1): ?>
                    <div class="d15" >
                        <h3><?= Html::encode("Увага з’явилась нова заявка №$max_id") ?></h3>

                    </div>
                <? endif; ?>
                 <? if($kol>1 && $kol<5): ?>
                    <div class="d15" >
                        <h3><?= Html::encode("Увага з’явилась $kol нові заявки, остання №$max_id") ?></h3>

                    </div>
                <? endif; ?>
                <? if($kol>4): ?>
                    <div class="d15" >
                        <h3><?= Html::encode("Увага з’явилось $kol нових заявок, остання №$max_id") ?></h3>

                    </div>
                <? endif; ?>
               <?php
                   echo $audio;
                   $sch_last->value = $max_id;
                   $sch_last->save();
                }
                   //unlink($filename);
                   
                }
              
     
            ?>
            
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

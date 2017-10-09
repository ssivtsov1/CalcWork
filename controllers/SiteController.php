<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\ContactForm;
use app\models\InputDataForm;
use app\models\Calc;
use app\models\spr_res;
use app\models\spr_uslug;
use app\models\spr_work;
use app\models\spr_costwork;
use app\models\spr_transp;
use app\models\klient;
use app\models\schet;
use app\models\viewschet;
use app\models\requestsearch;
use app\models\tofile;
use app\models\forExcel;
use app\models\info;
use app\models\User;
use app\models\loginform;
use app\models\potrebitel;
use app\models\refusal;
use app\models\input_refusal;
use kartik\mpdf\Pdf;
//use mpdf\mpdf;
use yii\web\UploadedFile;

class SiteController extends Controller
{  /**
 * 
 * @return type
 *
 */

    //public $defaultAction = 'index';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    //  Происходит при запуске сайта
    public function actionIndex()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect(['site/more']);
        }
        if(strpos(Yii::$app->request->url,'/cek')==0)
            return $this->redirect(['site/more']);
        $model = new loginform();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['site/more']);
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    //  Происходит после ввода пароля
    public function actionMore()
    {
        $model = new InputDataForm();
        if ($model->load(Yii::$app->request->post()))
        {
            return $this->redirect([ 'calc','id' => $model->work,'kol' => $model->kol,'poezdka' => $model->poezdka,
                'distance' => $model->distance,'res' => $model->res ,
                'potrebitel' => $model->potrebitel,
                'time_work' => $model->time_work,
                'time_prostoy' => $model->time_prostoy,
                'nazv' => $model->nazv,'adr_work' => $model->adr_potr,
                'geo' => $model->geo,'refresh' => 0,'schet' => '']);
            }
         else {

            return $this->render('inputdata', [
                'model' => $model,
            ]);
        }
    }

    //  Происходит при перерасчете заявки
    public function actionRefresh($work,$res,$geo,$kol=1,$schet='',$adr)
    {
        $model = new InputDataForm();
        $sql = 'select id as nom,usluga from costwork where work=:search';
        $model2 = Calc::findBySql($sql,[':search'=>"$work"])->all();
        $model_usl = spr_costwork::findbysql('Select min(id) as id,usluga from costwork where LENGTH(ltrim(rtrim(usluga)))<>1 group by usluga order by usluga')
            ->all();
        $usl = $model2[0]->usluga;
        $id_usl = 0;
        foreach ($model_usl as $arr){
            $u = $arr->usluga;
            if($u==$usl) {$id_usl = $arr->id;
                break;}
        }
        $model_res = spr_res::find()->where('nazv=:nazv',[':nazv' => $res])->all();
        $model->work = $model2[0]->nom;
        $model->usluga = $id_usl;
        $model->res = $model_res[0]->id;
        $model->geo = $geo;
        $model->kol = $kol;
        $model->addr_work = $adr;
        $model->refresh = 1;

        if ($model->load(Yii::$app->request->post()))
        {
            return $this->redirect([ 'calc','id' => $model->work,'kol' => $model->kol,'poezdka' => $model->poezdka,
                'distance' => $model->distance,'res' => $model->res ,
                'potrebitel' => $model->potrebitel,
                'time_work' => $model->time_work,
                'time_prostoy' => $model->time_prostoy,
                'nazv' => $model->nazv,'adr_work' => $model->adr_potr,
                'geo' => $model->geo,'refresh' => $model->refresh,'schet' => $schet]);
        }
        else {
            return $this->render('inputdata', [
                'model' => $model,
            ]);
        }
    }

// Оформление заявки пользователя
    public function actionProposal($rabota,$delivery,$transp,$all,$g,$u,$res,$adr,$geo,$kol,$refresh,$schet){

        if($delivery==-1) $delivery = 0;
        if($rabota==-1) $rabota = 0;
        if($transp==-1) $transp = 0;
        if($refresh==0) {
            $model = new Klient();
            $model->adr_work = str_replace('Адреса виконання робіт:', '', $adr);
            if ($model->load(Yii::$app->request->post())) {
                $inn = $model->inn;
                $iklient = klient::find()->select(['nazv', 'addr', 'okpo', 'regsvid', 'priz_nds', 'email', 'tel'])
                    ->where('inn=:inn',[':inn' => $inn])->all();
                $model1 = potrebitel::find()->where('inn=:inn',[':inn' => $inn])->one();
                if (!isset($iklient[0]->nazv)) {
                     $model->save();

                } else {
                    $model1->tel = $model->tel;
                    $model1->inn = $model->inn;
                    $model1->email = $model->email;
                    $model1->nazv = $model->nazv;
                    $model1->addr = $model->addr;
                    $model1->okpo = $model->okpo;
                    $model1->regsvid = $model->regsvid;
                    $model1->priz_nds = $model->priz_nds;
                    $model1->person = $model->person;
                    $model1->fio_dir = $model->fio_dir;
                    $model1->contact_person = $model->contact_person;
                    $model1->save();
                }

                return $this->redirect(['cnt', 'rabota' => $rabota, 'delivery' => $delivery,
                    'transp' => $transp, 'all' => $all, 'g' => $g, 'u' => $u,
                    'inn' => $inn, 'res' => $res, 'adr_work' => $model->adr_work,
                    'comment' => $model->comment, 'date_z' => $model->date_z,
                    'geo' => $geo, 'kol' => $kol]);
            } else {
                return $this->render('inputregistr', [
                    'model' => $model,
                ]);
            }
        }
        if($refresh==1) {  // Сохранение пересчитанной заявки
            $model = schet::find()->where('schet=:schet',[':schet'=>$schet])->one();
            $model->summa = $g;
            $model->usluga = $u;
            $model->summa_beznds = $all;
            $model->summa_work = $rabota;
            $model->summa_transport = $transp;
            $model->summa_delivery = $delivery;
            $model->res = $res;
            $model->geo = $geo;
            $model->kol = $kol;
            $model->status = -1;
            $model->adres = $adr;
            if(!$model->save(false)) {
                debug($model);
                return;
            }
            return $this->redirect(['sch','is' => 3,'nazv' => $model->schet]);
        }
    }

    // регистрация пользователя (сейчас не используется)
    public function actionRegistr()
    {
        $model = new Klient();
        if ($model->load(Yii::$app->request->post()))
        {
            $inn = $model->inn;
            $model->save();
            $model = new InputDataForm();
            $model->potrebitel = $inn;
            $this->refresh();
        }
        else {

            return $this->render('inputregistr', [
                'model' => $model,
            ]);
        }
    }

    // регистрация пользователя (сейчас не используется)
    public function actionOk_reg($nazv)
    {
            return $this->render('registr', ['nazv' => $nazv]);
    }

    // Отображение сформированого счета
    public function actionSch($is,$nazv)
    {

        return $this->render('sch', [
            'is' => $is,'nazv' => $nazv
        ]);
    }

    // Расчет показателей (происходит при нажатии на кн. OK)
    public function actionCalc($id,$kol,$poezdka,$distance,$res,$potrebitel,$time_work,
                               $time_prostoy,$nazv,$adr_work,$geo,$refresh,$schet)
    {
        $sql = Calc::Calc($id,$res,$distance);
        $pos = strripos($sql, 'a.id=');
        $work_value = substr($sql,$pos+5);
        $sql = substr($sql,0,$pos-1).' a.id=:id';
        $model1 = Calc::findBySql($sql,[':id' => $work_value])->all();
        $vid_w = $model1[0]->work;
        $sql = 'select sum(stavka_grn) as stavka_grn from costwork where work=:search';
        $model2 = Calc::findBySql($sql,[':search'=>"$vid_w"])->all();
        $name_res = spr_res::find()->where('id=:id',[':id'=>$res])->all();

        return $this->render('resultCalc', ['model1' => $model1,'model2' => $model2,
            'name_res' => $name_res,'kol' => $kol,'distance' => $distance*$poezdka,
            'potrebitel' => $potrebitel,'nazv' => $nazv, 'time_work' => $time_work,
            'time_prostoy' => $time_prostoy,'adr_work' => $adr_work,
            'geo' => $geo,'refresh' => $refresh,'schet' => $schet]);
    }

//  Эксперементальный метод для отображения новых заявок через заданное время
//  срабатывание проичходит по крону - службе в Unix
    public function actionCron_schet() {
        $f = fopen('cron_schet.dat','r');
        $s = fgets($f);
       // echo $s;
        $model = new schet();
        $sql = 'select max(cast(id as unsigned)) as id from schet';
        $sch = schet::findBySql($sql)->one();
        $id = $sch->id;
        if($id>$s)
        {
            echo " З’явилась нова заявка №$id";
            fclose($f);
            $f = fopen('cron_schet.dat','w');
            fputs($f,$id);
            passthru("mpg123 zvukovye-effekty-korotkie-fanfary.mp3");
            $model = new info();
            $model->title = "З’явилась нова заявка №$id";
            $model->info1 = "";
            $model->style1 = "d15";
            $model->style_title = "d9";
            return $this->render('info', [
                'model' => $model]);
        }
        else
        fclose($f);
    }
    
    // Формирование счета
    public function actionCnt($rabota,$delivery,$transp,$all,$g,$u,$inn,$res,$adr_work,$comment,$date_z,$geo,$kol)
    {
        $model = new schet();
        $sql = 'select inn from schet where inn=' . "'" . $inn . "'" . ' and usluga=' . "'" . $u . "'" .
            ' and summa=' . $g . ' and date=' . "'" . date('Y-m-d'). "'" ;

        $adr_work = str_replace('Адреса виконання робіт:','',$adr_work);
        $priz = schet::findBySql($sql)->one();
        $model->schet = '';
        if (isset($priz->inn)) $is = 1; else $is = 0;
        // Сохраняем если нет такого счета в базе
        if ($is == 0){
            $sql = 'select max(cast(schet as unsigned)) as schet from schet';
            $sch = schet::findBySql($sql)->one();
            $s = $sch->schet+1;
            $y = strlen($s);
            $s = str_pad('0', 8 - $y,'0') . $s;
            $data_res = spr_res::find()->select(['relat'])
                ->where('nazv=:nazv',[':nazv' => $res])->all();
            $cut_nazv = $data_res[0]->relat;  // Сокращ название РЭСа
            $data_usluga = spr_work::find()->select(['kod_uslug'])
                ->where('work=:work',['work' => $u])->all();
            $kod_usluga = $data_usluga[0]->kod_uslug;  // Код услуги
            // Создаём № договора
            $contract = mb_substr($cut_nazv,0,2,'UTF-8').$kod_usluga.'_'.$s;
            $model->usluga = $u;
            $model->summa = $g;
            $model->summa_work = $rabota;
            $model->summa_delivery = $delivery;
            $model->summa_transport = $transp;
            $model->summa_beznds = $all;
            $model->surely = 0;
            $model->status = 1;
            $model->schet = $s;
            $model->contract = $contract;
            $model->inn = $inn;
            $model->res = $res;
            $model->adres = $adr_work;

            if(!empty($date_z))
                $model->date_z = date("Y-m-d", strtotime($date_z));

            $model->comment = $comment;
            $model->geo = $geo;
            $model->kol = $kol;
            $model->save();
//            $model->validate();
//            print_r($model->getErrors());
//            return;
        }
            return $this->redirect(['sch','is' => $is,'nazv' => $model->schet]);
    }

// Связь с оператором
    public function actionRelat($sch)
    {
        $sql = 'select * from schet where schet=' . "'" . $sch . "'" ;
        $is = 2;
        $priz = schet::findBySql($sql)->one();
        //    ->where(['schet' => $sch])->one();
        $priz->surely = 1;
        $priz->save();
        return $this->redirect(['sch','is' => $is,'nazv' => $priz->schet]);
    }

// Оплата
    public function actionOpl()
    {
        $sch = Yii::$app->request->post('sch');
        $sql = 'select * from vschet where schet=:search';
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->one();
        return $this->render('sch_opl',['model' => $model,'style_title' => 'd9']);
    }
    
    // Формирование акта выполненных работ
    public function actionAct_work()
    {
        $sch = Yii::$app->request->post('sch');
        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
                . ' where a.res=b.nazv and schet=:search';
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->one();
        return $this->render('act_work',['model' => $model,'style_title' => 'd9']);
    }

    // Формирование договора
    public function actionContract()
    {
        $sch = Yii::$app->request->post('sch');
        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail,'
                . 'c.exec_person,c.exec_person_pp,c.exec_post,c.exec_post_pp'
                . ' from vschet a,spr_res b,spr_uslug c,costwork d'
                . ' where a.res=b.nazv and a.usluga=d.work '
                . ' and c.usluga=d.usluga'
                . ' and schet=:search ';
        
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->one();
        return $this->render('contract',['model' => $model,'style_title' => 'd9']);
    }

    // Формирование инф. сообщения
    public function actionMessage()
    {
        $sch = Yii::$app->request->post('sch');
        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
            . ' where a.res=b.nazv and schet=:search';
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->one();
        return $this->render('message',['model' => $model,'style_title' => 'd9']);
    }

    // Страница контактов
       public function actionContact()
    {
        $model = new spr_res();
        $model = $model::find()->all();
        $dataProvider = new ActiveDataProvider([
         'query' => spr_res::find(),
        ]); 
            return $this->render('contact', [
                'model' => $model,'dataProvider' => $dataProvider
            ]);
    }

    // Страница контактов Call - центра
    public function actionCallcenter()
    {     $model = new info();
          $model->title = 'Кол-центр, контакти:';
          $model->info1 = "0 800 300 015 безкоштовно цілодобово";
          $model->info2 = "E-mail: call_center@cek.dp.ua";
          $model->style2 = "d14";
          $model->style1 = "d14";
          $model->style_title = "d9";
          return $this->render('info', [
            'model' => $model]);
    }

// Происходит при нажатии на кнопку "Відмовитись формувати заявку"
    public function actionCancel($nazv,$summa,$res,$adr_work)
    {
        $model = new Input_refusal();
        if ($model->load(Yii::$app->request->post()))
        {
            $cause = $model->cause;
            $sel = $model->sel;
            $model = new Refusal();
            if($sel==1)
                $model->cause = "Не влаштовує вартість послуги";
            else
                $model->cause = $cause;
            $model->summa = $summa;
            $model->work = $nazv;
            $model->adr_work = str_replace('Адрес споживача:','',$adr_work);
            $model->res_id = $res;
            $model->save();
            if(!$_SERVER['REMOTE_ADDR']=='127.0.0.1')
                return $this->redirect("http://192.168.55.1/index.php/cpojivaham/zahalna-informatsiia/nashi-posluhy.html");
            else
                return $this->redirect("http://cek.dp.ua/index.php/cpojivaham/zahalna-informatsiia/nashi-posluhy.html");
        }
        else {
            return $this->render('input_refusal', [
                'model' => $model,
            ]);
        }
    }

    // Просмотр счетов
    public function actionViewschet($item='')
    {
        $searchModel = new viewschet();
        $flag=1;
        $role=0;
        if(!isset(Yii::$app->user->identity->role))
        {      $flag=0;}
        else{
            $role=Yii::$app->user->identity->role;
        }

        switch($role) {
             case 3: // Полный доступ
                $data = $searchModel::find()->orderBy(['status' => SORT_ASC])->all();
                break;
             case 2:  // финансовый отдел
                $data = $searchModel::find()->where('status=:status',[':status' => 2])->
                orderBy(['status' => SORT_ASC])->all();
                break;
             case 1:  // бухгалтерия
                $data = $searchModel::find()->where('status=:status',[':status' => 5])->
                orderBy(['status' => SORT_ASC])->all();
                break;
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$role);

        if (Yii::$app->request->get('item') == 'Excel' )
        {
            $newQuery = clone $dataProvider->query;
            $models = $newQuery->orderby(['date' => SORT_DESC])->all();
            $kind=1;
            $k1 = 'Інформація по рахункам';
//             Сброс в Excel
            if($kind==1){
                \moonland\phpexcel\Excel::widget([
                    'models' => $models,
                   
                    'mode' => 'export', //default value as 'export'
                    'format' => 'Excel2007',
                    'hap' => $k1,    //cтрока шапки таблицы
                    'data_model' => 1,
                    'columns' => ['status_sch','inn','nazv','addr','tel','schet','contract',
                        'usluga','summa','summa_beznds','summa_work','summa_delivery','summa_transport','res','date'],
                    'headers' => ['status_sch' => 'Cтатус заявки','inn' => 'ІНН','nazv' => 'Споживач','addr'=> 'Адрес','tel' => 'Телефон',
                        'schet' => 'Рахунок','contract' => '№ договору', 'usluga' => 'Послуга','summa' => 'Сума,грн.:','summa_beznds' => 'Сума без ПДВ,грн.:',
                        'summa_work' => 'Вартість робіт,грн.:','summa_delivery' => 'Доставка бригади,грн.:',
                        'summa_transport' => 'Транспорт всього,грн.:',
                        'res' => 'Виконавча служба:','date' => 'Дата'],
                ]);}
            return;
        }

        return $this->render('viewschet', [
            'model' => $searchModel,'dataProvider' => $dataProvider,'searchModel' => $searchModel,
        ]);
    }

    // Просмотр отказов
    public function actionViewcancel()
    {
        $searchModel = new Refusal();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('viewcancel', [
            'model' => $searchModel,'dataProvider' => $dataProvider,'searchModel' => $searchModel,
        ]);
    }

    // Просмотр типового договора
    public function actionTypical_contract()
    {
        return $this->render('typical_contract',['style_title' => 'd9']);
    }

    // Подгрузка видов работ - происходит при выборе услуги
    public function actionGetworks($id,$res) {
    Yii::$app->response->format = Response::FORMAT_JSON;
    if (Yii::$app->request->isAjax) {
        $usluga = Calc::find()->select(['usluga'])->where('id=:id',[':id' => $id])->all();
        $usl = $usluga[0]->usluga;
        
        if(empty($usl))
        $sql = "Select cast(min(id) as char(3)) as nomer,concat(cast(min(id) as char(3)),'  ',work) as work "
                . "from costwork group by work ";
        else
        {
            switch($usl) {
                case "Послуги з технічного обслуговування об'єктів":
                    $usl1 = "Послуги з технічного обслуговування об";
                    $sql = "Select min(id) as nomer,concat(cast(min(id) as char(3)),'  ',work) as work "
                        . "from costwork where usluga like " . "'%" . $usl1 . "%'" . " group by work";
                    break;
                case "Транспортні послуги":
                    $r = 'T_';
                    switch ($res) {
                        case 1:
                            $r = $r . 'Ap';
                            break;
                        case 2:
                            $r = $r . 'Vg';
                            break;
                        case 3:
                            $r = $r . 'Gv';
                            break;
                        case 4:
                            $r = $r . 'Dn';
                            break;
                        case 5:
                            $r = $r . 'Yv';
                            break;
                        case 6:
                            $r = $r . 'Pvg';
                            break;
                        case 7:
                            $r = $r . 'Ing';
                            break;
                        case 8:
                            $r = $r . 'Krr';
                            break;
                        case 9:
                            $r = 'a.Szoe';
                            break;
                        case 10:
                            $r = 'a.Sdizp';
                            break;
                        case 11:
                            $r = $r . 'Zp';
                            break;
                    }

                    $sql = "Select min(a.id) as nomer,concat(cast(min(a.id) as char(3)),
                         IF(b.rabota is null,' -','  '),a.work) as work "  // - нет данных в поле rabota< , + есть данные
                        . "from costwork a inner join transport b on a.$r=b.nomer
                        where a.usluga =" . "'" . $usl . "'"
                        . " and b.locale=$res group by a.work,b.rabota";
                        //. " and id_res=".$res." group by work";

                    break;
                default:
                        $sql = "Select min(id) as nomer,concat(cast(min(id) as char(3)),'  ',work) as work "
                        . "from costwork where usluga =" . "'" . $usl . "'" . " group by work";
            }
        
        }
        $works = Calc::findBySql($sql)->all();
        return ['success' => true, 'works' => $works,'usl' => $usl];
    }
    return ['oh no' => 'you are not allowed :('];
    }

    // Подгрузка видов работ - происходит при вводе ИНН
    public function actionGetklient($inn) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $iklient = klient::find()->select(['nazv','addr','okpo','regsvid','priz_nds',
                'email','tel','person','fio_dir','contact_person'])
                ->where('inn=:inn',[':inn' => $inn])->all();
            if(!isset($iklient[0]->nazv)) {
                $nazv = '';
                $addr = '';
                $email = '';
                $tel = '';
                $priz_nds = false;
                $person = '';
                $regsvid = '';
                $contact_person = '';
                $fio_dir = '';
            }
            else {
                $nazv = $iklient[0]->nazv;
                $addr = $iklient[0]->addr;
                $email = $iklient[0]->email;
                $tel = $iklient[0]->tel;
                $priz_nds = $iklient[0]->priz_nds;
                $person = $iklient[0]->person;
                $regsvid = $iklient[0]->regsvid;
                $fio_dir = $iklient[0]->fio_dir;
                $contact_person = $iklient[0]->contact_person;
            }
            return ['success' => true, 'nazv' => $nazv,'addr' => $addr,
                'email' => $email,'tel' => $tel,'priz_nds' => $priz_nds,
                'person' => $person,'regsvid' => $regsvid,
                'fio_dir' => $fio_dir,'contact_person' => $contact_person
            ];

        }
        return ['oh no' => 'you are not allowed :('];
    }

    // Определяем расстояние по дороге от базы до объекта - происходит при нажатии на карту
     public function actionGetdist($url,$origins,$destinations) {
          
    Yii::$app->response->format = Response::FORMAT_JSON;
    if (Yii::$app->request->isAjax) {
        $destinations = str_replace(' ', '', $destinations);
        $url = $url . '&origins='.$origins.'&destinations='.$destinations;
        $url = $url . '&language=ru&region=UA';
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$url ); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $output = curl_exec($ch); 
        curl_close($ch);
        $output = json_decode($output,true);
        return ['success' => true, 'output' => $output];
    }
    }

    // Определяем населенные пункты, найденные после ввода поискового адреса,
    // необходимо для поиска на карте по введенному адресу
    public function actionGetloc($loc,$key,$address) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $address = str_replace(' ', '+', $address);
            $loc = $loc . '&key='.$key.'&address='.$address;
            $loc = $loc . '&language=ru&region=UA';
            $ch = curl_init($loc);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            $s = curl_error ($ch);
            curl_close($ch);
            $output = json_decode($output,true);
            return ['success' => true, 'output' => $output];
        }
    }
    
    // Определяем гео-координаты выбранного РЭСа 
    public function actionGetres($id) {
    Yii::$app->response->format = Response::FORMAT_JSON;
    if (Yii::$app->request->isAjax) {
        /*
         * Поля, извлекаемые из таблицы справочника РЭСов:
         * geo_koord - гео-координаты РЭСа
         * geo_fromwhere_sd - гео-координаты места откуда едет машина (лаборатория)
         * geo_fromwhere_sz - гео-координаты места откуда едет машина (устан. приборов учета и технич. проверка)
         * town_fromwhere_sd - город откуда едет машина (лаборатория)
         * town_fromwhere_sz - город откуда едет машина (устан. приборов учета и технич. проверка)
         * */
        $model = spr_res::find()->select(['geo_koord','geo_fromwhere_sd',
            'geo_fromwhere_sz','town_fromwhere_sd','town_fromwhere_sz'])
            ->where('id=:id',[':id' => $id])->all();
        $geo_koord = $model[0]->geo_koord;
        $n = strpos($geo_koord, ',');
        $lat = substr($geo_koord,0,$n);
        $lng = substr($geo_koord,$n+1);
        $geo_sd = $model[0]->geo_fromwhere_sd;
        $n = strpos($geo_sd, ',');
        $lat_sd = substr($geo_sd,0,$n);
        $lng_sd = substr($geo_sd,$n+1);
        $geo_sz = $model[0]->geo_fromwhere_sz;
        $n = strpos($geo_sz, ',');
        $lat_sz = substr($geo_sz,0,$n);
        $lng_sz = substr($geo_sz,$n+1);
        $town_sd = $model[0]->town_fromwhere_sd;
        $town_sz = $model[0]->town_fromwhere_sz;

        return ['success' => true, 'geo_koord' => $geo_koord,'lat' => $lat,'lng' => $lng,
            'lat_sd' => $lat_sd,'lng_sd' => $lng_sd,
            'lat_sz' => $lat_sz,'lng_sz' => $lng_sz,
            'town_sd' => $town_sd,'town_sz' => $town_sz,
            'id' => $id];
    }
    return ['oh no' => 'you are not allowed :('];
    }
    
    // Запись данных в файл
    public function actionTofile()
    {
    $model = new tofile();

    if (Yii::$app->request->isPost) {
      $file = \yii\web\UploadedFile::getInstance($model, 'file');
    var_dump($file);
    return;
    $file->saveAs(\Yii::$app->basePath . $file);
    }
    return $this->render('tofile', ['model' => $model]);
    }  
    
    public function actionDownload($f)
    {
        $file = Yii::getAlias($f);
        return Yii::$app->response->sendFile($file);
    }

//    Страница о программе
    public function actionAbout()
    {
        $model = new info();
        $model->title = 'Про програму';
        $model->info1 = "Ця програма здійснює розрахунок робіт відповідно вибраному виду роботи, а також транспортні витрати.";
        $model->style1 = "d15";
        $model->style2 = "info-text";
        $model->style_title = "d9";

        return $this->render('about', [
            'model' => $model]);
    }

    //    Сброс в Excel результатов рассчета
    public function actionExcel($kind,$nazv,$rabota,$delivery,$transp,$all,$nds,$all_nds)
    {
        $k1='Результат розрахунку для послуги: '.$nazv;
        $param = 0;
        $model = new forExcel();
        $model->nazv = $nazv;
        $model->rabota = $rabota;
        $model->delivery = $delivery;
        $model->transp = $transp;
        $model->all = $all;
        $model->nds = $nds;
        $model->all_nds = $all_nds;
        if ($kind == 1) {
            \moonland\phpexcel\Excel::widget([
                'models' => $model,
                'mode' => 'export', //default value as 'export'
                'format' => 'Excel2007',
                'hap' => $k1, //cтрока шапки таблицы'hap' => $k1,
                'data_model' => $param,
                'columns' => ['nazv', 'rabota', 'delivery', 'transp', 'all', 'nds', 'all_nds',
                ],
            ]);
        }
        if ($kind == 2) {
            \moonland\phpexcel\Excel::widget([
                'models' => $model,
                'mode' => 'export', //default value as 'export'
                'format' => 'Excel2007',
                'hap' => $k1, //cтрока шапки таблицы'hap' => $k1,
                'data_model' => $param,
                'columns' => ['nazv', 'all', 'nds', 'all_nds',
                ],
            ]);
        }
        return;
    }

//    Обновление статуса заявки
    public function actionUpd($id,$mod)
    {
        // $id  id записи
        // $mod - название модели
        if($mod=='schet')
            $model = viewschet::find()->where('id=:id',[':id'=>$id])->one();
            $nazv = $model->schet;
            $inn = $model->inn;
            if(!empty($model->date))
                $model->date = date("d.m.Y", strtotime($model->date));
            
            if(!empty($model->date_z))
                $model->date_z = date("d.m.Y", strtotime($model->date_z));
            
        if ($model->load(Yii::$app->request->post()))
        {
            $model1 = schet::find()->where('id=:id',[':id'=>$id])->one();
            $model1->status = $model->status;
            $model1->adres = $model->adres;
            $model1->why_refusal = $model->why_refusal;
            if(!empty($model->date_z))
                $model1->date_z = date("Y-m-d", strtotime($model->date_z));
            if(!empty($model->date_opl))
             $model1->date_opl = date("Y-m-d", strtotime($model->date_opl));
            if(!empty($model->date_exec))
                $model1->date_exec = date("Y-m-d", strtotime($model->date_exec));
            $model1->comment = $model->comment;

            if($model->status==5)
            {
               // Создаем № акта выполненных работ, если меняется статус заявки на выполненную
                if(empty($model->act_work)) {
                    $sql = 'select max(cast(act_work as unsigned)) as act_work from schet';
                    $sch = schet::findBySql($sql)->one();
                    $s = $sch->act_work+1;
                    $model1->act_work = $s;
                    $model1->date_akt = date('Y-m-d');
                }
            }
            if(!$model1->save(false))
            {  var_dump($model1);return;}

            if($mod=='schet')
                return $this->redirect(['site/viewschet']);

        } else {
            if($mod=='schet')
                return $this->render('update_schet', [
                    'model' => $model,'nazv' => $nazv
                ]);
        }
    }

    //    Распечатка акта выполненных работ
    public function actionAct_print(){
        date_default_timezone_set('Europe/Kiev');
        $sch = Yii::$app->request->post('sch');
        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
            . ' where a.res=b.nazv and schet=:search';
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->one();

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $this->renderPartial('act_work_print',['model' => $model,'style_title' => 'd9']),
            'options' => [
                'title' => 'Друк акту виконаних робіт',
                'subject' => ''
            ],
            'methods' => [
                'SetHeader' => ['Створено для печаті: ' . date("d.m.Y H:i:s")],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);
        return $pdf->render();
    }
    
    //    Распечатка инф. сообщения
    public function actionMessage_print(){
        date_default_timezone_set('Europe/Kiev');
        $sch = Yii::$app->request->post('sch');
        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
            . ' where a.res=b.nazv and schet=:search';
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->one();

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $this->renderPartial('message_print',['model' => $model,'style_title' => 'd9']),
            'options' => [
                'title' => 'Друк повідомлення',
                'subject' => ''
            ],
            'methods' => [
                'SetHeader' => ['Створено для печаті: ' . date("d.m.Y H:i:s")],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);
        return $pdf->render();
    }
    
    //    Распечатка договора
    public function actionContract_print(){
        date_default_timezone_set('Europe/Kiev');
        $sch = Yii::$app->request->post('sch');
        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail,'
                . 'c.exec_person,c.exec_person_pp,c.exec_post,c.exec_post_pp'
                . ' from vschet a,spr_res b,spr_uslug c,costwork d'
                . ' where a.res=b.nazv and a.usluga=d.work '
                . ' and c.usluga=d.usluga'
                . ' and schet=:search ';
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->one();
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $this->renderPartial('contract_print',['model' => $model,'style_title' => 'd9']),
            'options' => [
                'title' => 'Друк договора',
                'subject' => ''
            ],
            'methods' => [
                'SetHeader' => ['Створено для печаті: ' . date("d.m.Y H:i:s")],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);
        return $pdf->render();
    }

    //    Распечатка счета
    public function actionSch_print(){
        date_default_timezone_set('Europe/Kiev');
        $sch = Yii::$app->request->post('sch');
        $sql = 'select * from vschet where schet=:search';
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->one();
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $this->renderPartial('sch_opl_print',['model' => $model,'style_title' => 'd9']),
            'options' => [
                'title' => 'Друк рахунку',
                'subject' => ''
            ],
            'methods' => [
                'SetHeader' => ['Створено для печаті: ' . date("d.m.Y H:i:s")],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);
        return $pdf->render();
    }

//  Отправка счета по Email
    public function actionSch_email(){
        $sch = Yii::$app->request->post('sch');
        $email = Yii::$app->request->post('email');
        $sql = 'select * from vschet where schet=:search';
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->one();
        $content=$this->renderPartial('sch_opl_print',['model' => $model,'style_title' => 'd9']);
        $cssFile = __DIR__ . '/../vendor/'.'kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css';
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $content,

            'options' => [
                'title' => 'Друк рахунку',
                'subject' => ''
            ],
            'methods' => [
                'SetHeader' => ['Generated By: Krajee Pdf Component||Generated On: ' . date("r")],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);

        $mpdf = $pdf->getApi();
        $stylesheet = file_get_contents($cssFile);
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($content,2);

        $mpdf->Output('./schet.pdf', 'F');
        Yii::$app->mailer->compose()
            ->setFrom('usluga@cek.dp.ua')
            ->setTo($email)
            ->setSubject('Рахунок за послуги від ПрАТ «ПЕЕМ «ЦЕК»')
            ->setHtmlBody('<b>Дякуємо за звернення до ПрАТ «ПЕЕМ «ЦЕК».</b><br>У вкладеному файлі знаходиться рахунок за послугу.')
            ->attach('./schet.pdf')
            ->send();

        Yii::$app->mailer->compose()
            ->setFrom('usluga@cek.dp.ua')
            ->setTo('usluga@cek.dp.ua')
            ->setSubject("Рахунок за послуги від ПрАТ «ПЕЕМ «ЦЕК» №$sch відправлено")
            ->setHtmlBody("Рахунок за послуги від ПрАТ «ПЕЕМ «ЦЕК» №$sch відправлено.")
            ->attach('./schet.pdf')
            ->send();

        // Запись признака в статус заявки, что заявка в обработке (status=2)
        $sql = 'select * from schet where schet=:search';
        $data = schet::findBySql($sql,[':search'=>"$sch"])->one();
        
        if($data->status<2)
            $data->status = 2;
        $model->date = date("d.m.Y", strtotime($model->date));
       
        if(!empty($date_z))
                $model->date_z = date("Y-m-d", strtotime($date_z));
        $data->save(false);
//        $data->validate();
//            print_r($data->getErrors());
//           return;
        $model = new info();
        $model->title = "Рахунок №$sch відправлено";
        $model->info1 = "";
        $model->style1 = "d15";
        $model->style_title = "d9";
        return $this->render('info', [
            'model' => $model]);
    }

    //  Отправка акта вып. работ по Email
    public function actionAct_email(){
        $sch = Yii::$app->request->post('sch');
        $email = Yii::$app->request->post('email');

        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
            . ' where a.res=b.nazv and schet=:search';
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->one();
        $content=$this->renderPartial('act_work_print',['model' => $model,'style_title' => 'd9']);
        $cssFile = __DIR__ . '/../vendor/'.'kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css';
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $content,

            'options' => [
                'title' => 'Друк акту',
                'subject' => ''
            ],
            'methods' => [
                'SetHeader' => ['Generated By: Krajee Pdf Component||Generated On: ' . date("r")],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);

        $mpdf = $pdf->getApi();
        $stylesheet = file_get_contents($cssFile);
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($content,2);

        $mpdf->Output('./act.pdf', 'F');
        Yii::$app->mailer->compose()
            ->setFrom('usluga@cek.dp.ua')
            ->setTo($email)
            ->setSubject('Акт виконаних робіт за послуги від ПрАТ «ПЕЕМ «ЦЕК»')
            ->setHtmlBody('<b>Бажаємо здоров’я.</b><br>У вкладеному файлі знаходиться акт виконаних робіт за послугу.')
            ->attach('./act.pdf')
            ->send();

        $model = new info();
        $model->title = "Акт виконаних робіт по рахунку №$sch відправлено";
        $model->info1 = "";
        $model->style1 = "d15";
        $model->style_title = "d9";
        return $this->render('info', [
            'model' => $model]);


    }

    //  Отправка инф. сообщения по Email
    public function actionMessage_email(){
        $sch = Yii::$app->request->post('sch');
        $email = Yii::$app->request->post('email');

        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
            . ' where a.res=b.nazv and schet=:search';
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->one();
        $content=$this->renderPartial('message_print',['model' => $model,'style_title' => 'd9']);
        $cssFile = __DIR__ . '/../vendor/'.'kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css';
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $content,

            'options' => [
                'title' => 'Друк повідомлення',
                'subject' => ''
            ],
            'methods' => [
                'SetHeader' => ['Generated By: Krajee Pdf Component||Generated On: ' . date("r")],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);

        $mpdf = $pdf->getApi();
        $stylesheet = file_get_contents($cssFile);
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($content,2);

        $mpdf->Output('./message.pdf', 'F');
        Yii::$app->mailer->compose()
            ->setFrom('usluga@cek.dp.ua')
            ->setTo($email)
            ->setSubject('Інформаційне повідомлення')
            ->setHtmlBody('<b>Бажаємо здоров’я.</b><br>У вкладеному файлі знаходиться інформаційне повідомлення.')
            ->attach('./message.pdf')
            ->send();

       
        $model = new info();
        $model->title = "Інформаційне повідомлення по рахунку №$sch відправлено";
        $model->info1 = "";
        $model->style1 = "d15";
        $model->style_title = "d9";
        return $this->render('info', [
            'model' => $model]);
    }

     //  Отправка договора по Email
    public function actionContract_email(){
        $sch = Yii::$app->request->post('sch');
        $email = Yii::$app->request->post('email');
       $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail,'
                . 'c.exec_person,c.exec_person_pp,c.exec_post,c.exec_post_pp'
                . ' from vschet a,spr_res b,spr_uslug c,costwork d'
                . ' where a.res=b.nazv and a.usluga=d.work '
                . ' and c.usluga=d.usluga'
                . ' and schet=:search ';
       
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->one();
        $content=$this->renderPartial('contract_print',['model' => $model,'style_title' => 'd9']);
        $cssFile = __DIR__ . '/../vendor/'.'kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css';
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $content,

            'options' => [
                'title' => 'Друк договора',
                'subject' => ''
            ],
            'methods' => [
                'SetHeader' => ['Generated By: Krajee Pdf Component||Generated On: ' . date("r")],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);

        $mpdf = $pdf->getApi();
        $stylesheet = file_get_contents($cssFile);
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($content,2);

        $mpdf->Output('./contract.pdf', 'F');
        Yii::$app->mailer->compose()
            ->setFrom('usluga@cek.dp.ua')
            ->setTo($email)
            ->setSubject('Договір від ПрАТ «ПЕЕМ «ЦЕК»')
            ->setHtmlBody('<b>Бажаємо здоров’я.</b><br>У вкладеному файлі знаходиться договір за послугу.')
            ->attach('./contract.pdf')
            ->send();

        $model = new info();
        $model->title = "Договір по рахунку №$sch відправлено";
        $model->info1 = "";
        $model->style1 = "d15";
        $model->style_title = "d9";
        return $this->render('info', [
            'model' => $model]);

    }

    //  Отправка всех документов по Email
    public function actionDoc_email(){
        $sch = Yii::$app->request->post('sch');
        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail,'
                . 'c.exec_person,c.exec_person_pp,c.exec_post,c.exec_post_pp'
                . ' from vschet a,spr_res b,spr_uslug c,costwork d'
                . ' where a.res=b.nazv and a.usluga=d.work '
                . ' and c.usluga=d.usluga'
                . ' and schet=:search ';
        
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->one();
        $mail = $model->mail;
        if(empty($model->date_exec)) {
            $model = new info();
            $model->title = "Увага! По заявці №$sch не введено дату виконання роботи."
                    . " Формування документів не можливе.";
            $model->info1 = "";
            $model->style1 = "d15";
            $model->style_title = "d9_danger";
            return $this->render('info', [
            'model' => $model]);
        }            
        
        $content=$this->renderPartial('sch_opl_print',['model' => $model,'style_title' => 'd9']);
        $cssFile = __DIR__ . '/../vendor/'.'kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css';
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $content,

            'options' => [
                'title' => 'Друк рахунку',
                'subject' => ''
            ],
            'methods' => [
                'SetHeader' => ['Generated By: Krajee Pdf Component||Generated On: ' . date("r")],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);

        $mpdf = $pdf->getApi();
        $stylesheet = file_get_contents($cssFile);
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($content,2);
        // Запись признака в статус заявки, что заявка в обработке (status=2)
        $sql = 'select * from schet where schet=:search';
        $data = schet::findBySql($sql,[':search'=>"$sch"])->one();

        if($data->status<2)
            $data->status = 2;
        
        $model->date = date("d.m.Y", strtotime($model->date));
        
        if(!empty($date_z))
            $model->date_z = date("Y-m-d", strtotime($date_z));
        $data->save(false);
        $mpdf->Output('./schet.pdf', 'F');

        $content=$this->renderPartial('contract_print',['model' => $model,'style_title' => 'd9']);
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $content,

            'options' => [
                'title' => 'Друк договора',
                'subject' => ''
            ],
            'methods' => [
                'SetHeader' => ['Generated By: Krajee Pdf Component||Generated On: ' . date("r")],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);

        $mpdf = $pdf->getApi();
        $stylesheet = file_get_contents($cssFile);
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($content,2);
        $mpdf->Output('./contract.pdf', 'F');

        $content=$this->renderPartial('act_work_print',['model' => $model,'style_title' => 'd9']);
        $cssFile = __DIR__ . '/../vendor/'.'kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css';
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $content,

            'options' => [
                'title' => 'Друк рахунку',
                'subject' => ''
            ],
            'methods' => [
                'SetHeader' => ['Generated By: Krajee Pdf Component||Generated On: ' . date("r")],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);

        $mpdf = $pdf->getApi();
        $stylesheet = file_get_contents($cssFile);
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($content,2);

        $mpdf->Output('./act.pdf', 'F');

        $content=$this->renderPartial('message_print',['model' => $model,'style_title' => 'd9']);
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $content,

            'options' => [
                'title' => 'Друк повідомлення',
                'subject' => ''
            ],
            'methods' => [
                'SetHeader' => ['Generated By: Krajee Pdf Component||Generated On: ' . date("r")],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);

        $mpdf = $pdf->getApi();
        $stylesheet = file_get_contents($cssFile);
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($content,2);
        $mpdf->Output('./message.pdf', 'F');
        
        Yii::$app->mailer->compose()
            ->setFrom('usluga@cek.dp.ua')
            ->setTo($mail)
            ->setSubject('Документи за послуги від ПрАТ «ПЕЕМ «ЦЕК»')
            ->setHtmlBody('<b>Дякуємо за звернення до ПрАТ «ПЕЕМ «ЦЕК».</b><br>
                        У вкладеному файлі знаходяться: рахунок за послугу,акт виконаних робіт та договір')
            ->attach('./schet.pdf')
            ->attach('./act.pdf')
            ->attach('./contract.pdf')
            ->attach('./message.pdf')
            ->send();

        $model = new info();
        $model->title = "Документи по рахунку №$sch відправлено";
        $model->info1 = "";
        $model->style1 = "d15";
        $model->style_title = "d9";
        return $this->render('info', [
            'model' => $model]);
    }

// Добавление новых пользователей
    public function actionAddAdmin() {
        $model = User::find()->where(['username' => 'buh1'])->one();
        if (empty($model)) {
            $user = new User();
            $user->username = 'buh1';
            $user->email = 'buh1@ukr.net';
            $user->setPassword('afynfpbz');
            $user->generateAuthKey();
            if ($user->save()) {
                echo 'good';
            }
        }
    }

// Выход пользователя
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}

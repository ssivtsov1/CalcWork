<?php

namespace app\controllers;

use app\models\Spr_towns;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\ContactForm;
use app\models\InputDataForm;
use app\models\InputPeriod;
use app\models\InputData;
use app\models\Calc;
use app\models\spr_res;
use app\models\spr_tmc;
use app\models\spr_uslug;
use app\models\analytics;
use app\models\spr_res_koord;
use app\models\vspr_res_koord;
use app\models\spr_work;
use app\models\spr_costwork;
use app\models\spr_costwork1;
use app\models\spr_transp;
use app\models\klient;
use app\models\schet;
use app\models\schet_opl;
use app\models\schet_opl1;
use app\models\viewschet;
use app\models\viewanalit;
use app\models\sprav_mvp;
use app\models\requestsearch;
use app\models\tofile;
use app\models\import_otp;
use app\models\forExcel;
use app\models\info;
use app\models\Inputcalc;
use app\models\settings;
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
    public $adm=0;  // Признак отображения логотипа (если 1 - то не отображается) - используется в шаблоне main

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

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = true;

        return parent :: beforeAction($action);
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
                'geo' => $model->geo,'refresh' => 0,'schet' => '',
                'nazv1' => $model->nazv1,'tmc' => $model->tmc,'mvp' => $model->mvp,
                'calc_ind' => $model->calc_ind]);
            }
         else {
            $flag=1;
            $role=0;
            if(!isset(Yii::$app->user->identity->role))
            {       $flag=0;}
            else{
                    $role=Yii::$app->user->identity->role;
            }
            return $this->render('inputdata', [
                'model' => $model,'role' => $role
            ]);
        }
    }

    //  Происходит при формировании отчета по оплаченным заявкам
    public function actionOpl_z()
    {
        $model = new InputPeriod();
        if ($model->load(Yii::$app->request->post()))
        {
            return $this->redirect([ 'oplz',
                'date1' => $model->date1,
                'date2' => $model->date2]);
        }
        else {
            $flag=1;
            $role=0;
            if(!isset(Yii::$app->user->identity->role))
            {       $flag=0;}
            else{
                $role=Yii::$app->user->identity->role;
            }
            return $this->render('inputperiod', [
                'model' => $model,'role' => $role
            ]);
        }
    }

    // Подгрузка населенных пунктов - происходит при наборе первых букв
    public function actionGet_check_work($name)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $name1 = mb_strtolower($name,"UTF-8");

        if (Yii::$app->request->isAjax) {
            $sql = 'select max(id) as id,work from costwork where lower(work) like '.'"'.$name1.'%"'.
               ' group by 2 limit 1';
            $cur = spr_work::findBySql($sql)->all();
            if(!isset($cur[0]->work)) {
                $work='';
            }
            else {
                $work= $cur[0]->work;
            }

            return ['success' => true, 'work' => $work];

        }
    }

    // Подгрузка населенных пунктов - происходит при наборе первых букв
    public function actionGet_search_town($name)
    {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $name1 = mb_strtolower($name,"UTF-8");
        $name2 = mb_strtoupper($name,"UTF-8");
        if (Yii::$app->request->isAjax) {
            $sql = 'select min(id) as id,district,town from spr_towns where town like '.'"'.$name1.'%"'.
                ' and length('.'"'.$name1.'")>3'.' group by district,town order by town,district';
            $cur = spr_towns::findBySql($sql)->all();

            return ['success' => true, 'cur' => $cur];

        }
    }


    //  Происходит при вводе индивидуальных какькуляций
    public function actionInput_calc()
    {
        $model = new Inputcalc();
        if ($model->load(Yii::$app->request->post()))
        {
//            debug($model);
//            return;

            for($i=1;$i<6;$i++) {
                $costwork = new spr_costwork1();
                $sql = 'select * from costwork where id=:search';
                if($i==1) {
                    $model5 = spr_costwork::findBySql($sql, [':search' => $model->id_brig1])->all();
                    $stavka=str_replace(",", ".", $model->stavka_brig1);
                }
                if($i==2) {
                    if ($model->id_brig2==166)  continue;
                    $model5 = spr_costwork::findBySql($sql, [':search' => $model->id_brig2])->all();
                    $stavka=str_replace(",", ".", $model->stavka_brig2);
                }
                if($i==3) {
                    if ($model->id_brig3==166) continue;
                    $model5 = spr_costwork::findBySql($sql, [':search' => $model->id_brig3])->all();
                    $stavka=str_replace(",", ".", $model->stavka_brig3);
                }
                if($i==4) {
                    if($model->id_brig4==166) continue;
                    $model5 = spr_costwork::findBySql($sql, [':search' => $model->id_brig4])->all();
                    $stavka=str_replace(",", ".", $model->stavka_brig4);
                }
                if($i==5) {
                    if($model->id_brig5==166) continue;
                    $model5 = spr_costwork::findBySql($sql, [':search' => $model->id_brig5])->all();
                    $stavka=str_replace(",", ".", $model->stavka_brig5);
                }

                $model2 = spr_costwork::findBySql($sql, [':search' => $model->usluga])->all();
                $sql1 = 'select *  from spr_uslug where trim(usluga)=:search';
                $sql1 = 'select *  from spr_uslug where trim(usluga) like '.'"%'.trim($model2[0]->usluga).'%"';
//                $model4 = spr_uslug::findBySql($sql1, [':search' => trim($model2[0]->usluga)])->all();
                $model4 = spr_uslug::findBySql($sql1)->all();

//                debug($sql1);
//                return;

                $costwork->brig = $model5[0]->brig;
                $costwork->stavka_grn = str_replace(",", ".", $stavka);
                $costwork->usluga = $model2[0]->usluga;


//                if(count($model4)<>0)
                    $costwork->kod_uslug = $model4[0]->kod;
//                else
//                    $costwork->kod_uslug = $model->n_work;

                $costwork->work = $model->work;
//                $costwork->cast_1 = str_replace(",", ".", $model->cost_work);
//                $costwork->cast_2 = str_replace(",", ".", $model->cost_work);
                $costwork->cast_1 = 0;
                $costwork->cast_2 = 0;
                $costwork->cast_3 = str_replace(",", ".", $model->cost_work);
                $costwork->cast_4 = str_replace(",", ".", $model->cost_work);
                $costwork->cast_5 = str_replace(",", ".", $model->cost_work);
                $costwork->cast_6 = str_replace(",", ".", $model->cost_work);
                $costwork->zp = str_replace(",", ".", $model->salary_brig);
                $costwork->time_transp = str_replace(",", ".", $model->time_prostoy);
                $costwork->time_work = str_replace(",", ".", $model->time_work);
                $costwork->norm_time = str_replace(",", ".", $model->expense_brig);
                $costwork->common_minus = str_replace(",", ".", $model->common_expense);
                $costwork->tmc = str_replace(",", ".", $model->tmc);
                $costwork->other = str_replace(",", ".", $model->other);
                $costwork->verification = str_replace(",", ".", $model->poverka);
                $costwork->n_work = $model->n_work;
                $costwork->exec = 'РЕМ';
                $costwork->calc_ind = 1;
                $fl_a=0;
                if (!empty($model->id_auto1) && $model->id_auto1<>166) {

                    $model3 = spr_costwork::findBySql($sql, [':search' => $model->id_auto1])->all();
                    $costwork->T_Ap = $model3[0]->T_Ap;
                    if($model->usluga==220){
                        $costwork->T_Vg = $model3[0]->T_Ap;
                        $costwork->T_Gv = $model3[0]->T_Ap;
                        $costwork->T_Dn = $model3[0]->T_Ap;
                        $costwork->T_Ing = $model3[0]->T_Ap;
                        $costwork->T_Yv = $model3[0]->T_Ap;
                        $costwork->T_Krr = $model3[0]->T_Ap;
                        $costwork->T_Pvg = $model3[0]->T_Ap;
                        $fl_a=1;
//                        $costwork->T_Szoe = $model3[0]->T_Ap;
//                        $costwork->T_Sdizp = $model3[0]->T_Ap;
//                        $costwork->T_Sp = $model3[0]->T_Ap;
                    }

                }
                if (!empty($model->id_auto2) && $model->id_auto2<>166 && $fl_a==0) {

                    if($model->id_auto2<300)
                        $model3 = spr_costwork::findBySql($sql, [':search' => $model->id_auto2])->all();
                   else
                    {
                        $sql_t = 'select id,number as T_Vg from a_transport where id=:search';
                        $model3 = spr_costwork::findBySql($sql_t, [':search' => $model->id_auto2-300])->all();
                    }
                    $costwork->T_Vg = $model3[0]->T_Vg;
                    if($model->usluga==220){
                        $costwork->T_Ap = $model3[0]->T_Vg;
                        $costwork->T_Gv = $model3[0]->T_Vg;
                        $costwork->T_Dn = $model3[0]->T_Vg;
                        $costwork->T_Ing = $model3[0]->T_Vg;
                        $costwork->T_Yv = $model3[0]->T_Vg;
                        $costwork->T_Krr = $model3[0]->T_Vg;
                        $costwork->T_Pvg = $model3[0]->T_Vg;
                        $fl_a=1;
//                        $costwork->T_Szoe = $model3[0]->T_Vg;
//                        $costwork->T_Sdizp = $model3[0]->T_Vg;
//                        $costwork->T_Sp = $model3[0]->T_Vg;
                    }

                }
                if (!empty($model->id_auto3)  && $model->id_auto3<>166 && $fl_a==0) {
                    $model3 = spr_costwork::findBySql($sql, [':search' => $model->id_auto3])->all();
                    $costwork->T_Gv = $model3[0]->T_Gv;

                    if($model->usluga==220){
                        $costwork->T_Vg = $model3[0]->T_Gv;
                        $costwork->T_Ap = $model3[0]->T_Gv;
                        $costwork->T_Dn = $model3[0]->T_Gv;
                        $costwork->T_Ing = $model3[0]->T_Gv;
                        $costwork->T_Yv = $model3[0]->T_Gv;
                        $costwork->T_Krr = $model3[0]->T_Gv;
                        $costwork->T_Pvg = $model3[0]->T_Gv;
                        $fl_a=1;
//                        $costwork->T_Szoe = $model3[0]->T_Gv;
//                        $costwork->T_Sdizp = $model3[0]->T_Gv;
//                        $costwork->T_Sp = $model3[0]->T_Gv;
                    }

                }
                if (!empty($model->id_auto4) && $model->id_auto4<>166 && $fl_a==0) {
                    if($model->id_auto4<300)
                        $model3 = spr_costwork::findBySql($sql, [':search' => $model->id_auto4])->all();
                    else
                    {
                        $sql_t = 'select id,number as T_Dn from a_transport where id=:search';
                        $model3 = spr_costwork::findBySql($sql_t, [':search' => $model->id_auto4-300])->all();
                    }
                    $costwork->T_Dn = $model3[0]->T_Dn;
                    if($model->usluga==220){

                        $costwork->T_Vg = $model3[0]->T_Dn;
                        $costwork->T_Gv = $model3[0]->T_Dn;
                        $costwork->T_Ap = $model3[0]->T_Dn;
                        $costwork->T_Ing = $model3[0]->T_Dn;
                        $costwork->T_Yv = $model3[0]->T_Dn;
                        $costwork->T_Krr = $model3[0]->T_Dn;
                        $costwork->T_Pvg = $model3[0]->T_Dn;
                        $fl_a=1;
//                        $costwork->T_Szoe = $model3[0]->T_Dn;
//                        $costwork->T_Sdizp = $model3[0]->T_Dn;
//                        $costwork->T_Sp = $model3[0]->T_Dn;

//                        debug($costwork);
//                        return;

                    }
                }
                if (!empty($model->id_auto5) && $model->id_auto5<>166 && $fl_a==0) {
                    $model3 = spr_costwork::findBySql($sql, [':search' => $model->id_auto5])->all();
                    $costwork->T_Ing = $model3[0]->T_Ing;
                    if($model->usluga==220){
                        $costwork->T_Vg = $model3[0]->T_Ing;
                        $costwork->T_Gv = $model3[0]->T_Ing;
                        $costwork->T_Dn = $model3[0]->T_Ing;
                        $costwork->T_Ap = $model3[0]->T_Ing;
                        $costwork->T_Yv = $model3[0]->T_Ing;
                        $costwork->T_Krr = $model3[0]->T_Ing;
                        $costwork->T_Pvg = $model3[0]->T_Ing;
                        $fl_a=1;
//                        $costwork->T_Szoe = $model3[0]->T_Ing;
//                        $costwork->T_Sdizp = $model3[0]->T_Ing;
//                        $costwork->T_Sp = $model3[0]->T_Ing;
                    }
                }
                if (!empty($model->id_auto6) && $model->id_auto6<>166 && $fl_a==0) {
                    $model3 = spr_costwork::findBySql($sql, [':search' => $model->id_auto6])->all();
                    $costwork->T_Yv = $model3[0]->T_Yv;
                    if($model->usluga==220){
                        $costwork->T_Vg = $model3[0]->T_Yv;
                        $costwork->T_Gv = $model3[0]->T_Yv;
                        $costwork->T_Dn = $model3[0]->T_Yv;
                        $costwork->T_Ing = $model3[0]->T_Yv;
                        $costwork->T_Ap = $model3[0]->T_Yv;
                        $costwork->T_Krr = $model3[0]->T_Yv;
                        $costwork->T_Pvg = $model3[0]->T_Yv;
                        $fl_a=1;
//                        $costwork->T_Szoe = $model3[0]->T_Yv;
//                        $costwork->T_Sdizp = $model3[0]->T_Yv;
//                        $costwork->T_Sp = $model3[0]->T_Yv;
                    }
                }
                if (!empty($model->id_auto7) && $model->id_auto7<>166 && $fl_a==0) {
                    if($model->id_auto7<300)
                        $model3 = spr_costwork::findBySql($sql, [':search' => $model->id_auto7])->all();
                    else
                    {
                        $sql_t = 'select id,number as T_Krr from a_transport where id=:search';
                        $model3 = spr_costwork::findBySql($sql_t, [':search' => $model->id_auto7-300])->all();
                    }
//                    $model3 = spr_costwork::findBySql($sql, [':search' => $model->id_auto7])->all();
                    $costwork->T_Krr = $model3[0]->T_Krr;
                    if($model->usluga==220){
                        $costwork->T_Vg = $model3[0]->T_Krr;
                        $costwork->T_Gv = $model3[0]->T_Krr;
                        $costwork->T_Dn = $model3[0]->T_Krr;
                        $costwork->T_Ing = $model3[0]->T_Krr;
                        $costwork->T_Yv = $model3[0]->T_Krr;
                        $costwork->T_Ap = $model3[0]->T_Krr;
                        $costwork->T_Pvg = $model3[0]->T_Krr;
                        $fl_a=1;
//                        $costwork->T_Szoe = $model3[0]->T_Krr;
//                        $costwork->T_Sdizp = $model3[0]->T_Krr;
//                        $costwork->T_Sp = $model3[0]->T_Krr;
                    }
                }

//                if (!empty($model->id_auto8) && $model->id_auto8<>166 && $fl_a==0) {
//                    $model3 = spr_costwork::findBySql($sql, [':search' => $model->id_auto8])->all();
//                    $costwork->T_Pvg = $model3[0]->T_Pvg;
//                    if($model->usluga==220){
//                        $costwork->T_Vg = $model3[0]->T_Pvg;
//                        $costwork->T_Gv = $model3[0]->T_Pvg;
//                        $costwork->T_Dn = $model3[0]->T_Pvg;
//                        $costwork->T_Ing = $model3[0]->T_Pvg;
//                        $costwork->T_Yv = $model3[0]->T_Pvg;
//                        $costwork->T_Krr = $model3[0]->T_Pvg;
//                        $costwork->T_Ap = $model3[0]->T_Pvg;
//                        $fl_a=1;
////                        $costwork->T_Szoe = $model3[0]->T_Pvg;
////                        $costwork->T_Sdizp = $model3[0]->T_Pvg;
////                        $costwork->T_Sp = $model3[0]->T_Pvg;
//                    }
//                }

                ////////////////////////////////////////////////////////////////////////
                if (!empty($model->id_auto8) && $model->id_auto8<>166 && $fl_a==0) {
                    if($model->id_auto8<300)
                        $model3 = spr_costwork::findBySql($sql, [':search' => $model->id_auto8])->all();
                    else
                    {
                        $sql_t = 'select id,number as T_Pvg from a_transport where id=:search';
                        $model3 = spr_costwork::findBySql($sql_t, [':search' => $model->id_auto8-300])->all();
                    }
//                    $model3 = spr_costwork::findBySql($sql, [':search' => $model->id_auto7])->all();
                    $costwork->T_Pvg = $model3[0]->T_Pvg;
                    if($model->usluga==220){
                        $costwork->T_Vg = $model3[0]->T_Pvg;
                        $costwork->T_Gv = $model3[0]->T_Pvg;
                        $costwork->T_Dn = $model3[0]->T_Pvg;
                        $costwork->T_Ing = $model3[0]->T_Pvg;
                        $costwork->T_Yv = $model3[0]->T_Pvg;
                        $costwork->T_Ap = $model3[0]->T_Pvg;
                        $costwork->T_Pvg = $model3[0]->T_Pvg;
                        $fl_a=1;
//                        $costwork->T_Szoe = $model3[0]->T_Krr;
//                        $costwork->T_Sdizp = $model3[0]->T_Krr;
//                        $costwork->T_Sp = $model3[0]->T_Krr;
                    }
                }

                if (!empty($model->id_auto9) && $model->id_auto9<>166 && $fl_a==0) {
                    $model3 = spr_costwork::findBySql($sql, [':search' => $model->id_auto9])->all();
                    $costwork->Szoe = $model3[0]->Szoe;
                    if($model->usluga==220){
                        $costwork->T_Vg = $model3[0]->Szoe;
                        $costwork->T_Gv = $model3[0]->Szoe;
                        $costwork->T_Dn = $model3[0]->Szoe;
                        $costwork->T_Ing = $model3[0]->Szoe;
                        $costwork->T_Yv = $model3[0]->Szoe;
                        $costwork->T_Krr = $model3[0]->Szoe;
                        $costwork->T_Pvg = $model3[0]->Szoe;
                        $costwork->T_Ap = $model3[0]->Szoe;
                        $fl_a=1;
//                        $costwork->T_Sdizp = $model3[0]->Szoe;
//                        $costwork->T_Sp = $model3[0]->Szoe;
                    }
                }
                if (!empty($model->id_auto10) && $model->id_auto10<>166 && $fl_a==0) {
                    $model3 = spr_costwork::findBySql($sql, [':search' => $model->id_auto10])->all();
                    $costwork->Sdizp = $model3[0]->Sdizp;
                    if($model->usluga==220){
                        $costwork->T_Vg = $model3[0]->Sdizp;
                        $costwork->T_Gv = $model3[0]->Sdizp;
                        $costwork->T_Dn = $model3[0]->Sdizp;
                        $costwork->T_Ing = $model3[0]->Sdizp;
                        $costwork->T_Yv = $model3[0]->Sdizp;
                        $costwork->T_Krr = $model3[0]->Sdizp;
                        $costwork->T_Pvg = $model3[0]->Sdizp;
//                        $costwork->T_Szoe = $model3[0]->Sdizp;
                        $costwork->T_Ap = $model3[0]->Sdizp;
//                        $costwork->T_Sp = $model3[0]->Sdizp;
                        $fl_a=1;
                    }
                }
                if (!empty($model->id_auto11) && $model->id_auto11<>166 && $fl_a==0) {
                    $model3 = spr_costwork::findBySql($sql, [':search' => $model->id_auto11])->all();
                    $costwork->T_Sp = $model3[0]->T_Sp;
                    if($model->usluga==220){
                        $costwork->T_Vg = $model3[0]->T_Sp;
                        $costwork->T_Gv = $model3[0]->T_Sp;
                        $costwork->T_Dn = $model3[0]->T_Sp;
                        $costwork->T_Ing = $model3[0]->T_Sp;
                        $costwork->T_Yv = $model3[0]->T_Sp;
                        $costwork->T_Krr = $model3[0]->T_Sp;
                        $costwork->T_Pvg = $model3[0]->T_Sp;
//                        $costwork->T_Szoe = $model3[0]->T_Sp;
//                        $costwork->T_Sdizp = $model3[0]->T_Sp;
                        $costwork->T_Ap = $model3[0]->T_Sp;
                        $fl_a=1;
                    }
                }
                $work = $model->work;
                if(empty($model->poverka)) $costwork->verification=0;
                $costwork->save();
            $costwork->validate();
            print_r($costwork->getErrors());
//            return;


            }
            $model1= new info();
            $model1->title = 'УВАГА!';
            $model1->info1 = "Індивідуальну калькуляцію `$work`, збережено.";
            $model1->style1 = "d15";
            $model1->style2 = "info-text";
            $model1->style_title = "d9";

            return $this->render('info', [
                'model' => $model1]);
        }
        else {
            $flag=1;
            $role=0;
            if(!isset(Yii::$app->user->identity->role))
            {       $flag=0;}
            else{
                $role=Yii::$app->user->identity->role;
            }
            return $this->render('inputcalc', [
                'model' => $model,'role' => $role
            ]);
        }
    }

    // Выгрузка в САП
    public function actionUpload_to_sap()
    {
        $model = new InputPeriod();
        if ($model->load(Yii::$app->request->post()))
        {

            return $this->redirect([ 'upload_sap',
                'date1' => $model->date1,
                'date2' => $model->date2,
                'usl'   => $model->usl,
                'id_sw'   => $model->id_sw]);
        }
        else {
            $flag=1;
            $role=0;
            if(!isset(Yii::$app->user->identity->role))
            {       $flag=0;}
            else{
                $role=Yii::$app->user->identity->role;
            }
            return $this->render('inputupload', [
                'model' => $model,'role' => $role,'parameter' => 1
            ]);
        }
    }

    // Отчет для контроля выгрузки в САП
    public function actionRep_for_sap()
    {
        $model = new InputPeriod();
        if ($model->load(Yii::$app->request->post()))
        {

            return $this->redirect([ 'rep_sap',
                'date1' => $model->date1,
                'date2' => $model->date2,
                'usl'   => $model->usl,
                'id_sw'   => $model->id_sw]);
        }
        else {
            $flag=1;
            $role=0;
            if(!isset(Yii::$app->user->identity->role))
            {       $flag=0;}
            else{
                $role=Yii::$app->user->identity->role;
            }
            return $this->render('inputupload', [
                'model' => $model,'role' => $role,'parameter' => 2
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
        //$model->region = 3;

        if ($model->load(Yii::$app->request->post()))
        {


            return $this->redirect([ 'calc','id' => $model->work,'kol' => $model->kol,'poezdka' => $model->poezdka,
                'distance' => $model->distance,'res' => $model->res ,
                'potrebitel' => $model->potrebitel,
                'time_work' => $model->time_work,
                'time_prostoy' => $model->time_prostoy,
                'nazv' => $model->nazv,'adr_work' => $model->adr_potr,
                'geo' => $model->geo,'refresh' => $model->refresh,
                'schet' => $schet,'nazv1' => $model->nazv1,
                'tmc' => $model->tmc]);
        }
        else {
            return $this->render('inputdata', [
                'model' => $model,
            ]);
        }
    }

// Оформление заявки пользователя
    public function actionProposal($rabota,$delivery,$transp,$all,$g,$u,$res,$adr,$geo,$kol,
                                   $refresh,$schet,$tmc,$tmc_name,$time_t,$mvp,$time_prostoy,$time_work,$cost_auto_work){

//        debug($g);
//        return;

        if($delivery==-1) $delivery = 0;
        if($rabota==-1) $rabota = 0;
        if($transp==-1) $transp = 0;
        if($refresh==0) {
            $model = new Klient();

            $model->adr_work = str_replace('Адреса виконання робіт:', '', $adr);
            if ($model->load(Yii::$app->request->post())) {
//                debug($model);
//                return;
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
                    $model1->pib_dir = $model->pib_dir;
                    $model1->post_dir = $model->post_dir;
                    $model1->contact_person = $model->contact_person;
                    $model1->save();
                }

                return $this->redirect(['cnt', 'rabota' => $rabota, 'delivery' => $delivery,
                    'transp' => $transp, 'all' => $all, 'g' => $g, 'u' => $u,
                    'inn' => $inn, 'res' => $res, 'adr_work' => $model->adr_work,
                    'comment' => $model->comment, 'date_z' => $model->date_z,
                    'geo' => $geo, 'kol' => $kol,'tmc' => $tmc,
                    'tmc_name' => $tmc_name,'time_t' => $time_t,'mvp' => $mvp,
                    'time_prostoy' => $time_prostoy,'time_work' => $time_work,
                    'cost_auto_work' =>$cost_auto_work]);

            } else {
                $flag=1;
                $role=0;
                $email='';
                if(!isset(Yii::$app->user->identity->role))
                {       $flag=0;}
                else{
                        $role=Yii::$app->user->identity->role;
                        $email=Yii::$app->user->identity->email;
                }

                return $this->render('inputregistr', [
                    'model' => $model,'role' => $role,'email' => $email,
                ]);
            }
        }
        if($refresh==1) {  // Сохранение пересчитанной заявки
            $model = schet::find()->where('schet=:schet',[':schet'=>$schet])->one();
            $model->summa = $g+$tmc;
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
                               $time_prostoy,$nazv,$adr_work,$geo,$refresh,$schet,$nazv1,$tmc,$mvp,$calc_ind)
    {
        $sql="select usluga from costwork where id=".$id;

        $z1 = viewschet::findBySql($sql)->asArray()->all();

        if (is_null($poezdka)) $poezdka=0;
        if (is_null($distance)) $distance=0;
// && $calc_ind<>1
        if($z1[0]['usluga']!="Оперативно-технічне обслуговування" ||
            ($z1[0]['usluga']=="Оперативно-технічне обслуговування" && $calc_ind==2))
            $sql = Calc::Calc($id,$res,$distance);
        else
            $sql1 = Calc::Calc_oto($id,$res,$distance);

        if($z1[0]['usluga']!="Оперативно-технічне обслуговування" ||
            ($z1[0]['usluga']=="Оперативно-технічне обслуговування" && $calc_ind==2)) {
            $pos = strripos($sql, 'a.id=');
            $work_value = substr($sql, $pos + 5);
            $sql = substr($sql, 0, $pos - 1) . ' a.id=:id';

//            debug( $sql);
//            debug( $id);
//            return;

            $model1 = Calc::findBySql($sql, [':id' => $work_value])->all();
//            $model1 = Calc::findBySql($sql)->all();
//            debug($work_value);
//            return;


            $vid_w = $model1[0]->work;
            $usluga=$model1[0]->usluga;
        }
        else
        {
            $model1 = Calc::findBySql($sql1)->asArray()->all();
//            debug($model1);
//            return;
            $vid_w = $model1[0]['work'];
            $usluga=$model1[0]['usluga'];
        }

        $sql = 'select case when lic=1 then 0 else sum(stavka_grn) end as stavka_grn,lic
                from costwork where work=:search group by lic';
        $model2 = Calc::findBySql($sql,[':search'=>"$vid_w"])->all();

//            debug($model2);
//            return;

        if($model2[0]['lic']==1)  $distance=0;    // Если лицензированная деятельность - тогда расстояние не учитывать
//        debug($vid_w);
//        return;

        $name_res = spr_res::find()->where('id=:id',[':id'=>$res])->all();

        $sql = 'select price,name_tmc from tmc where id=:search';

        $tmc_price = Spr_tmc::findBySql($sql,[':search'=>$tmc])->one();

        $tmc_name=$tmc_price['name_tmc'];
        $tmc_price=$tmc_price['price'];
        $tmc_price=0;

        if($usluga!="Оперативно-технічне обслуговування" ||
            ($z1[0]['usluga']=="Оперативно-технічне обслуговування" && $calc_ind==2))
            return $this->render('resultCalc', ['model1' => $model1,'model2' => $model2,
                'name_res' => $name_res,'kol' => $kol,'distance' =>  round((float) $distance * (float) $poezdka,2),
                'potrebitel' => $potrebitel,'nazv' => $nazv, 'time_work' => $time_work,
                'time_prostoy' => $time_prostoy,'adr_work' => $adr_work,
                'geo' => $geo,'refresh' => $refresh,
                'schet' => $schet,'nazv1' => $nazv1,'tmc_price'=> $tmc_price,
                'tmc_name'=> $tmc_name,'mvp'=> $mvp]);
        else
            return $this->render('resultCalc_oto', ['model1' => $model1,'model2' => $model2,
                'name_res' => $name_res,'kol' => $kol,'distance' => round((float) $distance * (float) $poezdka,2),
                'potrebitel' => $potrebitel,'nazv' => $nazv, 'time_work' => $time_work,
                'time_prostoy' => $time_prostoy,'adr_work' => $adr_work,
                'geo' => $geo,'refresh' => $refresh,
                'schet' => $schet,'nazv1' => $nazv1,'tmc_price'=> $tmc_price,
                'tmc_name'=> $tmc_name,'mvp'=> $mvp]);
    }

    // Формирование отчета по оплаченным заявкам
    public function actionOplz($date1,$date2)
    {
        $role=0;
        if(!isset(Yii::$app->user->identity->role))
        {      $flag=0;}
        else{
            $role=Yii::$app->user->identity->role;
        }

        switch($role) {
            case 5: // Полный доступ (тайный советник)
                $sql = "SELECT a.res,a.usluga,b.usluga as direct,count(a.usluga) as kol,sum(a.summa) as summa FROM vschet a 
               join costwork b on trim(a.usluga)=trim(b.work)
                WHERE  a.date_opl>='$date1' and a.date_opl<='$date2'
                and b.usluga is not null and trim(b.usluga)<>''
                group by a.res,a.usluga,b.usluga
                 union all select 'Всього:','','',count(*) as kol,sum(summa) as summa FROM vschet 
                WHERE  date_opl>='$date1' and date_opl<='$date2'";
                break;
            case 3: // Полный доступ админ
//                $sql = "SELECT res,usluga,count(usluga) as kol,sum(summa) as summa FROM vschet
//                WHERE date_opl>='$date1' and date_opl<='$date2'
//                group by res,usluga
//                union all select 'Всього:','',count(*) as kol,sum(summa) as summa FROM vschet
//                WHERE  date_opl>='$date1' and date_opl<='$date2'";

                $sql = "SELECT a.res,a.usluga,b.usluga as direct,count(a.usluga) as kol,sum(a.summa) as summa FROM vschet a 
               join costwork b on trim(a.usluga)=trim(b.work)
                WHERE  a.date_opl>='$date1' and a.date_opl<='$date2'
                and b.usluga is not null and trim(b.usluga)<>''
                group by a.res,a.usluga,b.usluga
                 union all select 'Всього:','','',count(*) as kol,sum(summa) as summa FROM vschet 
                WHERE  date_opl>='$date1' and date_opl<='$date2'";

                break;
            case 2:  // финансовый отдел
                $sql = "SELECT a.res,a.usluga,b.usluga as direct,count(a.usluga) as kol,sum(a.summa) as summa FROM vschet a 
               join costwork b on trim(a.usluga)=trim(b.work)
                WHERE  a.date_opl>='$date1' and a.date_opl<='$date2'
                and b.usluga is not null and trim(b.usluga)<>''
                group by a.res,a.usluga,b.usluga
                 union all select 'Всього:','','',count(*) as kol,sum(summa) as summa FROM vschet 
                WHERE  date_opl>='$date1' and date_opl<='$date2'";
                break;
            case 1:  // бухгалтерия
                $sql = "SELECT a.res,a.usluga,b.usluga as direct,count(a.usluga) as kol,sum(a.summa) as summa FROM vschet a 
               join costwork b on trim(a.usluga)=trim(b.work)
                WHERE  a.date_opl>='$date1' and a.date_opl<='$date2'
                and b.usluga is not null and trim(b.usluga)<>''
                group by a.res,a.usluga,b.usluga
                 union all select 'Всього:','','',count(*) as kol,sum(summa) as summa FROM vschet 
                WHERE  date_opl>='$date1' and date_opl<='$date2'";
                break;
            case 11: // Днепр РЭС
                $sql = "SELECT res,usluga,count(usluga) as kol,sum(summa) as summa FROM vschet 
                WHERE res='Дніпропетровські РЕМ' and date_opl>='$date1' and date_opl<='$date2'
                group by res,usluga
                union all select 'Всього:','',count(*) as kol,sum(summa) as summa FROM vschet 
                WHERE res='Дніпропетровські РЕМ' and date_opl>='$date1' and date_opl<='$date2'";
                break;
            case 12: // Гвардейские РЭС

                $sql = "SELECT res,usluga,count(usluga) as kol,sum(summa) as summa FROM vschet 
                WHERE res='Гвардійська дільниця' and date_opl>='$date1' and date_opl<='$date2'
                group by res,usluga
                union all select 'Всього:','',count(*) as kol,sum(summa) as summa FROM vschet 
                WHERE res='Гвардійська дільниця' and date_opl>='$date1' and date_opl<='$date2'";
                break;
            case 13:

                $sql = "SELECT res,usluga,count(usluga) as kol,sum(summa) as summa FROM vschet 
                WHERE (res='Криворізькі РЕМ' or res = 'Інгулецька дільниця' or res = 'Апостолівська дільниця')
                and date_opl>='$date1' and date_opl<='$date2'
                group by res,usluga
                union all select 'Всього:','',count(*) as kol,sum(summa) as summa FROM vschet 
                WHERE (res='Криворізькі РЕМ' or res = 'Інгулецька дільниця' or res = 'Апостолівська дільниця')
                and date_opl>='$date1' and date_opl<='$date2'";
                break;
            case 14: // Павлоградські РЕМ

                $sql = "SELECT res,usluga,count(usluga) as kol,sum(summa) as summa FROM vschet 
                WHERE res='Павлоградські РЕМ' and date_opl>='$date1' and date_opl<='$date2'
                group by res,usluga
                union all select 'Всього:','',count(*) as kol,sum(summa) as summa FROM vschet 
                WHERE res='Павлоградські РЕМ' and date_opl>='$date1' and date_opl<='$date2'";
                break;
            case 15: // Вілногірські РЕМ
                $sql = "SELECT res,usluga,count(usluga) as kol,sum(summa) as summa FROM vschet 
                WHERE res='Вільногірські РЕМ' and date_opl>='$date1' and date_opl<='$date2'
                group by res,usluga
                 union all select 'Всього:','',count(*) as kol,sum(summa) as summa FROM vschet 
                WHERE res='Вільногірські РЕМ' and date_opl>='$date1' and date_opl<='$date2'";
                break;

            case 16: // Жовтоводські РЕМ
                $sql = "SELECT a.res,a.usluga,b.usluga as direct,count(a.usluga) as kol,sum(a.summa) as summa FROM vschet a 
               join costwork b on trim(a.usluga)=trim(b.work)
                WHERE a.res='Жовтоводські РЕМ' and a.date_opl>='$date1' and a.date_opl<='$date2'
                and b.usluga is not null and trim(b.usluga)<>''
                group by a.res,a.usluga,b.usluga
                 union all select 'Всього:','','',count(*) as kol,sum(summa) as summa FROM vschet 
                WHERE res='Жовтоводські РЕМ' and date_opl>='$date1' and date_opl<='$date2'";
                break;
        }

//        SELECT a.res,a.usluga,b.usluga,count(a.usluga) as kol,sum(a.summa) as summa FROM vschet a
//				  join costwork b on trim(a.usluga)=trim(b.work)
//                WHERE a.res='Жовтоводські РЕМ' and a.date_opl>='2020-04-01' and a.date_opl<='2020-05-01'
//                and b.usluga is not null and trim(b.usluga)<>''
//                group by a.res,a.usluga,b.usluga



//debug($department);
//return;

        $searchModel = new schet_opl();
        $opl = Schet_opl::findBySql($sql)->all();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$sql);
        $dataProvider->pagination = false;
        $date1 = date("d.m.Y", strtotime($date1));
        $date2 = date("d.m.Y", strtotime($date2));

        return $this->render('resultOplz', ['model' => $opl,
            'dataProvider' => $dataProvider, 'date1' => $date1, 'date2' => $date2]);
    }


    // Детализация по оплаченным заявкам
    public function actionDet_opl($usl,$res,$date1,$date2)
    {
        $role=0;
        if(!isset(Yii::$app->user->identity->role))
        {      $flag=0;}
        else{
            $role=Yii::$app->user->identity->role;
        }
        $date1 = date("Y-m-d", strtotime($date1));
        $date2 = date("Y-m-d", strtotime($date2));

        $sql = "SELECT res,usluga,nazv,status_sch,date_opl,summa_beznds,summa,tel FROM vschet 
        WHERE date_opl>='$date1' and date_opl<='$date2' and usluga='$usl' and res='$res'";

        $searchModel = new schet_opl1();
        $opl = Schet_opl1::findBySql($sql)->all();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$sql);
        $dataProvider->pagination = false;
        $date1 = date("d.m.Y", strtotime($date1));
        $date2 = date("d.m.Y", strtotime($date2));

        return $this->render('result_detoplz', ['model' => $opl,
            'dataProvider' => $dataProvider, 'date1' => $date1, 'date2' => $date2,
            'usl' => $usl,'res' => $res]);
    }




//  Эксперементальный метод для отображения новых заявок через заданное время
//  срабатывание проиcходит по крону - службе в Unix [не используется]
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
    public function actionCnt($rabota,$delivery,$transp,$all,$g,$u,$inn,$res,$adr_work,$comment,
                              $date_z,$geo,$kol,$tmc,$tmc_name,$time_t,$mvp,$time_prostoy,$time_work,$cost_auto_work)

    {
        $model = new schet();
        $adr_work = str_replace('Адреса виконання робіт:','',$adr_work);

        if(strchr($u,'"'))
            $sql = 'select inn from schet where inn=' . "'" . $inn . "'" . ' and usluga=' . "'" . $u . "'" .
                ' and summa=' . $g . ' and date=' . "'" . date('Y-m-d'). "'" . ' and adres='.'"' . $adr_work. '"';
        else
            $sql = 'select inn from schet where inn=' . "'" . $inn . "'" . ' and usluga=' . '"' . $u . '"' .
            ' and summa=' . $g . ' and date=' . "'" . date('Y-m-d'). "'" . ' and adres='.'"' . $adr_work. '"';

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
            $data_usluga = spr_work::find()->select(['kod_uslug','n_work','norm_time'])
                ->where('trim(work)=:work',['work' => trim($u)])->all();

            $kod_usluga = $data_usluga[0]->kod_uslug;  // Код услуги
            $n_work = $data_usluga[0]->n_work;         // № услуги
            $norm_time = $data_usluga[0]->norm_time;   // Норма времени
            $data_mvp = sprav_mvp::find()->select(['code','descr'])
                ->where('ID=:id',['id' => $mvp])->all();
            $code_mvp = $data_mvp[0]->code;
            // Создаём № договора
            $contract = mb_substr($cut_nazv,0,2,'UTF-8').$kod_usluga.'_'.$s;
            $model->usluga = $u;
            $model->summa = $g;
            $model->summa_work = $rabota;
            $model->summa_delivery = $delivery;
            $model->summa_transport = $transp;
            $model->summa_tmc = $tmc;
            $model->tmc_name = $tmc_name;
            $model->cost_auto_work = $cost_auto_work;
            $model->summa_beznds = $all;
            $model->surely = 0;
            $model->status = 1;
            $model->schet = $s;
            $model->contract = $contract;
            $model->inn = $inn;
            $model->res = $res;
            $model->adres = $adr_work;
            $model->time_t = $time_t;
            $model->n_work = $n_work;
            $model->norm_time = $norm_time;
            $model->mvp = $code_mvp;
            $model->time_prostoy = $time_prostoy;
            $model->time_work = $time_work;

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
        $sch1 = Yii::$app->request->post('sch1');
        if(empty($sch1)) $sch1='0';
        $sql = "select * from vschet where schet=:search";
        //$sql = "select * from vschet where (schet=:search or cast(schet as dec(10,0)) in (".$sch1."))";
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->asArray()->all();
//        debug($model);
//        return;
        $q=count($model);
        $total=0;
        for ($i = 0; $i < $q; $i++)
            $total += $model[$i]['summa'];

        return $this->render('sch_opl',['model' => $model,'style_title' => 'd9','q' => $q,'total' => $total]);
    }

    // Формирование файла для САП
    public function actionZ2sap()
    {
        $sch = Yii::$app->request->post('sch');

        $sql = "select * from vschet where schet=:search";
        $z = viewschet::findBySql($sql,[':search'=>"$sch"])->asArray()->all();
        $code_mvp=$z[0]['mvp'];
        $u=$z[0]['usluga'];
        $kol_e=$z[0]['kol'];
        $res_t=$z[0]['res'];
        if(trim($res_t)=='Дніпропетровські РЕМ') $rem_t='ДнРЕМ';
        if(trim($res_t)=='Вільногірські РЕМ') $rem_t='ВгРЕМ';
        if(trim($res_t)=='Жовтоводські РЕМ') $rem_t='ЖвРЕМ';
        if(trim($res_t)=='Гвардійські РЕМ') $rem_t='ГвРЕМ';
        if(trim($res_t)=='Криворізькі РЕМ') $rem_t='КрРЕМ';
        if(trim($res_t)=='Павлоградські РЕМ') $rem_t='ПвРЕМ';
        if(trim($res_t)=='Апостолівська дільниця') $rem_t='АпРЕМ';
        if(trim($res_t)=='Інгулецька дільниця') $rem_t='ІнРЕМ';

//        debug($z);
//        return;

        $sql = "select * from costwork where work=:search";
        $z2 = viewschet::findBySql($sql,[':search'=>"$u"])->asArray()->all();
        $u1=$z2[0]['usluga'];
        $u2=$z2[0]['calc_ind'];
        $other = '0';
        $summa_delivery=0;

        if($u1!='Оперативно-технічне обслуговування' || ($u1=='Оперативно-технічне обслуговування' && $u2==1)) {
            $res = mb_substr($z[0]['contract'], 0, 2, "UTF-8");
            $sql1 = 'select case when lic=1 then 0 else sum(stavka_grn) end as stavka_grn,lic
                from costwork where work=:search group by lic';
            $model2 = Calc::findBySql($sql1,[':search'=>"$u"])->all();

            $pole = viewschet::tr_res($res);  // Определение поля с данными по автомобилю

//            debug($z[0]);
//            return;

            if($model2[0]->lic==0)
                if($u1<>'Транспортні послуги') {
                    $time_t = $z[0]['time_t'];        // Время проезда (только для нелицензированной деятельности)
                    $time_tc=$time_t;
                }
                else {
                    $time_t = $z[0]['time_t'] ; // Время проезда (только для нелицензированной деятельности)
                    $time_tc=$z[0]['time_t'] + $z[0]['time_work']; // Время трудозатрат водителей (только для трансп. услуг)
                }
            else
                $time_t = 0;

            $clear_time_t=$time_t;   // Время проезда (только для нелицензированной деятельности) - первоначальное значение

//        debug($time_t);
//        return;

            $summa_delivery=round($time_t*$model2[0]->stavka_grn,2);  // Доставка бригады

            $time_prostoy = $z[0]['time_prostoy'];        // Время простоя
            $time_work = $z[0]['time_work'];             // Время работы
            $n_work = trim($z[0]['n_work']);
            $norm_time = trim($z[0]['norm_time']);
            $norm_time = round((float)str_replace(',', '.', $norm_time)*$kol_e,2);
            $norm_time = str_replace('.', ',', $norm_time);

            if($u1<>'Транспортні послуги')
                $sql = "select zp,common_minus,time_transp,tmc,repair,usluga,lic,verification from costwork where work=:search";
            else
                $sql = "select a.zp,b.common_minus,a.time_transp,a.tmc,a.repair,a.usluga,lic,verification from costwork a
                            left join a_transport b on a.work=b.model and trim(b.place)='$rem_t'
                            where a.work=:search limit 1";

            $z1 = viewschet::findBySql($sql, [':search' => "$u"])->asArray()->all();

            debug($z1);
            debug($sql);
            debug($u);
            return;

            $zp = round((float)str_replace(',', '.', $z1[0]['zp'])*$kol_e,2);
            $zp_e = round(0.22 * $zp, 2);
            $verification = $z1[0]['verification'];     // Поверка
            if($u1<>'Транспортні послуги')
                $cm = $z1[0]['common_minus'];
            else
                $cm = round($z1[0]['common_minus']*$time_tc,2);

            $cm = round((float)str_replace(',', '.', $cm)*$kol_e,2);
            $cm_tr = $cm;
            $cm = str_replace('.', ',', $cm);

            $tmc = $z1[0]['tmc']; // ТМЦ
            $lic = $z1[0]['lic'];
            $tmc = round((float)str_replace(',', '.', $tmc)*$kol_e,2);
            $tmc = str_replace('.', ',', $tmc);
            $repair = $z1[0]['repair']; // Ремонты
            $repair = round((float)str_replace(',', '.', $repair)*$kol_e,2);
            $repair = str_replace('.', ',', $repair);
            $tr_usl = ($z1[0]['usluga'] == 'Транспортні послуги') ? 1 : 0;

            if ($tr_usl == 0)
                $time_prostoy = $z1[0]['time_transp']; // Время простоя

            $sql = "select $pole as nomer from costwork a where a.work=:search and $pole is not null";


            $z1 = viewschet::findBySql($sql, [':search' => "$u"])->asArray()->all();
            if (count($z1) > 0)
                $nomer = $z1[0]['nomer'];
            else
                $nomer = '';

            $sql = "select * from vw_transport a where a.number=:search";

            $z1 = viewschet::findBySql($sql, [':search' => "$nomer"])->asArray()->all();


            if (count($z1) > 0 && $lic==0) {
//                debug($z1);
//                return;
                $oil = $z1[0]['oil_p'];
                $amort = $z1[0]['amort'];
                $wage = $z1[0]['wage'];
                $c92 = $z1[0]['cost_92_move'];
                $c95 = $z1[0]['cost_95_move'];
                $cdf = $z1[0]['cost_df_move'];
                $cg = $z1[0]['cost_g_move'];

                $c92w = $z1[0]['cost_92_work'];
                $c95w = $z1[0]['cost_95_work'];
                $cdfw = $z1[0]['cost_df_work'];
                $cgw = $z1[0]['cost_g_work'];
                $cm_tr = $z1[0]['common_minus'];;
            } else {
                $oil = '0';
                $amort = '0';
                $wage = '0';
                $c92 = '0';
                $c95 = '0';
                $cdf = '0';
                $cg = '0';
                $c92w = '0';
                $c95w = '0';
                $cdfw = '0';
                $cgw = '0';
                $cm_tr = '0';
            }
            $esv = 22;

            // Транспорт проезд
            $fuel_92 = (float)str_replace(',', '.', $c92);
            $fuel_95 = (float)str_replace(',', '.', $c95);
            $fuel_df = (float)str_replace(',', '.', $cdf);
            $fuel_g = (float)str_replace(',', '.', $cg);
            $time_t = (float)str_replace(',', '.', $time_t);
            $time_tc = (float)str_replace(',', '.', $time_tc);
            $cm_tr = (float)str_replace(',', '.', $cm_tr);

            //debug($fuel_92);

            if ($tr_usl == 0)
                $time_prostoy = round((float)str_replace(',', '.', $time_prostoy)*$kol_e,2);


            $oil_c = (float)str_replace(',', '.', $oil);
            $amort_c = (float)str_replace(',', '.', $amort);

            $oil = round($oil_c * $time_t, 2);
            $fuel_92 = round($fuel_92 * $time_t, 2);
            $fuel_95 = round($fuel_95 * $time_t, 2);
            $fuel_df = round($fuel_df * $time_t, 2);
            $fuel_g = round($fuel_g * $time_t, 2);
            $amort = round($amort_c * $time_t, 2);
            $zp_drive_c = (float)str_replace(',', '.', $wage);
            $zp_drive = round($zp_drive_c * $time_t, 2);

            // Транспорт простой
            $oil_prostoy = round($oil_c * $time_prostoy, 2);
            $amort_prostoy = round($amort_c * $time_prostoy, 2);
            $zp_drive_prostoy = round($zp_drive_c * $time_prostoy, 2);

            // Транспорт работа
            $fuel_92_work = (float)str_replace(',', '.', $c92w);
            $fuel_95_work = (float)str_replace(',', '.', $c95w);
            $fuel_df_work = (float)str_replace(',', '.', $cdfw);
            $fuel_g_work = (float)str_replace(',', '.', $cgw);

            if ($tr_usl == 0) $time_work = $time_prostoy;

            if ($tr_usl == 1)
            {
                $oil_work = round($oil_c * $time_work, 2);
                $amort_work = round($amort_c * $time_work, 2);
                $zp_drive_work = round($zp_drive_c * $time_work, 2);
            }
            else
            {   $oil_work = 0;
                $amort_work =0;
                $zp_drive_work=0;
            }

            $fuel_92_work = round($fuel_92_work * $time_work, 2);
            $fuel_95_work = round($fuel_95_work * $time_work, 2);
            $fuel_df_work = round($fuel_df_work * $time_work, 2);
            $fuel_g_work = round($fuel_g_work * $time_work, 2);

            // --Подсчет итога--
            // Топливо:
            $fuel_92 = $fuel_92 + $fuel_92_work;        // 92-й бензин
            $fuel_95 = $fuel_95 + $fuel_95_work;        // 95-й бензин
            $fuel_df = $fuel_df + $fuel_df_work;        // Диз. топливо
            $fuel_g = $fuel_g + $fuel_g_work;           // Газ
            // Другие показатели
            $oil = $oil + $oil_prostoy+$oil_work;                 // Масло
            $amort = $amort + $amort_prostoy+$amort_work;           // Аммортизация
            $zp_drive = $zp_drive + $zp_drive_prostoy+$zp_drive_work;  // Зарплата водителей

            if ($time_t == 0) {
                $fuel_92 = 0;
                $fuel_95 = 0;
                $fuel_df = 0;
                $fuel_g = 0;
                $oil = 0;
                $zp_drive = 0;
                $amort = 0;
            }

            if(($fuel_92+$fuel_95+$fuel_df+$fuel_g)>0) $priz_proezd=1;
            else $priz_proezd=0;
            $time_drive=$time_t;
            if($time_t>0)
                $time_t = $time_t + $time_prostoy;

            $cm_tr = round($cm_tr*$time_t,2);


//            debug($cm_tr);
//            return;

//            $fuel_92_dd=round($fuel_92*$time_drive,2);
//            $fuel_95_dd=round($fuel_95*$time_drive,2);
//            $fuel_df_dd=round($fuel_df*$time_drive,2);
//            $fuel_g_dd=round($fuel_g*$time_drive,2);
//            $oil_dd=round( $oil*$time_t,2);

             // Преобразуем точку в запятую в показателях
            $time_t = str_replace('.', ',', $time_t);
            $time_tc = str_replace('.', ',', $time_tc);
            $clear_time_t = str_replace('.', ',', $clear_time_t);
            $verification = str_replace('.', ',', $verification);
            $fuel_92 = str_replace('.', ',', $fuel_92);
            $fuel_95 = str_replace('.', ',', $fuel_95);
            $fuel_df = str_replace('.', ',', $fuel_df);
            $fuel_g = str_replace('.', ',', $fuel_g);
            $zp = str_replace('.', ',', $zp);
            $zp_e = str_replace('.', ',', $zp_e);
            $zp_esv = str_replace('.', ',', round($esv * $zp_drive / 100, 2));
            $zp_d = str_replace('.', ',', $zp_drive);
            $cm = trim(str_replace('.', ',', $cm));
            $cm_tr = trim(str_replace('.', ',', $cm_tr));
            $amort = str_replace('.', ',', $amort);
            $oil = str_replace('.', ',', $oil);
        }

        if($u1=='Оперативно-технічне обслуговування'  &&  $u2==0) {
            $res = mb_substr($z[0]['contract'], 0, 2, "UTF-8");

            $time_t = $z[0]['time_t'];        // Время проезда
            $time_prostoy = $z[0]['time_prostoy'];        // Время простоя
            $time_work = $z[0]['time_work'];             // Время работы
            $n_work = trim($z[0]['n_work']);
            $verification = 0;
            $norm_time = trim($z[0]['norm_time']);

            $sql = "select a.zp,a.common_minus,a.time_transp,a.tmc,a.repair,a.usluga,a.other,
                    b.* from costwork a left join a_transport b on trim(a.work)=trim(b.number) 
                    where a.work=:search";

            $z1 = viewschet::findBySql($sql, [':search' => "$u"])->asArray()->all();
            $zp = (float)str_replace(',', '.', $z1[0]['zp']);
            $zp_e = round(0.22 * $zp, 2);
            $cm = $z1[0]['common_minus'];

            $time_prostoy = $z1[0]['time_transp']; // Время простоя
            $tmc = trim($z1[0]['tmc']); // ТМЦ
            $repair = $z1[0]['repair']; // Ремонты
            $other = $z1[0]['other'];   // Другие
            $tr_usl =  0;

            if (count($z1) > 0) {
                $oil_p = $z1[0]['oil_p'];
                $oil_move = $z1[0]['oil_move'];
                $amort = $z1[0]['amort'];
                $amort_move = $z1[0]['amort_move'];
                $wage = $z1[0]['wage'];
                $wage_move = $z1[0]['wage_move'];
                $c92 = $z1[0]['cost_92_move'];
                $c95 = $z1[0]['cost_95_move'];
                $cdf = $z1[0]['cost_df_move'];
                $cg = $z1[0]['cost_g_move'];

                $c92w = $z1[0]['cost_92_work'];
                $c95w = $z1[0]['cost_95_work'];
                $cdfw = $z1[0]['cost_df_work'];
                $cgw = $z1[0]['cost_g_work'];
            } else {
                $oil = '0';
                $amort = '0';
                $wage = '0';
                $c92 = '0';
                $c95 = '0';
                $cdf = '0';
                $cg = '0';

                $c92w = '0';
                $c95w = '0';
                $cdfw = '0';
                $cgw = '0';

            }
            $esv = 22;

            // Транспорт проезд
            $fuel_92 = (float)str_replace(',', '.', $c92);
            $fuel_95 = (float)str_replace(',', '.', $c95);
            $fuel_df = (float)str_replace(',', '.', $cdf);
            $fuel_g = (float)str_replace(',', '.', $cg);
            $time_t = (float)str_replace(',', '.', $time_t);
            $oil_p = (float)str_replace(',', '.', $oil_p);
            $oil_move = (float)str_replace(',', '.', $oil_move);
            $amort = (float)str_replace(',', '.', $amort);
            $amort_move = (float)str_replace(',', '.', $amort_move);
            $wage = (float)str_replace(',', '.', $wage);
            $wage_move = (float)str_replace(',', '.', $wage_move);

            if ($tr_usl == 0)
                $time_prostoy = (float)str_replace(',', '.', $time_prostoy);

            // Транспорт простой
            $oil_prostoy = round($oil_p * $time_prostoy, 2);
            $amort_prostoy = round($amort * $time_prostoy, 2);
            $zp_drive_prostoy = round($wage * $time_prostoy, 2);

            // --Подсчет итога--
            // Другие показатели
            $oil = $oil_move + $oil_prostoy;                 // Масло
            $amort = $amort_move + $amort_prostoy;           // Аммортизация
            $zp_drive = $wage_move + $zp_drive_prostoy;  // Зарплата водителей

            if ($time_t == 0) {
                $fuel_92 = 0;
                $fuel_95 = 0;
                $fuel_df = 0;
                $fuel_g = 0;
                $oil = 0;
                $zp_drive = 0;
                $amort = 0;
            }

            if(($fuel_92+$fuel_95+$fuel_df+$fuel_g)>0) $priz_proezd=1;
            else $priz_proezd=0;
            $time_drive=$time_t;
            $time_t = $time_t + $time_prostoy;
            $cm_tr = $cm*$time_t;

            $fuel_92_dd=round($fuel_92*$time_drive,2);
            $fuel_95_dd=round($fuel_95*$time_drive,2);
            $fuel_df_dd=round($fuel_df*$time_drive,2);
            $fuel_g_dd=round($fuel_g*$time_drive,2);
            $oil_dd=round( $oil*$time_drive,2);

            // Преобразуем точку в запятую в показателях
            $time_t = str_replace('.', ',', $time_t);
            $verification = str_replace('.', ',', $verification);
            $clear_time_t = str_replace('.', ',', $clear_time_t);
            $oil = str_replace('.', ',', $oil);
            $fuel_92 = str_replace('.', ',', $fuel_92);
            $fuel_95 = str_replace('.', ',', $fuel_95);
            $fuel_df = str_replace('.', ',', $fuel_df);
            $fuel_g = str_replace('.', ',', $fuel_g);
            $zp = str_replace('.', ',', $zp);
            $zp_e = str_replace('.', ',', $zp_e);
            $zp_esv = str_replace('.', ',', round($esv * $zp_drive / 100, 2));
            $zp_d = str_replace('.', ',', $zp_drive);
            $cm = trim(str_replace('.', ',', $cm));
            $cm_tr = trim(str_replace('.', ',', $cm_tr));
            $amort = str_replace('.', ',', $amort);
        }

        $hap="БО;Номер послуги;МВП (підрозділ);ТМЦ;Зарплата бригади;ЄСВ_Зарплата бригади;Відрядження Добові_Бригади;Відрядження Проїзд_Бригади;Відрядження Проживання_Бригади;А-92_А/транспорт;А-95 А/транспорт;ДП А/транспорт;Газ_А/транспорт;Автомастила_А/транспорт;Зарплата водіїв;ЄСВ_зарплата водіїв;Амортизація_А/транспорт;Відрядження Добові_Водії;Відрядження Проживання_Водії;Повірка приладів обліку;Інші;Ремонт підр.спос.;Загальновиробничі витрати;АКТ/Особовий рахунок;№ договора (тільки для приєднання);Нормативні кошторисні трудовитрати бригади, люд-год. ;Нормативні кошторисні трудовитрати водіїв, люд-год. ";
//

        $fn=date('d.m.Y').'.csv';
        $f=fopen($fn,"w+");
        $hap = mb_convert_encoding($hap, 'CP1251', mb_detect_encoding($hap));

//        fputs($f,$tr_usl);
//        fputs($f,$priz_proezd);

        fputs($f,$hap);
       // Добавляем к зарплате сумму доставки бригады
        $zp = (float)str_replace(',', '.', $zp);
        $zp_e = (float)str_replace(',', '.', $zp_e);
//        $zp = $zp+round($summa_delivery/1.22,2);
        $zp_dd = round($summa_delivery/1.22,2);

//        debug($summa_delivery);
//        debug($zp_dd);
//        return;
//        $zp_e = $zp_e+round(round($summa_delivery/1.22,2)*0.22,2);
        $zp_edd = round(round($summa_delivery/1.22,2)*0.22,2);
        $zp = str_replace('.', ',', $zp);
        $zp_dd = str_replace('.', ',', $zp_dd);
        $zp_e = str_replace('.', ',', $zp_e);
        $zp_edd = str_replace('.', ',', $zp_edd);

//        debug($tmc);
//        return;

        if ($priz_proezd==0 || $tr_usl==1) {

            $e[0] = 'CK01';         // const
            $e[1] = $n_work;       // № услуги
            $e[2] = $code_mvp;    //  МВП пока const
            $e[3] = $tmc;        // ТМЦ
            $e[4] = $zp;       // Зарплата бригады
            $e[5] = $zp_e;      // Соц. взнос от зарплаты бригады
            $e[6] = 0;        // Коммандировки
            $e[7] = 0;       // Коммандировки
            $e[8] = 0;      // Коммандировки
            $e[9] = $fuel_92;        // топливо а-92
            $e[10] = $fuel_95;       // топливо а-95
            $e[11] = $fuel_df;       // топливо дт
            $e[12] = $fuel_g;        // топливо газ
            $e[13] = $oil;        // масло
            $e[14] = $zp_d;       // зп водителей
            $e[15] = $zp_esv;        // Соц. взнос от зарплаты водителей
            $e[16] = $amort;        // Аммортизация транспорт
            $e[17] = 0;        // Коммандировки
            $e[18] = 0;        // Коммандировки
            $e[19] = $verification;        // Поверка средств учета
            $e[20] = $other;          // other
            $e[21] = $repair;       // Ремонт
            $e[22] = $cm;        // Общепроизводственные затраты
            $e[23] = $sch;     // Счет
            $e[24] = '';       // № договора
            $e[25] = $norm_time;    // Нормативные трудозатраты бригады
            $e[26] = $time_tc;        // Нормативные трудозатраты водителей
            fputs($f, "\n");
            $content = implode(";", $e);
            $content = mb_convert_encoding($content, 'CP1251', mb_detect_encoding($content));
            fputs($f, $content);
        }
        else{
                $e[0] = 'CK01';         // const
                $e[1] = $n_work;       // № услуги
                $e[2] = $code_mvp;    //  МВП пока const
                $e[3] = $tmc;        // ТМЦ
                $e[4] = $zp;        // Зарплата бригады
                $e[5] = $zp_e; ;     // Соц. взнос от зарплаты бригады
                $e[6] = 0;        // Коммандировки
                $e[7] = 0;       // Коммандировки
                $e[8] = 0;      // Коммандировки
                $e[9] = 0;        // топливо а-92
                $e[10] = 0;       // топливо а-95
                $e[11] = 0;       // топливо дт
                $e[12] = 0;        // топливо газ
                $e[13] = 0;        // масло
                $e[14] = 0;       // зп водителей
                $e[15] = 0;        // Соц. взнос от зарплаты водителей
                $e[16] = 0;        // Аммортизация транспорт
                $e[17] = 0;        // Коммандировки
                $e[18] = 0;        // Коммандировки
                $e[19] = $verification;        // Поверка средств учета
                $e[20] = $other;        // other
                $e[21] = $repair;        // Ремонт
                $e[22] = $cm;        // Общепроизводственные затраты
                $e[23] = $sch;     // Счет
                $e[24] = '';       // № договора
                $e[25] = $norm_time;      // Нормативные трудозатраты бригады
                $e[26] = 0;        // Нормативные трудозатраты водителей
                fputs($f, "\n");
                $content = implode(";", $e);
                $content = mb_convert_encoding($content, 'CP1251', mb_detect_encoding($content));
                fputs($f, $content);

                $e[0] = 'CK01';         // const

//                if ($tr_usl == 0)
//                    $e[1] = '2300000380';       // № услуги
//                else
//                    $e[1] = '2300000385';       // № услуги

                $e[1] = $n_work;
                $e[2] = $code_mvp;    //  МВП пока const
                $e[3] = 0;        // ТМЦ
                $e[4] = $zp_dd;        // Зарплата бригады
                $e[5] = $zp_edd;     // Соц. взнос от зарплаты бригады
                $e[6] = 0;        // Коммандировки
                $e[7] = 0;       // Коммандировки
                $e[8] = 0;      // Коммандировки
                $e[9] = $fuel_92;        // топливо а-92
                $e[10] = $fuel_95;       // топливо а-95
                $e[11] = $fuel_df;       // топливо дт
                $e[12] = $fuel_g;        // топливо газ
                $e[13] = $oil;        // масло
                $e[14] = $zp_d;       // зп водителей
                $e[15] = $zp_esv;        // Соц. взнос от зарплаты водителей
                $e[16] = $amort;        // Аммортизация транспорт
                $e[17] = 0;        // Коммандировки
                $e[18] = 0;        // Коммандировки
                $e[19] = 0;        // Поверка средств учета
                $e[20] = 0;        // other
                $e[21] = 0;        // Ремонт
                $e[22] = $cm_tr;        // Общепроизводственные затраты - было 0
                $e[23] = $sch;     // Счет
                $e[24] = '';       // № договора
                $e[25] = $clear_time_t;      // Нормативные трудозатраты бригады
                $e[26] = $time_t;        // Нормативные трудозатраты водителей
                fputs($f, "\n");
                $content = implode(";", $e);
                $content = mb_convert_encoding($content, 'CP1251', mb_detect_encoding($content));
                fputs($f, $content);

        }
//        debug($e);
//        return;

        if (file_exists($fn)) {
            return \Yii::$app->response->sendFile($fn);
        }
        else{
            $model = new info();
            $model->title = 'УВАГА!';
            $model->info1 = "Помилка при формуванні файлу експорту в САП.";
            $model->style1 = "d15";
            $model->style2 = "info-text";
            $model->style_title = "d9";

            return $this->render('info', [
                'model' => $model]);
        }
    }

    public function actionRep_sap($date1,$date2,$usl,$id_sw)
    {
        $sql = "select usluga from costwork WHERE id=$usl";
        $z = viewschet::findBySql($sql)->asArray()->all();
        $u=trim($z[0]['usluga']);

//        debug($u);
//        return;

        if($u!='Підключення та/або відключення електроустановок')
        {          $sql = "select distinct a.*,b.work,b.usluga,
            b.repair,b.tmc,b.norm_time,b.other,b.common_minus,a.nazv,a.summa from vschet a 
            inner join costwork b on b.work=a.usluga WHERE a.date_akt>='$date1' and a.date_akt<='$date2'
             and trim(b.usluga)=".'"'.$u.'"'." and a.status=7";

//            debug($sql);
//            return;
        }
        else {
            if($id_sw==0)
            {
                $sql = "select distinct a.*,b.work,b.usluga,
                    b.repair,b.tmc,b.norm_time,b.other,b.common_minus,a.nazv,a.summa from vschet a 
                    inner join costwork b on b.work=a.usluga WHERE a.date_akt>='$date1' and a.date_akt<='$date2'
                     and trim(b.usluga)='$u' and a.status=7";
            }
            else{
                $sql = "select distinct a.*,b.work,b.usluga,
                    b.repair,b.tmc,b.norm_time,b.other,b.common_minus,a.nazv,a.summa from vschet a 
                    inner join costwork b on b.work=a.usluga 
                     inner join spr_con_usl c on b.work=c.work 
                     WHERE a.date_akt>='$date1' and a.date_akt<='$date2'
                     and trim(b.usluga)='$u' and a.status=7 and c.type=2-$id_sw";
            }
        }

        $z2 = viewschet::findBySql($sql)->asArray()->all();

        if(count($z2)==0)
        {
            $model = new info();
            $model->title = 'УВАГА!';
            $model->info1 = "Немає жодної послуги для вигрузки.";
            $model->style1 = "d15";
            $model->style2 = "info-text";
            $model->style_title = "d9";

            return $this->render('info', [
                'model' => $model]);
        }

        $u1=$u;
        $other = '0';
        $hap = "Споживач;Сума з ПДВ;БО;Номер послуги;МВП (підрозділ);ТМЦ;Зарплата бригади;ЄСВ_Зарплата бригади;Відрядження Добові_Бригади;Відрядження Проїзд_Бригади;Відрядження Проживання_Бригади;А-92_А/транспорт;А-95 А/транспорт;ДП А/транспорт;Газ_А/транспорт;Автомастила_А/транспорт;Зарплата водіїв;ЄСВ_зарплата водіїв;Амортизація_А/транспорт;Відрядження Добові_Водії;Відрядження Проживання_Водії;Повірка приладів обліку;Інші;Ремонт підр.спос.;Загальновиробничі витрати;АКТ/Особовий рахунок;№ договора (тільки для приєднання);Нормативні кошторисні трудовитрати бригади, люд-год. ;Нормативні кошторисні трудовитрати водіїв, люд-год. ";
        $fn = 'Report_'.date('d.m.Y') . '.csv';
        $f = fopen($fn, "w+");
        $hap = mb_convert_encoding($hap, 'CP1251', mb_detect_encoding($hap));
        $cnt=0;
        $summa_delivery=0;

//        debug($sql);
//        return;

        foreach($z2 as $z) {
            $kol_e=$z['kol'];
            $nazv=$z['nazv'];
            $summa=$z['summa'];
            if ($u1 != 'Оперативно-технічне обслуговування') {
                $cnt++;
                $res = mb_substr($z['contract'], 0, 2, "UTF-8");
                $u=trim($z['work']);
                $sql1 = 'select case when lic=1 then 0 else sum(stavka_grn) end as stavka_grn,lic
                from costwork where work=:search group by lic';
                $model2 = Calc::findBySql($sql1,[':search'=>"$u"])->all();

                $pole = viewschet::tr_res($res);  // Определение поля с данными по автомобилю
                if($model2[0]->lic==0) {
                    $time_t = $z['time_t'];        // Время проезда (только для нелицензированной деятельности)
                    $notlic=1;  // Признак нелицензированной деятельности
                }
                else {
                    $time_t = 0;
                    $notlic=0;   // Признак нелицензированной деятельности
                }
                $summa_delivery=round($time_t*$model2[0]->stavka_grn,2);  // Доставка бригады

                $time_prostoy = $z['time_prostoy'];        // Время простоя
                $time_work = $z['time_work'];             // Время работы
                $n_work = trim($z['n_work']);
//                $norm_time = trim($z['norm_time']);
                $norm_time = trim($z['norm_time']);
                $norm_time = round((float)str_replace(',', '.', $norm_time)*$kol_e,2);
                $norm_time = str_replace('.', ',', $norm_time);
                $code_mvp=$z['mvp'];
                $sch=$z['schet'];
                $w = $z['work'];

                $sql = "select zp,common_minus,time_transp,tmc,repair,usluga,lic,verification from costwork where work=:search";
                $z1 = viewschet::findBySql($sql, [':search' => "$u"])->asArray()->all();
                $zp = round((float) str_replace(',', '.', $z1[0]['zp'])*$kol_e,2);
                $zp_e = round(0.22 * $zp, 2);
                $cm = $z1[0]['common_minus'];
                $verification = $z1[0]['verification'];
                $cm = round((float)str_replace(',', '.', $cm)*$kol_e,2);
                $cm_tr=$cm;
                $tmc = $z1[0]['tmc']; // ТМЦ
                $tmc = round((float)str_replace(',', '.', $tmc)*$kol_e,2);

                $lic = $z1[0]['lic'];    // Признак лицензированной деятельности
                $repair = $z1[0]['repair']; // Ремонты
                $repair = round((float)str_replace(',', '.', $repair)*$kol_e,2);

                $tr_usl = ($z1[0]['usluga'] == 'Транспортні послуги') ? 1 : 0;

                if ($tr_usl == 0)
                    $time_prostoy = $z1[0]['time_transp']; // Время простоя

                $sql = "select $pole as nomer from costwork a where a.work=:search and $pole is not null";

                $z1 = viewschet::findBySql($sql, [':search' => "$u"])->asArray()->all();
                if (count($z1) > 0)
                    $nomer = $z1[0]['nomer'];
                else
                    $nomer = '';

                $sql = "select * from vw_transport a where a.number=:search";

                $z1 = viewschet::findBySql($sql, [':search' => "$nomer"])->asArray()->all();
                if (count($z1) > 0 && $lic==0) {
                    $oil = $z1[0]['oil_p'];
                    $amort = $z1[0]['amort'];
                    $wage = $z1[0]['wage'];
                    $c92 = $z1[0]['cost_92_move'];
                    $c95 = $z1[0]['cost_95_move'];
                    $cdf = $z1[0]['cost_df_move'];
                    $cg = $z1[0]['cost_g_move'];

                    $c92w = $z1[0]['cost_92_work'];
                    $c95w = $z1[0]['cost_95_work'];
                    $cdfw = $z1[0]['cost_df_work'];
                    $cgw = $z1[0]['cost_g_work'];
                } else {
                    $oil = '0';
                    $amort = '0';
                    $wage = '0';
                    $c92 = '0';
                    $c95 = '0';
                    $cdf = '0';
                    $cg = '0';

                    $c92w = '0';
                    $c95w = '0';
                    $cdfw = '0';
                    $cgw = '0';

                }
                $esv = 22;

                // Транспорт проезд
                $fuel_92 = (float)str_replace(',', '.', $c92);
                $fuel_95 = (float)str_replace(',', '.', $c95);
                $fuel_df = (float)str_replace(',', '.', $cdf);
                $fuel_g = (float)str_replace(',', '.', $cg);
                $time_t = (float)str_replace(',', '.', $time_t);
                if ($tr_usl == 0)
                    $time_prostoy = (float)str_replace(',', '.', $time_prostoy);


                $oil_c = (float)str_replace(',', '.', $oil);
                $amort_c = (float)str_replace(',', '.', $amort);

                $oil = round($oil_c * $time_t, 2);
                $fuel_92 = round($fuel_92 * $time_t, 2);
                $fuel_95 = round($fuel_95 * $time_t, 2);
                $fuel_df = round($fuel_df * $time_t, 2);
                $fuel_g = round($fuel_g * $time_t, 2);
                $amort = round($amort_c * $time_t, 2);
                $zp_drive_c = (float)str_replace(',', '.', $wage);
                $zp_drive = round($zp_drive_c * $time_t, 2);

                // Транспорт простой
                $oil_prostoy = round($oil_c * $time_prostoy, 2);
                $amort_prostoy = round($amort_c * $time_prostoy, 2);
                $zp_drive_prostoy = round($zp_drive_c * $time_prostoy, 2);

                // Транспорт работа
                $fuel_92_work = (float)str_replace(',', '.', $c92w);
                $fuel_95_work = (float)str_replace(',', '.', $c95w);
                $fuel_df_work = (float)str_replace(',', '.', $cdfw);
                $fuel_g_work = (float)str_replace(',', '.', $cgw);

                if ($tr_usl == 0) $time_work = $time_prostoy;

                if ($tr_usl == 1) {
                    $oil_work = round($oil_c * $time_work, 2);
                    $amort_work = round($amort_c * $time_work, 2);
                    $zp_drive_work = round($zp_drive_c * $time_work, 2);
                } else {
                    $oil_work = 0;
                    $amort_work = 0;
                    $zp_drive_work = 0;
                }

                $fuel_92_work = round($fuel_92_work * $time_work, 2);
                $fuel_95_work = round($fuel_95_work * $time_work, 2);
                $fuel_df_work = round($fuel_df_work * $time_work, 2);
                $fuel_g_work = round($fuel_g_work * $time_work, 2);

                // --Подсчет итога--
                // Топливо:
                $fuel_92 = $fuel_92 + $fuel_92_work;        // 92-й бензин
                $fuel_95 = $fuel_95 + $fuel_95_work;        // 95-й бензин
                $fuel_df = $fuel_df + $fuel_df_work;        // Диз. топливо
                $fuel_g = $fuel_g + $fuel_g_work;           // Газ
                // Другие показатели
                $oil = $oil + $oil_prostoy + $oil_work;                 // Масло
                $amort = $amort + $amort_prostoy + $amort_work;           // Аммортизация
                $zp_drive = $zp_drive + $zp_drive_prostoy + $zp_drive_work;  // Зарплата водителей

                if ($time_t == 0) {
                    $fuel_92 = 0;
                    $fuel_95 = 0;
                    $fuel_df = 0;
                    $fuel_g = 0;
                    $oil = 0;
                    $zp_drive = 0;
                    $amort = 0;
                }

                if (($fuel_92 + $fuel_95 + $fuel_df + $fuel_g) > 0) $priz_proezd = 1;
                else $priz_proezd = 0;

                if($time_t>0)
                    $time_t = $time_t + ($notlic==1) ? $time_prostoy:0;

                $cm_tr = round($cm_tr*$time_t,2);
                $ff=fopen('aaa_sap.txt','w+');
                fputs($ff,$notlic);
                fputs($ff,"\n");
                fputs($ff,$time_prostoy);
                fputs($ff,"\n");
                fputs($ff,$time_t);

                // Преобразуем точку в запятую в показателях
                $time_t = str_replace('.', ',', $time_t);
                $oil = str_replace('.', ',', $oil);
                $fuel_92 = str_replace('.', ',', $fuel_92);
                $fuel_95 = str_replace('.', ',', $fuel_95);
                $fuel_df = str_replace('.', ',', $fuel_df);
                $fuel_g = str_replace('.', ',', $fuel_g);
                $zp = str_replace('.', ',', $zp);
                $zp_e = str_replace('.', ',', $zp_e);
                $zp_esv = str_replace('.', ',', round($esv * $zp_drive / 100, 2));
                $zp_d = str_replace('.', ',', $zp_drive);
                $cm = trim(str_replace('.', ',', $cm));
                $cm_tr = trim(str_replace('.', ',', $cm_tr));
                $amort = str_replace('.', ',', $amort);
            }

            if ($u1 == 'Оперативно-технічне обслуговування') {
                $cnt++;
                $res = mb_substr($z['contract'], 0, 2, "UTF-8");

                $time_t = $z['time_t'];        // Время проезда
                $time_prostoy = $z['time_prostoy'];        // Время простоя
                $time_work = $z['time_work'];             // Время работы
                $n_work = trim($z['n_work']);
                $norm_time = trim($z['norm_time']);
                $code_mvp=$z['mvp'];
                $sch=$z['schet'];
                $w = $z['work'];
                $verification = 0;  // Поверка

                $sql = "select a.zp,a.common_minus,a.time_transp,a.tmc,a.repair,a.usluga,a.other,
                    b.* from costwork a left join a_transport b on trim(a.work)=trim(b.number) 
                    where a.work=:search";
                $z1 = viewschet::findBySql($sql, [':search' => "$w"])->asArray()->all();

//                debug($sql);
//                return;

                $zp = (float)str_replace(',', '.', $z1[0]['zp']);
                $zp_e = round(0.22 * $zp, 2);
                $cm = $z1[0]['common_minus'];
                $cm_tr = 0;
                $time_prostoy = $z1[0]['time_transp']; // Время простоя
                $tmc = trim($z1[0]['tmc']); // ТМЦ
                $repair = $z1[0]['repair']; // Ремонты
                $other = $z1[0]['other'];   // Другие
                $tr_usl = 0;

                if (count($z1) > 0) {
                    $oil_p = $z1[0]['oil_p'];
                    $oil_move = $z1[0]['oil_move'];
                    $amort = $z1[0]['amort'];
                    $amort_move = $z1[0]['amort_move'];
                    $wage = $z1[0]['wage'];
                    $wage_move = $z1[0]['wage_move'];
                    $c92 = $z1[0]['cost_92_move'];
                    $c95 = $z1[0]['cost_95_move'];
                    $cdf = $z1[0]['cost_df_move'];
                    $cg = $z1[0]['cost_g_move'];

                    $c92w = $z1[0]['cost_92_work'];
                    $c95w = $z1[0]['cost_95_work'];
                    $cdfw = $z1[0]['cost_df_work'];
                    $cgw = $z1[0]['cost_g_work'];
                } else {
                    $oil = '0';
                    $amort = '0';
                    $wage = '0';
                    $c92 = '0';
                    $c95 = '0';
                    $cdf = '0';
                    $cg = '0';

                    $c92w = '0';
                    $c95w = '0';
                    $cdfw = '0';
                    $cgw = '0';

                }
                $esv = 22;


                // Транспорт проезд
                $fuel_92 = (float)str_replace(',', '.', $c92);
                $fuel_95 = (float)str_replace(',', '.', $c95);
                $fuel_df = (float)str_replace(',', '.', $cdf);
                $fuel_g = (float)str_replace(',', '.', $cg);
                $time_t = (float)str_replace(',', '.', $time_t);
                $oil_p = (float)str_replace(',', '.', $oil_p);
                $oil_move = (float)str_replace(',', '.', $oil_move);
                $amort = (float)str_replace(',', '.', $amort);
                $amort_move = (float)str_replace(',', '.', $amort_move);
                $wage = (float)str_replace(',', '.', $wage);
                $wage_move = (float)str_replace(',', '.', $wage_move);

                if ($tr_usl == 0)
                    $time_prostoy = (float)str_replace(',', '.', $time_prostoy);

                // Транспорт простой
                $oil_prostoy = round($oil_p * $time_prostoy, 2);
                $amort_prostoy = round($amort * $time_prostoy, 2);
                $zp_drive_prostoy = round($wage * $time_prostoy, 2);

                // --Подсчет итога--
                // Другие показатели
                $oil = $oil_move + $oil_prostoy;                 // Масло
                $amort = $amort_move + $amort_prostoy;           // Аммортизация
                $zp_drive = $wage_move + $zp_drive_prostoy;  // Зарплата водителей

                if ($time_t == 0) {
                    $fuel_92 = 0;
                    $fuel_95 = 0;
                    $fuel_df = 0;
                    $fuel_g = 0;
                    $oil = 0;
                    $zp_drive = 0;
                    $amort = 0;
                }

                if (($fuel_92 + $fuel_95 + $fuel_df + $fuel_g) > 0) $priz_proezd = 1;
                else $priz_proezd = 0;
                $time_t = $time_t + $time_prostoy;
                // Преобразуем точку в запятую в показателях
                $time_t = str_replace('.', ',', $time_t);
                $oil = str_replace('.', ',', $oil);
                $fuel_92 = str_replace('.', ',', $fuel_92);
                $fuel_95 = str_replace('.', ',', $fuel_95);
                $fuel_df = str_replace('.', ',', $fuel_df);
                $fuel_g = str_replace('.', ',', $fuel_g);
                $zp = str_replace('.', ',', $zp);
                $zp_e = str_replace('.', ',', $zp_e);
                $zp_esv = str_replace('.', ',', round($esv * $zp_drive / 100, 2));
                $zp_d = str_replace('.', ',', $zp_drive);
                $cm = trim(str_replace('.', ',', $cm));
                $amort = str_replace('.', ',', $amort);
            }

//        fputs($f,$tr_usl);
//        fputs($f,$priz_proezd);

            if($cnt==1)
                fputs($f, $hap);

            // Добавляем к зарплате сумму доставки бригады
            $zp = (float)str_replace(',', '.', $zp);
            $zp_e = (float)str_replace(',', '.', $zp_e);
            $zp = $zp+round($summa_delivery/1.22,2);
            $zp_e = $zp_e+round(round($summa_delivery/1.22,2)*0.22,2);
            $zp = str_replace('.', ',', $zp);
            $zp_e = str_replace('.', ',', $zp_e);
            $summa = str_replace('.', ',', $summa);

            if ($priz_proezd == 0 || $tr_usl == 1) {
                $e[-2] = $nazv;            // Поставщик
                $e[-1] = $summa;           // Сумма с НДС
                $e[0] = 'CK01';           // const
                $e[1] = $n_work;        // № услуги
                $e[2] = $code_mvp;    //  МВП пока const
                $e[3] = $tmc;        // ТМЦ
                $e[4] = $zp;        // Зарплата бригады
                $e[5] = $zp_e;     // Соц. взнос от зарплаты бригады
                $e[6] = 0;        // Коммандировки
                $e[7] = 0;       // Коммандировки
                $e[8] = 0;      // Коммандировки
                $e[9] = $fuel_92;        // топливо а-92
                $e[10] = $fuel_95;       // топливо а-95
                $e[11] = $fuel_df;       // топливо дт
                $e[12] = $fuel_g;        // топливо газ
                $e[13] = $oil;        // масло
                $e[14] = $zp_d;       // зп водителей
                $e[15] = $zp_esv;        // Соц. взнос от зарплаты водителей
                $e[16] = $amort;        // Аммортизация транспорт
                $e[17] = 0;        // Коммандировки
                $e[18] = 0;        // Коммандировки
                $e[19] = $verification;        // Поверка средств учета
                $e[20] = $other;        // other
                $e[21] = $repair;        // Ремонт
                $e[22] = $cm;        // Общепроизводственные затраты
                $e[23] = $sch;     // Счет
                $e[24] = '';       // № договора
                $e[25] = $norm_time;      // Нормативные трудозатраты бригады
                $e[26] = $time_t;        // Нормативные трудозатраты водителей
                fputs($f, "\n");
                $content = implode(";", $e);
                $content = mb_convert_encoding($content, 'CP1251', mb_detect_encoding($content));
                fputs($f, $content);
            } else {
                $e[-2] = $nazv;            // Поставщик
                $e[-1] = $summa;           // Сумма с НДС
                $e[0] = 'CK01';         // const
                $e[1] = $n_work;       // № услуги
                $e[2] = $code_mvp;    //  МВП пока const
                $e[3] = $tmc;        // ТМЦ
                $e[4] = $zp;        // Зарплата бригады
                $e[5] = $zp_e;     // Соц. взнос от зарплаты бригады
                $e[6] = 0;        // Коммандировки
                $e[7] = 0;       // Коммандировки
                $e[8] = 0;      // Коммандировки
                $e[9] = 0;        // топливо а-92
                $e[10] = 0;       // топливо а-95
                $e[11] = 0;       // топливо дт
                $e[12] = 0;        // топливо газ
                $e[13] = 0;        // масло
                $e[14] = 0;       // зп водителей
                $e[15] = 0;        // Соц. взнос от зарплаты водителей
                $e[16] = 0;        // Аммортизация транспорт
                $e[17] = 0;        // Коммандировки
                $e[18] = 0;        // Коммандировки
                $e[19] = 0;        // Поверка средств учета
                $e[20] = $other;        // other
                $e[21] = $repair;        // Ремонт
                $e[22] = $cm;        // Общепроизводственные затраты
                $e[23] = $sch;     // Счет
                $e[24] = '';       // № договора
                $e[25] = $norm_time;      // Нормативные трудозатраты бригады
                $e[26] = 0;        // Нормативные трудозатраты водителей
                fputs($f, "\n");
                $content = implode(";", $e);
                $content = mb_convert_encoding($content, 'CP1251', mb_detect_encoding($content));
                fputs($f, $content);

                $e[-2] = $nazv;            // Поставщик
                $e[-1] = $summa;           // Сумма с НДС
                $e[0] = 'CK01';         // const
                if ($tr_usl == 0)
                    $e[1] = '2300000380';       // № услуги
                else
                    $e[1] = '2300000385';       // № услуги

                $e[2] = $code_mvp;    //  МВП пока const
                $e[3] = 0;        // ТМЦ
                $e[4] = 0;        // Зарплата бригады
                $e[5] = 0;     // Соц. взнос от зарплаты бригады
                $e[6] = 0;        // Коммандировки
                $e[7] = 0;       // Коммандировки
                $e[8] = 0;      // Коммандировки
                $e[9] = $fuel_92;        // топливо а-92
                $e[10] = $fuel_95;       // топливо а-95
                $e[11] = $fuel_df;       // топливо дт
                $e[12] = $fuel_g;        // топливо газ
                $e[13] = $oil;        // масло
                $e[14] = $zp_d;       // зп водителей
                $e[15] = $zp_esv;        // Соц. взнос от зарплаты водителей
                $e[16] = $amort;        // Аммортизация транспорт
                $e[17] = 0;        // Коммандировки
                $e[18] = 0;        // Коммандировки
                $e[19] = 0;        // Поверка средств учета
                $e[20] = 0;        // other
                $e[21] = 0;        // Ремонт
                $e[22] = $cm_tr;        // Общепроизводственные затраты
                $e[23] = $sch;     // Счет
                $e[24] = '';       // № договора
                $e[25] = 0;      // Нормативные трудозатраты бригады
                $e[26] = $time_t;        // Нормативные трудозатраты водителей
                fputs($f, "\n");
                $content = implode(";", $e);
                $content = mb_convert_encoding($content, 'CP1251', mb_detect_encoding($content));
                fputs($f, $content);

            }
        }
//        debug($e);
//        return;

        if (file_exists($fn)) {
            return \Yii::$app->response->sendFile($fn);
        }
        else{
            $model = new info();
            $model->title = 'УВАГА!';
            $model->info1 = "Помилка при формуванні файлу експорту в САП.";
            $model->style1 = "d15";
            $model->style2 = "info-text";
            $model->style_title = "d9";

            return $this->render('info', [
                'model' => $model]);
        }
    }

    // Выгрузка за период в САП
    public function actionUpload_sap($date1,$date2,$usl,$id_sw)
    {

        $sql = "select usluga from costwork WHERE id=$usl";
        $z = viewschet::findBySql($sql)->asArray()->all();
        $u=trim($z[0]['usluga']);

//        debug($u);
//        return;

        if($u!='Підключення та/або відключення електроустановок')
{          $sql = "select distinct a.*,b.work,b.usluga,
            b.repair,b.tmc,b.norm_time,b.other,b.common_minus from vschet a 
            inner join costwork b on b.work=a.usluga WHERE a.date_akt>='$date1' and a.date_akt<='$date2'
             and trim(b.usluga)=".'"'.$u.'"'." and a.status=7";

//            debug($sql);
//            return;
}
        else {
            if($id_sw==0)
            {
                    $sql = "select distinct a.*,b.work,b.usluga,
                    b.repair,b.tmc,b.norm_time,b.other,b.common_minus from vschet a 
                    inner join costwork b on b.work=a.usluga WHERE a.date_akt>='$date1' and a.date_akt<='$date2'
                     and trim(b.usluga)='$u' and a.status=7";
            }
            else{
                    $sql = "select distinct a.*,b.work,b.usluga,
                    b.repair,b.tmc,b.norm_time,b.other,b.common_minus from vschet a 
                    inner join costwork b on b.work=a.usluga 
                     inner join spr_con_usl c on b.work=c.work 
                     WHERE a.date_akt>='$date1' and a.date_akt<='$date2'
                     and trim(b.usluga)='$u' and a.status=7 and c.type=2-$id_sw";
            }
        }

        $z2 = viewschet::findBySql($sql)->asArray()->all();

        if(count($z2)==0)
        {
            $model = new info();
            $model->title = 'УВАГА!';
            $model->info1 = "Немає жодної послуги для вигрузки.";
            $model->style1 = "d15";
            $model->style2 = "info-text";
            $model->style_title = "d9";

            return $this->render('info', [
                'model' => $model]);
        }

        $u1=$u;
        $other = '0';
        $hap = "БО;Номер послуги;МВП (підрозділ);ТМЦ;Зарплата бригади;ЄСВ_Зарплата бригади;Відрядження Добові_Бригади;Відрядження Проїзд_Бригади;Відрядження Проживання_Бригади;А-92_А/транспорт;А-95 А/транспорт;ДП А/транспорт;Газ_А/транспорт;Автомастила_А/транспорт;Зарплата водіїв;ЄСВ_зарплата водіїв;Амортизація_А/транспорт;Відрядження Добові_Водії;Відрядження Проживання_Водії;Повірка приладів обліку;Інші;Ремонт підр.спос.;Загальновиробничі витрати;АКТ/Особовий рахунок;№ договора (тільки для приєднання);Нормативні кошторисні трудовитрати бригади, люд-год. ;Нормативні кошторисні трудовитрати водіїв, люд-год. ";
        $fn = date('d.m.Y') . '.csv';
        $f = fopen($fn, "w+");
        $hap = mb_convert_encoding($hap, 'CP1251', mb_detect_encoding($hap));
        $cnt=0;
        $summa_delivery=0;

//        debug($sql);
//        return;

        foreach($z2 as $z) {
            $kol_e=$z['kol'];
            if ($u1 != 'Оперативно-технічне обслуговування') {
                $cnt++;
                $res = mb_substr($z['contract'], 0, 2, "UTF-8");
                $u=trim($z['work']);
                $sql1 = 'select case when lic=1 then 0 else sum(stavka_grn) end as stavka_grn,lic
                from costwork where work=:search group by lic';
                $model2 = Calc::findBySql($sql1,[':search'=>"$u"])->all();

                $pole = viewschet::tr_res($res);  // Определение поля с данными по автомобилю
                if($model2[0]->lic==0) {
                    $time_t = $z['time_t'];        // Время проезда (только для нелицензированной деятельности)
                    $notlic=1;  // Признак нелицензированной деятельности
                }
                else {
                    $time_t = 0;
                    $notlic=0;   // Признак нелицензированной деятельности
                }
                $summa_delivery=round($time_t*$model2[0]->stavka_grn,2);  // Доставка бригады
               
                $time_prostoy = $z['time_prostoy'];        // Время простоя
                $time_work = $z['time_work'];             // Время работы
                $n_work = trim($z['n_work']);
//                $norm_time = trim($z['norm_time']);
                $norm_time = trim($z['norm_time']);
                $norm_time = round((float)str_replace(',', '.', $norm_time)*$kol_e,2);
                $norm_time = str_replace('.', ',', $norm_time);
                 $code_mvp=$z['mvp'];
                $sch=$z['schet'];
                $w = $z['work'];

                $sql = "select zp,common_minus,time_transp,tmc,repair,usluga,lic,verification from costwork where work=:search";
                $z1 = viewschet::findBySql($sql, [':search' => "$u"])->asArray()->all();
                $zp = round((float) str_replace(',', '.', $z1[0]['zp'])*$kol_e,2);
                $zp_e = round(0.22 * $zp, 2);
                $cm = $z1[0]['common_minus'];
                $verification = $z1[0]['verification'];
                $cm = round((float)str_replace(',', '.', $cm)*$kol_e,2);
                $cm_tr=$cm;
                $tmc = $z1[0]['tmc']; // ТМЦ
                $tmc = round((float)str_replace(',', '.', $tmc)*$kol_e,2);

                $lic = $z1[0]['lic'];    // Признак лицензированной деятельности
                $repair = $z1[0]['repair']; // Ремонты
                $repair = round((float)str_replace(',', '.', $repair)*$kol_e,2);

                $tr_usl = ($z1[0]['usluga'] == 'Транспортні послуги') ? 1 : 0;

                if ($tr_usl == 0)
                    $time_prostoy = $z1[0]['time_transp']; // Время простоя

                $sql = "select $pole as nomer from costwork a where a.work=:search and $pole is not null";

                $z1 = viewschet::findBySql($sql, [':search' => "$u"])->asArray()->all();
                if (count($z1) > 0)
                    $nomer = $z1[0]['nomer'];
                else
                    $nomer = '';

                $sql = "select * from vw_transport a where a.number=:search";

                $z1 = viewschet::findBySql($sql, [':search' => "$nomer"])->asArray()->all();
                if (count($z1) > 0 && $lic==0) {
                    $oil = $z1[0]['oil_p'];
                    $amort = $z1[0]['amort'];
                    $wage = $z1[0]['wage'];
                    $c92 = $z1[0]['cost_92_move'];
                    $c95 = $z1[0]['cost_95_move'];
                    $cdf = $z1[0]['cost_df_move'];
                    $cg = $z1[0]['cost_g_move'];

                    $c92w = $z1[0]['cost_92_work'];
                    $c95w = $z1[0]['cost_95_work'];
                    $cdfw = $z1[0]['cost_df_work'];
                    $cgw = $z1[0]['cost_g_work'];
                } else {
                    $oil = '0';
                    $amort = '0';
                    $wage = '0';
                    $c92 = '0';
                    $c95 = '0';
                    $cdf = '0';
                    $cg = '0';

                    $c92w = '0';
                    $c95w = '0';
                    $cdfw = '0';
                    $cgw = '0';

                }
                $esv = 22;

                // Транспорт проезд
                $fuel_92 = (float)str_replace(',', '.', $c92);
                $fuel_95 = (float)str_replace(',', '.', $c95);
                $fuel_df = (float)str_replace(',', '.', $cdf);
                $fuel_g = (float)str_replace(',', '.', $cg);
                $time_t = (float)str_replace(',', '.', $time_t);
                if ($tr_usl == 0)
                    $time_prostoy = (float)str_replace(',', '.', $time_prostoy);


                $oil_c = (float)str_replace(',', '.', $oil);
                $amort_c = (float)str_replace(',', '.', $amort);

                $oil = round($oil_c * $time_t, 2);
                $fuel_92 = round($fuel_92 * $time_t, 2);
                $fuel_95 = round($fuel_95 * $time_t, 2);
                $fuel_df = round($fuel_df * $time_t, 2);
                $fuel_g = round($fuel_g * $time_t, 2);
                $amort = round($amort_c * $time_t, 2);
                $zp_drive_c = (float)str_replace(',', '.', $wage);
                $zp_drive = round($zp_drive_c * $time_t, 2);

                // Транспорт простой
                $oil_prostoy = round($oil_c * $time_prostoy, 2);
                $amort_prostoy = round($amort_c * $time_prostoy, 2);
                $zp_drive_prostoy = round($zp_drive_c * $time_prostoy, 2);

                // Транспорт работа
                $fuel_92_work = (float)str_replace(',', '.', $c92w);
                $fuel_95_work = (float)str_replace(',', '.', $c95w);
                $fuel_df_work = (float)str_replace(',', '.', $cdfw);
                $fuel_g_work = (float)str_replace(',', '.', $cgw);

                if ($tr_usl == 0) $time_work = $time_prostoy;

                if ($tr_usl == 1) {
                    $oil_work = round($oil_c * $time_work, 2);
                    $amort_work = round($amort_c * $time_work, 2);
                    $zp_drive_work = round($zp_drive_c * $time_work, 2);
                } else {
                    $oil_work = 0;
                    $amort_work = 0;
                    $zp_drive_work = 0;
                }

                $fuel_92_work = round($fuel_92_work * $time_work, 2);
                $fuel_95_work = round($fuel_95_work * $time_work, 2);
                $fuel_df_work = round($fuel_df_work * $time_work, 2);
                $fuel_g_work = round($fuel_g_work * $time_work, 2);


                // --Подсчет итога--
                // Топливо:
                $fuel_92 = $fuel_92 + $fuel_92_work;        // 92-й бензин
                $fuel_95 = $fuel_95 + $fuel_95_work;        // 95-й бензин
                $fuel_df = $fuel_df + $fuel_df_work;        // Диз. топливо
                $fuel_g = $fuel_g + $fuel_g_work;           // Газ
                // Другие показатели
                $oil = $oil + $oil_prostoy + $oil_work;                 // Масло
                $amort = $amort + $amort_prostoy + $amort_work;           // Аммортизация
                $zp_drive = $zp_drive + $zp_drive_prostoy + $zp_drive_work;  // Зарплата водителей

                if ($time_t == 0) {
                    $fuel_92 = 0;
                    $fuel_95 = 0;
                    $fuel_df = 0;
                    $fuel_g = 0;
                    $oil = 0;
                    $zp_drive = 0;
                    $amort = 0;
                }

                if (($fuel_92 + $fuel_95 + $fuel_df + $fuel_g) > 0) $priz_proezd = 1;
                else $priz_proezd = 0;

                if($time_t>0)
                    $time_t = $time_t + ($notlic==1) ? $time_prostoy:0;

                $cm_tr = round($cm_tr*$time_t,2);
                $ff=fopen('aaa_sap.txt','w+');
                fputs($ff,$notlic);
                fputs($ff,"\n");
                fputs($ff,$time_prostoy);
                fputs($ff,"\n");
                fputs($ff,$time_t);

                // Преобразуем точку в запятую в показателях
                $time_t = str_replace('.', ',', $time_t);
                $oil = str_replace('.', ',', $oil);
                $fuel_92 = str_replace('.', ',', $fuel_92);
                $fuel_95 = str_replace('.', ',', $fuel_95);
                $fuel_df = str_replace('.', ',', $fuel_df);
                $fuel_g = str_replace('.', ',', $fuel_g);
                $zp = str_replace('.', ',', $zp);
                $zp_e = str_replace('.', ',', $zp_e);
                $zp_esv = str_replace('.', ',', round($esv * $zp_drive / 100, 2));
                $zp_d = str_replace('.', ',', $zp_drive);
                $cm = trim(str_replace('.', ',', $cm));
                $cm_tr = trim(str_replace('.', ',', $cm_tr));
                $amort = str_replace('.', ',', $amort);
            }

            if ($u1 == 'Оперативно-технічне обслуговування') {
                $cnt++;
                $res = mb_substr($z['contract'], 0, 2, "UTF-8");

                $time_t = $z['time_t'];        // Время проезда
                $time_prostoy = $z['time_prostoy'];        // Время простоя
                $time_work = $z['time_work'];             // Время работы
                $n_work = trim($z['n_work']);
                $norm_time = trim($z['norm_time']);
                $code_mvp=$z['mvp'];
                $sch=$z['schet'];
                $w = $z['work'];
                $verification = 0;  // Поверка

                $sql = "select a.zp,a.common_minus,a.time_transp,a.tmc,a.repair,a.usluga,a.other,
                    b.* from costwork a left join a_transport b on trim(a.work)=trim(b.number) 
                    where a.work=:search";
                $z1 = viewschet::findBySql($sql, [':search' => "$w"])->asArray()->all();

//                debug($sql);
//                return;

                $zp = (float)str_replace(',', '.', $z1[0]['zp']);
                $zp_e = round(0.22 * $zp, 2);
                $cm = $z1[0]['common_minus'];
                $cm_tr = 0;
                $time_prostoy = $z1[0]['time_transp']; // Время простоя
                $tmc = trim($z1[0]['tmc']); // ТМЦ
                $repair = $z1[0]['repair']; // Ремонты
                $other = $z1[0]['other'];   // Другие
                $tr_usl = 0;

                if (count($z1) > 0) {
                    $oil_p = $z1[0]['oil_p'];
                    $oil_move = $z1[0]['oil_move'];
                    $amort = $z1[0]['amort'];
                    $amort_move = $z1[0]['amort_move'];
                    $wage = $z1[0]['wage'];
                    $wage_move = $z1[0]['wage_move'];
                    $c92 = $z1[0]['cost_92_move'];
                    $c95 = $z1[0]['cost_95_move'];
                    $cdf = $z1[0]['cost_df_move'];
                    $cg = $z1[0]['cost_g_move'];

                    $c92w = $z1[0]['cost_92_work'];
                    $c95w = $z1[0]['cost_95_work'];
                    $cdfw = $z1[0]['cost_df_work'];
                    $cgw = $z1[0]['cost_g_work'];
                } else {
                    $oil = '0';
                    $amort = '0';
                    $wage = '0';
                    $c92 = '0';
                    $c95 = '0';
                    $cdf = '0';
                    $cg = '0';

                    $c92w = '0';
                    $c95w = '0';
                    $cdfw = '0';
                    $cgw = '0';

                }
                $esv = 22;


                // Транспорт проезд
                $fuel_92 = (float)str_replace(',', '.', $c92);
                $fuel_95 = (float)str_replace(',', '.', $c95);
                $fuel_df = (float)str_replace(',', '.', $cdf);
                $fuel_g = (float)str_replace(',', '.', $cg);
                $time_t = (float)str_replace(',', '.', $time_t);
                $oil_p = (float)str_replace(',', '.', $oil_p);
                $oil_move = (float)str_replace(',', '.', $oil_move);
                $amort = (float)str_replace(',', '.', $amort);
                $amort_move = (float)str_replace(',', '.', $amort_move);
                $wage = (float)str_replace(',', '.', $wage);
                $wage_move = (float)str_replace(',', '.', $wage_move);

                if ($tr_usl == 0)
                    $time_prostoy = (float)str_replace(',', '.', $time_prostoy);

                // Транспорт простой
                $oil_prostoy = round($oil_p * $time_prostoy, 2);
                $amort_prostoy = round($amort * $time_prostoy, 2);
                $zp_drive_prostoy = round($wage * $time_prostoy, 2);

                // --Подсчет итога--
                // Другие показатели
                $oil = $oil_move + $oil_prostoy;                 // Масло
                $amort = $amort_move + $amort_prostoy;           // Аммортизация
                $zp_drive = $wage_move + $zp_drive_prostoy;  // Зарплата водителей

                if ($time_t == 0) {
                    $fuel_92 = 0;
                    $fuel_95 = 0;
                    $fuel_df = 0;
                    $fuel_g = 0;
                    $oil = 0;
                    $zp_drive = 0;
                    $amort = 0;
                }

                if (($fuel_92 + $fuel_95 + $fuel_df + $fuel_g) > 0) $priz_proezd = 1;
                else $priz_proezd = 0;
                $time_t = $time_t + $time_prostoy;
                // Преобразуем точку в запятую в показателях
                $time_t = str_replace('.', ',', $time_t);
                $oil = str_replace('.', ',', $oil);
                $fuel_92 = str_replace('.', ',', $fuel_92);
                $fuel_95 = str_replace('.', ',', $fuel_95);
                $fuel_df = str_replace('.', ',', $fuel_df);
                $fuel_g = str_replace('.', ',', $fuel_g);
                $zp = str_replace('.', ',', $zp);
                $zp_e = str_replace('.', ',', $zp_e);
                $zp_esv = str_replace('.', ',', round($esv * $zp_drive / 100, 2));
                $zp_d = str_replace('.', ',', $zp_drive);
                $cm = trim(str_replace('.', ',', $cm));
                $amort = str_replace('.', ',', $amort);
            }

//        fputs($f,$tr_usl);
//        fputs($f,$priz_proezd);

            if($cnt==1)
            fputs($f, $hap);

            // Добавляем к зарплате сумму доставки бригады
            $zp = (float)str_replace(',', '.', $zp);
            $zp_e = (float)str_replace(',', '.', $zp_e);
            $zp = $zp+round($summa_delivery/1.22,2);
            $zp_e = $zp_e+round(round($summa_delivery/1.22,2)*0.22,2);
            $zp = str_replace('.', ',', $zp);
            $zp_e = str_replace('.', ',', $zp_e);

            if ($priz_proezd == 0 || $tr_usl == 1) {

                $e[0] = 'CK01';           // const
                $e[1] = $n_work;        // № услуги
                $e[2] = $code_mvp;    //  МВП пока const
                $e[3] = $tmc;        // ТМЦ
                $e[4] = $zp;        // Зарплата бригады
                $e[5] = $zp_e;     // Соц. взнос от зарплаты бригады
                $e[6] = 0;        // Коммандировки
                $e[7] = 0;       // Коммандировки
                $e[8] = 0;      // Коммандировки
                $e[9] = $fuel_92;        // топливо а-92
                $e[10] = $fuel_95;       // топливо а-95
                $e[11] = $fuel_df;       // топливо дт
                $e[12] = $fuel_g;        // топливо газ
                $e[13] = $oil;        // масло
                $e[14] = $zp_d;       // зп водителей
                $e[15] = $zp_esv;        // Соц. взнос от зарплаты водителей
                $e[16] = $amort;        // Аммортизация транспорт
                $e[17] = 0;        // Коммандировки
                $e[18] = 0;        // Коммандировки
                $e[19] = $verification;        // Поверка средств учета
                $e[20] = $other;        // other
                $e[21] = $repair;        // Ремонт
                $e[22] = $cm;        // Общепроизводственные затраты
                $e[23] = $sch;     // Счет
                $e[24] = '';       // № договора
                $e[25] = $norm_time;      // Нормативные трудозатраты бригады
                $e[26] = $time_t;        // Нормативные трудозатраты водителей
                fputs($f, "\n");
                $content = implode(";", $e);
                $content = mb_convert_encoding($content, 'CP1251', mb_detect_encoding($content));
                fputs($f, $content);
            } else {

                $e[0] = 'CK01';         // const
                $e[1] = $n_work;       // № услуги
                $e[2] = $code_mvp;    //  МВП пока const
                $e[3] = $tmc;        // ТМЦ
                $e[4] = $zp;        // Зарплата бригады
                $e[5] = $zp_e;     // Соц. взнос от зарплаты бригады
                $e[6] = 0;        // Коммандировки
                $e[7] = 0;       // Коммандировки
                $e[8] = 0;      // Коммандировки
                $e[9] = 0;        // топливо а-92
                $e[10] = 0;       // топливо а-95
                $e[11] = 0;       // топливо дт
                $e[12] = 0;        // топливо газ
                $e[13] = 0;        // масло
                $e[14] = 0;       // зп водителей
                $e[15] = 0;        // Соц. взнос от зарплаты водителей
                $e[16] = 0;        // Аммортизация транспорт
                $e[17] = 0;        // Коммандировки
                $e[18] = 0;        // Коммандировки
                $e[19] = 0;        // Поверка средств учета
                $e[20] = $other;        // other
                $e[21] = $repair;        // Ремонт
                $e[22] = $cm;        // Общепроизводственные затраты
                $e[23] = $sch;     // Счет
                $e[24] = '';       // № договора
                $e[25] = $norm_time;      // Нормативные трудозатраты бригады
                $e[26] = 0;        // Нормативные трудозатраты водителей
                fputs($f, "\n");
                $content = implode(";", $e);
                $content = mb_convert_encoding($content, 'CP1251', mb_detect_encoding($content));
                fputs($f, $content);


                $e[0] = 'CK01';         // const
                if ($tr_usl == 0)
                    $e[1] = '2300000380';       // № услуги
                else
                    $e[1] = '2300000385';       // № услуги

                $e[2] = $code_mvp;    //  МВП пока const
                $e[3] = 0;        // ТМЦ
                $e[4] = 0;        // Зарплата бригады
                $e[5] = 0;     // Соц. взнос от зарплаты бригады
                $e[6] = 0;        // Коммандировки
                $e[7] = 0;       // Коммандировки
                $e[8] = 0;      // Коммандировки
                $e[9] = $fuel_92;        // топливо а-92
                $e[10] = $fuel_95;       // топливо а-95
                $e[11] = $fuel_df;       // топливо дт
                $e[12] = $fuel_g;        // топливо газ
                $e[13] = $oil;        // масло
                $e[14] = $zp_d;       // зп водителей
                $e[15] = $zp_esv;        // Соц. взнос от зарплаты водителей
                $e[16] = $amort;        // Аммортизация транспорт
                $e[17] = 0;        // Коммандировки
                $e[18] = 0;        // Коммандировки
                $e[19] = 0;        // Поверка средств учета
                $e[20] = 0;        // other
                $e[21] = 0;        // Ремонт
                $e[22] = $cm_tr;        // Общепроизводственные затраты
                $e[23] = $sch;     // Счет
                $e[24] = '';       // № договора
                $e[25] = 0;      // Нормативные трудозатраты бригады
                $e[26] = $time_t;        // Нормативные трудозатраты водителей
                fputs($f, "\n");
                $content = implode(";", $e);
                $content = mb_convert_encoding($content, 'CP1251', mb_detect_encoding($content));
                fputs($f, $content);

            }
        }
//        debug($e);
//        return;

        if (file_exists($fn)) {
            return \Yii::$app->response->sendFile($fn);
        }
        else{
            $model = new info();
            $model->title = 'УВАГА!';
            $model->info1 = "Помилка при формуванні файлу експорту в САП.";
            $model->style1 = "d15";
            $model->style2 = "info-text";
            $model->style_title = "d9";

            return $this->render('info', [
                'model' => $model]);
        }
    }

    // Формирование акта выполненных работ
    public function actionAct_work()
    {
        $sch = Yii::$app->request->post('sch');
        $mail = Yii::$app->request->post('mail');
        $sch1 = Yii::$app->request->post('sch1');
        if(empty($sch1)) $sch1='0';
        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
                . ' where a.res=b.nazv and schet=:search';
//        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
//                . ' where a.res=b.nazv and (schet=:search or cast(schet as dec(10,0)) in ('.$sch1."))";
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->asArray()->all();
        $q=count($model);
        $total_beznds=0;
        $total=0;
        for ($i = 0; $i < $q; $i++)
        { $total+= $model[$i]['summa'];
          $total_beznds+= $model[$i]['summa_beznds'];}

        return $this->render('act_work',['model' => $model,'style_title' => 'd9','mail' => $mail,
            'q' => $q,'total' => $total,'total_beznds' => $total_beznds,'sch' => $sch,'sch1' => $sch1]);
    }

    // Формирование договора
    public function actionContract()
    {
        $sch = Yii::$app->request->post('sch');
        $mail = Yii::$app->request->post('mail');
        $sch1 = Yii::$app->request->post('sch1');

//        debug($sch);
//        return;

        if(empty($sch1)) $sch1='0';
        if(!isset(Yii::$app->user->identity->role))
        {      $flag=0;}
        else {
            $role = Yii::$app->user->identity->role;
        }

        if($role<>11) {
            $sql = 'select distinct a.*,b.Director,b.parrent_nazv,b.mail,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.exec_person else c.exec_person end as exec_person,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.exec_person_pp else c.exec_person_pp end as exec_person_pp,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.exec_post else c.exec_post end as exec_post,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.exec_post_pp else c.exec_post_pp end as exec_post_pp,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.assignment else c.assignment end as assignment,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.date_assignment else c.date_assignment end as date_assignment, 
               c.usluga as usl
                from vschet a left join spr_res b on a.res=b.nazv
                left join costwork d on a.usluga=d.work 
                left join spr_uslug c on c.usluga=d.usluga 
                left join spr_uslug e on 1=1 and e.id=17'.
                ' where schet=:search or cast(schet as dec(10,0)) in ('.$sch1.")" .' limit 1';
        }
        else
        {
            $sql = 'select distinct a.*,b.Director,b.parrent_nazv,b.mail,'
                . 'c.exec_person,c.exec_person_pp,c.exec_post,c.exec_post_pp,c.assignment,c.date_assignment,c.usluga as usl'
                . ' from vschet a,spr_res b,spr_uslug c,costwork d'
                . ' where a.res=b.nazv and a.usluga=d.work '
                . ' and c.id=14'
                . ' and (schet=:search or cast(schet as dec(10,0)) in ('.$sch1."))".' limit 1';
        }
        $model1 = viewschet::findBySql($sql,[':search'=>"$sch"])->asArray()->all();

        $model = new InputData();
        if ($model->load(Yii::$app->request->post()))
        {
//            debug($model);
//            return;
            // Меняем в базе № договора
            $sql = 'select * from schet where schet=:search limit 1';

            $model2 = schet::findBySql($sql,[':search'=>"$model->sch"])->one();
//            debug($model2);
//            return;
            $model2->contract=$model->n_cnt;
            $model2->save(false);

            return $this->redirect([ 'create_contract','n_cnt' => $model->n_cnt,'sch' => $model->sch,'mail' => $model->mail,'sch1' => $model->sch1]);
        }
        else {
             return $this->render('input_contract', [
                'model' => $model, 'model1' => $model1,'sch' => $sch,'mail' => $mail,'sch1' => $sch1
            ]);
        }

    }

     public function actionCreate_contract($n_cnt,$sch,$mail,$sch1)
     {  $sch = Yii::$app->request->get('sch');
         $mail = Yii::$app->request->get('mail');
         $sch1 = Yii::$app->request->get('sch1');

         if(empty($sch1)) $sch1='0';
         if(!isset(Yii::$app->user->identity->role))
         {      $flag=0;}
         else {
             $role = Yii::$app->user->identity->role;
         }

         if($role<>11) {
             $sql = 'select distinct a.*,b.Director,b.parrent_nazv,b.mail,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.exec_person else c.exec_person end as exec_person,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.exec_person_pp else c.exec_person_pp end as exec_person_pp,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.exec_post else c.exec_post end as exec_post,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.exec_post_pp else c.exec_post_pp end as exec_post_pp,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.assignment else c.assignment end as assignment,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.date_assignment else c.date_assignment end as date_assignment, 
               c.usluga as usl
                from vschet a left join spr_res b on a.res=b.nazv
                left join costwork d on a.usluga=d.work 
                left join spr_uslug c on c.usluga=d.usluga 
                left join spr_uslug e on 1=1 and e.id=17'.
                 ' where schet=:search or cast(schet as dec(10,0)) in ('.$sch1.")" .' limit 1';
         }
         else
         {
             $sql = 'select distinct a.*,b.Director,b.parrent_nazv,b.mail,'
                 . 'c.exec_person,c.exec_person_pp,c.exec_post,c.exec_post_pp,c.assignment,c.date_assignment,c.usluga as usl'
                 . ' from vschet a,spr_res b,spr_uslug c,costwork d'
                 . ' where a.res=b.nazv and a.usluga=d.work '
                 . ' and c.id=14'
                 . ' and (schet=:search or cast(schet as dec(10,0)) in ('.$sch1."))".' limit 1';
         }
         $model = viewschet::findBySql($sql,[':search'=>"$sch"])->asArray()->all();

//         debug($model);
//        return;

         $q=count($model);
         $total_beznds=0;
         $total=0;
         for ($i = 0; $i < $q; $i++)
         { $total+= $model[$i]['summa'];
             $total_beznds+= $model[$i]['summa_beznds'];}
         $model[0]['usluga']=del_brackets($model[0]['usluga']);

         return $this->render('contract',['model' => $model,'style_title' => 'd9','mail' => $mail,'n_cnt' => $n_cnt,
             'q' => $q,'total' => $total,'total_beznds' => $total_beznds,'sch' => $sch,'sch1' => $sch1]);
     }

    // Формирование инф. сообщения
    public function actionMessage()
    {
        $sch = Yii::$app->request->post('sch');
        $mail = Yii::$app->request->post('mail');
        $sch1 = Yii::$app->request->post('sch1');
        if(empty($sch1)) $sch1='0';

        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
            . ' where a.res=b.nazv and (schet=:search or cast(schet as dec(10,0)) in ('.$sch1."))";
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->asArray()->all();

        $q=count($model);
        $total_beznds=0;
        $total=0;
        for ($i = 0; $i < $q; $i++)
        { $total+= $model[$i]['summa'];
          $total_beznds+= $model[$i]['summa_beznds'];}
        return $this->render('message',['model' => $model,'style_title' => 'd9','mail' => $mail,
            'q' => $q,'total' => $total,'total_beznds' => $total_beznds,'sch' => $sch,'sch1' => $sch1]);
    }

    // Формирование инф. сообщения для исполнителя
    public function actionInfo_exec()
    {
        $sch = Yii::$app->request->post('sch');
        $mail = Yii::$app->request->post('mail');
        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
            . ' where a.res=b.nazv and schet=:search';
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->one();
        return $this->render('info_exec',['model' => $model,'style_title' => 'd9','mail' => $mail]);
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

    // Просмотр счетов (заявок)
    public function actionViewschet($item='')
    {
        $searchModel = new viewschet();
        $this->adm=1; // Признак отображения логотипа (если 1 - то не отображается)
        $flag=1;
        $role=0;
        if(!isset(Yii::$app->user->identity->role))
        {      $flag=0;}
        else{
            $role=Yii::$app->user->identity->role;
        }

        switch($role) {
             case 5: // Полный доступ (тайный советник)
                $data = $searchModel::find()->orderBy(['status' => SORT_ASC])->all();
                 $last = schet::findBySql(
                     'select  max(date_edit) as date_edit from schet')
                     ->all();
                 $last_r = $last[0]->date_edit;
                break;
             case 3: // Полный доступ админ
                $data = $searchModel::find()->orderBy(['status' => SORT_ASC])->all();
                 $last = schet::findBySql(
                     'select  max(date_edit) as date_edit from schet')
                     ->all();
                 $last_r = $last[0]->date_edit;
                break;
             case 2:  // финансовый отдел
                $data = $searchModel::find()->where('status=:status',[':status' => 2])->
                orderBy(['status' => SORT_ASC])->all();
                 $last = schet::findBySql(
                     'select  max(date_edit) as date_edit from schet where status=:status',[':status'=>3])
                     ->all();
                 $last_r = $last[0]->date_edit;
                break;
             case 1:  // бухгалтерия
                $data = $searchModel::find()->where('status=:status',[':status' => 5])->
                orderBy(['status' => SORT_ASC])->all();
                 $last = schet::findBySql(
                     'select  max(date_edit) as date_edit from schet where status=:status',[':status'=>5])
                     ->all();
                 $last_r = $last[0]->date_edit;
                break;
             case 11: // Днепр РЭС
                $data = $searchModel::find()->where('res=:res',[':res' => 'Дніпропетровські РЕМ'])
                     ->orderBy(['status' => SORT_ASC])->all();
                 $last = schet::findBySql(
                     'select  max(date_edit) as date_edit from schet where res=:res',[':res'=>'Дніпропетровські РЕМ'])
                     ->all();
                 $last_r = $last[0]->date_edit;

                 $sql="update schet set status=8 WHERE date<(now()- INTERVAL 370 DAY) and status<3
                          and res='Дніпропетровські РЕМ'";

                $data = \Yii::$app->db->createCommand($sql)->execute();;
                break;
             case 12: // Гвардейские РЭС
                $data = $searchModel::find()->where('res=:res',[':res' => 'Гвардійська дільниця'])
                    ->orderBy(['status' => SORT_ASC])->all();
                 $last = schet::findBySql(
                     'select  max(date_edit) as date_edit from schet where res=:res',[':res'=>'Гвардійська дільниця'])
                     ->all();
                 $last_r = $last[0]->date_edit;
//                 $sql="update schet set status=8 WHERE date<(now()- INTERVAL 30 DAY) and status<3
//                          and res='Гвардійська дільниця'";

//                 $data = \Yii::$app->db->createCommand($sql)->execute();

                break;
            case 13: // Криворізькі РЕМ
                $data = $searchModel::find()->where("res='Криворізькі РЕМ' or res = 'Інгулецька дільниця' or res = 'Апостолівська дільниця'")
                        ->orderBy(['status' => SORT_ASC])->all();
                $last = schet::findBySql(
                    'select  max(date_edit) as date_edit from schet where res=:res',[':res'=>'Криворізькі РЕМ'])
                    ->all();

//                $sql="update schet set status=8 WHERE date<(now()- INTERVAL 30 DAY) and status<3
//                          and res in('Криворізькі РЕМ','Інгулецька дільниця','Апостолівська дільниця')";

//                $data = \Yii::$app->db->createCommand($sql)->execute();

                $last_r = $last[0]->date_edit;
                break;
            case 14: // Павлоградські РЕМ
                $data = $searchModel::find()->where('res=:res',[':res' => 'Павлоградські РЕМ'])
                    ->orderBy(['status' => SORT_ASC])->all();
                $last = schet::findBySql(
                    'select  max(date_edit) as date_edit from schet where res=:res',[':res'=>'Павлоградські РЕМ'])
                    ->all();
                $last_r = $last[0]->date_edit;

//                $sql="update schet set status=8 WHERE date<(now()- INTERVAL 30 DAY) and status<3
//                          and res='Павлоградські РЕМ'";
//
//                $data = \Yii::$app->db->createCommand($sql)->execute();

                break;
            case 15: // Вілногірські РЕМ
                $data = $searchModel::find()->where('res=:res',[':res' => 'Вілногірські РЕМ'])
                    ->orderBy(['status' => SORT_ASC])->all();
                $last = schet::findBySql(
                    'select  max(date_edit) as date_edit from schet where res=:res',[':res'=>'Вілногірські РЕМ'])
                    ->all();
                $last_r = $last[0]->date_edit;

                $sql="update schet set status=8 WHERE date<(now()- INTERVAL 30 DAY) and status<3
                          and res='Вілногірські РЕМ'";

                $data = \Yii::$app->db->createCommand($sql)->execute();
                break;
            case 16: // Жовтоводські РЕМ
                $data = $searchModel::find()->where('res=:res',[':res' => 'Жовтоводські РЕМ'])
                    ->orderBy(['status' => SORT_ASC])->all();
                $last = schet::findBySql(
                    'select  max(date_edit) as date_edit from schet where res=:res',[':res'=>'Жовтоводські РЕМ'])
                    ->all();
                $last_r = $last[0]->date_edit;
                $sql="update schet set status=8 WHERE date<(now() - INTERVAL 220 DAY) and status<3
                          and res='Жовтоводські РЕМ'";

                $data = \Yii::$app->db->createCommand($sql)->execute();
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
            'model' => $searchModel,'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'role' => $role,'last' => $last_r
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
    public function actionGetworks($id,$res,$calc_ind) {
    Yii::$app->response->format = Response::FORMAT_JSON;
     $calc_ind--;
    if (Yii::$app->request->isAjax) {
        $usluga = Calc::find()->select(['usluga'])->where('id=:id',[':id' => $id])->all();
        $usl = $usluga[0]->usluga;

        if(empty($usl))
        $sql = "Select cast(min(id) as char(3)) as nomer,concat(cast(min(id) as char(3)),'  ',trim(work),'  ',cast(cast_4 as char(10))) as work "
                . "from costwork where calc_ind=$calc_ind group by work,cast_4 ";
        else
        {
            switch($usl) {
                case "Послуги з технічного обслуговування об'єктів":
                    $usl1 = "Послуги з технічного обслуговування об";
                    $sql = "Select min(id) as nomer,concat(cast(min(id) as char(3)),'  ',trim(work),'  ',cast(cast_4 as char(10))) as work "
                        . "from costwork where usluga like " . "'%" . $usl1 . "%'" . " group by work,cast_4";
                    break;
                case "Оперативно-технічне обслуговування":
                    $usl1 = "Оперативно-технічне обслуговування";
//                    $sql = "Select min(id) as nomer,concat(cast(min(id) as char(3)),'  ',trim(work)) as work "
//                        . "from costwork where usluga like " . "'%" . $usl1 . "%'" .
//                        ' and rem='.$res." group by work";

                    $sql = "Select min(id) as nomer,concat(cast(min(id) as char(3)),'  ',trim(work)) as work "
                        . "from costwork where usluga like " . "'%" . $usl1 . "%'" .
                        " group by work";

//                    debug($sql);
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
                         IF(b.rabota is null,' -','  '),trim(a.work),'  ',cast(a.cast_4 as char(10)),' ','грн.') as work "  // - нет данных в поле rabota< , + есть данные
                        . "from costwork a inner join transport b on a.$r=b.nomer
                        where a.usluga =" . "'" . $usl . "'"
                        . " and b.locale=$res and a.calc_ind=$calc_ind group by a.work,b.rabota,a.cast_4";
                        //. " and id_res=".$res." group by work";

                    break;
                default:
                        $sql = "Select min(id) as nomer,concat(cast(min(id) as char(3)),
                        '  ',trim(work),'  ',cast(cast_4 as char(10)),' ','грн.') as work "
                        . "from costwork where usluga =" . "'" . $usl . "'" . " and calc_ind=$calc_ind group by work,cast_4";
            }

        }
//        var_dump($sql);
        $works = Calc::findBySql($sql)->all();
        return ['success' => true, 'works' => $works,'usl' => $usl];
    }
    return ['oh no' => 'you are not allowed :('];
    }

    // Подгрузка видов подразделений - происходит при выборе РЭСа
    public function actionGetmvp($res) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {

            $sql = "Select id as nomer,concat(cast(id as char(3)),'  ',trim(descr)) as descr "
                . "from sprav_mvp where code_podr =" .  $res  ;

            $mvp = Calc::findBySql($sql)->asarray()->all();
//            debug($mvp);
            return ['success' => true, 'mvp' => $mvp];
        }
        return ['oh no' => 'you are not allowed :('];
    }


    // Подгрузка видов работ - происходит при выборе услуги (применяется в аналитике)
    public function actionGetworks1($id,$res) {
    Yii::$app->response->format = Response::FORMAT_JSON;
    if (Yii::$app->request->isAjax) {
        $usluga = Calc::find()->select(['usluga'])->where('id=:id',[':id' => $id])->all();
        $usl = $usluga[0]->usluga;

        if(empty($usl))
        $sql = "Select cast(min(id) as char(3)) as nomer,concat(cast(min(id) as char(3)),'  ',trim(work)) as work "
                . "from costwork group by work ";
        else
        {
            switch($usl) {
                case "Послуги з технічного обслуговування об'єктів":
                    $usl1 = "Послуги з технічного обслуговування об";
                    $sql = "Select min(id) as nomer,concat(cast(min(id) as char(3)),'  ',trim(work)) as work "
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
                         IF(b.rabota is null,' -','  '),trim(a.work)) as work "  // - нет данных в поле rabota< , + есть данные
                        . "from costwork a inner join transport b on a.$r=b.nomer
                        where a.usluga =" . "'" . $usl . "'"
                        . " and b.locale=$res group by a.work,b.rabota";
                        //. " and id_res=".$res." group by work";

                    break;
                default:
                        $sql = "select 1000 as id,'    ' as work from costwork union Select min(id) as nomer,concat(cast(min(id) as char(3)),'  ',trim(work)) as work "
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

// Подгрузка какой используется транспорт - происходит при вводе вида работы
    public function actionGettransp_cek($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $idata = spr_work::find()->select(['transp_cek'])
                ->where('id=:id',[':id' => $id])->all();

                $transp_cek = $idata[0]->transp_cek;

            return ['success' => true, 'transp_cek' => $transp_cek];

        }
        return ['oh no' => 'you are not allowed :('];
    }

// Определяем расстояние по дороге от базы до объекта - происходит при нажатии на карту (
// с ресурса (GoogleMAp)
     public function actionGetdist($url,$origins,$destinations) {

    Yii::$app->response->format = Response::FORMAT_JSON;
    if (Yii::$app->request->isAjax) {
        $destinations = str_replace(' ', '', $destinations);
        //$destinations = str_replace('%20', '', $destinations);
        $url = $url . '&origins='.$origins.'&destinations='.$destinations;
        $url = $url . '&language=ru&region=UA';
        $url = $url . '&key=AIzaSyDSyQ_ATqeReytiFrTiqQAS9FyIIwuHQS4';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output,true);

        return ['success' => true, 'output' => $output];
    }
    }

    // Определяем расстояние по дороге от базы до объекта - происходит при нажатии на карту
    // с ресурса (calc-api.ru)
    public function actionGetdist_calc($url) {

        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            //$destinations = str_replace(' ', '', $destinations);
            //$destinations = str_replace('%20', '', $destinations);
//            $url = $url . '&origins='.$origins.'&destinations='.$destinations;
//            $url = $url . '&language=ru&region=UA';
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

    // Настройки программы
    public function actionSettings()
    {
        $model = new settings();
        $session = Yii::$app->session;
        if($session->has('contract_hap'))
             $model->contract_hap = $session->get('contract_hap');
        else
             $model->contract_hap = 1;
         if ($model->load(Yii::$app->request->post())) {
            $session->open();
            $session->set('contract_hap', $model->contract_hap);
            $this->goBack();
         }
        return $this->render('settings', [
            'model' => $model]);
    }

    //    Сброс в Excel результатов рассчета
    public function actionExcel($kind,$nazv,$rabota,$delivery,$transp,$all,$nds,$all_nds,$nazv1)
    {

        $k1='Результат розрахунку для послуги: '.$nazv;
        $param = 0;
        $model = new forExcel();
        $model->nazv = $nazv;
        if(!empty($nazv1)) $k1=$k1.'.  Замовник'.'  '.$nazv1.'.';
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
        $flag=1;
        $role=0;
        if(!isset(Yii::$app->user->identity->role))
        {      $flag=0;}
        else{
            $role=Yii::$app->user->identity->role;
        }
        if($mod=='schet')
            $model = viewschet::find()->where('id=:id',[':id'=>$id])->one();
            $nazv = $model->schet;
            $inn = $model->inn;
            $res = $model->res;
            $usl = $model->usluga;
            $res1 = mb_substr($model->contract, 0, 2, "UTF-8");

            if(!empty($model->date))
                $model->date = date("d.m.Y", strtotime($model->date));

            if(!empty($model->date_z))
                $model->date_z = date("d.m.Y", strtotime($model->date_z));

//      Определяем данные исполнительной службы
        $usluga = spr_work::find()->select('usluga')->where('work=:usl',[':usl' => $usl])->all();
        if(isset($usluga[0]->usluga)) {
            $usluga = $usluga[0]->usluga;

        }
        else {
            $usluga = '';

        }

        $pole = viewschet::tr_res($res1);  // Определение поля с данными по автомобилю

        $sql = "select $pole as nomer from costwork a where a.work=:search and $pole is not null";
        $z1 = viewschet::findBySql($sql, [':search' => "$usl"])->asArray()->all();
        if (count($z1) > 0)
            $nomer = $z1[0]['nomer'];
        else
            $nomer = '';

        $z1 = viewschet::findBySql($sql, [':search' => "$usl"])->asArray()->all();
        if (count($z1) > 0)
            $nomer = $z1[0]['nomer'];
        else
            $nomer = '';

        $exec = spr_uslug::find()->select('exec_office')->where('usluga=:usluga',[':usluga' => $usluga])->all();
        if(isset($exec[0]->exec_office))
             $exec = $exec[0]->exec_office;
        else
             $exec = 'РЕМ';

        if($exec=='СД'){
            $town = spr_res::find()->select('town_fromwhere_sd')->where('nazv=:nazv',[':nazv' => $res])->all();
            $town_sd = $town[0]->town_fromwhere_sd;
            if(!empty($town_sd))
                $data_res = spr_res::find()->select('id,mail')->where(['like', 'town', "$town_sd"])->all();
            else
                $data_res = spr_res::find()->select('id,mail')->where('nazv=:nazv',[':nazv' => $res])->all();
        }
        if($exec=='СЗ'){
            $town = spr_res::find()->select('town_fromwhere_sz')->where('nazv=:nazv',[':nazv' => $res])->all();
            $town_sz = $town[0]->town_fromwhere_sz;
            if(!empty($town_sz))
                $data_res = spr_res::find()->select('id,mail')->where(['like', 'town', "$town_sz"])->all();
            else
                $data_res = spr_res::find()->select('id,mail')->where('nazv=:nazv',[':nazv' => $res])->all();
        }
        if($exec=='РЕМ' || $exec=='ТР'){
           $data_res = spr_res::find()->select('id,mail')->where('nazv=:nazv',[':nazv' => $res])->all();
        }
        //$mail = $data_res[0]->mail;
        $id_res = $data_res[0]->id;

        if($id_res==4)
            $data_koord = vspr_res_koord::find()->where('id_res=:id',[':id' => $id_res])->
                andwhere('type_usl=:exec',[':exec' => $exec])->all();
        else
            $data_koord = vspr_res_koord::find()->where('id_res=:id',[':id' => $id_res])->all();
        $mail = $data_koord[0]->email;


        if ($model->load(Yii::$app->request->post()))
        {
            $model1 = schet::find()->where('id=:id',[':id'=>$id])->one();
            $model1->status = $model->status;
            $model1->read_z = $model->read_z;
            $model1->adres = $model->adres;
            $model1->date_edit = date("Y-m-d");
            $model1->why_refusal = $model->why_refusal;
            if(!empty($model->date_z))
                $model1->date_z = date("Y-m-d", strtotime($model->date_z));

            if(!empty($model->date_opl))
                 $model1->date_opl = date("Y-m-d", strtotime($model->date_opl));
            else
                 $model1->date_opl = null;

            if(!empty($model->date_exec))
                $model1->date_exec = date("Y-m-d", strtotime($model->date_exec));
            if(!empty($model->date_akt))
                $model1->date_akt = date("Y-m-d", strtotime($model->date_akt));
            if(!empty($model->act_work))
                $model1->act_work = $model->act_work;
            $model1->comment = $model->comment;

             $union = $model->union_sch;

            if(!empty($union)){
                $u_mas=explode(",", $union);
                $union_z='';
                foreach($u_mas as $v){
                        $w1 = 0+str_replace('"','',$v);
                        debug($w1);
                        $union_z.=$w1.',';
                }
                $union_z=substr($union_z,0,strlen($union_z)-1);
                $model1->union_sch=$union_z;
            }
            if(empty($union)){
                 $model1->union_sch='';
            }
            if($model->status==5)
            {
               // Создаем № акта выполненных работ, если меняется статус заявки на выполненную
//                if(empty($model->act_work)) {
//                    $sql = 'select max(cast(act_work as unsigned)) as act_work from schet';
//                    $sch = schet::findBySql($sql)->one();
//                    $s = $sch->act_work+1;
//                    $model1->act_work = $s;
//                    $model1->date_akt = date('Y-m-d');
//                }

            }
            if(!$model1->save(false))
            {  var_dump($model1);return;}

            if($mod=='schet')
                return $this->redirect(['site/viewschet']);

        } else {
//            debug($model);
//            return;
            if($mod=='schet')
                if($role<>2)
                    return $this->render('update_schet', [
                        'model' => $model,'nazv' => $nazv,'mail'=> $mail,'data_koord' => $data_koord,'nomer'=>$nomer
                    ]);
                else
                    return $this->render('update_schet_opl', [
                        'model' => $model,'nazv' => $nazv,'mail'=> $mail,'data_koord' => $data_koord,'nomer'=>$nomer
                    ]);
        }
    }

    //    Распечатка акта выполненных работ
    public function actionAct_print(){
        date_default_timezone_set('Europe/Kiev');
        $sch = Yii::$app->request->post('sch');
        $sch1 = Yii::$app->request->post('sch1');
        if(empty($sch1)) $sch1='0';
        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
                . ' where a.res=b.nazv and schet=:search';
//        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
//                . ' where a.res=b.nazv and (schet=:search or cast(schet as dec(10,0)) in ('.$sch1."))";
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->asArray()->all();
        $q=count($model);
        $total_beznds=0;
        $total=0;
        for ($i = 0; $i < $q; $i++)
        { $total+= $model[$i]['summa'];
          $total_beznds+= $model[$i]['summa_beznds'];}

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $this->renderPartial('act_work_print',['model' => $model,'style_title' => 'd9',
                'q' => $q,'total' => $total,'total_beznds' => $total_beznds,'sch' => $sch,'sch1' => $sch1]),
            'options' => [
                'title' => 'Друк акту виконаних робіт',
                'subject' => ''
            ],
            'methods' => [
//                'SetHeader' => ['Створено для печаті: ' . date("d.m.Y H:i:s")],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);
        return $pdf->render();
    }

    //    Распечатка инф. сообщения
    public function actionMessage_print(){
        date_default_timezone_set('Europe/Kiev');
        $sch = Yii::$app->request->post('sch');
       $sch1 = Yii::$app->request->post('sch1');
        if(empty($sch1)) $sch1='0';

        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
            . ' where a.res=b.nazv and (schet=:search or cast(schet as dec(10,0)) in ('.$sch1."))";
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->asArray()->all();

        $q=count($model);
        $total_beznds=0;
        $total=0;
        for ($i = 0; $i < $q; $i++)
        { $total+= $model[$i]['summa'];
          $total_beznds+= $model[$i]['summa_beznds'];}

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $this->renderPartial('message_print',['model' => $model,'style_title' => 'd9',
                'q' => $q,'total' => $total,'total_beznds' => $total_beznds,'sch' => $sch,'sch1' => $sch1]),
            'options' => [
                'title' => 'Друк повідомлення',
                'subject' => ''
            ],
            'methods' => [
//                'SetHeader' => ['Створено для печаті: ' . date("d.m.Y H:i:s")],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);
        return $pdf->render();
    }

     //    Распечатка первоначального инф. сообщения для исполнителя
    public function actionInfo_exec_print(){
        date_default_timezone_set('Europe/Kiev');
        $sch = Yii::$app->request->post('sch');
        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
            . ' where a.res=b.nazv and schet=:search';
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->one();

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $this->renderPartial('info_exec_print',['model' => $model,'style_title' => 'd9']),
            'options' => [
                'title' => 'Друк повідомлення',
                'subject' => ''
            ],
            'methods' => [
//                'SetHeader' => ['Створено для печаті: ' . date("d.m.Y H:i:s")],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);
        return $pdf->render();
    }

    //    Распечатка типового договора
    public function actionTypical_contract_print(){
        date_default_timezone_set('Europe/Kiev');

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $this->renderPartial('typical_contract_print'),
            'options' => [
                'title' => 'Друк договора',
                'subject' => ''
            ],
            'methods' => [
//                'SetHeader' => ['Створено для печаті: ' . date("d.m.Y H:i:s")],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);
        return $pdf->render();
    }


    //    Распечатка договора
    public function actionContract_print(){
        date_default_timezone_set('Europe/Kiev');
        $sch = Yii::$app->request->post('sch');
        $sch1 = Yii::$app->request->post('sch1');
        $n_cnt = Yii::$app->request->post('n_cnt');

        if(empty($sch1)) $sch1='0';
        if(!isset(Yii::$app->user->identity->role))
        {      $flag=0;}
        else {
            $role = Yii::$app->user->identity->role;
        }
//        debug($role);
//        return;
        if($role<>11) {
//            $sql = 'select distinct a.*,b.Director,b.parrent_nazv,b.mail,'
//                . 'c.exec_person,c.exec_person_pp,c.exec_post,c.exec_post_pp,c.assignment,c.date_assignment'
//                . ' from vschet a,spr_res b,spr_uslug c,costwork d'
//                . ' where a.res=b.nazv and a.usluga=d.work '
//                . ' and c.usluga=d.usluga'
//                . ' and (schet=:search or cast(schet as dec(10,0)) in (' . $sch1 . "))";
           $sql = 'select distinct a.*,b.Director,b.parrent_nazv,b.mail,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.exec_person else c.exec_person end as exec_person,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.exec_person_pp else c.exec_person_pp end as exec_person_pp,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.exec_post else c.exec_post end as exec_post,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.exec_post_pp else c.exec_post_pp end as exec_post_pp,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.assignment else c.assignment end as assignment,
                case when a.res in ("СДІЗП","СЗОЕ","СЦ","СПС") then e.date_assignment else c.date_assignment end as date_assignment, 
               c.usluga as usl
                from vschet a left join spr_res b on a.res=b.nazv
                left join costwork d on a.usluga=d.work 
                left join spr_uslug c on c.usluga=d.usluga 
                left join spr_uslug e on 1=1 and e.id=17'.
                ' where schet=:search or cast(schet as dec(10,0)) in ('.$sch1.")" .' limit 1';
        }
        else
        {
            $sql = 'select distinct a.*,b.Director,b.parrent_nazv,b.mail,'
                . 'c.exec_person,c.exec_person_pp,c.exec_post,c.exec_post_pp,c.assignment,c.date_assignment,c.usluga as usl'
                . ' from vschet a,spr_res b,spr_uslug c,costwork d'
                . ' where a.res=b.nazv and a.usluga=d.work '
                . ' and c.id=14'
                . ' and (schet=:search or cast(schet as dec(10,0)) in (' . $sch1 . "))".' limit 1';
        }


        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->asArray()->all();

        $q=count($model);
        $total_beznds=0;
        $total=0;
        for ($i = 0; $i < $q; $i++)
        { $total+= $model[$i]['summa'];
          $total_beznds+= $model[$i]['summa_beznds'];}
        $model[0]['usluga']=del_brackets($model[0]['usluga']);
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $this->renderPartial('contract_print',['model' => $model,'style_title' => 'd9','n_cnt' => $n_cnt,
                'q' => $q,'total' => $total,'total_beznds' => $total_beznds]),
            'options' => [
                'title' => 'Друк договора',
                'subject' => ''
            ],
            'methods' => [
//                'SetHeader' => ['Створено для печаті: ' . date("d.m.Y H:i:s")],
                'SetHeader' => [''],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);
        return $pdf->render();
    }

    //    Распечатка счета
    public function actionSch_print(){
        date_default_timezone_set('Europe/Kiev');
        $sch = Yii::$app->request->post('sch');
        $sch1 = Yii::$app->request->post('sch1');
        if(empty($sch1)) $sch1='0';
         $sql = "select * from vschet where schet=:search";
        //$sql = "select * from vschet where (schet=:search or cast(schet as dec(10,0)) in (".$sch1."))";
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->asArray()->all();
//        debug($model);
//        return;
        $q=count($model);
        $total=0;
        for ($i = 0; $i < $q; $i++)
            $total += $model[$i]['summa'];

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8 , // leaner size using standard fonts
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'content' => $this->renderPartial('sch_opl_print',['model' => $model,'style_title' => 'd9','q' => $q,'total' => $total]),
            'options' => [
                'title' => 'Друк рахунку',
                'subject' => ''
            ],
            'methods' => [
//                'SetHeader' => ['Створено для печаті: ' . date("d.m.Y H:i:s")],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);
        return $pdf->render();
    }

//  Отправка счета по Email
    public function actionSch_email(){
        $sch = Yii::$app->request->post('sch');
        $email = Yii::$app->request->post('email');
        $sch1 = Yii::$app->request->post('sch1');
        if(empty($sch1)) $sch1='0';
        $sql = "select * from vschet where schet=:search";
        //$sql = "select * from vschet where (schet=:search or cast(schet as dec(10,0)) in (".$sch1."))";
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->asArray()->all();
//        debug($model);
//        return;
        $q=count($model);
        $total=0;
        for ($i = 0; $i < $q; $i++)
            $total += $model[$i]['summa'];

        $email2 = "oneclick@cek.dp.ua";
        $content=$this->renderPartial('sch_opl_print',['model' => $model,'style_title' => 'd9','q' => $q,'total' => $total]);
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
            ->setSubject("[1click] Рахунок за послуги від ПрАТ «ПЕЕМ «ЦЕК» №$sch відправлено")
            ->setHtmlBody("Рахунок за послуги від ПрАТ «ПЕЕМ «ЦЕК» №$sch відправлено.")
            ->attach('./schet.pdf')
            ->send();

        // Запись признака в статус заявки, что заявка в обработке (status=2)
        $sql = 'select * from schet where schet=:search';
        $data = schet::findBySql($sql,[':search'=>"$sch"])->one();

        if($data->status<2)
            $data->status = 2;
        if (!empty($model->date))
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

       $sch1 = Yii::$app->request->post('sch1');
        if(empty($sch1)) $sch1='0';
        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
                . ' where a.res=b.nazv and schet=:search';
//        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
//                . ' where a.res=b.nazv and (schet=:search or cast(schet as dec(10,0)) in ('.$sch1."))";
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->asArray()->all();
        $q=count($model);
        $total_beznds=0;
        $total=0;
        for ($i = 0; $i < $q; $i++)
        { $total+= $model[$i]['summa'];
          $total_beznds+= $model[$i]['summa_beznds'];}
        $content=$this->renderPartial('act_work_print',['model' => $model,'style_title' => 'd9',
            'q' => $q,'total' => $total,'total_beznds' => $total_beznds,'sch' => $sch,'sch1' => $sch1]);
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
            ->setSubject('[1click] Акт виконаних робіт за послуги від ПрАТ «ПЕЕМ «ЦЕК»')
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
        $sch1 = Yii::$app->request->post('sch1');
        if(empty($sch1)) $sch1='0';

        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
            . ' where a.res=b.nazv and (schet=:search or cast(schet as dec(10,0)) in ('.$sch1."))";
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->asArray()->all();

        $q=count($model);
        $total_beznds=0;
        $total=0;
        for ($i = 0; $i < $q; $i++)
        { $total+= $model[$i]['summa'];
          $total_beznds+= $model[$i]['summa_beznds'];}
        $nazv = $model[0]['nazv'];
        $content=$this->renderPartial('message_print',['model' => $model,'style_title' => 'd9',
            'q' => $q,'total' => $total,'total_beznds' => $total_beznds,'sch' => $sch,'sch1' => $sch1]);
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
            ->setSubject('[1click] Інформаційне повідомлення для контрагента '.$nazv)
            ->setHtmlBody('<b>Бажаємо здоров’я.</b><br>У вкладеному файлі знаходиться інформаційне повідомлення.')
            ->attach('./message.pdf')
            ->send();


        $model = new info();
        $model->title = "Інформаційне повідомлення по рахунку №$sch для виконавчої служби, відправлено";
        $model->info1 = "";
        $model->style1 = "d15";
        $model->style_title = "d9";
        return $this->render('info', [
            'model' => $model]);
    }

     //  Отправка инф. сообщения по Email
    public function actionInfo_exec_email(){
        $sch = Yii::$app->request->post('sch');
        $email = Yii::$app->request->post('email');

        $sql = 'select a.*,b.Director,b.parrent_nazv,b.mail from vschet a,spr_res b'
            . ' where a.res=b.nazv and schet=:search';
        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->one();
        $nazv = $model->nazv;
        $content=$this->renderPartial('info_exec_print',['model' => $model,'style_title' => 'd9']);
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
            ->setSubject('[1click] Інформаційне повідомлення для контрагента '.$nazv)
            ->setHtmlBody('<b>Бажаємо здоров’я.</b><br>У вкладеному файлі знаходиться інформаційне повідомлення.')
            ->attach('./message.pdf')
            ->send();


        $model = new info();
         $model->title = "Інформаційне повідомлення по рахунку №$sch для виконавчої служби, відправлено";
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
        $sch1 = Yii::$app->request->post('sch1');
        if(empty($sch1)) $sch1='0';
        if(!isset(Yii::$app->user->identity->role))
        {      $flag=0;}
        else {
            $role = Yii::$app->user->identity->role;
        }

//        $sql = 'select distinct a.*,b.Director,b.parrent_nazv,b.mail,'
//                . 'c.exec_person,c.exec_person_pp,c.exec_post,c.exec_post_pp,c.assignment,c.date_assignment'
//                . ' from vschet a,spr_res b,spr_uslug c,costwork d'
//                . ' where a.res=b.nazv and a.usluga=d.work '
//                . ' and c.usluga=d.usluga'
//                . ' and (schet=:search or cast(schet as dec(10,0)) in ('.$sch1."))";

        if($role<>11) {
//            $sql = 'select distinct a.*,b.Director,b.parrent_nazv,b.mail,'
//                . 'c.exec_person,c.exec_person_pp,c.exec_post,c.exec_post_pp,c.assignment,c.date_assignment'
//                . ' from vschet a,spr_res b,spr_uslug c,costwork d'
//                . ' where a.res=b.nazv and a.usluga=d.work '
//                . ' and c.usluga=d.usluga'
//                . ' and (schet=:search or cast(schet as dec(10,0)) in (' . $sch1 . "))";
            $sql = 'select distinct a.*,b.Director,b.parrent_nazv,b.mail,
                c.exec_person,c.exec_person_pp,c.exec_post,c.exec_post_pp,c.assignment,c.date_assignment,c.usluga as usl
                from vschet a left join spr_res b on a.res=b.nazv
                
                left join costwork d on a.usluga=d.work 
                left join spr_uslug c on c.usluga=d.usluga '.
                ' where schet=:search or cast(schet as dec(10,0)) in ('.$sch1.")".' limit 1';
        }
        else
        {
            $sql = 'select distinct a.*,b.Director,b.parrent_nazv,b.mail,'
                . 'c.exec_person,c.exec_person_pp,c.exec_post,c.exec_post_pp,c.assignment,c.date_assignment,c.usluga as usl'
                . ' from vschet a,spr_res b,spr_uslug c,costwork d'
                . ' where a.res=b.nazv and a.usluga=d.work '
                . ' and c.id=14'
                . ' and (schet=:search or cast(schet as dec(10,0)) in (' . $sch1 . "))".' limit 1';
        }

        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->asArray()->all();
        $q=count($model);
        $total=0;
        $total_beznds=0;
        for ($i = 0; $i < $q; $i++)
        { $total+= $model[$i]['summa'];
          $total_beznds+= $model[$i]['summa_beznds'];}

        $content=$this->renderPartial('contract_print',['model' => $model,'style_title' => 'd9',
            'q' => $q,'total' => $total,'total_beznds' => $total_beznds]);
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
            ->setSubject('[1click] Договір від ПрАТ «ПЕЕМ «ЦЕК»')
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
                . 'c.exec_person,c.exec_person_pp,c.exec_post,c.exec_post_pp,c.assignment,c.date_assignment'
                . ' from vschet a,spr_res b,spr_uslug c,costwork d'
                . ' where a.res=b.nazv and a.usluga=d.work '
                . ' and c.usluga=d.usluga'
                . ' and schet=:search ';

        $model = viewschet::findBySql($sql,[':search'=>"$sch"])->one();
        //$mail = $model->mail;
        $usl = $model->usluga;
        $res = $model->res;
        $nazv_zakaz = $model->nazv;

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

        //     Определяем данные исполнительной службы
        $usluga = spr_work::find()->select('usluga')->where('work=:usl',[':usl' => $usl])->all();
        $usluga = $usluga[0]->usluga;

        $exec = spr_uslug::find()->select('exec_office')->where('usluga=:usluga',[':usluga' => $usluga])->all();
        $exec = $exec[0]->exec_office;

        if($exec=='СД'){
            $town = spr_res::find()->select('town_fromwhere_sd')->where('nazv=:nazv',[':nazv' => $res])->all();
            $town_sd = $town[0]->town_fromwhere_sd;
            if(!empty($town_sd))
                $data_res = spr_res::find()->select('id,mail')->where(['like', 'town', "$town_sd"])->all();
            else
                $data_res = spr_res::find()->select('id,mail')->where('nazv=:nazv',[':nazv' => $res])->all();
        }
        if($exec=='СЗ'){
            $town = spr_res::find()->select('town_fromwhere_sz')->where('nazv=:nazv',[':nazv' => $res])->all();
            $town_sz = $town[0]->town_fromwhere_sz;
            if(!empty($town_sz))
                $data_res = spr_res::find()->select('id,mail')->where(['like', 'town', "$town_sz"])->all();
            else
                $data_res = spr_res::find()->select('id,mail')->where('nazv=:nazv',[':nazv' => $res])->all();
        }
        if($exec=='РЕМ'){
           $data_res = spr_res::find()->select('id,mail')->where('nazv=:nazv',[':nazv' => $res])->all();
        }
        //$mail = $data_res[0]->mail;
        $id_res = $data_res[0]->id;
        if($id_res==4)
            $data_koord = vspr_res_koord::find()->where('id_res=:id',[':id' => $id_res])->
                andwhere('type_usl=:exec',[':exec' => $exec])->all();
        else
            $data_koord = vspr_res_koord::find()->where('id_res=:id',[':id' => $id_res])->all();
        $mail = $data_koord[0]->email;

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
                'title' => 'Друк акта',
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
            ->setSubject('[1click] Документи за послуги від ПрАТ «ПЕЕМ «ЦЕК» для контрагента '.$nazv_zakaz)
            ->setHtmlBody('<b>Дякуємо за звернення до ПрАТ «ПЕЕМ «ЦЕК».</b><br>
                        У вкладеному файлі знаходяться: рахунок за послугу,акт виконаних робіт та договір')
            ->attach('./schet.pdf')
            ->attach('./act.pdf')
            ->attach('./contract.pdf')
            ->attach('./message.pdf')
            ->send();

        $model = new info();
        $model->title = "Документи по рахунку №$sch для контрагента $nazv_zakaz, відправлено";
        $model->info1 = "";
        $model->style1 = "d15";
        $model->style_title = "d9";
        return $this->render('info', [
            'model' => $model]);
    }


    // Сброс аналитики в Excel

    public function actionAnalytics_excel()
    {

        $session = Yii::$app->session;
        if($session->has('sql_analytics'))
            $sql = $session->get('sql_analytics');
        else
            $sql='';

            $data = viewanalit::findBySql($sql)->all();
            $data1 = viewanalit::findBySql($sql)->asarray()->all();

            if(count($data1))
            {    $a = array_keys($data1[0]);
            }
            else {
                echo "Без результату";
                return;
            }


            $cols = [
            'id' => 'ID',
            'inn' => 'ІНН:',
            'schet' => 'Заявка:',
            'usluga' => 'Послуга, яка заказується споживачем:',
            'summa' => 'Сума з ПДВ,грн.:',
            'okpo' => 'ЄДРПОУ:',
            'regsvid' => '№ рег. посвідч.',
            'nazv' => 'Споживач:',
            'addr' => 'Адреса:',
            'adres' => '* Адреса виконання робіт:',
            'res' => 'Підрозділ:',
            'comment' => 'Коментар споживача:',
            'tel' => 'Телефон:',
            'priz_nds' => 'Платник ПДВ:',
            'plat_yesno' => 'Платник ПДВ:',
            'date' => 'Дата заявки:',
            'email' => 'Адреса ел. почти:',
            'time' => 'Час:',
            'surely' => 'Передзвонити:',
            'status' => '* Статус заявки:',
            'status_sch' => 'Статус заявки:',
            'date_z' => '* Бажана дата отримання послуги:',
            'date_opl' => 'Дата оплати:',
            'date_akt' => 'Дата акта виконаних робіт:',
            'date_exec' => 'Дата виконання роботи:',
            'act_work' => '№ акта виконаних робіт:',
            'contract' => '№ договору:',
            'summa_work' => 'Вартість робіт,грн.:',
            'summa_transport' => 'Транспорт всього,грн.:',
            'summa_delivery' => 'Доставка бригади,грн.:',
            'summa_beznds' => 'Сума без ПДВ,грн.:',
            'why_refusal' => '* Причина відмови:',
            'union_sch' => "Об'єднання заявок:",
            'main_u' => 'U',
            'read_z' => 'Прочитана',
            'pib_dir' => 'П.І.Б. уповноваженої особи',
            'post_dir' => 'Посада уповноваженої особи',
        ];

            // Формирование массива названий колонок
            $list='';  // Список полей для сброса в Excel
            $h=[];
            $i=0;

             foreach($a as $v){

                 $col="'".$v."'";
                 $key = in_array($v, $cols);
                 debug($key);
                 if($key){
                     $h[$i]['col']=$col;
                     //$h[$i]['name']="'".$cols[$key]."'";
                 }
                 $i++;

            }
            debug($h);
            return;

            $k1='Результат аналітики';
             \moonland\phpexcel\Excel::widget([
                    'models' => $data,

                    'mode' => 'export', //default value as 'export'
                    'format' => 'Excel2007',
                    'hap' => $k1,    //cтрока шапки таблицы
                    'data_model' => 1,
                    'columns' => $a,
                    'headers' => ['status_sch' => 'Cтатус заявки','summa_beznds' => 'Сума без ПДВ,грн.:'],
                ]);
             return;

        }


    // Аналитика по данным заявок
    public function actionAnalytics()
    {
        $model = new analytics();
        if ($model->load(Yii::$app->request->post())){
            if($model->ord=='error') {echo 'Введіть поле групування!'; return;}
            // Генерация SQL-запроса

            if(empty($model->gr_status_sch) || is_null($model->gr_status_sch))
                $k1=0;
            else
                $k1=1;


            if(empty($model->gr_res) || is_null($model->gr_res))
                $k2=0;
            else
                $k2=1;

            if(empty($model->gr_date) || is_null($model->gr_date))
                $k3=0;
            else
                $k3=1;

            if(empty($model->gr_date_opl) || is_null($model->gr_date_opl))
                $k4=0;
            else
                $k4=1;

            if(empty($model->gr_usluga) || is_null($model->gr_usluga))
                $k5=0;
            else
                $k5=1;

            if(empty($model->gr_usl) || is_null($model->gr_usl))
                $k6=0;
            else
                $k6=1;

            if(empty($model->gra_summa) || is_null($model->gra_summa) || $model->gra_summa=='')
                $ka1=0;
            else
                $ka1=1;

            if(empty($model->gra_summa_beznds) || is_null($model->gra_summa_beznds))
                $ka2=0;
            else
                $ka2=1;

            if(empty($model->gra_summa_work) || is_null($model->gra_summa_work))
                $ka3=0;
            else
                $ka3=1;

            if(empty($model->gra_summa_transport) || is_null($model->gra_summa_transport))
                $ka4=0;
            else
                $ka4=1;

            if(empty($model->gra_summa_delivery) || is_null($model->gra_summa_delivery))
                $ka5=0;
            else
                $ka5=1;

            if(empty($model->grh_having) || is_null($model->grh_having))
                $kh1=0;
            else
                $kh1=1;


            $kr=$k1+$k2+$k3+$k4+$k5+$k6+$ka1+$ka2+$ka3+$ka4+$ka5+$kh1;
            if($kr==0)
                $select = 'select * from vw_analit ';
            else{
                $select='';
                $select1='';
                $select2='';
                if(($k1+$k2+$k3+$k4+$k5+$k6)>=1){
                    $mas=explode(" ",$model->ord);
                    $gr='';
                    foreach($mas as $v){
                        $gr.=substr($v,3).',';
                    }
                    $gr=substr($gr,1,strlen($gr));
                    $select=$gr;


                }

                 if(($ka1+$ka2+$ka3+$ka4+$ka5)>=1){

                     if($model->gra_oper==1) $o='SUM(';
                     if($model->gra_oper==2) $o='MAX(';
                     if($model->gra_oper==3) $o='MIN(';
                     if($model->gra_oper==4) $o='AVG(';
                     if($model->gra_oper==5) $o='COUNT(';
                     if($ka1==1) {
                         $select1.=$o.'summa'.') as summa'.',';
                         $select2.=$o.'summa'.')';

                     }
                     if($ka2==1){
                         $select1.=$o.'summa_beznds'.') as summa_beznds'.',';
                         $select2.=$o.'summa_beznds'.')';
                     }
                     if($ka3==1){
                          $select1.=$o.'summa_work'.') as summa_work'.',';
                          $select2.=$o.'summa_work'.')';
                     }
                     if($ka4==1){
                         $select1.=$o.'summa_transport'.') as summa_transport'.',';
                         $select2.=$o.'summa_transport'.')';
                     }
                     if($ka5==1){
                         $select1.=$o.'summa_delivery'.') as summa_delivery'.',';
                         $select2.=$o.'summa_delivery'.')';
                     }
                         $select1=substr($select1,0,strlen($select1)-1);

                     //debug($select);

                 }

                 if($model->gra_oper==5){
                     $select1='COUNT(*) as kol';

                 }
                if(!empty($select1))
                    $select = 'select '.$select.$select1.' from vw_analit ';
                else
                    $select = 'select '.$select.' from vw_analit ';
            }
            $sql='';
            // WHERE
            if(!empty($model->date1)){
                 $sql.=' and date>='."'".$model->date1."'";
            }
            if(!empty($model->date2)){
                 $sql.=' and date<='."'".$model->date2."'";
            }

            if(!empty($model->date_opl1)){
                 $sql.=' and date_opl>='."'".$model->date_opl1."'";
            }
            if(!empty($model->date_opl2)){
                 $sql.=' and date_opl<='."'".$model->date_opl2."'";
            }

            if(!empty($model->res)){
                $res=spr_res::findbysql(
                    "select nazv from spr_res where "
            . 'id=:id ',[':id' => $model->res])->all();
                $sql.=' and res='."'".$res[0]->nazv."'";
            }
            if(!empty($model->status))
                $sql.=' and status='.$model->status;
            if(trim($model->work)=='style="font-size:') $model->work='';
            if(!empty($model->work) && !is_null($model->work)){
                $work = spr_costwork::findbysql('Select work from costwork where '
            . 'id=:id ',[':id' => $model->work])
            ->all();

               $sql.=' and usluga='."'".$work[0]->work."'";
            }
            if(!empty($model->usluga)){
                $usl = spr_costwork::findbysql('Select usluga from costwork where '
            . 'id=:id ',[':id' => $model->usluga])
            ->all();
                $sql.=' and usl='."'".$usl[0]->usluga."'";

            }
            if(!empty($sql))
                $sql=$select.' where '.mb_substr($sql,4,400,"UTF-8");
            else{
                $sql=$select;
            }

            // Добавляем GROUP BY
            if(!empty($gr)){
                $gr=substr($gr,0,strlen($gr)-1);
                $sql.=' GROUP BY '.$gr;
            }
            // Добавляем HAVING
            $having='';
            $oh='';
             if(!empty($model->grh_having)){
                 $kh1=$model->grh_having;
                 if(!empty($model->grh_value)){
                     $having = ' HAVING '.$select2;

                     switch ($kh1) {
                         case 1:
                             $oh='=';
                             break;
                         case 2:
                             $oh='>';
                             break;
                         case 3:
                             $oh='>=';
                             break;
                         case 4:
                             $oh='<';
                             break;
                         case 5:
                             $oh='<=';
                             break;
                         case 6:
                             $oh='<>';
                             break;

                     }
                   $having.=$oh.$model->grh_value;
                   $sql.=$having;
                 }
             }
            // Добавляем ORDER BY
            $orderby='';
            $oo='';
            if(!empty($model->grs_sort)){
                $ks1=$model->grs_sort;
                switch ($ks1) {
                         case 1:
                             $oo='res';
                             break;
                         case 2:
                             $oo='status_sch';
                             break;
                         case 3:
                             $oo='date';
                             break;
                         case 4:
                             $oo='date_opl';
                             break;
                         case 5:
                             $oo='usl';
                             break;
                         case 6:
                             $oo='usluga';
                             break;
                         case 7:
                             $oo='summa';
                             break;
                         case 8:
                             $oo='summa_beznds';
                             break;
                         case 9:
                             $oo='summa_work';
                             break;
                         case 10:
                             $oo='summa_transport';
                             break;
                         case 11:
                             $oo='summa_delivery';
                             break;

                     }
                    $orderby = ' ORDER BY '.$oo;
                    if(!empty($model->grs_dir)){
                        if($model->grs_dir==2) $orderby.=' DESC ';
                    }
                     $sql.=$orderby;
            }
//            debug($sql);
//            return;
            $dataProvider = new ActiveDataProvider([
            'query' => viewanalit::findBySql($sql),
                'pagination' => [
                    'pageSize' => 500,
                     ],
            ]);
            //$dataProvider = viewanalit::findBySql($sql);
            $data = viewanalit::findBySql($sql)->all();
            $data1 = viewanalit::findBySql($sql)->asarray()->all();
            // Запоминаем sql запрос в сессии
            $session = Yii::$app->session;
            $session->open();
            $session->set('sql_analytics', $sql);

            $model->ord = trim($model->ord);
            $q_all = count($data1);
            if(count($data1))
            {    $a = array_keys($data1[0]);
            }
            else {
                echo "Без результату";
                return;
            }
//            echo Yii::$app->request->get('item');
//            return;

//             if (Yii::$app->request->get('item') == 'Excel' )
//             {
//                //$newQuery = clone $dataProvider->query;
//                //$models = $newQuery->orderby(['date' => SORT_DESC])->all();
//                 debug($a);
//                  $k1='Результат аналітики';
//                   \moonland\phpexcel\Excel::widget([
//                    'models' => $data,
//
//                    'mode' => 'export', //default value as 'export'
//                    'format' => 'Excel2007',
//                    'hap' => $k1,    //cтрока шапки таблицы
//                    'data_model' => 1,
//                    'columns' => $a
//                    'headers' => ['status_sch' => 'Cтатус заявки','inn' => 'ІНН','nazv' => 'Споживач','addr'=> 'Адрес','tel' => 'Телефон',
//                        'schet' => 'Рахунок','contract' => '№ договору', 'usluga' => 'Послуга','summa' => 'Сума,грн.:','summa_beznds' => 'Сума без ПДВ,грн.:',
//                        'summa_work' => 'Вартість робіт,грн.:','summa_delivery' => 'Доставка бригади,грн.:',
//                        'summa_transport' => 'Транспорт всього,грн.:',
//                        'res' => 'Виконавча служба:','date' => 'Дата'],
//                ]);
//             return;
//
//             }
            //debug($a);

            //return;
            //Формируем файл результата (view)
           // array_map('unlink', glob("analytics_res.php"));
            $file = '../views/site/analytics_res.php';
            $file_src = 'analit_src.php';
            $f = fopen($file,'w');
            $fsrc = fopen($file_src,'r');
            while (!feof($fsrc)) {
                $s=fgets($fsrc);
                fputs($f,$s);
            }
            fclose($fsrc);
            $s="Всього: ".$q_all;
            fputs($f,$s);
            $s="<?= GridView::widget([
            'dataProvider' => ".'$dataProvider,'.
            "'tableOptions' => ['class' => 'table table-striped table-bordered table-condensed'],
            'summary' => false,
            'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ";
            fputs($f,$s);
            foreach($a as $v){
                $s="'".$v."',";
                fputs($f,$s);
            }
            fputs($f,'] ]); ?> ');
            fputs($f,'</div>');
            fputs($f,"<?php echo Html::a('Сброс в Excel', ['site/analytics_excel'],
                ['class' => 'btn btn-info']); ?>");
            fclose($f);


             return $this->render('analytics_res',['data' => $data,'dataProvider' => $dataProvider,'style_title' => 'd9']);

        }
        else {
        return $this->render('analytics',['model' => $model,'style_title' => 'd9']);}
    }

    // Импорт с выписки для проставления оплаты
    // с OTP банка финансовому отделу атоматом
    public function actionImport_otp() {
        $model = new Import_otp();
        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model,'file');;
            if($model->file) {
                $model->upload('file');
            }
            $file = $model->file->name;
            $f = fopen($file, 'r');
            $ff = fopen($file, 'r');
            $i = 0;
            $res = [];
            $res1 = [];
            $res2 = [];
            $data_exl =[];
            $start1 = 0;
            $trig1 = 0;
            $trig2 = 0;
            $elem = 0;
            $elem1 = 0;
            $elem2 = 0;
            $exl = 0;
            $j = 0;
            // Узнаем кол-во строк в файле и дату оплаты
            while (!feof($ff)) {
                $j++;
                $s = fgets($ff);
                $pos = strpos($s, "Дата / Date:");
                if (!($pos === false)) {
                    preg_match('/(\d{1,2}).(\d{2}).(\d{4})/', $s, $match_d);
                    $date = $match_d[0];
                }

            }

            fclose($ff);
            while (!feof($f)) {
                $status = 1;
                $i++;
                $s = fgets($f);

                $pos = strpos($s, "Opening balance:");
                if (!($pos === false)) $start1 = 1;
                if ($start1 == 1) {
                    $pos1 = strpos($s, "</tr>");
                    if (!($pos1 === false) || $elem > 0) {
                        $trig1 = 1;
                    }

                    if ($trig1 == 1) {
                        while ($status == 1) {
                            $pos = strpos($s, '<tr style="height:1px">');
                            if (!($pos === false)) {
                                $trig2 = 1;
                            }

                            if ($trig2 == 1) {
                                $pos = strpos($s, 'Платіжне доручення');
                                if ($pos === false) $pos = strpos($s, 'Меморіальний ордер');
                                if (!($pos === false)) {
                                    $s1 = substr($s,$pos);

                                    $pos1 = preg_match('/00\d{6}\s/', $s1);
                                    if ($pos1 == 1) {
                                        $pieces = explode(" ", substr($s, $pos));
//                                        debug($s1);
//                                        debug( $pieces);
//                                        debug('7777777');

                                        $summa_o = $pieces[8];
                                        //debug($summa_o);
                                        $pos = strpos($summa_o, '>');
                                        $summa_o = substr($summa_o, $pos + 1);

                                        //debug($summa_o);
                                        //return;
                                        if(isset($pieces[9]))
                                        {
                                            $pos9 = strpos($pieces[9], '>');
//                                            $summa_o9 = substr($pieces[9], $pos9 + 1);
                                            $summa_o=$summa_o.$pieces[9];
                                            //debug($summa_o9);

                                        }
                                        //debug($summa_o);

                                        preg_match('/([\d.\s])+/', $summa_o, $match1);
                                        $summa_o = $match1[0];
                                        $res['summa'][$elem] = $summa_o;
                                        $res1[$elem1] = $summa_o;
                                        //preg_match('/[а-яА-Я]{4}\d\d_\d{8}/', $s, $match);
                                        preg_match('/000\d{6}\s/', $s1, $match);

                                        if (isset($match[0]))
                                            $match[0]=substr($match[0],1);
                                        else
                                            preg_match('/00\d{6}\s/', $s1, $match);
                                        $res['contract'][$elem] = $match[0];

//                                        debug($match[0]);

                                        $short_res = trim(mb_substr($match[0], 0, 2, 'UTF-8'));
                                        $sql = 'Select res,summa from schet where schet=' . '"' . $match[0] . '"'.
                                        ' and status<3';
//                                        debug($sql);
//                                        return;

//                                        $f1=fopen('aac_','w+');
//                                        fputs($f1,$sql);

                                        $spr = schet::findbysql($sql)->all();

//                                        debug($spr);
//                                        return;
                                        $j_count=count($spr);
                                        if($j_count<>0)
                                            $res['res'][$elem] = $spr[0]->res;
                                        else
                                            $res['res'][$elem] = '';

                                        // Сравниваем сумму по заявке и оплаченную

                                        if($j_count<>0) {
                                            if (abs($spr[0]->summa - (float) $summa_o)<=0.1)
                                                $res['note'][$elem] = 'округлення до 10 коп.';
                                            else
                                                $res['note'][$elem] = 'часткова оплата';

                                            if ($spr[0]->summa == $summa_o)
                                                $res['note'][$elem] = '';

//                                            if ($spr[0]->summa <> $summa_o)
//                                                $res['note'][$elem] = 'часткова оплата';
//                                            else
//                                                $res['note'][$elem] = '';
                                        }
                                        else
                                        {
                                            $res['note'][$elem] = '';
                                            $res2['summa'][$elem2] = 'ПОМИЛКА';
                                            $res2['note'][$elem2] = 'Неправильно вказаний рахунок в виписці, № рахунку '.$match[0];
                                            $elem2++;
                                        }

                                        $sql = 'update schet set status=3,date_opl=' . '"' .
                                            date("Y-m-d", strtotime($date)) . '"' .
                                            ' where schet=' . '"' . trim($match[0]) . '"';
                                        Yii::$app->db->createCommand($sql)->execute();

                                        //debug($sql);

                                        $elem++;

                                    }
                                    else{
                                        // Если не указан № счета или договора
                                        $pieces = explode(" ", substr($s, $pos));
                                        $ss='';
                                        $y=count($pieces);
                                        for($a=9;$a<$y;$a++){
                                            $ps2=0;
                                            $ps3=0;
                                            $ps4=0;
                                            $ps5=0;
                                            if($a<>($y-1)) {
                                                $pos2 = strpos($pieces[$a], '>');
                                                if ($pos2 === false) $ps2 = 0; else $ps2 = 1;
                                                $pos3 = strpos($pieces[$a], 'colspan');
                                                if ($pos3 === false) $ps3 = 0; else $ps3 = 1;
                                                $pos4 = strpos($pieces[$a], 'rowspan');
                                                if ($pos4 === false) $ps4 = 0; else $ps4 = 1;
                                                $pos5 = strpos($pieces[$a], 'class');
                                                if ($pos5 === false) $ps5 = 0; else $ps5 = 1;
                                            }
                                            if(($ps2+$ps3+$ps4+$ps5)==0)
                                                       $ss.=$pieces[$a].' ';
                                        }
                                        $summa_o = $pieces[8];
                                        $pos = strpos($summa_o, '>');
                                        $summa_o = substr($summa_o, $pos + 1);
                                        $pos2 = preg_match('/([\d.\s])+/', $summa_o, $match1);
                                        if($pos2==1) {
                                            $summa_o = $match1[0];
                                            if($summa_o<1000 && $summa_o>100 ) {
                                                $res2['summa'][$elem2] = $summa_o;
                                                $res2['note'][$elem2] = $ss;
                                                $elem2++;
                                            }
                                        }
//                                        $res['summa'][0] = 0;
//                                        $res['contract'][0]='';
//                                        $res['res'][0]='';
//                                        $res['note'][0]='';


                                    }
                                    $exl++;
                                    $data_exl[$exl]=$exl;
                                    $trig2 = 0;
                                    $trig1 = 0;
                                    $status = 0;
                                    $elem1++;

                                }
                            }
                            $i++;
                            $s = fgets($f);
                            if ($i > $j) $status = 0;
                        }
                    }
                }

            }

//            debug($res);
//            return;

            end($res1);         // move the internal pointer to the end of the array
            $key = key($res1);
            $prop=[];
            $i=0;
            for($e=0;$e<=$key;$e++){
                if (!array_key_exists($e, $res1)) {
                    $prop[$i]=$e;   // Пропущенные записи
                    $i++;
                }
            }


            return $this->render('import_otp', ['result' => $res, 'date' => $date,
                'prop' => $prop,'warn' => $res2,'kol_warn' => $elem2]);
        }
        else {
            return $this->render('upload_file_opl', [
                'model' => $model,
            ]);
        }
    }

    // Импорт с выписки для проставления оплаты
    // с OTP банка финансовому отделу атоматом (для новой выписки с банка
    //    формат выписки поменялся 04.08.2020)
    public function actionImport_otp_new() {
        $model = new Import_otp();
        $ff=fopen('aotp.txt','w+');
        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model,'file');;
            if($model->file) {
                $model->upload('file');
            }
            $file = $model->file->name;
            $f = fopen($file, 'r');

            $i = 0;
            $res = [];
            $res1 = [];
            $res2 = [];
            $data_exl =[];
            $start1 = 0;
            $trig1 = 0;
            $trig2 = 0;
            $elem = 0;
            $elem1 = 0;
            $elem2 = 0;
            $exl = 0;
            $j = 0;
            // Узнаем кол-во строк в файле и дату оплаты

            $s = file_get_contents($file);

            $p = xml_parser_create();
            xml_parse_into_struct($p, $s, $vals, $index);
            xml_parser_free($p);

//            debug($vals);
//            return;

            $y=count($vals);
            $j=0;
            $j2=0;
            for($i=0;$i<$y;$i++){
                if(trim($vals[$i]['tag'])<>'ROW')  continue;
                $date = $vals[$i]['attributes']['BOOKEDDATE'];   // Дата оплаты

                if(trim($vals[$i]['attributes']['DOCSUBTYPESNAME'])=='Меморіальний ордер' ||
                    trim($vals[$i]['attributes']['DOCSUBTYPESNAME'])=='Платіжне доручення' ||
                    trim($vals[$i]['attributes']['DOCSUBTYPESNAME'])=='Мемориальный ордер' ||
                    trim($vals[$i]['attributes']['DOCSUBTYPESNAME'])=='Платежное поручение' ||
                    trim($vals[$i]['attributes']['DOCSUBTYPESNAME'])=='Вход мем ордер по СЄП') {
                    $s1 = $vals[$i]['attributes']['PLATPURPOSE'];
                    $pattern = '/000\d{6}\s/';
                    preg_match('/000\d{6}\s/', $s1, $match);
                    if (isset($match[0]))
                        $match[0] = substr($match[0], 1);
                    else {
                        $pattern = '/00\d{6}\s/';
                        preg_match('/00\d{6}\s/', $s1, $match);
                }


                    if (!isset($match[0])){
                        $pattern = '/00\d{6},\s/';
                        preg_match('/00\d{6},/', $s1, $match);
                        if (isset($match[0]))
                             $match[0]=str_replace(',','',$match[0]);

                     }
//                    debug($s1);
//                    debug($match);


//                    if (isset($match[0]))
//                        preg_match('/00\d{6};/', $s1, $match);
//                    else {
//                        if (count($match) == 0) {
//                            preg_match('/00\d{6};/', $s1, $match);
//                            $match[0]=str_replace(',','',$match[0]);
//                        }
//                    }

//                   if($vals[$i]['attributes']['SUMMA']=='833771')
//                   {           debug($match);
//                                return;}



                    if(($vals[$i]['attributes']['DOCSUBTYPESNAME']=='Платіжне доручення' ||
                        $vals[$i]['attributes']['DOCSUBTYPESNAME']=='Платежное поручение') && isset($match[0])==false)
                        continue;

                    $res['date'][$j] =$date;
                    $summa_o = ((int) $vals[$i]['attributes']['SUMMA'])/100;
                    $res['summa'][$j] = $summa_o;

                    if (isset($match[0]))
                        $res['contract'][$j] = $match[0];
                    else
                        $res['contract'][$j] ='';

                    // Выявление  правильного № счета из нескольких счетов
                    preg_match_all($pattern, $s1, $all_schets);
                    $y_schets = count($all_schets[0]);
                    if($y_schets>1) {
                        $true_schet = '';
                        foreach ($all_schets[0] as $v_i) {
                            // Находим счет который является только утвержденным
                            $z_i = "select schet from schet where schet='$v_i' and status=2";
                            $data_i = schet::findbysql($z_i)->asarray()->all();
                            if(isset( $data_i[0]['schet']))
                                $true_schet=$v_i;  // и если находится - тогда берем этот счет
                        }
                        $res['contract'][$j]=$true_schet;
//                         debug($true_schet);
//                         return;
                    }
                    // End

//                    debug($true_schet);
//                    return;

                    if( $res['contract'][$j]<>'')
                    $sql = 'Select res,summa from schet where trim(schet)=' . '"' . trim($res['contract'][$j]) . '"'.
                        ' and status<3';
                    else
                        $sql = 'Select res,summa from schet where 1=2';


                    $spr = schet::findbysql($sql)->all();

                    fputs($ff,$sql);

//                                        debug($spr);
//                                        return;
                    $j_count=count($spr);
                    if($j_count<>0)
                        $res['res'][$j] = $spr[0]->res;
                    else
                        $res['res'][$j] = '';

                    // Сравниваем сумму по заявке и оплаченную

                    if($j_count<>0) {
                        if (abs($spr[0]->summa - (float) $summa_o)<=0.1)
                            $res['note'][$j] = 'округлення до 10 коп.';
                        else
                            $res['note'][$j] = 'часткова оплата';

                        if ($spr[0]->summa == $summa_o)
                            $res['note'][$j] = '';

//                                            if ($spr[0]->summa <> $summa_o)
//                                                $res['note'][$elem] = 'часткова оплата';
//                                            else
//                                                $res['note'][$elem] = '';
                    }
                    else
                    {
                        $res['note'][$j] = '';
                        $res1['summa'][$j2] = 'ПОМИЛКА';
                        $res1['note'][$j2] = 'Неправильно вказаний рахунок в виписці, № рахунку '.$res['contract'][$j];
                        $j2++;
                    }

                    $sql1 = 'update schet set status=3,date_opl=' . '"' .
                        date("Y-m-d", strtotime($date)) . '"' .
                        ' where schet=' . '"' . trim($res['contract'][$j]) . '"' . ' and status=2';
                    Yii::$app->db->createCommand($sql1)->execute();

                    $j++;
                }

            }
//            debug($res);
//            return;

            end($res1);         // move the internal pointer to the end of the array
            $key = key($res1);
            if(is_null(($key)) || empty($key))
                $key=-1;
            $prop=[];
            $i=0;
            for($e=0;$e<=$key;$e++){
                if (!array_key_exists($e, $res1)) {
                    $prop[$i]=$e;   // Пропущенные записи
                    $i++;
                }
            }

//            echo 'res';
//            debug($res);
//            echo 'res1';
//            debug($res1);
//            echo 'prop';
//            debug($prop);
//            echo $key;

            return $this->render('import_otp', ['result' => $res, 'date' => $date,
                'prop' => $prop,'warn' => $res1,'kol_warn' => $j2]);



            while (!feof($f)) {
                $status = 1;
                $i++;
                $s = fgets($f);

                $pos = strpos($s, "Opening balance:");
                if (!($pos === false)) $start1 = 1;
                if ($start1 == 1) {
                    $pos1 = strpos($s, "</tr>");
                    if (!($pos1 === false) || $elem > 0) {
                        $trig1 = 1;
                    }

                    if ($trig1 == 1) {
                        while ($status == 1) {
                            $pos = strpos($s, '<tr style="height:1px">');
                            if (!($pos === false)) {
                                $trig2 = 1;
                            }

                            if ($trig2 == 1) {
                                $pos = strpos($s, 'Платіжне доручення');
                                if ($pos === false) $pos = strpos($s, 'Меморіальний ордер');
                                if (!($pos === false)) {
                                    $s1 = substr($s,$pos);

                                    $pos1 = preg_match('/00\d{6}\s/', $s1);
                                    if ($pos1 == 1) {
                                        $pieces = explode(" ", substr($s, $pos));
//                                        debug($s1);
//                                        debug( $pieces);
//                                        debug('7777777');

                                        $summa_o = $pieces[8];
                                        //debug($summa_o);
                                        $pos = strpos($summa_o, '>');
                                        $summa_o = substr($summa_o, $pos + 1);

                                        //debug($summa_o);
                                        //return;
                                        if(isset($pieces[9]))
                                        {
                                            $pos9 = strpos($pieces[9], '>');
//                                            $summa_o9 = substr($pieces[9], $pos9 + 1);
                                            $summa_o=$summa_o.$pieces[9];
                                            //debug($summa_o9);

                                        }
                                        //debug($summa_o);

                                        preg_match('/([\d.\s])+/', $summa_o, $match1);
                                        $summa_o = $match1[0];
                                        $res['summa'][$elem] = $summa_o;
                                        $res1[$elem1] = $summa_o;
                                        //preg_match('/[а-яА-Я]{4}\d\d_\d{8}/', $s, $match);
                                        preg_match('/000\d{6}\s/', $s1, $match);

                                        if (isset($match[0]))
                                            $match[0]=substr($match[0],1);
                                        else
                                            preg_match('/00\d{6}\s/', $s1, $match);
                                        $res['contract'][$elem] = $match[0];

//                                        debug($match[0]);

                                        $short_res = trim(mb_substr($match[0], 0, 2, 'UTF-8'));
                                        $sql = 'Select res,summa from schet where schet=' . '"' . $match[0] . '"'.
                                            ' and status<3';
//                                        debug($sql);
//                                        return;

//                                        $f1=fopen('aac_','w+');
//                                        fputs($f1,$sql);

                                        $spr = schet::findbysql($sql)->all();

//                                        debug($spr);
//                                        return;
                                        $j_count=count($spr);
                                        if($j_count<>0)
                                            $res['res'][$elem] = $spr[0]->res;
                                        else
                                            $res['res'][$elem] = '';

                                        // Сравниваем сумму по заявке и оплаченную

                                        if($j_count<>0) {
                                            if (abs($spr[0]->summa - (float) $summa_o)<=0.1)
                                                $res['note'][$elem] = 'округлення до 10 коп.';
                                            else
                                                $res['note'][$elem] = 'часткова оплата';

                                            if ($spr[0]->summa == $summa_o)
                                                $res['note'][$elem] = '';

//                                            if ($spr[0]->summa <> $summa_o)
//                                                $res['note'][$elem] = 'часткова оплата';
//                                            else
//                                                $res['note'][$elem] = '';
                                        }
                                        else
                                        {
                                            $res['note'][$elem] = '';
                                            $res2['summa'][$elem2] = 'ПОМИЛКА';
                                            $res2['note'][$elem2] = 'Неправильно вказаний рахунок в виписці, № рахунку '.$match[0];
                                            $elem2++;
                                        }

                                        $sql = 'update schet set status=3,date_opl=' . '"' .
                                            date("Y-m-d", strtotime($date)) . '"' .
                                            ' where schet=' . '"' . trim($match[0]) . '"';
                                        Yii::$app->db->createCommand($sql)->execute();

                                        //debug($sql);

                                        $elem++;

                                    }
                                    else{
                                        // Если не указан № счета или договора
                                        $pieces = explode(" ", substr($s, $pos));
                                        $ss='';
                                        $y=count($pieces);
                                        for($a=9;$a<$y;$a++){
                                            $ps2=0;
                                            $ps3=0;
                                            $ps4=0;
                                            $ps5=0;
                                            if($a<>($y-1)) {
                                                $pos2 = strpos($pieces[$a], '>');
                                                if ($pos2 === false) $ps2 = 0; else $ps2 = 1;
                                                $pos3 = strpos($pieces[$a], 'colspan');
                                                if ($pos3 === false) $ps3 = 0; else $ps3 = 1;
                                                $pos4 = strpos($pieces[$a], 'rowspan');
                                                if ($pos4 === false) $ps4 = 0; else $ps4 = 1;
                                                $pos5 = strpos($pieces[$a], 'class');
                                                if ($pos5 === false) $ps5 = 0; else $ps5 = 1;
                                            }
                                            if(($ps2+$ps3+$ps4+$ps5)==0)
                                                $ss.=$pieces[$a].' ';
                                        }
                                        $summa_o = $pieces[8];
                                        $pos = strpos($summa_o, '>');
                                        $summa_o = substr($summa_o, $pos + 1);
                                        $pos2 = preg_match('/([\d.\s])+/', $summa_o, $match1);
                                        if($pos2==1) {
                                            $summa_o = $match1[0];
                                            if($summa_o<1000 && $summa_o>100 ) {
                                                $res2['summa'][$elem2] = $summa_o;
                                                $res2['note'][$elem2] = $ss;
                                                $elem2++;
                                            }
                                        }
//                                        $res['summa'][0] = 0;
//                                        $res['contract'][0]='';
//                                        $res['res'][0]='';
//                                        $res['note'][0]='';


                                    }
                                    $exl++;
                                    $data_exl[$exl]=$exl;
                                    $trig2 = 0;
                                    $trig1 = 0;
                                    $status = 0;
                                    $elem1++;

                                }
                            }
                            $i++;
                            $s = fgets($f);
                            if ($i > $j) $status = 0;
                        }
                    }
                }

            }

//            debug($res);
//            return;

            end($res1);         // move the internal pointer to the end of the array
            $key = key($res1);
            $prop=[];
            $i=0;
            for($e=0;$e<=$key;$e++){
                if (!array_key_exists($e, $res1)) {
                    $prop[$i]=$e;   // Пропущенные записи
                    $i++;
                }
            }


            return $this->render('import_otp', ['result' => $res, 'date' => $date,
                'prop' => $prop,'warn' => $res2,'kol_warn' => $elem2]);
        }
        else {
            return $this->render('upload_file_opl', [
                'model' => $model,
            ]);
        }
    }

// Добавление новых пользователей
    public function actionAddAdmin() {
        $model = User::find()->where(['username' => 'zvres'])->one();
        if (empty($model)) {
            $user = new User();
            $user->username = 'zvres';
            $user->email = 'zvres@ukr.net';
            $user->setPassword('ghbdtncndbt');
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

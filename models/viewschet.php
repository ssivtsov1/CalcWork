<?php
/**
 * Created by PhpStorm.
 * User: ssivtsov
 * Используется для просмотра счетов из вида
 */
namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;


class Viewschet extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public $Director;
    public $parrent_nazv;
    public $mail;
    public $exec_person_pp;
    public $exec_person;
    public $exec_post;
    public $exec_post_pp;
    public $plat_yesno = 'ні';


    public static function tableName()
    {
        return 'vschet'; //Это вид
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inn' => 'ІНН:',
            'schet' => 'Заявка:',
            'usluga' => 'Послуга:',
            'summa' => 'Сума з ПДВ,грн.:',
            'okpo' => 'ЄДРПОУ:',
            'regsvid' => '№ рег. посвідч.',
            'nazv' => 'Споживач:',
            'addr' => 'Адреса:',
            'adres' => '* Адреса виконання робіт:',
            'res' => 'Виконавча служба:',
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

        ];
    }


    public function rules()
    {
        return [

            [['id','inn','schet','usluga','summa','date','act_work','date_akt',
                'okpo','nazv','addr','tel','summa_work','fio_dir','why_refusal',
                'summa_delivery','summa_transport','summa_beznds','plat_yesno',
                'priz_nds','email','adres','status','status_sch',
                'comment','res','time','date_z','date_exec','date_opl','contract','geo','kol'], 'safe'],
            ['date_z', 'compare',
                'compareValue' => date('Y-m-d'), 'operator' => '>=',
                'type' => 'string','message' => "Введено минулу дату"],
            ['date_z','date', 'format' => 'Y-m-d'],
            ['date_opl','date', 'format' => 'Y-m-d'],
            ['date_akt','date', 'format' => 'Y-m-d'],
            ['date_exec','date', 'format' => 'Y-m-d'],

        ];
    }

    public function search($params,$role)
    {
        //$query = viewschet::find();

        switch($role) {
            case 3: // Полный доступ
                $query = viewschet::find();
                break;
            case 2:  // финансовый отдел
                $query = viewschet::find()->where('status=:status1',[':status1' => 2])->
                orwhere('status=:status2',[':status2' => 3]);
                break;
            case 1:  // бухгалтерия
                $query = viewschet::find()->where('status=:status1',[':status1' => 5])->
                orwhere('status=:status2',[':status2' => 7]);
                break;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder'=> ['status'=>SORT_ASC,'date'=>SORT_DESC,'time'=>SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'usluga', $this->usluga]);
        $query->andFilterWhere(['like', 'status_sch', $this->status_sch]);
        $query->andFilterWhere(['like', 'inn', $this->inn]);
        $query->andFilterWhere(['like', 'schet', $this->schet]);
        $query->andFilterWhere(['=', 'summa', $this->summa]);
        $query->andFilterWhere(['like', 'nazv', $this->nazv]);
        $query->andFilterWhere(['like', 'addr', $this->addr]);
        $query->andFilterWhere(['like', 'tel', $this->tel]);
        $query->andFilterWhere(['like', 'okpo', $this->okpo]);
        $query->andFilterWhere(['like', 'contract', $this->contract]);
        $query->andFilterWhere(['like', 'regsvid', $this->regsvid]);
        $query->andFilterWhere(['=', 'priz_nds', $this->priz_nds]);
        $query->andFilterWhere(['=', 'date_opl', $this->date_opl]);
        //$query->andFilterWhere(['=', 'id', $this->id]);
        //$query->andFilterWhere(['=', 'date', $this->date]);

        return $dataProvider;
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public static function getDb()
    {
       
            return Yii::$app->get('db');
    }


}


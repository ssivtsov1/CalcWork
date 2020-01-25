<?php
// Заявки пользователей
namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;


class Schet extends \yii\db\ActiveRecord
{
   
    public $nazv;

    public static function tableName()
    {
        return 'schet';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inn' => 'ІНН:',
            'schet' => 'Заявка:',
            'usluga' => 'Послуга:',
            'summa' => 'Сума з ПДВ,грн:',
            'adres' => 'Адреса робіт:',
            'object' => 'Назва об`єкта:',
            'res' => 'Виконавча служба:',
            'comment' => 'Коментарій споживача:',
            'time' => 'Час створення',
            'date_z' => 'Дата виконання послуги',
            'date_opl' => 'Дата оплати:',
            'date_akt' => 'Дата акта виконаних робіт:',
            'act_work' => '№ акта виконаних робіт:',
            'status' => 'Статус заявки:',
            'contract' => '№ договору:',
            'summa_work' => 'Вартість робіт:',
            'summa_transport' => 'Транспорт всього,грн.:',
            'summa_tmc' => 'Матеріали та устаткування,грн.:',
            'summa_delivery' => 'Доставка бригади,грн.:',
            'summa_beznds' => 'Сума без ПДВ:',
            'union_sch' => "Об'єднання заявок:",
            'read_z' => 'Прочитана',
            'time_t' => 'Годин в дорозі'
        ];
    }

    public function rules()
    {
        date_default_timezone_set('Europe/Kiev');
        return [

            [['inn','schet','usluga','summa','date','summa_work','act_work','date_akt',

                'summa_delivery','summa_transport','summa_beznds','summa_tmc',
              'time','res','adres','comment','date_z','status','time_t',
                'contract','geo','kol','date_opl','union_sch','read_z','date_edit','nazv'], 'safe'],

            [['date'], 'default', 'value' => date('Y-m-d')],
            [['date_akt'], 'default', 'value' => date('Y-m-d')],
            ['date_z', 'compare',
                'compareValue' => date('Y-m-d'), 'operator' => '>=',
                'type' => 'string','message' => "Введено минулу дату"],
           // ['date_z','date', 'format' => 'Y-m-d'],
           // [['date_z'], 'only_forward'],
            [['time'], 'default', 'value' => date('H:i')],

        ];
    }

    public function only_forward($date) {
        $d_tek = strtotime(date());
        $date = strtotime($date);
        if($date<$d_tek) $this->addError("Введено минулу дату");
    }

    public function search($params)
    {
        $query = schet::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'usluga', $this->usluga]);
        $query->andFilterWhere(['like', 'inn', $this->inn]);
        $query->andFilterWhere(['like', 'schet', $this->schet]);
        $query->andFilterWhere(['=', 'summa', $this->summa]);

        return $dataProvider;
    }

    public function search1($params,$sql)
    {
        $query = schet::findBySql($sql);
        $query->sql = $sql;

        $dataProvider = new ActiveDataProvider([
            'query' => $query

        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

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


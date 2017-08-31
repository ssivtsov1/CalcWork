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
            'date' => 'Дата заявки:',
            'email' => 'Адреса ел. почти:',
            'time' => 'Час:',
            'surely' => 'Передзвонити:',
            'status' => '* Статус заявки:',
            'status_sch' => 'Статус заявки:',
            'date_z' => '* Бажана дата отримання послуги:',
            'contract' => '№ договору:',
            'summa_work' => 'Вартість робіт,грн.:',
            'summa_transport' => 'Транспорт всього,грн.:',
            'summa_delivery' => 'Доставка бригади,грн.:',
            'summa_beznds' => 'Сума без ПДВ,грн.:',

        ];
    }


    public function rules()
    {
        return [

            [['id','inn','schet','usluga','summa','date',
                'okpo','nazv','addr','tel','summa_work',
                'summa_delivery','summa_transport','summa_beznds',
                'priz_nds','email','adres','status','status_sch',
                'comment','res','time','date_z','contract','geo','kol'], 'safe'],
            ['date_z', 'compare',
                'compareValue' => date('Y-m-d'), 'operator' => '>=',
                'type' => 'string','message' => "Введено минулу дату"],
            ['date_z','date', 'format' => 'Y-m-d'],

        ];
    }

    public function search($params)
    {
        $query = viewschet::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder'=> ['id'=>SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'usluga', $this->usluga]);
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


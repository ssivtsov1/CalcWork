<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

class Potrebitel extends \yii\db\ActiveRecord
{
    public $adr_work;  // Адрес выполнения работ
    public $comment;   // Комментарий заказчика услуги

    public static function tableName()
    {
        return 'klient';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inn' => 'Індивідуальний податковий №:',
            'okpo' => 'ЄДРПОУ:',
            'regsvid' => '№ реєтраційного посвідчення',
            'nazv' => 'Прізвище, ім’я та по батькові:',
            'addr' => 'Адреса:',
            'tel' => 'Телефон:',
            'priz_nds' => 'Платник ПДВ:',
            'person' => '',
            'date_reg' => 'Дата реєстрації:',
            'reg' => 'Признак реєстрації',
            'email' => 'Адреса ел. почти:',
            'contact_person' => 'П.І.Б. контактної особи:',
            'fio_dir' => 'Посада та П.І.Б. уповноваженої особи (стара версія)',
            'pib_dir' => 'П.І.Б. уповноваженої особи:',
            'post_dir' => 'Посада уповноваженої особи:',

        ];
    }

    public function rules()
    {
        return [

            [['inn', 'nazv','addr'],'required','message'=>'Поле обов’язкове'],
            [['tel','priz_nds','okpo','regsvid','reg','person','date_reg',
              'email','fio_dir','contact_person','pib_dir','post_dir'], 'safe'],
            ['inn','string'],
            [['date_reg'], 'default', 'value' => date('Y-m-d')],
            [['reg'], 'default', 'value' => 1],
            [['person'], 'default', 'value' => 1],
            ['inn', 'unique','targetAttribute' => 'inn'],

        ];
    }

   
     public function search($params)
    {
        $query = potrebitel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'nazv', $this->nazv]);
        $query->andFilterWhere(['like', 'addr', $this->addr]);
        $query->andFilterWhere(['like', 'tel', $this->tel]);
        $query->andFilterWhere(['like', 'inn', $this->inn]);
        $query->andFilterWhere(['like', 'okpo', $this->okpo]);
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


<?php
/**
 * Created by PhpStorm.
 * User: ssivtsov
 * Date: 21.06.2017
 * Time: 9:49
 */
namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;


class Klient extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public $adr_work;
    public $comment;
    public $date_z;
    public $ddate;

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
            'regsvid' => '№ регістраційного посвідчення',
            'nazv' => 'Прізвище, ім’я та по батькові:',
            'contact_person' => 'П.І.Б. контактної особи:',
            'fio_dir' => 'Посада та П.І.Б. уповноваженої особи:',
            'addr' => 'Адреса проживання:',
            'tel' => 'Контактний телефон:',
            'priz_nds' => 'Платник ПДВ:',
            'person' => '',
            'date_reg' => 'Дата реєстрації:',
            'reg' => 'Признак реєстрації',
            'email' => 'E-Mail:',
            'adr_work' => 'Адреса виконання робіт:',
            'comment' => 'Коментар споживача:',
            'date_z' => 'Бажана дата отримання послуги:',
        ];
    }


    public function rules()
    {
        return [

            [['inn', 'nazv','addr','email'],'required','message'=>'Поле обов’язкове'],
            [['tel','priz_nds','okpo','regsvid','reg',
              'person','date_reg','email','fio_dir','contact_person'], 'safe'],
            [['adr_work','comment','date_z'], 'safe'],
            ['inn','string','length'=>[10,10],'tooShort'=>'ІНН повинно бути 10 значним',
                'tooLong'=>'ІНН повинно бути 10 значним'],
            [['date_reg'], 'default', 'value' => date('Y-m-d')],
            [['reg'], 'default', 'value' => 1],
            [['person'], 'default', 'value' => 1],
            [['priz_nds'], 'default', 'value' => 0],
            //['date_z', \nepstor\validators\DateTimeCompareValidator::className(),
             //   'compareAttribute' => date('d.m.Y'), 'format' => 'd.m.Y', 'operator' => '>='],
            //['date_z', 'only_forward1', 'skipOnEmpty'=> false],
            //[ ['ddate'],'default','value' => strtotime('date_z')],
           // ['date_z','date', 'format' => 'Y-m-d'],
           
            ['date_z','default', 'value' => date('Y-m-d')],
            ['inn', 'unique','targetAttribute' => 'inn'],
            ['email', 'email','message'=>'Не корректний адрес почти'],

        ];
    }

    public function only_forward1($attribute) {
        $d_tek = strtotime(date('d.m.Y'));
        $date = strtotime($this->$attribute);
        if($date<$d_tek)  $this->addError($attribute,"Введено минулу дату");

    }
   
     public function search($params)
    {
        $query = klient::find();

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
        if (isset(Yii::$app->user->identity->role))
            return Yii::$app->get('db');
        else
            return Yii::$app->get('db');

    }

    public function afterValidate()
    {

        $this->date_z = date("d.m.Y", strtotime($this->date_z));;

    }
}


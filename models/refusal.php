<?php
/**
 Отказы пользователя
 */
namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;


class Refusal extends \yii\db\ActiveRecord
{

   
    public static function tableName()
    {
        return 'refusal';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usluga' => 'Послуга',
            'work' => 'Робота',
            'res_id' => 'РЕМ',
            'adr_work' => 'Адреса виконання',
            'cause' => 'Причина відмови',
            'summa' => 'Сума',
            'date' => 'Дата',
            'time' => 'Час',

        ];
    }

    public function rules()
    {
        return [

            [['id','usluga','work','res_id','adr_work',
            'cause','summa'], 'safe'],
            [['date'], 'default', 'value' => date('Y-m-d')],
            [['time'], 'default', 'value' => date('H:i')]

        ];
    }

//   Метод, необходимый для поиска
     public function search($params)
    {
        $query = refusal::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
             'pageSize' => 15,],
            'sort'=> ['defaultOrder' => ['date'=>SORT_DESC]],
        ]);
        if (!($this->load($params) && $this->validate())) {
           
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'usluga', $this->transport]);
        $query->andFilterWhere(['like', 'work', $this->nomer]);

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



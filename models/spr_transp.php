<?php
/**
 * Created by PhpStorm.
 * User: ssivtsov
 * Date: 03.07.2017
 * Time: 9:49
 */
namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;


class Spr_transp extends \yii\db\ActiveRecord
{

   
    public static function tableName()
    {
//        Используется вид на SQL сервере
        return 'vtransport';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transport' => 'Транспорт',
            'nomer' => 'Номер',
            'locale' => 'Розположення',
            'prostoy' => 'Вартість простою',
            'proezd' => 'Вартість проїзду',
            'rabota' => 'Вартість роботи',
            'nazv' => 'Розположення',

        ];
    }

    public function rules()
    {
        return [

            [['transport','nomer'], 'safe'],
        ];
    }

//   Метод, необходимый для поиска
     public function search($params)
    {
        $query = spr_transp::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,'pagination' => [
            'pageSize' => 15,],
        ]);
        if (!($this->load($params) && $this->validate())) {
           
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'transport', $this->transport]);
        $query->andFilterWhere(['like', 'nomer', $this->nomer]);

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

}



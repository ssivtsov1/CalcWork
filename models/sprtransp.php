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


class Sprtransp extends \yii\db\ActiveRecord
{

   
    public static function tableName()
    {
        return 'transport';
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
           
          
           
        ];
    }
    

    public function rules()
    {
        return [

            [['transport','nomer','proezd','prostoy','locale'], 'safe'],


        ];
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



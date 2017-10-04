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


class Regions extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
   
    public static function tableName()
    {
        return 'regions';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'obl' => 'Область',
        ];
    }


    public function rules()
    {
        return [

            [['id','obl'], 'safe'],
        ];
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


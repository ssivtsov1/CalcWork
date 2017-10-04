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


class Max_schet extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
   
    public static function tableName()
    {
        return 'max_schet';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'value' => 'макс. значення номера заявки',
        ];
    }


    public function rules()
    {
        date_default_timezone_set('Europe/Kiev');
        return [

            [['id','value'], 'safe'],
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


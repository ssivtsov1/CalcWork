<?php
// ТМЦ
namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class Spr_tmc extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'tmc';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name_tmc' => 'Назва ТМЦ',
           
        ];
    }

    public function rules()
    {
        return [
            [['name_tmc', 'id','name_tmc'], 'safe'],
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

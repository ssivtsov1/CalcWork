<?php
// Справочник видов должностей
namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class Spr_brig extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'spr_brig';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nazv' => 'Назва посади',
           
        ];
    }

    public function rules()
    {
        return [
            [['nazv', 'id'], 'safe'],
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

<?php
// Заявки пользователей
namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;


class Describe_fields extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'describe_fields';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name_table' => 'Назва таблиці:',
            'field' => 'Поле таблиці:',
            'describe_f' => 'Опис поля:',
        ];
    }

    public function rules()
    {
        date_default_timezone_set('Europe/Kiev');
        return [

            [['field','name_table','describe_f'
               ], 'safe'],
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


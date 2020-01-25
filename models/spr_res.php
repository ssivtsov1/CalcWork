<?php
// Справочник РЭСов
namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;


class Spr_res extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'spr_res';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nazv' => 'РЕМ',
            'addr' => 'Адреса',
            'geo_koord' => 'Гео-коорд.',
            'geo_fromwhere_sd' => 'Гео-коорд. звідки їде машина (лабораторія)',
            'geo_fromwhere_sz' => "Гео-коорд. звідки їде машина (метрологи)",
            'town_fromwhere_sd'  => 'Місто звідки їде машина (лабораторія)',
            'town_fromwhere_sz' => 'Місто звідки їде машина (метрологи)',
            'tel' => 'Телефон',
            'relat' => 'Коротка назва',
            'mail' => 'Ел.пошта',
            'Director'  => 'Директор',
            'parrent_nazv'  => 'Назва в род. відмінку'
        ];
    }

    public function rules()
    {
        return [

            [['nazv', 'id', 'addr', 'tel'], 'required'],
            [['geo_fromwhere_sd','geo_fromwhere_sz','mail',
                'town_fromwhere_sd','town_fromwhere_sz','nazv',
                'geo_koord','relat','Director','parrent_nazv'],'safe'],
           
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

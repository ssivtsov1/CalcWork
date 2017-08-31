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
            'nazv' => 'Назва РЕМ',
            'addr' => 'Адреса РЕМ',
            'geo_koord' => 'Гео-координати',
            'geo_fromwhere_sd' => 'Гео-координати звідки їде машина (лабораторія)',
            'geo_fromwhere_sz' => "Гео-координати звідки їде машина (метрологи)",
            'town_fromwhere_sd'  => 'Місто звідки їде машина (лабораторія)',
            'town_fromwhere_sz' => 'Місто звідки їде машина (метрологи)',
            'tel' => 'Телефон',
            'relat' => 'Коротка назва'
        ];
    }

    public function rules()
    {
        return [

            [['nazv', 'id', 'addr', 'tel'], 'required'],
            [['geo_fromwhere_sd','geo_fromwhere_sz',
                'town_fromwhere_sd','town_fromwhere_sz',
                'geo_koord','relat'],'safe'],
           
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

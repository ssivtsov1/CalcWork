<?php
/**
 * Модель для вывода разных информационных страниц
 * с небольшим содержанием информации.
 */

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;


class Input_refusal extends Model
{
    public $cause='';     // Причина отказа
    public $sel;

    public function attributeLabels()
    {
        return [
            'sel' => '',
            'cause' => 'Введіть причину відмови:',
        ];
    }


    public function rules()
    {
        return [
            [['cause','sel'], 'safe'],
        ];
    }
}

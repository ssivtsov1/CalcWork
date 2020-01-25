<?php
/*Ввод основных данных для рассчета*/

namespace app\models;

use Yii;
use yii\base\Model;

class InputPeriod extends Model
{
    public $date1;               // Дата нач
    public $date2;              // Дата конец
    public $usl;               // Услуга
    private $_user;

    public function attributeLabels()
    {
        return [
            'date1' => 'Період з ',
            'date2' => 'Період по',
            'usl' => 'Напрямок роботи (послуги):',

        ];
    }

    public function rules()
    {
        return [

            ['date1', 'safe'],
            ['date2', 'safe'],
            ['usl', 'safe'],
        ];
    }

}

<?php
/*Ввод данных для настройки*/

namespace app\models;

use Yii;
use yii\base\Model;

class Settings extends Model
{
    public $contract_hap=true;               // Друк виконавця в шапці договора 
    
    public function attributeLabels()
    {
        return [
            'contract_hap' => 'Друк виконавця в шапці договора',
            ];
    }

    public function rules()
    {
        return [
             ['contract_hap', 'safe'],
        ];
    }

}

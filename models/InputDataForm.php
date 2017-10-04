<?php
/**
 * Created by PhpStorm.
 * User: ssivtsov
 * Date: 21.06.2017
 * Time: 9:41
 */

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;


class InputDataForm extends Model
{
    public $res;
    public $id;
    public $potrebitel;
    public $inn;
    public $addr;
    public $addr_work;   // Адрес работ (вводится для поиска на карте)
    public $nazv = '';
    public $work;
    public $usluga;
    public $kol = 1;
    public $koord = '';
    public $distance = 0;   // Расстояние до объекта туда и назад
    public $poezdka = 1;    // Количество выездов бригады
    public $time_work = 1;  // Время работы в часах (для транспортных услуг)
    public $time_prostoy = 1;  // Время простоя в часах (для транспортных услуг)
    public $adr_potr = '';     // Адрес с карты
    public $geo = '';     // Координаты с карты
    public $region;     // Область
    public $refresh = 0;  // Признак перерасчета заявки
    private $_user;


    public function attributeLabels()
    {
        return [
            'res' => 'РЕМ:',
            'potrebitel' => 'Споживач ІНН:',
            'usluga' => 'Напрямок роботи (послуги):',
            'work' => 'Найменування роботи (послуги):',
            'kol' => 'Кількість калькуляційних одиниць:',
            'distance' => 'Відстань від бази до місця проведення робіт (в обидві сторони),км:',
            'koord' => '',
            'poezdka' => 'Кількість виїздів бригади:',
            'time_work' => 'Кількість годин роботи (тільки для транспортних послуг):',
            'time_prostoy' => 'Кількість годин простою (тільки для транспортних послуг):',
            'nazv' => 'Споживач назва: ',
            'addr' => 'Адреса споживача: ',
            'addr_work' => 'Адреса виконання робіт (для пошуку на карті) - Пишіть українською мовою : ',
            'region' => 'Область:'
        ];
    }

    public function rules()
    {
        return [
            [['work', 'kol', 'distance','poezdka'], 'required'],
            ['res','required','message' => "Введіть РЕМ"],
            ['potrebitel','safe'],
//            ['nazv','required','message' => "Потрібно зареєтруватись."],
//            ['addr','required','message' => "Потрібно зареєтруватись."],
            ['res', 'default', 'value'=>'Дніпропетровський РЕМ'],
            ['potrebitel','string','length'=>[10,10],'tooShort'=>'ІНН повинно бути 10 значним',
                'tooLong'=>'ІНН повинно бути 10 значним'],
            ['time_work', 'safe'],
            ['adr_potr', 'safe'],
            ['geo', 'safe'],
            ['refresh', 'safe'],
            ['region', 'safe'],
            ['time_prostoy', 'safe'],

        ];
    }

}

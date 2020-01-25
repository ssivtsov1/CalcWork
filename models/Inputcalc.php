<?php
/*Ввод основных данных для ввода калькуляций*/

namespace app\models;
use Yii;
use yii\base\Model;

class Inputcalc extends Model
{
    public $expense_brig;  // Расходы бригады
    public $cost_work;        // Стоимость работы
    public $id_brig1;           // Состав бригады для проезда 1
    public $id_brig2;           // Состав бригады для проезда 2
    public $id_brig3;           // Состав бригады для проезда 3
    public $id_brig4;           // Состав бригады для проезда 4
    public $id_brig5;           // Состав бригады для проезда 5
    public $stavka_brig1;           // Ставка 1 человека бригады
    public $stavka_brig2;           // Ставка 2 человека бригады
    public $stavka_brig3;           // Ставка 3 человека бригады
    public $stavka_brig4;           // Ставка 4 человека бригады
    public $stavka_brig5;           // Ставка 5 человека бригады
    public $id_res_transp1;           // № РЭСа для выбора машины 1
    public $id_res_transp2;           // № РЭСа для выбора машины 2
    public $id_res_transp3;           // № РЭСа для выбора машины 3
    public $id_res_transp4;           // № РЭСа для выбора машины 4
    public $id_res_transp5;           // № РЭСа для выбора машины 5
    public $id_res_transp6;           // № РЭСа для выбора машины 6
    public $id_res_transp7;           // № РЭСа для выбора машины 7
    public $id_res_transp8;           // № РЭСа для выбора машины 8
    public $id_auto1;           // Автомобиль 1
    public $id_auto2;           // Автомобиль 2
    public $id_auto3;           // Автомобиль 3
    public $id_auto4;           // Автомобиль 4
    public $id_auto5;           // Автомобиль 5
    public $id_auto6;           // Автомобиль 6
    public $id_auto7;           // Автомобиль 7
    public $id_auto8;           // Автомобиль 8
    public $id_auto9;           // Автомобиль 6
    public $id_auto10;           // Автомобиль 7
    public $id_auto11;           // Автомобиль 8
    public $time_prostoy ;  // Время простоя в часах (для транспортных услуг)
    public $norm_time;         // Норма времени
    public $salary_brig;         // Зарплата бригады
    public $common_expense;         // Общепроизводственные расходы
    public $tmc;               // ТМЦ
    public $other;               // Другие
    public $poverka;               // Поверка
    public $otv_contract;             // Ответственное лицо для договора
    public $work;              // Вид работы
    public $usluga;            // Название услуги
    public $usluga1;               // Название новой услуги
    public $n_work;               // Код услуги


    public function attributeLabels()
    {
        return [
            'expense_brig' => 'Нормативно-кошторисні витрати бригади:',
            'cost_work' => 'Вартість робіт без ПДВ:',
            'usluga' => 'Напрямок роботи (послуги):',
            'usluga1' => 'Напрямок роботи (послуги) нова:',
            'work' => 'Найменування роботи (послуги):',
            'tmc' => 'Матеріали та устаткування для роботи:',
            'id_brig1' => 'Робітник №1:',
            'id_brig2' => 'Робітник №2:',
            'id_brig3' => 'Робітник №3:',
            'id_brig4' => 'Робітник №4:',
            'id_brig5' => 'Робітник №5:',
            'stavka_brig1' => 'Ставка:',
            'stavka_brig2' => 'Ставка:',
            'stavka_brig3' => 'Ставка:',
            'stavka_brig4' => 'Ставка:',
            'stavka_brig5' => 'Ставка:',
            'time_prostoy' => 'Кількість годин простою (тільки для транспортних послуг):',
            'id_auto1' => 'Апостолівська дільниця: ',
            'id_auto2' => 'Вільногірські РЕМ: ',
            'id_auto3' => 'Гвардійська дільниця: ',
            'id_auto4' => 'Дніпровські РЕМ: ',
            'id_auto5' => 'Інгулецькі РЕМ: ',
            'id_auto6' => 'Жовтоводські РЕМ: ',
            'id_auto7' => 'Криворізькі РЕМ: ',
            'id_auto8' => 'Павлоградські РЕМ: ',
            'id_auto9' => 'СЗОЄ: ',
            'id_auto10' => 'СДІЗП: ',
            'id_auto11' => 'Служба підстанцій: ',
            'norm_time' => 'Норма часу: ',
            'salary_brig' => 'Зарплата бригади:',
            'common_expense' => 'Загальн. витрати:',
            'tmc' => 'ТМЦ:',
            'other' => 'Інші:',
            'poverka' => 'Повірка:',
            'otv_contract' => 'Відповідальна особа (для договору):',
            'n_work' => 'Код послуги:',

        ];
    }

    public function rules()
    {
        return [
            [['work','n_work', 'cost_work', 'id_brig1','stavka_brig1','expense_brig'], 'required'],

           [ ['other','poverka','otv_contract', 'common_expense','salary_brig' ,'norm_time',
                'id_auto11','id_auto10','id_auto9','id_auto8','id_auto7','id_auto6','id_auto5',
                'id_auto4','id_auto3','id_auto2','id_auto1','time_prostoy','stavka_brig4','stavka_brig3',
                'stavka_brig2','stavka_brig1','usluga','usluga1','id_brig1','id_brig2','id_brig3'
               ,'id_brig4','id_brig5'],'safe']
        ];
    }
}

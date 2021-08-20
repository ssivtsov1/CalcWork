<?php
/*Ввод данных для аналитики по заявкам*/

namespace app\models;

use Yii;
use yii\base\Model;

class Analytics extends Model
{
    public $res;               // Название РЭСа 
    public $status;            // Название статуса заявки
    public $date1;             // Дата заявки начальный период
    public $date2;             // Дата заявки конечный период
    public $date_opl1;         // Дата оплаты заявки начальный период
    public $date_opl2;         // Дата оплаты заявки конечный период
    public $date_act1;         // Дата акта вып. работ начальный период
    public $date_act2;         // Дата акта вып. работ конечный период
    public $usluga;            // Вид услуги 
    public $work;              // Вид работы
    public $gr_status=0;         // статус заявки [груп. опер.]
    public $gr_status_sch=0;         // статус заявки [груп. опер.]
    public $gr_res=0;            // Название РЭСа [груп. опер.]
    public $gr_date=0;           // Дата заявки [груп. опер.]
    public $gr_date_opl=0;       // Дата оплаты [груп. опер.]
    public $gr_usl=0;         // Вид услуги [груп. опер.]
    public $gr_usluga=0;           // Вид работы [груп. опер.]
    public $gra_summa=0;         // Сумма с НДС [груп. опер. поле в агрегатной функции]
    public $gra_summa_beznds=0;      // Сумма без ндс [груп. опер. поле в агрегатной функции]
    public $gra_summa_work=0;        // Сумма стоимости работы [груп. опер. поле в агрегатной функции]
    public $gra_summa_transport=0;   // Сумма стоимости проезда [груп. опер. поле в агрегатной функции]
    public $gra_summa_delivery=0;    // Сумма стоимости доставки бригады [груп. опер. поле в агрегатной функции]
    public $gra_kol=0;        // Добавити кількість
    public $gra_oper;          // Группировочная операция (sum,max,min ...)
    public $grh_having;        // Фильтровочно - группировочная операция (внутри having: =, >, < ...)
    public $grh_value;         // Значение внутри having
    public $grs_sort;          // Поля сортировки
    public $grs_dir;           // Направление сортировки
    public $ord='';               // Порядок группировки полей 
    public $kol;
    

    public function attributeLabels()
    {
        return [
            'res' => 'Підрозділ:',
            'status' => 'Статус:',
            'usluga' => 'Напрямок роботи (послуги):',
            'work' => 'Найменування роботи (послуги):',
            'date1' => 'Дата заявки початкова:',
            'date2' => 'Дата заявки кінцева:',
            'date_opl1' => 'Дата оплати початкова:',
            'date_opl2' => 'Дата оплати кінцева:',
            'date_act1' => 'Дата акта початкова:',
            'date_act2' => 'Дата акта кінцева:',
            'gr_status' => 'Статус заявки:',
            'gr_status_sch' => 'Статус заявки:',
            'gr_res' => 'Підрозділ:',
            'gr_date' => 'Дата заявки:',
            'gr_date_opl' => 'Дата оплати:',
            'gr_usl' => 'Напрямок роботи (послуги):',
            'gr_usluga' => 'Найменування роботи (послуги):',
            'gra_summa' => 'Сума з ПДВ:',
            'gra_summa_beznds' => 'Сума без ПДВ:',
            'gra_summa_work' => 'Сума роботи:',
            'gra_summa_transport' => 'Сума транспорту:',
            'gra_summa_delivery' => 'Сума дост. бригади:',
            'gra_kol' => 'Добавити кількість:',
            'gra_oper' => 'Операція:',
            'grh_having' => 'Операція:',
            'grh_value' => 'Значення:',
            'grs_sort' => 'Поле сортування:',
            'grs_dir' => 'Вид сортування:',
            'kol' => 'Кількість:',
            
        ];
    }

    public function rules()
    {
        return [
            ['res', 'safe'],
            ['grh_value', 'safe'],
            ['grh_having', 'safe'],
            ['gra_oper', 'safe'],
            ['gra_summa_delivery', 'safe'],
            ['gra_summa_transport', 'safe'],
            ['gra_summa_work', 'safe'],
            ['gra_summa', 'safe'],
            ['gra_summa_beznds', 'safe'],
            ['gra_kol', 'safe'],
            ['status', 'safe'],
            ['status_sch', 'safe'],
            ['gr_usluga', 'safe'],
            ['gr_usl', 'safe'],
            ['gr_res', 'safe'],
            ['gr_work', 'safe'],
            ['gr_date', 'safe'],
            ['gr_date_opl', 'safe'],
            ['gr_status', 'safe'],
            ['gr_status_sch', 'safe'],
            ['date_opl1', 'safe'],
            ['date1', 'safe'],
            ['date2', 'safe'],
            ['date_opl2', 'safe'],
            ['usluga', 'safe'],
            ['work', 'safe'],
            ['grs_sort', 'safe'],
            ['grs_dir', 'safe'],
            ['ord', 'safe'],
            ['kol', 'safe'],
        ];
    }

}

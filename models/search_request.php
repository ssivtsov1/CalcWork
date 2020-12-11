<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;



class Search_request extends Model
{
    public $name;
    public $text;
    public $tel;
    public $date1;
    public $date2;
    public $summ1;
    public $summ2;
    public $summ_pdv;
    public $summ_c;
    public $pidrozdil;
    public $status;
    public $result;
    public $sorting;
    public $summ_pdv_1;
    public $usl;

    public function attributeLabels()
    {
        return [
            'name' => 'Заявка',
            'text' => 'Споживач',
            'tel' => 'Телефон',
            'date1' => 'Дата оплати з ',
            'date2' => 'Дата оплати по ',
            'summ1' => 'Сума з ПДВ, грн з',
            'summ2' => 'Сума з ПДВ, грн по',
            'summ_pdv' => 'Сума з ПДВ, грн (приблизно)',
            'summ_c' => 'Сума з ПДВ з двох складових, грн',
            'summ_pdv_1' => 'Сума з ПДВ, грн (точно)',
            'pidrozdil' => 'Підрозділ',
            'status' => 'Статус заявки',
            'result' => 'Сортувати результат по полю',
            'sorting' => 'Сортування за',
            'usl' => 'Напрямок роботи (послуги):',
        ];
    }

    public function rules()
    {
        return [
            [['summ1', 'summ2', 'summ_pdv','pidrozdil','status','result','sorting',
                'summ_c','tel','name','text','date1','date2','summ_pdv_1','usl'], 'safe'],

        ];
    }

}
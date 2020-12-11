<?php
/*Ввод основных данных для рассчета*/

namespace app\models;

use Yii;
use yii\base\Model;

class InputData extends Model
{
    public $n_cnt;               // № договора
    public $sch;
    public $sch1;
    public $mail;
    private $_user;

    public function attributeLabels()
    {
        return [
            'n_cnt' => '№ договору ',

        ];
    }

    public function rules()
    {
        return [

            ['n_cnt', 'safe'],
            ['sch', 'safe'],
            ['sch1', 'safe'],
            ['mail', 'safe'],
        ];
    }

}

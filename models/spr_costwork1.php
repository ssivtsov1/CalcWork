<?php
// Справочник видов работ
namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;

class Spr_costwork1 extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'costwork';
    }


    public function rules()
    {
        return [
            [['work'], 'required'],
            [['T_Ap','T_Vg','T_Krr','T_Yv','T_Dn','T_Pvg','T_Gv','T_Sp','T_ing','Szoe',
                'Sdizp','calc_ind','verification'],'safe']
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

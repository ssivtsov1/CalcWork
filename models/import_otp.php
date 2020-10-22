<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\helpers;

class Import_otp extends Model
{
    public $file;

    public function attributeLabels()
    {
        return [
            'file' => 'Виберіть файл виписки',

        ];
    }
    public function rules()
    {

        return [
            [['file'],'file','skipOnEmpty' => true,'extensions'=>'xml']
            ];
    }


    public function upload($d)
    {
        $path = $this->$d->basename.'.'.$this->$d->extension;
        $this->$d->saveas($path);
        return true;
    }


}


<?php
/**
 * Created by PhpStorm.
 * User: ssivtsov
 * Date: 21.06.2017
 * Time: 9:49
 */
namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;


class Spr_uslug extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'spr_uslug';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usluga' => 'Назва послуги',
            'kod' => 'Код послуги',

        ];
    }

    public function rules()
    {
        return [

            [['nazv', 'id','kod'], 'required'],

        ];
    }

    //   Метод, необходимый для поиска
    public function search($params)
    {
        $query = spr_uslug::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,'pagination' => [
                'pageSize' => 15,],
        ]);
        if (!($this->load($params) && $this->validate())) {

            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'usluga', $this->usluga]);

        return $dataProvider;
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

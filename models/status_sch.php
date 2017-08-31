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


class Status_sch extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'status_sch';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nazv' => 'Назва статусу',

        ];
    }

    public function rules()
    {
        return [

            [['nazv', 'id'], 'required'],

        ];
    }

    //   Метод, необходимый для поиска
    public function search($params)
    {
        $query = status_sch::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,'pagination' => [
                'pageSize' => 15,],
        ]);
        if (!($this->load($params) && $this->validate())) {

            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'nazv', $this->nazv]);

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

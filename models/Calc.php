<?php
// Используется для рассчетов стоимости работ 
namespace app\models;

use Yii;
use yii\db\ActiveRecord;


class Calc extends ActiveRecord
{
    public $cost;
    public $nom_tr;
    public $transport;
    public $proezd;
    public $prostoy;
    public $rabota;
    public $a;
    public $nomer;
    public $id;
    public $nom;
    public $all_p;
    public $all_move;

    public static function tableName()
    {
        return 'costwork';
    }

    public function rules()
    {
        return [
            ['work','usluga','safe'],
            ['kod_uslug', 'safe'],
            ['brig', 'safe'],
            ['time_transp', 'safe'],
            ['stavka_grn', 'safe'],
            ['exec', 'safe'],
        ];
    }

//  Формирование строки SQL запроса для связи с несколькими таблицами
    public static function Calc($vid_work, $res, $distance)
    {   $m = intval(date('n'));
        $v = '';
        $r = 'a.T_';
        if ($m == 4 || $m == 5 || $m == 9 || $m == 10 || $m == 11) $v = $v . '+a.cast_1';
        if ($m == 3) $v = $v . '+a.cast_2';
        if ($m == 3 || $m == 4 ||  $m == 5 || $m == 9 || $m == 10 || $m == 11) $v = $v . '+a.cast_3';
        if ($m == 6 || $m == 7 ||  $m == 8) $v = $v . '+a.cast_4';
        if ($m == 12) $v = $v . '+a.cast_5';
        if ($m == 1 || $m == 2) $v = $v . '+a.cast_6';
        if(substr($v, 0, 1)=='+') $v = substr($v, 1);
        switch ($res) {
            case 1:
                $r = $r . 'Ap';
                break;
            case 2:
                $r = $r . 'Vg';
                break;
            case 3:
                $r = $r . 'Gv';
                break;
            case 4:
                $r = $r . 'Dn';
                break;
            case 5:
                $r = $r . 'Yv';
                break;
            case 6:
                $r = $r . 'Pvg';
                break;
            case 7:
                $r = $r . 'Ing';
                break;
            case 8:
                $r = $r . 'Krr';
                break;
            case 9:
                $r = $r . 'Sp';
                break;
            case 12:
                $r = $r . 'Dn';
               // $r = 'a.Szoe';
                break;
            case 13:
                $r = $r . 'Dn';
                //$r = 'a.Sdizp';
                break;
            case 11:
                $r = 'a.T_Zp';
                break;
            case 14:
                $r = $r . 'Sc';
                break;
        }
        if($distance)
          $sql = 'SELECT '.$v.' as cost,a.work,a.usluga,
                  case when a.lic=1 then 0 else a.stavka_grn end as stavka_grn,
                  case when a.lic=1 then 0 else a.time_transp end as time_transp,'.$r.' as nom_tr,b.transport,
                  case when a.lic=1 then 0 else b.proezd end as proezd,
                  case when a.lic=1 then 0 else b.prostoy end as prostoy,
                  case when a.lic=1 then 0 else b.rabota end as rabota'.
                ' from costwork a left join transport b on '.$r.'=ltrim(rtrim(b.nomer)) where a.id='.$vid_work;
//        $sql = 'SELECT '.$v.' as cost,a.work,a.usluga,a.stavka_grn,a.time_transp,'.$r.' as nom_tr,b.transport,b.proezd,b.prostoy,b.rabota'.
//        ' from costwork a,transport b where a.id='.$vid_work.
//        ' and '.$r.'=ltrim(rtrim(b.nomer))';
        else
//            Если не указано расстояние - время простоя транспорта берется 0
//        $sql = 'SELECT '.$v.' as cost,a.work,a.usluga,a.stavka_grn,0 as time_transp,'.$r.' as nom_tr,b.transport,b.proezd,b.prostoy,b.rabota'.
//        ' from costwork a,transport b where a.id='.$vid_work.
//        ' and '.$r.'=ltrim(rtrim(b.nomer))';
        $sql = 'SELECT '.$v.' as cost,a.work,a.usluga,case when a.lic=1 then 0 else a.stavka_grn end as stavka_grn,
                0 as time_transp,'.$r.' as nom_tr,b.transport,
                case when a.lic=1 then 0 else b.proezd end as proezd,
                case when a.lic=1 then 0 else b.prostoy end as prostoy,
                case when a.lic=1 then 0 else b.rabota end as rabota'.
            ' from costwork a left join transport b on '.$r.'=ltrim(rtrim(b.nomer)) where a.id='.$vid_work;



        return $sql;
    }

    //  Формирование строки SQL запроса для связи с несколькими таблицами
    //  для оперативно-технического обслуживания
    public static function Calc_oto($vid_work, $res='', $distance='')
    {
            $sql = 'SELECT a.*,b.number as nom_tr,cast(replace(b.all_move,",",".") as decimal(8,2)) as all_move,
             cast(replace(b.all_p,",",".") as decimal(8,2)) as all_p'.
                ' from costwork a left join a_transport b on trim(a.work)=ltrim(rtrim(b.number))
                 where a.id='.$vid_work;

        return $sql;
    }


//    Не используется
    public static function T_Stavka($vid_work)
    {  
        $sql = 'SELECT work from costwork where id='.$vid_work;
        return $sql;
    }

    public static function getDb()
    {
        return Yii::$app->get('db');
    }

}

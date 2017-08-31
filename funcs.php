<?php
function debug($var)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}

// Нормализация № телефона
function tel_normal($tel){
    $len = strlen($tel);
    $rez = '';
    switch ($len){
        case 7:
            $op = substr($tel,0,3);
            $rez.=$op.'-';
            $add = substr($tel,3,2);
            $rez.=$add.'-';
            $add = substr($tel,5);
            $rez.=$add;
            return $rez;
        case 6:
            $op = substr($tel,0,2);
            $rez.=$op.'-';
            $add = substr($tel,2,2);
            $rez.=$add.'-';
            $add = substr($tel,4);
            $rez.=$add;
            return $rez;
        case 5:
            $op = substr($tel,0,1);
            $rez.=$op.'-';
            $add = substr($tel,1,2);
            $rez.=$add.'-';
            $add = substr($tel,3);
            $rez.=$add;
            return $rez;
    }
}

?>

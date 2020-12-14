// Выгрузка за период в САП
    public function actionUpload_sap($date1,$date2,$usl)
    {

        $sql = "select usluga,work from costwork WHERE id=$usl";
        $z = viewschet::findBySql($sql)->asArray()->all();
        $u=trim($z[0]['usluga']);
        $w=trim($z[0]['work']);

        $sql = "select distinct a.*,b.* from vschet a 
                inner join costwork b on b.work=a.usluga WHERE a.date_akt>='$date1' and a.date_akt<='$date2'
                 and trim(b.usluga)='$u' and a.status=7";

        $z2 = viewschet::findBySql($sql)->asArray()->all();

//        debug($sql);
//        return;

        $u1=$u;
        $other = '0';
        $hap = "БО;Номер послуги;МВП (підрозділ);ТМЦ;Зарплата бригади;ЄСВ_Зарплата бригади;Відрядження Добові_Бригади;Відрядження Проїзд_Бригади;Відрядження Проживання_Бригади;А-92_А/транспорт;А-95 А/транспорт;ДП А/транспорт;Газ_А/транспорт;Автомастила_А/транспорт;Зарплата водіїв;ЄСВ_зарплата водіїв;Амортизація_А/транспорт;Відрядження Добові_Водії;Відрядження Проживання_Водії;Повірка приладів обліку;Інші;Ремонт підр.спос.;Загальновиробничі витрати;АКТ/Особовий рахунок;№ договора (тільки для приєднання);Нормативні кошторисні трудовитрати бригади, люд-год. ;Нормативні кошторисні трудовитрати водіїв, люд-год. ";
        $fn = date('d.m.Y') . '.csv';
        $f = fopen($fn, "w+");
//        $hap = mb_convert_encoding($hap, 'CP1251', mb_detect_encoding($hap));
        $cnt=0;

//        debug($sql);
//        return;

        foreach($z2 as $z) {

            if ($u1 != 'Оперативно-технічне обслуговування') {
                $cnt++;
                $res = mb_substr($z['contract'], 0, 2, "UTF-8");

                $pole = viewschet::tr_res($res);  // Определение поля с данными по автомобилю
                $time_t = $z['time_t'];        // Время проезда
                $time_prostoy = $z['time_prostoy'];        // Время простоя
                $time_work = $z['time_work'];             // Время работы
                $n_work = trim($z['n_work']);
                $norm_time = trim($z['norm_time']);
                $u=trim($z['work']);
                $code_mvp=$z['mvp'];
                $sch=$z['schet'];

                $sql = "select zp,common_minus,time_transp,tmc,repair,usluga from costwork where work=:search";
                $z1 = viewschet::findBySql($sql, [':search' => "$u"])->asArray()->all();
                $zp = (float)str_replace(',', '.', $z1[0]['zp']);
                $zp_e = round(0.22 * $zp, 2);
                $cm = $z1[0]['common_minus'];

                $tmc = $z1[0]['tmc']; // ТМЦ
                $repair = $z1[0]['repair']; // Ремонты
                $tr_usl = ($z1[0]['usluga'] == 'Транспортні послуги') ? 1 : 0;

                if ($tr_usl == 0)
                    $time_prostoy = $z1[0]['time_transp']; // Время простоя


                $sql = "select $pole as nomer from costwork a where a.work=:search and $pole is not null";


                $z1 = viewschet::findBySql($sql, [':search' => "$u"])->asArray()->all();
                if (count($z1) > 0)
                    $nomer = $z1[0]['nomer'];
                else
                    $nomer = '';

                $sql = "select * from vw_transport a where a.number=:search";

                $z1 = viewschet::findBySql($sql, [':search' => "$nomer"])->asArray()->all();
                if (count($z1) > 0) {
                    $oil = $z1[0]['oil_p'];
                    $amort = $z1[0]['amort'];
                    $wage = $z1[0]['wage'];
                    $c92 = $z1[0]['cost_92_move'];
                    $c95 = $z1[0]['cost_95_move'];
                    $cdf = $z1[0]['cost_df_move'];
                    $cg = $z1[0]['cost_g_move'];

                    $c92w = $z1[0]['cost_92_work'];
                    $c95w = $z1[0]['cost_95_work'];
                    $cdfw = $z1[0]['cost_df_work'];
                    $cgw = $z1[0]['cost_g_work'];
                } else {
                    $oil = '0';
                    $amort = '0';
                    $wage = '0';
                    $c92 = '0';
                    $c95 = '0';
                    $cdf = '0';
                    $cg = '0';

                    $c92w = '0';
                    $c95w = '0';
                    $cdfw = '0';
                    $cgw = '0';

                }
                $esv = 22;

                // Транспорт проезд
                $fuel_92 = (float)str_replace(',', '.', $c92);
                $fuel_95 = (float)str_replace(',', '.', $c95);
                $fuel_df = (float)str_replace(',', '.', $cdf);
                $fuel_g = (float)str_replace(',', '.', $cg);
                $time_t = (float)str_replace(',', '.', $time_t);
                if ($tr_usl == 0)
                    $time_prostoy = (float)str_replace(',', '.', $time_prostoy);


                $oil_c = (float)str_replace(',', '.', $oil);
                $amort_c = (float)str_replace(',', '.', $amort);

                $oil = round($oil_c * $time_t, 2);
                $fuel_92 = round($fuel_92 * $time_t, 2);
                $fuel_95 = round($fuel_95 * $time_t, 2);
                $fuel_df = round($fuel_df * $time_t, 2);
                $fuel_g = round($fuel_g * $time_t, 2);
                $amort = round($amort_c * $time_t, 2);
                $zp_drive_c = (float)str_replace(',', '.', $wage);
                $zp_drive = round($zp_drive_c * $time_t, 2);

                // Транспорт простой
                $oil_prostoy = round($oil_c * $time_prostoy, 2);
                $amort_prostoy = round($amort_c * $time_prostoy, 2);
                $zp_drive_prostoy = round($zp_drive_c * $time_prostoy, 2);

                // Транспорт работа
                $fuel_92_work = (float)str_replace(',', '.', $c92w);
                $fuel_95_work = (float)str_replace(',', '.', $c95w);
                $fuel_df_work = (float)str_replace(',', '.', $cdfw);
                $fuel_g_work = (float)str_replace(',', '.', $cgw);

                if ($tr_usl == 0) $time_work = $time_prostoy;

                if ($tr_usl == 1) {
                    $oil_work = round($oil_c * $time_work, 2);
                    $amort_work = round($amort_c * $time_work, 2);
                    $zp_drive_work = round($zp_drive_c * $time_work, 2);
                } else {
                    $oil_work = 0;
                    $amort_work = 0;
                    $zp_drive_work = 0;
                }

                $fuel_92_work = round($fuel_92_work * $time_work, 2);
                $fuel_95_work = round($fuel_95_work * $time_work, 2);
                $fuel_df_work = round($fuel_df_work * $time_work, 2);
                $fuel_g_work = round($fuel_g_work * $time_work, 2);


                // --Подсчет итога--
                // Топливо:
                $fuel_92 = $fuel_92 + $fuel_92_work;        // 92-й бензин
                $fuel_95 = $fuel_95 + $fuel_95_work;        // 95-й бензин
                $fuel_df = $fuel_df + $fuel_df_work;        // Диз. топливо
                $fuel_g = $fuel_g + $fuel_g_work;           // Газ
                // Другие показатели
                $oil = $oil + $oil_prostoy + $oil_work;                 // Масло
                $amort = $amort + $amort_prostoy + $amort_work;           // Аммортизация
                $zp_drive = $zp_drive + $zp_drive_prostoy + $zp_drive_work;  // Зарплата водителей

                if ($time_t == 0) {
                    $fuel_92 = 0;
                    $fuel_95 = 0;
                    $fuel_df = 0;
                    $fuel_g = 0;
                    $oil = 0;
                    $zp_drive = 0;
                    $amort = 0;
                }

                if (($fuel_92 + $fuel_95 + $fuel_df + $fuel_g) > 0) $priz_proezd = 1;
                else $priz_proezd = 0;


                // Преобразуем точку в запятую в показателях
                $time_t = str_replace('.', ',', $time_t);
                $oil = str_replace('.', ',', $oil);
                $fuel_92 = str_replace('.', ',', $fuel_92);
                $fuel_95 = str_replace('.', ',', $fuel_95);
                $fuel_df = str_replace('.', ',', $fuel_df);
                $fuel_g = str_replace('.', ',', $fuel_g);
                $zp = str_replace('.', ',', $zp);
                $zp_e = str_replace('.', ',', $zp_e);
                $zp_esv = str_replace('.', ',', round($esv * $zp_drive / 100, 2));
                $zp_d = str_replace('.', ',', $zp_drive);
                $cm = trim(str_replace('.', ',', $cm));
                $amort = str_replace('.', ',', $amort);
            }

            if ($u1 == 'Оперативно-технічне обслуговування') {
                $cnt++;
                $res = mb_substr($z['contract'], 0, 2, "UTF-8");

                $time_t = $z['time_t'];        // Время проезда
                $time_prostoy = $z['time_prostoy'];        // Время простоя
                $time_work = $z['time_work'];             // Время работы
                $n_work = trim($z['n_work']);
                $norm_time = trim($z['norm_time']);
                $code_mvp=$z['mvp'];
                $sch=$z['schet'];

                $sql = "select a.zp,a.common_minus,a.time_transp,a.tmc,a.repair,a.usluga,a.other,
                    b.* from costwork a left join a_transport b on trim(a.work)=trim(b.number) 
                    where a.work=:search";
                $z1 = viewschet::findBySql($sql, [':search' => "$w"])->asArray()->all();

//                debug($sql);
//                return;

                $zp = (float)str_replace(',', '.', $z1[0]['zp']);
                $zp_e = round(0.22 * $zp, 2);
                $cm = $z1[0]['common_minus'];
                $time_prostoy = $z1[0]['time_transp']; // Время простоя
                $tmc = trim($z1[0]['tmc']); // ТМЦ
                $repair = $z1[0]['repair']; // Ремонты
                $other = $z1[0]['other'];   // Другие
                $tr_usl = 0;

                if (count($z1) > 0) {
                    $oil_p = $z1[0]['oil_p'];
                    $oil_move = $z1[0]['oil_move'];
                    $amort = $z1[0]['amort'];
                    $amort_move = $z1[0]['amort_move'];
                    $wage = $z1[0]['wage'];
                    $wage_move = $z1[0]['wage_move'];
                    $c92 = $z1[0]['cost_92_move'];
                    $c95 = $z1[0]['cost_95_move'];
                    $cdf = $z1[0]['cost_df_move'];
                    $cg = $z1[0]['cost_g_move'];

                    $c92w = $z1[0]['cost_92_work'];
                    $c95w = $z1[0]['cost_95_work'];
                    $cdfw = $z1[0]['cost_df_work'];
                    $cgw = $z1[0]['cost_g_work'];
                } else {
                    $oil = '0';
                    $amort = '0';
                    $wage = '0';
                    $c92 = '0';
                    $c95 = '0';
                    $cdf = '0';
                    $cg = '0';

                    $c92w = '0';
                    $c95w = '0';
                    $cdfw = '0';
                    $cgw = '0';

                }
                $esv = 22;


                // Транспорт проезд
                $fuel_92 = (float)str_replace(',', '.', $c92);
                $fuel_95 = (float)str_replace(',', '.', $c95);
                $fuel_df = (float)str_replace(',', '.', $cdf);
                $fuel_g = (float)str_replace(',', '.', $cg);
                $time_t = (float)str_replace(',', '.', $time_t);
                $oil_p = (float)str_replace(',', '.', $oil_p);
                $oil_move = (float)str_replace(',', '.', $oil_move);
                $amort = (float)str_replace(',', '.', $amort);
                $amort_move = (float)str_replace(',', '.', $amort_move);
                $wage = (float)str_replace(',', '.', $wage);
                $wage_move = (float)str_replace(',', '.', $wage_move);

                if ($tr_usl == 0)
                    $time_prostoy = (float)str_replace(',', '.', $time_prostoy);

                // Транспорт простой
                $oil_prostoy = round($oil_p * $time_prostoy, 2);
                $amort_prostoy = round($amort * $time_prostoy, 2);
                $zp_drive_prostoy = round($wage * $time_prostoy, 2);

                // --Подсчет итога--
                // Другие показатели
                $oil = $oil_move + $oil_prostoy;                 // Масло
                $amort = $amort_move + $amort_prostoy;           // Аммортизация
                $zp_drive = $wage_move + $zp_drive_prostoy;  // Зарплата водителей


                if ($time_t == 0) {
                    $fuel_92 = 0;
                    $fuel_95 = 0;
                    $fuel_df = 0;
                    $fuel_g = 0;
                    $oil = 0;
                    $zp_drive = 0;
                    $amort = 0;
                }


                if (($fuel_92 + $fuel_95 + $fuel_df + $fuel_g) > 0) $priz_proezd = 1;
                else $priz_proezd = 0;

                // Преобразуем точку в запятую в показателях
                $time_t = str_replace('.', ',', $time_t);
                $oil = str_replace('.', ',', $oil);
                $fuel_92 = str_replace('.', ',', $fuel_92);
                $fuel_95 = str_replace('.', ',', $fuel_95);
                $fuel_df = str_replace('.', ',', $fuel_df);
                $fuel_g = str_replace('.', ',', $fuel_g);
                $zp = str_replace('.', ',', $zp);
                $zp_e = str_replace('.', ',', $zp_e);
                $zp_esv = str_replace('.', ',', round($esv * $zp_drive / 100, 2));
                $zp_d = str_replace('.', ',', $zp_drive);
                $cm = trim(str_replace('.', ',', $cm));
                $amort = str_replace('.', ',', $amort);
            }


//        fputs($f,$tr_usl);
//        fputs($f,$priz_proezd);

            if($cnt==1)
            fputs($f, $hap);

            debug($time_t);
            debug($priz_proezd);
            debug($tr_usl);
            return;

            if ($priz_proezd == 0 || $tr_usl == 1) {

                $e[0] = 'CK01';         // const
                $e[1] = $n_work;       // № услуги
                $e[2] = $code_mvp;    //  МВП пока const
                $e[3] = $tmc;        // ТМЦ
                $e[4] = $zp;        // Зарплата бригады
                $e[5] = $zp_e;     // Соц. взнос от зарплаты бригады
                $e[6] = 0;        // Коммандировки
                $e[7] = 0;       // Коммандировки
                $e[8] = 0;      // Коммандировки
                $e[9] = $fuel_92;        // топливо а-92
                $e[10] = $fuel_95;       // топливо а-95
                $e[11] = $fuel_df;       // топливо дт
                $e[12] = $fuel_g;        // топливо газ
                $e[13] = $oil;        // масло
                $e[14] = $zp_d;       // зп водителей
                $e[15] = $zp_esv;        // Соц. взнос от зарплаты водителей
                $e[16] = $amort;        // Аммортизация транспорт
                $e[17] = 0;        // Коммандировки
                $e[18] = 0;        // Коммандировки
                $e[19] = 0;        // Поверка средств учета
                $e[20] = $other;        // other
                $e[21] = $repair;        // Ремонт
                $e[22] = $cm;        // Общепроизводственные затраты
                $e[23] = $sch;     // Счет
                $e[24] = '';       // № договора
                $e[25] = $norm_time;      // Нормативные трудозатраты бригады
                $e[26] = $time_t;        // Нормативные трудозатраты водителей
                fputs($f, "\n");
                $content = implode(";", $e);
                $content = mb_convert_encoding($content, 'CP1251', mb_detect_encoding($content));
                fputs($f, $content);
            } else {

                $e[0] = 'CK01';         // const
                $e[1] = $n_work;       // № услуги
                $e[2] = $code_mvp;    //  МВП пока const
                $e[3] = $tmc;        // ТМЦ
                $e[4] = $zp;        // Зарплата бригады
                $e[5] = $zp_e;     // Соц. взнос от зарплаты бригады
                $e[6] = 0;        // Коммандировки
                $e[7] = 0;       // Коммандировки
                $e[8] = 0;      // Коммандировки
                $e[9] = 0;        // топливо а-92
                $e[10] = 0;       // топливо а-95
                $e[11] = 0;       // топливо дт
                $e[12] = 0;        // топливо газ
                $e[13] = 0;        // масло
                $e[14] = 0;       // зп водителей
                $e[15] = 0;        // Соц. взнос от зарплаты водителей
                $e[16] = 0;        // Аммортизация транспорт
                $e[17] = 0;        // Коммандировки
                $e[18] = 0;        // Коммандировки
                $e[19] = 0;        // Поверка средств учета
                $e[20] = $other;        // other
                $e[21] = $repair;        // Ремонт
                $e[22] = $cm;        // Общепроизводственные затраты
                $e[23] = $sch;     // Счет
                $e[24] = '';       // № договора
                $e[25] = $norm_time;      // Нормативные трудозатраты бригады
                $e[26] = 0;        // Нормативные трудозатраты водителей
                fputs($f, "\n");
                $content = implode(";", $e);
                $content = mb_convert_encoding($content, 'CP1251', mb_detect_encoding($content));
                fputs($f, $content);


                $e[0] = 'CK01';         // const
                if ($tr_usl == 0)
                    $e[1] = '2300000380';       // № услуги
                else
                    $e[1] = '2300000385';       // № услуги

                $e[2] = $code_mvp;    //  МВП пока const
                $e[3] = 0;        // ТМЦ
                $e[4] = 0;        // Зарплата бригады
                $e[5] = 0;     // Соц. взнос от зарплаты бригады
                $e[6] = 0;        // Коммандировки
                $e[7] = 0;       // Коммандировки
                $e[8] = 0;      // Коммандировки
                $e[9] = $fuel_92;        // топливо а-92
                $e[10] = $fuel_95;       // топливо а-95
                $e[11] = $fuel_df;       // топливо дт
                $e[12] = $fuel_g;        // топливо газ
                $e[13] = $oil;        // масло
                $e[14] = $zp_d;       // зп водителей
                $e[15] = $zp_esv;        // Соц. взнос от зарплаты водителей
                $e[16] = $amort;        // Аммортизация транспорт
                $e[17] = 0;        // Коммандировки
                $e[18] = 0;        // Коммандировки
                $e[19] = 0;        // Поверка средств учета
                $e[20] = 0;        // other
                $e[21] = 0;        // Ремонт
                $e[22] = 0;        // Общепроизводственные затраты
                $e[23] = $sch;     // Счет
                $e[24] = '';       // № договора
                $e[25] = 0;      // Нормативные трудозатраты бригады
                $e[26] = $time_t;        // Нормативные трудозатраты водителей
                fputs($f, "\n");
                $content = implode(";", $e);
                $content = mb_convert_encoding($content, 'CP1251', mb_detect_encoding($content));
                fputs($f, $content);

            }
        }
//        debug($e);
//        return;

        if (file_exists($fn)) {
            return \Yii::$app->response->sendFile($fn);
        }
        else{
            $model = new info();
            $model->title = 'УВАГА!';
            $model->info1 = "Помилка при формуванні файлу експорту в САП.";
            $model->style1 = "d15";
            $model->style2 = "info-text";
            $model->style_title = "d9";

            return $this->render('about', [
                'model' => $model]);
        }
    }

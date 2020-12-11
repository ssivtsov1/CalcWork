<?php
// Ввод основных данных для рассчета стоимости работ и услуг

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

$this->title = 'Розрахунок вартості робіт';
//echo phpinfo();
?>

 <script>

     window.onload=function(){

        $("#inputdataform-region").val(3);

        $('.field-inputdataform-transp_cek').hide();
        localStorage.setItem("lat1", '');
        localStorage.setItem("lng1", '');
        localStorage.setItem("geo_res", '');
        localStorage.setItem("geo_lat", '');
        localStorage.setItem("geo_lng", '');
        localStorage.setItem("geo_lat_sd", '');
        localStorage.setItem("geo_lng_sd", '');
        localStorage.setItem("geo_lat_sz", '');
        localStorage.setItem("geo_lng_sz", '');
        localStorage.setItem("id_res", '');
        localStorage.setItem("usluga", '');
        //localStorage.setItem("work", '');
        localStorage.setItem("town_sz", '');
        localStorage.setItem("town_sd", '');
        var geo,y1,p1,lat,lon;
        geo = $("#inputdataform-geo").val();

        localStorage.setItem("geo_marker", '');
        localStorage.setItem("geo_k", '');
        if(geo!='') {
            y1 = geo.length;
            p1 = geo.indexOf(',') - 1
            lat = geo.substring(0, p1);
            lon = geo.substring(p1 + 2);
            localStorage.setItem("geo_lat", lat);
            localStorage.setItem("geo_lng", lon);
            localStorage.setItem("geo_lat_save", lat);
            localStorage.setItem("geo_lng_save", lon);
            localStorage.setItem("geo_marker", '('+geo+')');
            localStorage.setItem("geo_k", geo);
            $("#inputdataform-res").change();
            //initMap();
        }

        var p,u = $("#inputdataform-potrebitel").val();
        p = u.length;
        if(p!=0)
        $("#inputdataform-potrebitel").blur();
    });
   </script>
   
   <script>
       
    function set_dist(p){   
        if(p==0)
           $('.dst').val(0);
    }
    // Показывает или прячет блок время работы (в зависимости от выбранной машины)
    // работает только при выборе транспортных услуг
    // если первый символ текста выбр. машины '-', тогда работа прячется, т.к. сумма работы в табл. Transport
    // для этого автомобиля не проставлена.
    function hidepole_rabota(p){
       var f = p.substr(1,1);
        if(p.length>98){
        $('.img_uslug').show();
        $('.span_work').text(p);
        $('.span_work').slideDown('slow');
        }
        else
        {
          $('.img_uslug').hide();  
          $('.span_work').text('');
        }    
    
    if(localStorage.getItem("usluga")=="transp") {
        if (f == '-') {
            $('.field-inputdataform-time_work').hide();
        }
        else {
            $('.field-inputdataform-time_work').show();
        }
    }
    
        
    }

    function fill_mvp(r){
        //alert(r);
        if(r==3) r=6;
        if(r==1 || r==7) r=8;
        $.ajax({
            url: '/CalcWork/web/site/getmvp',

            data: {res: r },
            type: 'GET',
            success: function(data){
                $("#inputdataform-mvp").empty();
                for(var ii = 0; ii<data.mvp.length; ii++) {
                    var q = data.mvp[ii].descr;

                    if (q == null) continue;
                    var q1 = q.substr(3);
                    var n = q.substr(0, 3);

                    $("#inputdataform-mvp").append("<option value=" + n +
                        " style=" + String.fromCharCode(34) + "font-size: 10px;" +
                        String.fromCharCode(34) + ">" + q1 + "</option>");
                }

                    if(!res) alert('Данные не верны!');
            },
            error: function (data) {
                console.log('Error', data);
            },

        });

    }


    function find_on_map(addr){

        localStorage.setItem("addr_work", addr);
                var addr_work = addr;
                var region = $("#inputdataform-region option:selected").text();
                if(typeof(addr_work)!='undefuned' && addr_work!=''){
                    //addr_work = addr_work+region+' область';
                    addr_work = region+' область '+addr_work;

                    var addr_request = 'https://maps.googleapis.com/maps/api/geocode/json?'+
                            'components=country:UA'+'&key='+'AIzaSyDSyQ_ATqeReytiFrTiqQAS9FyIIwuHQS4'+
                            '&address='+addr_work;
                  
                    $.getJSON('/CalcWork/web/site/getloc?loc='+addr_request, function(data) {

                        var lat1 = data.output.results[0].geometry.location.lat;
                        var lng1 = data.output.results[0].geometry.location.lng;
                        localStorage.setItem("lat1",lat1);
                        localStorage.setItem("lng1",lng1);
                         //var location = {lat: alat, lng: alng};

                    });

                }
       
        setTimeout(function () {
                        initMap();
                    }, 1700); // время в мс
                    

    }
    //$(document).ready(function(){
   
</script>

<div class="site-login">

    <h2><?= Html::encode($this->title) ?></h2>

    <p>Введіть параметри для розрахунку:</p>

    <div class="row">
        <div class="col-lg-6">
            <?php $form = ActiveForm::begin(['id' => 'inputdata',
                'options' => [
                    'class' => 'form-horizontal col-lg-25',
                    'enctype' => 'multipart/form-data'
                    
                ]]);

            ?>


            <?php if($role==5)
                echo $form->field($model, 'nazv1')->textInput();
                //select id,concat(town,'  (',nazv,')') as nazv from spr_res
            ?>    
            <? if($model->refresh==0): ?>
            <?=$form->field($model, 'res')->dropDownList(
                    ArrayHelper::map(app\models\spr_res::findbysql(
                            select_res($role))->all(), 'id', 'nazv'), 
            [
            'prompt' => 'Виберіть виробничий підрозділ, який обслуговує Ваш регіон.',
            'onchange' => '$.get("' . Url::to('/CalcWork/web/site/getres?id=') . 
             '"+$(this).val(),
                    function(data) {
                    
//                     $.getJSON("/CalcWork/web/site/getmvp?res=1", function(data) {
//
//                         //$("#inputdataform-mvp").empty();
//                         alert("mvp");
//
//                    });
                    
                    
                     $("#inputdataform-addr_work").empty();
                     localStorage.setItem("lat1", "");
                     localStorage.setItem("lng1", "");
                     localStorage.setItem("geo_res", data.geo_koord);
                     var geo_marker = localStorage.getItem("geo_marker");
                     //alert(geo_marker);
                     if(geo_marker=="" || geo_marker==null) {
                     localStorage.setItem("geo_lat", data.lat);
                     localStorage.setItem("geo_lng", data.lng);
                     localStorage.setItem("geo_lat_save", data.lat);
                     localStorage.setItem("geo_lng_save", data.lng);
                     }
                     localStorage.setItem("geo_lat_res", data.lat);
                     localStorage.setItem("geo_lng_res", data.lng);
                     
                     localStorage.setItem("geo_lat_sd", data.lat_sd);
                     localStorage.setItem("geo_lng_sd", data.lng_sd);
                     localStorage.setItem("geo_lat_sz", data.lat_sz);
                     localStorage.setItem("geo_lng_sz", data.lng_sz);
                     localStorage.setItem("town_sd", data.town_sd);
                     localStorage.setItem("town_sz", data.town_sz);
                     localStorage.setItem("id_res", data.id);
                     //alert(data.geo_koord); 
                    // if(("#inputdataform-usluga").val()=="Транспортні послуги")
//                     if(geo_marker=="")
//                     var qza = $("#inputdataform-work").val();
//                     alert(qza);

                     //if(geo_marker=="")
                     $("#inputdataform-usluga").change();
                     
                     //$("#inputdataform-work").change();
                     
//                     if(geo_marker=="")
//                     $("#inputdataform-work").val(qza);
                     
                    $(".dst").val(0); 
                    $(".primech").text("");
                    $(".adr_potr").text("");
                    
//                var addr_work = localStorage.getItem("addr_work");
//                //alert("change_res");
//                if(typeof(addr_work)!="undefuned" && addr_work!=""){
//                    addr_work = addr_work+"+Дніпропетровська+область";
//                    var addr_request = "https://maps.googleapis.com/maps/api/geocode/json?"+
//                            "components=country:UA"+"&key="+"AIzaSyDSyQ_ATqeReytiFrTiqQAS9FyIIwuHQS4"+
//                            "&address="+addr_work;
//
//
//                    //alert(addr_request);
//                    $.getJSON("/CalcWork/web/site/getloc?loc="+addr_request, function(data) {
//
//                        var lat1 = data.output.results[0].geometry.location.lat;
//                        var lng1 = data.output.results[0].geometry.location.lng;
//                        localStorage.setItem("lat1",lat1);
//                        localStorage.setItem("lng1",lng1);
//                        //alert("blur lng1");
//                        
//                         //var location = {lat: alat, lng: alng};
//
//                    });
//
//                    //alert(location);
//                }

                    setTimeout(function () {
                        initMap();
                    }, 1000); // время в мс
                    
                    var punct = $("#inputdataform-res :selected").text();
                    
                    var pos = punct.indexOf("(");
                    punct = punct.substr(0,pos);
                    $("#inputdataform-addr_work").val(punct);
                    
                    //var region_res = $("#inputdataform-res option:selected").text();
                    if(punct == "м. Запоріжжя  ")
                        $("#inputdataform-region").val(7);
                    else
                        $("#inputdataform-region").val(3);
                        
                    //if(!$("#inputdataform-addr_work").val()){
                        
                    
                    $("#inputdataform-addr_work").blur();
                    
                    $("#inputdataform-mvp1").blur();
                   
                                      
                    
                       
                });',
                     ]
                    ) ?>
            <? endif;  ?>

            <? if($model->refresh==1): ?>
                <?=$form->field($model, 'res')->dropDownList(
                    ArrayHelper::map(app\models\spr_res::findbysql(
                        "select id,concat(town,'  (',nazv,')') as nazv from spr_res")->all(), 'id', 'nazv'),
                    [
                        'prompt' => 'Виберіть виробничий підрозділ, який обслуговує Ваш регіон.',
                        'onchange' => '$.get("' . Url::to('/CalcWork/web/site/getres?id=') .
                            '"+$(this).val(),
                    function(data) {
                     
                     $("#inputdataform-addr_work").empty();
                     localStorage.setItem("lat1", "");
                     localStorage.setItem("lng1", "");
                     localStorage.setItem("geo_res", data.geo_koord);
                     var geo_marker = localStorage.getItem("geo_marker");

                     if(geo_marker=="") {
                     localStorage.setItem("geo_lat", data.lat);
                     localStorage.setItem("geo_lng", data.lng);
                     }
                     localStorage.setItem("geo_lat_res", data.lat);
                     localStorage.setItem("geo_lng_res", data.lng);
                     
                     localStorage.setItem("geo_lat_sd", data.lat_sd);
                     localStorage.setItem("geo_lng_sd", data.lng_sd);
                     localStorage.setItem("geo_lat_sz", data.lat_sz);
                     localStorage.setItem("geo_lng_sz", data.lng_sz);
                     localStorage.setItem("town_sd", data.town_sd);
                     localStorage.setItem("town_sz", data.town_sz);
                     localStorage.setItem("id_res", data.id);
                     //alert(data.geo_koord); 
                    // if(("#inputdataform-usluga").val()=="Транспортні послуги")
//                     if(geo_marker=="")
//                     var qza = $("#inputdataform-work").val();
//                     alert(qza);

                     //if(geo_marker=="")
                     $("#inputdataform-usluga").change();
                     
                     //$("#inputdataform-work").change();
                     
//                     if(geo_marker=="")
//                     $("#inputdataform-work").val(qza);
                     
                    $(".dst").val(0); 
                    $(".primech").text("");
                    $(".adr_potr").text("");
                    
//                var addr_work = localStorage.getItem("addr_work");
//                //alert("change_res");
//                if(typeof(addr_work)!="undefuned" && addr_work!=""){
//                    addr_work = addr_work+"+Дніпропетровська+область";
//                    var addr_request = "https://maps.googleapis.com/maps/api/geocode/json?"+
//                            "components=country:UA"+"&key="+"AIzaSyDSyQ_ATqeReytiFrTiqQAS9FyIIwuHQS4"+
//                            "&address="+addr_work;
//
//
//                    //alert(addr_request);
//                    $.getJSON("/CalcWork/web/site/getloc?loc="+addr_request, function(data) {
//
//                        var lat1 = data.output.results[0].geometry.location.lat;
//                        var lng1 = data.output.results[0].geometry.location.lng;
//                        localStorage.setItem("lat1",lat1);
//                        localStorage.setItem("lng1",lng1);
//                        //alert("blur lng1");
//                        
//                         //var location = {lat: alat, lng: alng};
//
//                    });
//
//                    //alert(location);
//                }

                    setTimeout(function () {
                        initMap();
                    }, 1000); // время в мс
                    
//                    var punct = $("#inputdataform-res :selected").text();
//                    var pos = punct.indexOf("(");
//                    punct = punct.substr(0,pos);
                    punct = $("#inputdataform-geo").val();
                    //alert(punct);
                    //$("#inputdataform-addr_work").val(punct);
                    
                    //var region_res = $("#inputdataform-res option:selected").text();
                    if(punct == "м. Запоріжжя  ")
                        $("#inputdataform-region").val(7);
                    else
                        $("#inputdataform-region").val(3);
                        
                    //if(!$("#inputdataform-addr_work").val()){
                        
                    
                    $("#inputdataform-addr_work").blur();
                    
                       
                });',
                    ]
                ) ?>
            <? endif;  ?>
           
            <?= $form->field($model, 'potrebitel')->textInput(
                ['maxlength' => true,'onblur' => '$.get("' . Url::to('/CalcWork/web/site/getklient?inn=') .
                    '"+$(this).val(),
                    function(data) {
                    // alert(data.nazv); 
                    $(".field-inputdataform-nazv").show();
                    $(".field-inputdataform-addr").show();
                    $("#inputdataform-nazv").empty();
                    $("#inputdataform-addr").empty();
                    $("#inputdataform-nazv").val(data.nazv); 
                    $("#inputdataform-addr").val(data.addr);
                    if(data.nazv=="")
                    {$(".nazv_kl").text("Споживач не зареєстрований. Потрібно зареєтруватись.");
                     $(".nazv_kl").show();
                     $(".s_reestr").show();
                    }
                    else
                    { $(".nazv_kl").hide();
                      $(".s_reestr").hide();
                      $(".help-block-error").text("");
                      $("#inputdataform-nazv").focus();
                      $("#inputdataform-nazv").blur();
                    }
                       
                });',
                ]) ?>


            <?= $form->field($model, 'mvp1')->textInput(['maxlength' => true,
                'onBlur' => 'fill_mvp(localStorage.getItem("id_res"))']) ?>

<!--            --><?//=$form->field($model, 'mvp1')->
//            dropDownList(ArrayHelper::map(
//                app\models\sprav_mvp::findbysql('Select min(id) as id,descr from sprav_mvp  group by 2 order by 2')
//                    ->all(), 'id', 'descr'),
//                [
//                    'prompt' => 'Виберіть підрозділ в головній службі',
//                    'onchange' => '$.get("' . Url::to('/CalcWork/web/site/getmvp?res=') .
//                        '"+localStorage.getItem("id_res"),
//                    function(data) {
//                         $("#inputdataform-mvp").empty();
//                         for(var ii = 0; ii<data.mvp.length; ii++) {
//                         var q = data.mvp[ii].descr;
//                         //alert(q);
//                         if(q==null) continue;
//                         var q1 = q.substr(3);
//                         var n = q.substr(0,3);
//
//                         $("#inputdataform-mvp").append("<option value="+n+
//                         " style="+String.fromCharCode(34)+"font-size: 10px;"+
//                         String.fromCharCode(34)+">"+q1+"</option>");
//                  });',
//                ]
//            ) ?>

            <?=$form->field($model, 'mvp')->
            dropDownList(ArrayHelper::map(
                app\models\sprav_mvp::findbysql('Select min(id) as id,descr from sprav_mvp  group by 2 order by 2')
                    ->all(), 'id', 'descr'),
                [

                ]
            ) ?>

            <?= $form->field($model, 'calc_ind')
                ->dropDownList([
                    '1' => 'Типова',
                    '2' => 'Індивідуальна',
                ],
                    [
                        'prompt' => ''
                    ]);?>


            <span class="nazv_kl"></span>

            <?php  if(strpos(Yii::$app->request->url,'/web')==0)
                        {
                          echo "<a class='s_reestr' href=./web/site/registr>Реєстрація</a>";}
                    else
                        { echo "<a class='s_reestr' href=./site/registr>Реєстрація</a>";}
            ?>

<!--            <a class="s_reestr" href="./web/site/registr">Реєстрація</a>-->

            <?= $form->field($model, 'nazv')->textInput(['readonly' => true]) ?>
            <?= $form->field($model, 'addr')->textInput(['readonly' => true]) ?>

<!--            <span class="nazv_kl"></span>-->
<!--            <br/>-->
<!--            <span class="adr_kl"></span>-->

            <?=$form->field($model, 'usluga')->
            dropDownList(ArrayHelper::map(
               app\models\spr_costwork::findbysql('Select min(id) as id,usluga from costwork where LENGTH(ltrim(rtrim(usluga)))<>0 group by usluga order by usluga')
                   ->all(), 'id', 'usluga'),
                    [
            'prompt' => 'Виберіть послугу',
            'onchange' => '$.get("' . Url::to('/CalcWork/web/site/getworks?id=') . 
             '"+$(this).val()+"&res="+localStorage.getItem("id_res")+"&calc_ind="+$("#inputdataform-calc_ind").val(),
                    function(data) {
                         var flag=0,fl=0;
                         var id_usl=$("#inputdataform-usluga").val();
                         if(id_usl==220){
                            $("#inputdataform-distance").val(1);
                            //$("#inputdataform-distance").attr("disabled", true);
                         }
                         else
                         {  
                            $("#inputdataform-distance").val(0);
                            $("#inputdataform-distance").attr("disabled", false);
                         }
                         var geo_marker = localStorage.getItem("geo_marker");
                         if(geo_marker!="")
                         {var tmp_work = $("#inputdataform-work").val();
                         }
                         localStorage.setItem("work", "");
                         localStorage.setItem("usluga", "");
                         //if(geo_marker=="")
                         $("#inputdataform-work").empty();
                         for(var ii = 0; ii<data.works.length; ii++) {
                         var q = data.works[ii].work;
                         //alert(q);
//                         alert($(this).val());
                         if(q==null) continue;
                         var q1 = q.substr(3);
                         var n = q.substr(0,3);
                         //var pr_rab = q.substr(4,1);
                         //if(geo_marker=="") 
                         $("#inputdataform-work").append("<option value="+n+
                         " style="+String.fromCharCode(34)+"font-size: 10px;"+
                         String.fromCharCode(34)+">"+q1+"</option>");
                         
                         if((+n>=166) && (+n<193)) flag=1; // Транспортні послуги
                         
                         if((+n==209) || (+n==210)) flag=0;
                         if((+n>=211) && (+n<=214)) flag=0;
                      // if((+n>=219) && (+n<=222)) flag=2;
                         if(+n==90)  fl=1;
                         if((+n==88) || (+n==37)) fl=2;
//                         alert(n);
//                         alert(flag);
                        } 
                         if(geo_marker!="")
                         {$("#inputdataform-work").val(tmp_work);
                         }
                        
                         if(flag==1) {
                             localStorage.setItem("usluga", "transp");  // Признак что выбраны Транспортні послуги
                             $(".field-inputdataform-time_work").show();
                             $(".field-inputdataform-time_prostoy").show();
                             $(".field-inputdataform-kol").hide();
                             $(".field-inputdataform-poezdka").hide();
                         }
                          if(flag==2) {
                             $(".field-inputdataform-tmc").show();
                         }
                         else
                        {
                             $(".field-inputdataform-tmc").hide();
                         }
                         if(flag==0) {
                             $(".field-inputdataform-time_work").hide();
                             $(".field-inputdataform-time_prostoy").hide();
                             $(".field-inputdataform-kol").show();
                             $(".field-inputdataform-poezdka").show();
                             
                         }
                         if(fl==1) localStorage.setItem("work", "sd");
                         if(fl==2) localStorage.setItem("work", "sz");
                         $(".primech").text("");
                         //alert(localStorage.getItem("work"));
                         var geo_marker = localStorage.getItem("geo_marker");
                         //if(geo_marker=="") 
                         $("#inputdataform-work").change();
                  });',
                     ]
                    ) ?>
            
                
<!--            --><?//=$form->field($model, 'work')->
//            dropDownList(ArrayHelper::map(
//               app\models\spr_costwork::findbysql('Select min(id) as id,work from costwork where '
//                       . 'hide=0 and work is not null group by work order by work')
//                   ->all(), 'id', 'work'),
//                ['onchange' => 'hidepole_rabota($("#inputdataform-work :selected").text());']
//                ) ?>

        <!--            --><?//=$form->field($model, 'work')->
        //    dropDownList(ArrayHelper::map(
        //    app\models\spr_costwork::findbysql('Select min(id) as id,work from costwork where '
        //    . 'hide=:hide and work is not null group by work order by work',[':hide' => 0])
        //   all(), 'id', 'work'),
        //    ['onchange' => 'hidepole_rabota($("#inputdataform-work :selected").text());']
        //    ) ?>

            <?=$form->field($model, 'work')->
            dropDownList(ArrayHelper::map(
            app\models\spr_costwork::findbysql('Select min(id) as id,work from costwork where '
            . 'hide=:hide and work is not null group by work order by work',[':hide' => 0])
            ->all(), 'id', 'work')
//            ['onchange' => '$.get("' . Url::to('/CalcWork/web/site/gettransp_cek?id=') .
//                    '"+$(this).val(),
//                    function(data) {
//                     //alert(data.transp_cek);
//                    var r = $("#inputdataform-work :selected").text();
//                    hidepole_rabota(r);
//                    if(data.transp_cek==0){
//                         $(".field-inputdataform-transp_cek").show();
//                        }
//                    else
//                    {
//                        $(".field-inputdataform-transp_cek").hide();
//                    }
//
//
//                });',]
            ) ?>

           <img src="sarrow31.png" class="img_uslug">
           <span class="span_work"><? echo $model->work; ?> </span>

            <?=$form->field($model, 'tmc')->
            dropDownList(ArrayHelper::map(
                app\models\spr_tmc::findbysql('Select id,name_tmc from tmc')
                    ->all(), 'id', 'name_tmc'),
                []
            ) ?>
            
            <?= $form->field($model, 'kol') ?>
            <?= $form->field($model, 'time_work') ?>
            <?= $form->field($model, 'time_prostoy') ?>
            <?= $form->field($model, 'transp_cek')->checkbox([
                'onchange' => 'set_dist(this.checked);',
                'label' => 'Транспорт ЦЕК',
                'labelOptions' => [
                    'class' => 'lab-tr'
                ],
              ]); ?>

            <?= $form->field($model, 'geo') ?>

             <?=$form->field($model, 'region',['inputTemplate' => '<div class="input-group"><span class="input-group-addon">'
            . '<span class="glyphicon glyphicon-globe"></span></span>{input}</div>'])->
            dropDownList(ArrayHelper::map(
               app\models\regions::findbysql('Select id,obl from regions')
                   ->all(), 'id', 'obl'),
                    [
                    'prompt' => 'Виберіть область',
                     ]
                    ) ?>
            <?php $model->region = 3; //"Дніпропетровська"
//             if($model->refresh==1)
//                 $model->addr_work = $model->geo;
            ?>

            <?= $form->field($model, 'addr_work',['inputTemplate' => '<div class="input-group"><span class="input-group-addon">'
            . '<span class="glyphicon glyphicon-globe"></span></span>{input}</div>'])->textInput(['maxlength' => true,
                'onBlur' => 'find_on_map($(this).val())','onDblClick' => 'rmenu($(this).val(),"#inputdataform-addr_work")']) ?>
                 <div class='rmenu' id='rmenu-inputdataform-addr_work'></div> 
            <p>Виберіть на карті місце виконання робіт (для обліку транспортних витрат):</p>
            <div id="map_q"></div>

            <?= $form->field($model, 'distance')->textInput(['maxlength' => 255, 'class' => 'dst']) ?>
            <span class="primech"></span>
            <span class="adr_potr"></span>

            <? echo $form->field($model, 'adr_potr')->hiddenInput(['value' => ''])->label(false); ?>

            <?= $form->field($model, 'koord')->textInput(['maxlength' => 255, 'class' => 'distance']) ?>
            
            
            <div style="color:#999;margin:1em 0">
                <!-- If you forgot your password you can <?= Html::a('reset it', ['site/request-password-reset'])  ?>. -->
            </div>

            <div class="form-group">
                <?= Html::submitButton('OK', ['class' => 'btn btn-primary']); ?>
<!--                --><?//= Html::a('OK', ['/CalcWork/web'], ['class' => 'btn btn-success']) ?>
            </div>

            <?php

            ActiveForm::end(); ?>
        </div>
    </div>
</div>

 <script type="text/javascript">

        // Определяем переменную map
        var map;
            
        var vzoom = 0;
        var geo_marker = localStorage.getItem("geo_marker");
        if(geo_marker=='') {
            if (localStorage.getItem("geo_koord") == null) {
                localStorage.setItem("geo_lat", 48.446203);
                localStorage.setItem("geo_lng", 35.002512);
            }
        }
         
        // Функция initMap которая отрисует карту на странице
        function initMap() {
            
            
            var geo_marker = localStorage.getItem("geo_marker");

            if(geo_marker!='') {
            var lat1 = +localStorage.getItem("geo_lat");
            var lng1 = +localStorage.getItem("geo_lng");

            }
            else{
                var lat1 = +localStorage.getItem("geo_lat_res");
                var lng1 = +localStorage.getItem("geo_lng_res");
            }
            var idr = localStorage.getItem("id_res");

            var addr_work = localStorage.getItem("addr_work");
            //var addr_work = $("#inputdataform-addr_work").text();
            
            if(typeof(addr_work)!='undefuned' && addr_work!=''){
                 var lat1 = +localStorage.getItem("lat1");
                 var lng1 = +localStorage.getItem("lng1");
                 var vzoom = 0;
                 if ( addr_work.search(/\d/) != -1 ) vzoom = 1;
             }
            
                  
            //while(true){
            if(lat1==48.446203)
            {    
            // В переменной map создаем объект карты GoogleMaps и вешаем эту переменную на <div id="map"></div>
            map = new google.maps.Map(document.getElementById('map_q'), {
                // При создании объекта карты необходимо указать его свойства
                // center - определяем точку на которой карта будет центрироваться
                center: {lat: 48.446203, lng: 35.002512},
               
                // zoom - определяет масштаб. 0 - видно всю планету. 18 - видно дома и улицы города.
                zoom: 17
                
            });
            }
            else
            {    
                   
                if(!vzoom)
                map = new google.maps.Map(document.getElementById('map_q'), {
                // При создании объекта карты необходимо указать его свойства
                // center - определяем точку на которой карта будет центрироваться
                center: {lat: lat1, lng: lng1},
                // zoom - определяет масштаб. 0 - видно всю платнеу. 18 - видно дома и улицы города.
                 zoom: 15
            });
                
                else
                 map = new google.maps.Map(document.getElementById('map_q'), {
                // При создании объекта карты необходимо указать его свойства
                // center - определяем точку на которой карта будет центрироваться
                center: {lat: lat1, lng: lng1},
                 zoom: 17
            });   
            }
        // if(typeof(map)!='undefuned') break; 
        //}
       
              
        var marker;
        $('.distance').val('');

 //           alert(localStorage.getItem("geo_marker"));
//            alert(localStorage.getItem("geo_k"));
            var geo_k = localStorage.getItem("geo_k");
            var geo_marker = localStorage.getItem("geo_marker");

        //alert(geo_k);
        if(geo_marker!='') {
//            var lat_save = localStorage.getItem("geo_lat_save");
//            var lon_save = localStorage.getItem("geo_lng_save");

            var myLatLng = {lat: lat1, lng: lng1};
            marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
            });

            lat1 = +localStorage.getItem("geo_lat_res");
            lng1 = +localStorage.getItem("geo_lng_res");

            //            Уст. координат откуда будет ехать машина
            if(localStorage.getItem("work")=='sd'
                && ((idr==1) || (idr==2) || (idr==3) || (idr==4) || (idr==7) || (idr==8) || (idr==11)))
            {
                var lat1 = +localStorage.getItem("geo_lat_sd");
                var lng1 = +localStorage.getItem("geo_lng_sd");
                var town = localStorage.getItem("town_sd");
                if(idr!=4)
                $('.primech').text('Увага! Машина їде з міста '+town+'.');
//                localStorage.setItem("work", '');
            }

            if(localStorage.getItem("work")=='sz'
                && ((idr==1) || (idr==2) || (idr==3) || (idr==4) || (idr==6) || (idr==7) || (idr==11)))
            {
                var lat1 = +localStorage.getItem("geo_lat_sz");
                var lng1 = +localStorage.getItem("geo_lng_sz");

                var town = localStorage.getItem("town_sz");
                if(idr!=4)
                $('.primech').text('Увага! Машина їде з міста '+town+'.');
//                localStorage.setItem("work", '');
            }


            var url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins="+
                lat1+','+lng1+'&destinations='+'1';
            url = url + geo_k;
            // alert(url);

            $.getJSON('/CalcWork/web/site/getdist?url='+url, function(data) {

                a=data.output.rows[0].elements[0].distance.value;
                a=Number(a)*2/1000;
                a=a.toFixed(2);

                tr_cek = $("#inputdataform-transp_cek").prop('checked');
                if(tr_cek==true){
                if(a>1700)
                    $('.dst').val(0);
                else
                    $('.dst').val(a);
                }
                else{
                     $('.dst').val(0);
                 }
                if(a>1700)
                    adr = '';
                else
                    adr = 'Адреса виконання робіт: '+data.output.destination_addresses;

                adr = adr.replace("Украина","Україна");

                if(a<=1700)
                {
                    $('.adr_potr').text(adr);
                    $('.dst').val(a);
                }
                else
                {
                    $('.dst').val(0);
                }
                $('#inputdataform-adr_potr').val(adr);
                $('#inputdataform-geo').val(k);
                //localStorage.setItem("geo_marker","1");

            });

        }

         google.maps.event.addListener(map, 'click', function(e) {
       
         var location = e.latLng;
         
         //localStorage.setItem("geo_marker","");
         $("#inputdataform-geo").val('1');
         $('.distance').val(location);
        if(marker != undefined) marker.setMap(null);
         marker = new google.maps.Marker({
             position: location,
             map: map
         });
 
        var url,k;
         var geo_marker = localStorage.getItem("geo_marker");

         if(geo_marker=='') {
             var lat1 = localStorage.getItem("geo_lat");
             var lng1 = localStorage.getItem("geo_lng");
         }
             else {
             var lat1 = localStorage.getItem("geo_lat_res");
             var lng1 = localStorage.getItem("geo_lng_res");
         }

            $('.primech').text("");

//            Уст. координат откуда будет ехать машина
            if(localStorage.getItem("work")=='sd'
                && ((idr==1) || (idr==2) || (idr==3) || (idr==4) || (idr==7) || (idr==8) || (idr==11)))
            {
                var lat1 = +localStorage.getItem("geo_lat_sd");
                var lng1 = +localStorage.getItem("geo_lng_sd");
                var town = localStorage.getItem("town_sd");
                if(idr!=4)
                $('.primech').text('Увага! Машина їде з міста '+town+'.');
            }

            if(localStorage.getItem("work")=='sz'
                && ((idr==1) || (idr==2) || (idr==3) || (idr==4) || (idr==6) || (idr==7) || (idr==11)))
            {
                var lat1 = +localStorage.getItem("geo_lat_sz");
                var lng1 = +localStorage.getItem("geo_lng_sz");
                var town = localStorage.getItem("town_sz");
                if(idr!=4)
                $('.primech').text('Увага! Машина їде з міста '+town+'.');
            }

        if(lat1=="48.446203")
        url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=48.446203,35.002512&destinations=";
        else
        {
            url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins="+lat1+','+lng1+'&destinations=';
        }    
        k = location.toString();


        l = k.length;
        k = k.substring(1,l-1);
        url = url + k;

        // Определение расстояния с ресурса calc-api.ru
        //      k=k.replace('(', '');
        //      k=k.replace(')', '');
        //      k=k.replace(', ', '%20');
        //      url = "http://calc-api.ru/app:geo-api/null?a="+lat1+"%20"+lng1+"&b="+k;
        //      url="http://router.project-osrm.org/viaroute?loc=57.818023681640625,28.342592239379883&loc=57.812679290771484,28.28318214416504";
             //alert(url);
             // $.ajax({
             //     url: url,
             //     dataType: "json",
             //     success: function(result){
             //         //console.log(result);
             //         alert(result);
             //     }
             // });


        // Определение расстояния с ресурса GoogleMap
        url = url + "&key=" + "AIzaSyDSyQ_ATqeReytiFrTiqQAS9FyIIwuHQS4";

        var priz=1,cnt_i=1;
       //r while (priz==1) {
         //r   if(cnt_i>5) break;

          //r   alert("Попытка "+cnt_i);

            setTimeout($.getJSON('/CalcWork/web/site/getdist?url=' + url, function (data) {
                a = data.output.status;
                //alert(a);
                if (a == 'OK') {
                    priz=0;

                    a = data.output.rows[0].elements[0].distance.value;
                    a = Number(a) * 2 / 1000;
                    a = a.toFixed(2);

                    tr_cek = $("#inputdataform-transp_cek").prop('checked');
                    if (tr_cek == true) {
                        if (a > 1700)
                            $('.dst').val(0);
                        else
                            $('.dst').val(a);
                    }
                    else {
                        $('.dst').val(0);
                    }

                    adr = 'Адреса виконання робіт: ' + data.output.destination_addresses;
                    adr = adr.replace("Украина", "Україна");
                    if (a <= 1700) {
                        $('.adr_potr').text(adr);
                        $('.dst').val(a);
                    }
                    else {
                        $('.dst').val(0);
                    }
                    $('#inputdataform-adr_potr').val(adr);
                    $('#inputdataform-geo').val(k);
                    //alert("Рассчет произведен");

                }
                 else{
                    //sleep(300);
                    cnt_i++;
                    //alert(cnt_i);
                 }


            }),1500);
        //}
         });
      }

      function handleLocationError(browserHasGeolocation, infoWindow, pos) {
        infoWindow.setPosition(pos);
        infoWindow.setContent(browserHasGeolocation ?
                              'Error: The Geolocation service failed.' :
                              'Error: Your browser doesn\'t support geolocation.');

      }

   //map.setMapDisplayLanguage(new Locale("ru"));
   //map.setMapSecondaryDisplayLanguage(new Locale("ru"));


    </script>
    
    <script



    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDSyQ_ATqeReytiFrTiqQAS9FyIIwuHQS4&callback=initMap&language=ru&region=UA"
        async defer>
            
    
            
    </script>
    
 
<?php
    // AIzaSyCio9wud5U4kI-Q_da8fgJ1yBSzFVvyFjo
    // AIzaSyDSyQ_ATqeReytiFrTiqQAS9FyIIwuHQS4
    function select_res($role) {
        if(!($role>0)) 
//            $r = "select id,concat(town,'  (',nazv,')') as nazv from spr_res where id<>12 and id<>13";
              if ($role==13)
                  $r = "select a.id,concat(a.town,'  (',a.nazv,')') as nazv,b.role,b.department from costwork.spr_res a 
                  left join costwork.user b on trim(a.nazv) = trim(b.department) COLLATE utf8_unicode_ci
                  where a.id<>12 and a.id<>13
                  and a.id in (1,7,8)";
              else
                  $r = "select a.id,concat(a.town,'  (',a.nazv,')') as nazv,b.role,b.department from costwork.spr_res a 
                  left join costwork.user b on trim(a.nazv) = trim(b.department) COLLATE utf8_unicode_ci
                  where a.id<>12 and a.id<>13
                  and case when $role<6 then 1=1 else b.role=$role and b.department is not null end ";
        else
//            $r = "select id,concat(town,'  (',nazv,')') as nazv from spr_res";
            if ($role==13)
                $r = "select a.id,concat(a.town,'  (',a.nazv,')') as nazv,b.role,b.department from costwork.spr_res a 
                  left join costwork.user b on trim(a.nazv) = trim(b.department) COLLATE utf8_unicode_ci
                  where a.id in (1,7,8)";
            else
                $r = "select a.id,concat(a.town,'  (',a.nazv,')') as nazv,b.role,b.department from costwork.spr_res a 
                  left join costwork.user b on trim(a.nazv) = trim(b.department) COLLATE utf8_unicode_ci
                  where case when $role<6 then 1=1 else b.role=$role and b.department is not null end ";


            return $r;

    }

?>




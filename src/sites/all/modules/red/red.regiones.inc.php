<?php
function red_regiones_define_regiones_options($with_empty=0){
    $result=array();
    if($with_empty){
        $result['']='';
    }
    $result['EUROPE']=array();
    $result['EUROPE']['Western Europe']=array();
    $result['EUROPE']['Western Europe']['Andorra']='Andorra';
    $result['EUROPE']['Western Europe']['Austria']='Austria';
    $result['EUROPE']['Western Europe']['Belgium']='Belgium';
    $result['EUROPE']['Western Europe']['Denmark']='Denmark';
    $result['EUROPE']['Western Europe']['Finland']='Finland';
    $result['EUROPE']['Western Europe']['France']='France';
    $result['EUROPE']['Western Europe']['Germany']='Germany';
    $result['EUROPE']['Western Europe']['Greece']='Greece';
    $result['EUROPE']['Western Europe']['Iceland']='Iceland';
    $result['EUROPE']['Western Europe']['Ireland']='Ireland';
    $result['EUROPE']['Western Europe']['Italy']='Italy';
    $result['EUROPE']['Western Europe']['Liechtenstein']='Liechtenstein';
    $result['EUROPE']['Western Europe']['Luxembourg']='Luxembourg';
    $result['EUROPE']['Western Europe']['Malta']='Malta';
    $result['EUROPE']['Western Europe']['Monaco']='Monaco';
    $result['EUROPE']['Western Europe']['The Netherlands']='The Netherlands';
    $result['EUROPE']['Western Europe']['Norway']='Norway';
    $result['EUROPE']['Western Europe']['Portugal']='Portugal';
    $result['EUROPE']['Western Europe']['San Marino']='San Marino';
    $result['EUROPE']['Western Europe']['Spain']=array(
    //
'Andalucía',
'Aragón',
'Asturias',
'Baleares',
'Canarias',
'Cantabria',
'Castilla La Mancha',
'Castilla y León',
'Cataluña',
'Ceuta',
'Comunidad Valenciana',
'Extremadura',
'Galicia',
'Baleares',
'La Rioja',
'Madrid',
'Melilla',
'Navarra',
'País Vasco',
'Región de Murcia');
    $result['EUROPE']['Western Europe']['Spain']=array_combine($result['EUROPE']['Western Europe']['Spain'],$result['EUROPE']['Western Europe']['Spain']);
    //
    $result['EUROPE']['Western Europe']['Sweden']='Sweden';
    $result['EUROPE']['Western Europe']['Switzerland']='Switzerland';
    $result['EUROPE']['Western Europe']['The United Kingdom']='The United Kingdom';
    $result['EUROPE']['Western Europe']['The Vatican']='The Vatican';
    //
    $result['EUROPE']['Eastern Europe']=array();
    $result['EUROPE']['Eastern Europe']['Albania']='Albania';
    $result['EUROPE']['Eastern Europe']['Belarus']='Belarus';
    $result['EUROPE']['Eastern Europe']['Bosnia and Herzegovina']='Bosnia and Herzegovina';
    $result['EUROPE']['Eastern Europe']['Bulgaria']='Bulgaria';
    $result['EUROPE']['Eastern Europe']['Croatia']='Croatia';
    $result['EUROPE']['Eastern Europe']['The Czech Republic']='The Czech Republic';
    $result['EUROPE']['Eastern Europe']['Estonia']='Estonia';
    $result['EUROPE']['Eastern Europe']['Hungary']='Hungary';
    $result['EUROPE']['Eastern Europe']['Latvia']='Latvia';
    $result['EUROPE']['Eastern Europe']['Lithuania']='Lithuania';
    $result['EUROPE']['Eastern Europe']['Macedonia']='Macedonia';
    $result['EUROPE']['Eastern Europe']['Moldova']='Moldova';
    $result['EUROPE']['Eastern Europe']['Montenegro']='Montenegro';
    $result['EUROPE']['Eastern Europe']['Poland']='Poland';
    $result['EUROPE']['Eastern Europe']['Romania']='Romania';
    $result['EUROPE']['Eastern Europe']['Russia']='Russia';
    $result['EUROPE']['Eastern Europe']['Serbia']='Serbia';
    $result['EUROPE']['Eastern Europe']['Slovakia']='Slovakia';
    $result['EUROPE']['Eastern Europe']['Slovenia']='Slovenia';
    $result['EUROPE']['Eastern Europe']['Ukraine']='Ukraine';
    //
    $result['ASIA']=array();
    $result['ASIA']['The Middle East']=array(
    'Bahrain'=>'Bahrain',
    'Cyprus'=>'Cyprus',
    'Iran'=>'Iran',
    'Iraq'=>'Iraq',
    'Israel'=>'Israel',
    'Jordan'=>'Jordan',
    'Kuwait'=>'Kuwait',
    'Lebanon'=>'Lebanon',
    'Oman'=>'Oman',
    'Palestine'=>'Palestine',
    'Qatar'=>'Qatar',
    'Saudi Arabia'=>'Saudi Arabia',
    'Syria'=>'Syria',
    'Turkey'=>'Turkey',
    'The United Arab Emirates'=>'The United Arab Emirates',
    'Yemen'=>'Yemen');
    $result['ASIA']['Central Asia']=array(
    'Afghanistan'=>'Afghanistan',
    'Armenia'=>'Armenia',
    'Azerbaijan'=>'Azerbaijan',
    'Bangladesh'=>'Bangladesh',
    'Bhutan'=>'Bhutan',
    'Georgia'=>'Georgia',
    'India'=>'India',
    'Kazakhstan'=>'Kazakhstan',
    'Kyrgyzstan'=>'Kyrgyzstan',
    'Mongolia'=>'Mongolia',
    'Nepal'=>'Nepal',
    'Pakistan'=>'Pakistan',
    'Sri Lanka'=>'Sri Lanka',
    'Tajikistan'=>'Tajikistan',
    'Tibet'=>'Tibet',
    'Turkmenistan'=>'Turkmenistan',
    'Uzbekistan'=>'Uzbekistan');
    $result['ASIA']['East Asia']=array(
    'Brunei Darussalam'=>'Brunei Darussalam',
    'Cambodia'=>'Cambodia',
    'China'=>'China',
    'Indonesia'=>'Indonesia',
    'Japan'=>'Japan',
    'North Korea'=>'North Korea',
    'South Korea'=>'South Korea',
    'Laos'=>'Laos',
    'Malaysia'=>'Malaysia',
    'Maldives'=>'Maldives',
    'Myanmar'=>'Myanmar',
    'The Philippines'=>'The Philippines',
    'Singapore'=>'Singapore',
    'Taiwan'=>'Taiwan',
    'Thailand'=>'Thailand',
    'Timor-Leste'=>'Timor-Leste',
    'Vietnam'=>'Vietnam');
    //
    $result['OCEANIA']=array(
    'Australia'=>'Australia',
    'Fiji'=>'Fiji',
    'Kiribati'=>'Kiribati',
    'The Marshall Islands'=>'The Marshall Islands',
    'Micronesia'=>'Micronesia',
    'Nauru'=>'Nauru',
    'New Zealand'=>'New Zealand',
    'Palau'=>'Palau',
    'Papua New Guinea'=>'Papua New Guinea',
    'Samoa'=>'Samoa',
    'The Solomon Islands'=>'The Solomon Islands',
    'Tonga'=>'Tonga',
    'Tuvalu'=>'Tuvalu',
    'Vanuatu'=>'Vanuatu');
    //
    $result['AFRICA']=array();
    $result['AFRICA']['North Africa']=array(
    'Algeria'=>'Algeria',
    'Chad'=>'Chad',
    'Djibouti'=>'Djibouti',
    'Egypt'=>'Egypt',
    'Eritrea'=>'Eritrea',
    'Ethiopia'=>'Ethiopia',
    'Libya'=>'Libya',
    'Mali'=>'Mali',
    'Mauritania'=>'Mauritania',
    'Morocco'=>'Morocco',
    'Niger'=>'Niger',
    'Sudan'=>'Sudan',
    'Tunisia'=>'Tunisia');        
    $result['AFRICA']['East and South Africa']=array(
    'Angola'=>'Angola',
    'Botswana'=>'Botswana',
    'The Comoros'=>'The Comoros',
    'Kenya'=>'Kenya',
    'Lesotho'=>'Lesotho',
    'Madagascar'=>'Madagascar',
    'Malawi'=>'Malawi',
    'Mauritius'=>'Mauritius',
    'Mozambique'=>'Mozambique',
    'Namibia'=>'Namibia',
    'Seychelles'=>'Seychelles',
    'Somalia'=>'Somalia',
    'South Africa'=>'South Africa',
    'Swaziland'=>'Swaziland',
    'Tanzania'=>'Tanzania',
    'Zambia'=>'Zambia',
    'Zimbabwe'=>'Zimbabwe');        
    $result['AFRICA']['West & Central Africa']=array(
     'Benin'=>'Benin',
     'Burkina Faso'=>'Burkina Faso',
     'Burundi'=>'Burundi',
     'Cameroon'=>'Cameroon',
     'Cape Verde'=>'Cape Verde',
     'The Central African Republic'=>'The Central African Republic',
     'The Democratic Republic of the Congo'=>'The Democratic Republic of the Congo',
     'The Republic of the Congo'=>'The Republic of the Congo',
     'Cote dIvoire'=>'Cote dIvoire',
     'Equatorial Guinea'=>'Equatorial Guinea',
     'Gabon'=>'Gabon',
     'The Gambia'=>'The Gambia',
     'Ghana'=>'Ghana',
     'Guinea'=>'Guinea',
     'Guinea-Bissau'=>'Guinea-Bissau',
     'Liberia'=>'Liberia',
     'Nigeria'=>'Nigeria',
     'Rwanda'=>'Rwanda',
     'Sao Tome and Principe'=>'Sao Tome and Principe',
     'Senegal'=>'Senegal',
     'Sierra Leone'=>'Sierra Leone',
     'Togo'=>'Togo',
     'Uganda'=>'Uganda');
    $result['AMERICA']=array();
    $result['AMERICA']['South America']=array(
    'Argentina'=>'Argentina',
    'Bolivia'=>'Bolivia',
    'Brazil'=>'Brazil',
    'Chile'=>'Chile',
    'Colombia'=>'Colombia',
    'Ecuador'=>'Ecuador',
    'Guyana'=>'Guyana',
    'Paraguay'=>'Paraguay',
    'Peru'=>'Peru',
    'Suriname'=>'Suriname',
    'Uruguay'=>'Uruguay',
    'Venezuela'=>'Venezuela');
    $result['AMERICA']['Central America']=array(
    'Antigua and Barbuda'=>'Antigua and Barbuda',
    'The Bahamas'=>'The Bahamas',
    'Barbados'=>'Barbados',
    'Belize'=>'Belize',
    'Costa Rica'=>'Costa Rica',
    'Cuba'=>'Cuba',
    'Dominica'=>'Dominica',
    'The Dominican Republic'=>'The Dominican Republic',
    'El Salvador'=>'El Salvador',
    'Grenada'=>'Grenada',
    'Guatemala'=>'Guatemala',
    'Haiti'=>'Haiti',
    'Honduras'=>'Honduras',
    'Jamaica'=>'Jamaica',
    'Mexico'=>'Mexico',
    'Nicaragua'=>'Nicaragua',
    'Panama'=>'Panama',
    'Saint Kitts and Nevis'=>'Saint Kitts and Nevis',
    'Saint Lucia'=>'Saint Lucia',
    'Saint Vincent and the Grenadines'=>'Saint Vincent and the Grenadines',
    'Trinidad and Tobago'=>'Trinidad and Tobago');
    $result['AMERICA']['North America']=array(
    'Canada'=>'Canada',
    'The United States of America'=>'The United States of America');
    $result['INTERNACIONAL']='INTERNACIONAL';

    return $result;
}
function red_regiones_define_regiones_continentes_options($with_empty=0){
    $result=array();
    if($with_empty){
        $result['']='';
    }
    $regiones=red_regiones_define_regiones_options();
    $continentes=array_keys($regiones);
    $continentes=array_combine($continentes,$continentes);
    $result=array_merge($result,$continentes);
    return $result;
}
function red_regiones_define_fieldset($node,$filtro_region_value=''){
    $continente_value='';
    $region_value='';
    $pais_value='';
    $autonomia_value='';
    if((isset($node->nid) && !empty($node->nid)) || !empty($filtro_region_value)){
        $info_region=red_regiones_get_info($node,$filtro_region_value);
        $continente_value=$info_region['continente_value'];
        $region_value=$info_region['region_value'];
        $pais_value=$info_region['pais_value'];
        $autonomia_value=$info_region['autonomia_value'];
    }
    //simulando
    //$continente_value='ASIA';
    //$region_value='Central Asia';
    //
            $result=array(
                '#type'=>'fieldset',
                '#title'=>t('Region'),
                '#attributes'=>array('class'=>'region_fs'),
            );
            $result['field_region_continente']=array(
                '#type'=>'select',
                '#title'=>t('Continent'),
                '#options'=>red_regiones_define_regiones_continentes_options(1),
                '#default_value'=>$continente_value,
                '#validated'=>TRUE,
            );
            $region_options=red_regiones_define_regiones_regiones_options(1,$continente_value);
            if(!empty($continente_value) && $continente_value=='OCEANIA'){
                $pais_options=$region_options;
                $region_options=array();
            }else{
                $pais_options=red_regiones_define_regiones_paises_options(1,$continente_value,$region_value);
            }
            $autonomia_options=red_regiones_define_regiones_autonomias_options(1,$continente_value,$region_value,$pais_value);            
            $result['field_region_region']=array(
                '#type'=>'select',
                '#title'=>t('Subcontinent'),
                '#options'=>$region_options,
                '#default_value'=>$region_value,
                '#validated'=>TRUE,
            );
            $result['field_region_pais']=array(
                '#type'=>'select',
                '#title'=>t('Country'),
                '#options'=>$pais_options,
                '#default_value'=>$pais_value,
                '#validated'=>TRUE,
            );
            $result['field_region_autonomia']=array(
                '#type'=>'select',
                '#title'=>t('Region'),
                '#options'=>$autonomia_options,
                '#default_value'=>$autonomia_value,
                '#validated'=>TRUE,
            );
            
            red_regiones_fieldset_add_js();
            return $result;
}
function red_regiones_define_regiones_regiones_options($with_empty=0,$continente_value=''){
    $result=array();
    if(empty($continente_value)){
        return $result;
    }
    if($with_empty){
        $result['']='';
    }
    $regiones=red_regiones_define_regiones_options();
    if(isset($regiones[$continente_value])){
        $keys=array_keys($regiones[$continente_value]);
        $keys=array_combine($keys,$keys);
        $result=array_merge($result,$keys);
    }
    return $result;
}
function red_regiones_define_regiones_paises_options($with_empty=0,$continente_value='',$region_value=''){
    $result=array();
    if(empty($continente_value) && empty($region_value)){
        return $result;
    }
    if($with_empty){
        $result['']='';
    }
    $regiones=red_regiones_define_regiones_options();
    if(isset($regiones[$continente_value])){
        $regiones_array=$regiones[$continente_value];
        if(isset($regiones_array[$region_value])){
            $keys=array_keys($regiones_array[$region_value]);
            $keys=array_combine($keys,$keys);
            $result=array_merge($result,$keys);
        }
    }
    return $result;
}
function red_regiones_on_save_source($op,&$node){
    if(isset($_POST['field_region_continente']) || isset($_POST['field_region_region']) || isset($_POST['field_region_pais'])){
        $region_value=red_regiones_get_region_post_value();
        red_regiones_save_content_field_fuente_region_row($node->nid,$node->vid,$region_value);
    }            
}
function red_regiones_get_region_post_value(){
    $result=array();
    $result[0]='';
    if(isset($_POST['field_region_autonomia']) && !empty($_POST['field_region_autonomia'])){
        $result[0]=$_POST['field_region_autonomia'];
    }    
    $result[1]='';
    if(isset($_POST['field_region_pais']) && !empty($_POST['field_region_pais'])){
        //return $_POST['field_region_pais'];
        $result[1]=$_POST['field_region_pais'];
    }
    $result[2]='';
    if(isset($_POST['field_region_region']) && !empty($_POST['field_region_region'])){
        //return $_POST['field_region_region'];
        $result[2]=$_POST['field_region_region'];
    }
    $result[3]='';
    if(isset($_POST['field_region_continente']) && !empty($_POST['field_region_continente'])){
        //return $_POST['field_region_continente'];
        $result[3]=$_POST['field_region_continente'];
    }
    $result=array_reverse($result);
    $region_value='#'.implode('#',$result).'#';
    return $region_value;
}
function red_regiones_save_content_field_fuente_region_row($nid,$vid,$region_value){
        $row=red_regiones_get_content_field_fuente_region_row($nid,$vid);
        if(isset($row->nid) && !empty($row->nid)){    
           $res=db_query('UPDATE {content_field_fuente_region} SET field_fuente_region_value="%s" WHERE nid=%d AND vid=%d',$region_value,$nid,$vid);
        }else{
           $res=db_query('INSERT INTO {content_field_fuente_region}(nid,vid,field_fuente_region_value) VALUES(%d,%d,"%s")',$nid,$vid,$region_value);
        }    
}
function red_regiones_get_content_field_fuente_region_row($nid,$vid){
    $res=db_query('SELECT * FROM {content_field_fuente_region} WHERE nid=%d AND vid=%d',$nid,$vid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function red_regiones_get_info($node,$filtro_region_value=''){
    $result=array();
    $result['continente_value']='';
    $result['region_value']='';
    $result['pais_value']='';
    $result['autonomia_value']='';
    if(!empty($filtro_region_value)){
        $region_value=$filtro_region_value;
    }else if(isset($node->type) && !empty($node->type) && in_array($node->type,array('canal_de_supercanal','canal_de_yql'))){
        $region_value=red_regiones_get_content_field_canal_region_value($node->nid,$node->vid); 
    }else{
        $region_value=red_regiones_get_content_field_fuente_region_value($node->nid,$node->vid);        
    }
    $region_value=trim($region_value,'#');
    $my_array=explode('#',$region_value);
    if(isset($my_array[0])){
        $result['continente_value']=$my_array[0];
    }
    if(isset($my_array[1])){
        $result['region_value']=$my_array[1];
    }
    if(isset($my_array[2])){
        $result['pais_value']=$my_array[2];
    }
    if(isset($my_array[3])){
        $result['autonomia_value']=$my_array[3];
    }
    /*if(!empty($region_value)){
         $regiones=red_regiones_define_regiones_options();
         if(!empty($regiones)){
             foreach($regiones as $continente=>$regiones_array){
                 if($continente==$region_value){
                     $result['continente_value']=$continente;
                     return $result;
                 }else{
                    if(!empty($regiones_array)){
                        foreach($regiones_array as $region=>$paises_array){
                            if($region==$region_value){
                                if($continente=='OCEANIA'){
                                    $result['continente_value']=$continente;
                                    $result['pais_value']=$region;
                                    return $result;
                                }else{
                                    $result['continente_value']=$continente;
                                    $result['region_value']=$region;
                                    return $result;
                                }
                            }
                            if(!empty($paises_array) && is_array($paises_array)){
                                foreach($paises_array as $pais=>$value){
                                    if($pais==$region_value){
                                           $result['continente_value']=$continente;
                                           $result['region_value']=$region;
                                           $result['pais_value']=$pais;
                                           return $result;
                                    }
                                }
                            }
                        }
                    }
                 }    
             }
         }
    }*/
    return $result;
}
function red_regiones_get_content_field_fuente_region_value($nid,$vid){
    $row=red_regiones_get_content_field_fuente_region_row($nid,$vid);
    if(isset($row->nid) && !empty($row->nid)){
        return $row->field_fuente_region_value;
    }
    return '';
}
function red_regiones_fieldset_add_js(){
    global $base_url;
    $js='$(document).ready(function()
    {
        $("#edit-field-region-continente").unbind("change");
        $("#edit-field-region-continente").bind("change",function(){
            var continente=$(this).attr("value");
             jQuery.ajax({
				type: "POST",
				url: "'.$base_url.'/red/red_regiones_get_continente_regiones_options_ajax?my_time="+new Date().getTime(),
				data: {continente:continente},
				dataType:"json",
				success: function(my_result){
                                    $("#edit-field-region-region").children().remove();
                                    $("#edit-field-region-pais").children().remove();
                                    $("#edit-field-region-autonomia").children().remove();
                                    for(var region in my_result.regiones){
                                        var option = document.createElement("option");
                                        option.text = region;
                                        option.value = region;
                                        if(my_result.continente=="OCEANIA"){
                                            $("#edit-field-region-pais").append(option);
                                        }else{
                                            $("#edit-field-region-region").append(option);
                                        }    
                                    }
				}
			});
        });
        $("#edit-field-region-region").unbind("change");
        $("#edit-field-region-region").bind("change",function(){
            var continente=$("#edit-field-region-continente").attr("value");
            var region=$(this).attr("value");
             jQuery.ajax({
				type: "POST",
				url: "'.$base_url.'/red/red_regiones_get_region_paises_options_ajax?my_time="+new Date().getTime(),
				data: {continente:continente,region:region},
				dataType:"json",
				success: function(my_result){
                                    $("#edit-field-region-pais").children().remove();
                                    $("#edit-field-region-autonomia").children().remove();
                                    for(var pais in my_result.paises){
                                        var option = document.createElement("option");
                                        option.text = pais;
                                        option.value = pais;
                                        $("#edit-field-region-pais").append(option);
                                    }
				}
			});
        });
        $("#edit-field-region-pais").unbind("change");
        $("#edit-field-region-pais").bind("change",function(){
            var continente=$("#edit-field-region-continente").attr("value");
            var region=$("#edit-field-region-region").attr("value");
            var pais=$(this).attr("value");
             jQuery.ajax({
				type: "POST",
				url: "'.$base_url.'/red/red_regiones_get_region_autonomias_options_ajax?my_time="+new Date().getTime(),
				data: {continente:continente,region:region,pais:pais},
				dataType:"json",
				success: function(my_result){
                                    $("#edit-field-region-autonomia").children().remove();
                                    if(my_result.autonomias){
                                        for(var autonomia in my_result.autonomias){
                                            var option = document.createElement("option");
                                            option.text = autonomia;
                                            option.value = autonomia;
                                            $("#edit-field-region-autonomia").append(option);
                                        }
                                    }
				}
			});
        });
    });';
    drupal_add_js($js,'inline');
}
function red_regiones_get_continente_regiones_options_ajax_callback(){
    $result=array();
    $result['continente']=$_POST['continente'];
    $result['regiones']=red_regiones_define_regiones_regiones_options(1,$result['continente']);        
    print json_encode($result);
    exit();
}
function red_regiones_get_region_paises_options_ajax_callback(){
    $result=array();
    $result['continente']=$_POST['continente'];
    $result['region']=$_POST['region'];
    $result['paises']=red_regiones_define_regiones_paises_options(1,$result['continente'],$result['region']);        
    print json_encode($result);
    exit();
}
function red_regiones_get_region_decode_value($region_value){
    $info_region=red_regiones_get_info('',$region_value);
    if(isset($info_region['autonomia_value']) && !empty($info_region['autonomia_value'])){
        return $info_region['autonomia_value'];        
    }
    if(isset($info_region['pais_value']) && !empty($info_region['pais_value'])){
        return $info_region['pais_value'];        
    }
    if(isset($info_region['region_value']) && !empty($info_region['region_value'])){
        return $info_region['region_value'];
    }
    if(isset($info_region['continente_value']) && !empty($info_region['continente_value'])){
        return $info_region['continente_value'];
    }
    return '';
}
function red_regiones_on_save_canal($op,&$node){
    if(in_array($node->type,array('canal_de_supercanal','canal_de_yql'))){
        if(isset($_POST['field_region_continente']) || isset($_POST['field_region_region']) || isset($_POST['field_region_pais'])){
            $region_value=red_regiones_get_region_post_value();
            red_regiones_save_content_field_canal_region_row($node->nid,$node->vid,$region_value);
        }
    }
}
function red_regiones_save_content_field_canal_region_row($nid,$vid,$region_value){
        $row=red_regiones_get_content_field_canal_region_row($nid,$vid);
        if(isset($row->nid) && !empty($row->nid)){    
           $res=db_query('UPDATE {content_field_canal_region} SET field_canal_region_value="%s" WHERE nid=%d AND vid=%d',$region_value,$nid,$vid);
        }else{
           $res=db_query('INSERT INTO {content_field_canal_region}(nid,vid,field_canal_region_value) VALUES(%d,%d,"%s")',$nid,$vid,$region_value);
        }    
}
function red_regiones_get_content_field_canal_region_row($nid,$vid){
    $res=db_query('SELECT * FROM {content_field_canal_region} WHERE nid=%d AND vid=%d',$nid,$vid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function red_regiones_get_content_field_canal_region_value($nid,$vid){
    $row=red_regiones_get_content_field_canal_region_row($nid,$vid);
    if(isset($row->nid) && !empty($row->nid)){
        return $row->field_canal_region_value;
    }
    return '';
}
function red_regiones_define_regiones_autonomias_options($with_empty=0,$continente_value='',$region_value='',$pais_value){
    $result=array();
    if(empty($continente_value) && empty($region_value) && empty($pais_value)){
        return $result;
    }
    if($with_empty){
        $result['']='';
    }
    $regiones=red_regiones_define_regiones_options();
    if(isset($regiones[$continente_value])){
        $regiones_array=$regiones[$continente_value];
        if(isset($regiones_array[$region_value])){
            $paises_array=$regiones_array[$region_value];
            if(isset($paises_array[$pais_value]) && is_array($paises_array[$pais_value])){
                $keys=array_keys($paises_array[$pais_value]);
                $keys=array_combine($keys,$keys);
                $result=array_merge($result,$keys);
            }
        }
    }
    return $result;
}
function red_regiones_get_region_autonomias_options_ajax_callback(){
    $result=array();
    $result['continente']=$_POST['continente'];
    $result['region']=$_POST['region'];
    $result['pais']=$_POST['pais'];
    $autonomias=red_regiones_define_regiones_autonomias_options(1,$result['continente'],$result['region'],$result['pais']); 
    if(!empty($autonomias)){    
        $result['autonomias']=$autonomias;       
    }
    print json_encode($result);
    exit();
}
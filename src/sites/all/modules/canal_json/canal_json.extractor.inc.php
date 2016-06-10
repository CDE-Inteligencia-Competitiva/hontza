<?php
function canal_json_extractor_get_result($extractor){
    if(canal_json_extractor_is_extractor($extractor)){
        $result='';
        /*echo print_r($extractor,1);
        exit();*/        
        //if(canal_json_extractor_is_extractor_data_array($extractor)){
             $result=canal_json_extractor_get_result_data_array($extractor);
        //}
        if(isset($result->results)){
            return $result;
        }
    }
    return $extractor;
}
function canal_json_extractor_is_extractor($extractor){
    if(isset($extractor->extractorData) && !empty($extractor->extractorData)){
        return 1;
    }
    return 0;
}
function canal_json_extractor_is_activado(){
    if(defined('_IS_CANAL_JSON_EXTRACTOR') && _IS_CANAL_JSON_EXTRACTOR==1){
        return 1;
    }
    return 0;
}
function canal_json_extractor_is_extractor_data_array($extractor){
    if(isset($extractor->extractorData->data) && isset($extractor->extractorData->data[0])){
        if(isset($extractor->extractorData->data[0]->group) && isset($extractor->extractorData->data[0]->group[0])){
            $fields=array_keys((array) $extractor->extractorData->data[0]->group[0]);
            $num=count($fields);
            print $num;exit();
            echo print_r($extractor->extractorData->data[0]->group[0],1);
            exit();
        }
        return 1;
    }
    return 0;
}
function canal_json_extractor_get_result_data_array($extractor){
    $result=new stdClass();
        $kont=0;
        if(isset($extractor->extractorData->data)){
            foreach($extractor->extractorData->data as $i_data=>$row_data){
                if(!empty($row_data)){
                    foreach($row_data as $field_data=>$my_array){
                        if(!empty($my_array)){
                            foreach($my_array as $i=>$value_array){
                                $row=new stdClass();
                                foreach($value_array as $row_field=>$row_value){
                                    if(isset($row_value[0])){
                                       foreach($row_value[0] as $field=>$value){
                                           $field_name=$row_field.'#'.$field;
                                           $row->$field_name=$value;
                                       }
                                    }
                                }
                                $result->results[$kont]=$row;
                                $kont++;
                            }
                            
                        }
                    }
                }
            }
        }
        return $result;
}        
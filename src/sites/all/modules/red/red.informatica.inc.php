<?php
function red_informatica_is_informatica_activado(){
	if(defined('_IS_INFORMATICA') && _IS_INFORMATICA==1){
		return 1;
	}
	return 0;
}
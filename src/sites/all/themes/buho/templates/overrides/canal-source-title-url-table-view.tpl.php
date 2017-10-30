<?php if(red_despacho_is_canal_fuente_titulo_activado()):?>
	<?php $num=count($node->field_canal_source_url);?>
	<?php if($num>0):?>
		<?php foreach($node->field_canal_source_url as $i=>$row):?>
			<?php $kont=red_despacho_get_field_kont_form_alter($i,$num);?>
			<tr class="tr_node_view">
	            <td class="td_label_node_view"><b><?php print t('Source Title').$kont;?></b>:&nbsp;</td>
	            <td class="td_value_node_view">
	                <?php print red_despacho_decode_source_title_url_value($node->field_canal_source_title[$i]['value']);?>
	            </td>
        	</tr>	
			<tr class="tr_node_view">
	            <td class="td_label_node_view"><b><?php print t('Source Url').$kont;?></b>:&nbsp;</td>
	            <td class="td_value_node_view">
	                <?php print red_despacho_decode_source_title_url_value($row['value'],1);?>
	            </td>
        	</tr>
		<?php endforeach;?>
	<?php endif;?>		
<?php endif;?>	
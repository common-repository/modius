<?php
/*-------------------------------------------------------------
 Name:      modius

 Purpose:   Muestra el contenido del Modblock por ID
 Receive:   $id
 Return:	-none-
-------------------------------------------------------------*/
function modius($id) {
	global $wpdb;

		$get_modius = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."modius` WHERE `id` LIKE ".$id);
			if ($get_modius) {
				foreach($get_modius as $get_modblock) {
					 echo stripslashes(html_entity_decode($get_modblock->modius_content));
				}
			} else {
				echo "error";
			}
}


/*-------------------------------------------------------------
 Name:      modius_credits

 Purpose:   Créditos del plugin Modius
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function modius_credits() { ?>
<table class="widefat" style="margin-top: .5em">
	<thead>
    	<tr valign="top">
        	<th>Plugin Modius para Wordpress</th>
		</tr>
	</thead>
	<tbody>
		<tr>
        	<td>Cualquier sugerencia o comentario sobre el plugin, comunicarse con el correo del autor <a href="mailto:miodesigner@hotmail.com">miodesigner@hotmail.com</a> | <a href="http://www.miodesigner.com">www.miodesigner.com</a></td>
        </tr>
	</tbody>
</table>
<?php
}
?>
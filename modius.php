<?php 
/*
Plugin Name: Modius
Plugin URI: http://miodesigner.com
Description: Permite crear bloques de texto cualquier lugar del blog, administrables desde el m&oacute;dulo correspondiente, estos se colocal manualmente en el template.
Version: 0.1
Author: Lord MIO
Author URI: http://miodesigner.com
*/

#---------------------------------------------------
# Cargando otras páginas y configuración
#---------------------------------------------------
include_once(ABSPATH.'wp-content/plugins/modius/modius-setup.php');
include_once(ABSPATH.'wp-content/plugins/modius/modius-functions.php');
include_once(ABSPATH.'wp-content/plugins/modius/modius-manage.php');

register_activation_hook(__FILE__, 'modius_activate');
register_deactivation_hook(__FILE__, 'modius_deactivate');


setlocale(LC_ALL, get_locale().'.'.DB_CHARSET);

add_action('admin_menu', 'modius_dashboard', 1);

if (isset($_POST['modius_submit'])) 			add_action('init', 'modius_insert_input');
if (isset($_POST['delete_modius'])) add_action('init', 'modius_request_delete');
//if (isset($_POST['modius_upgrade'])) 			add_action('init', 'modius_upgrade'); // en revisión 

/*-------------------------------------------------------------
 Name:      modius_dashboard

 Purpose:   Agrega paginas al menu de administración
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function modius_dashboard() {
	add_object_page('Modius', 'Modius', 6, 'modius', 'modius_manage');
		add_submenu_page('modius', 'Modius > ModBlocks', 'ModBlocks', 6, 'modius', 'modius_manage');
		add_submenu_page('modius', 'Modius > A&ntilde;adir nuevo', 'A&ntilde;adir nuevo', 6, 'modius2', 'modius_add');

	//add_options_page('Modius', 'Modius', 6, 'modius3', 'modius_options'); // en una próxima version xD
}

/*-------------------------------------------------------------
 Name:      modius_manage

 Purpose:   Página de administración de los ModBlocks
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function modius_manage() {
	global $wpdb;

	$action = $_GET['action'];
	if(isset($_POST['order'])) {
		$order = $_POST['order'];
	} else {
		$order = 'id ASC';
	} ?>
	<div class="wrap">
		<h2><?php _e('ModBlocks'); ?></h2>

		<?php if ($action == 'delete-modius') { ?>
			<div id="message" class="updated fade"><p><?php _e('Modblock <strong>borrado</strong>'); ?></p></div>
		<?php } else if ($action == 'updated') { ?>
			<div id="message" class="updated fade"><p><?php _e('ModBlock <strong>actualizado</strong>'); ?></p></div>
		<?php } else if ($action == 'no_access') { ?>
			<div id="message" class="updated fade"><p><?php _e('Acci&oacute;n prohibida'); ?></p></div>
		<?php } ?>

		<form name="modius" id="post" method="post" action="admin.php?page=modius">
		<div class="tablenav">

			<div class="alignleft actions">
				<input onclick="return confirm('<?php _e('Est&aacute; a punto de borrar el ModBlock');?>')" type="submit" value="<?php _e('Borrar ModBlock'); ?>" name="delete_modius" class="button-secondary delete" />
				<select name='order'>
			        <option value="id ASC" <?php if($order == "ID ASC") { echo 'selected="selected"'; } ?>><?php _e('ordernar por ID (ascendente)'); ?></option>
			        <option value="id DESC" <?php if($order == "ID DESC") { echo 'selected="selected"'; } ?>><?php _e('ordernar por ID (descendente)'); ?></option>
			        <option value="modius_title ASC" <?php if($order == "modius_title ASC") { echo 'selected="selected"'; } ?>><?php _e('ordenar por T&iacute;tulo'); ?> (A-Z)</option>
			        <option value="modius_title DESC" <?php if($order == "modius_title DESC") { echo 'selected="selected"'; } ?>><?php _e('ordenar por T&iacute;tulo'); ?> (Z-A)</option>
				</select>
				<input type="submit" id="post-query-submit" value="<?php _e('Ordenar'); ?>" class="button-secondary" />
			</div>
		</div>
		<table class="widefat">
  			<thead>
  				<tr>
					<th scope="col" class="check-column">&nbsp;</th>
					<th scope="col" width="8%"><?php _e('ID'); ?></th>
					<th scope="col" width="20%"><?php _e('Nombre'); ?></th>
					<th scope="col"><?php _e('Contenido'); ?></th>
				</tr>
  			</thead>
  			<tbody>
		<?php 
		if(modius_mysql_table_exists($wpdb->prefix.'modius')) {
			$modblocks = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."modius` ORDER BY $order");
			if ($modblocks) {
				foreach($modblocks as $modblock) { ?>
				    <tr id='modius-<?php echo $modblock->id; ?>' class=' <?php echo $class; ?>'>
						<th scope="row" class="check-column"><input type="checkbox" name="modiuscheck[]" value="<?php echo $modblock->id; ?>" /></th>
                        <td><?php echo $modblock->id; ?></td>
						<td><strong><a class="row-title" href="<?php echo get_option('siteurl').'/wp-admin/admin.php?page=modius2&amp;edit_modius='.$modblock->id;?>" title="<?php _e('Editar'); ?>"><?php echo stripslashes(html_entity_decode($modblock->modius_title));?></a></strong></td>
						<td><?php echo $modblock->modius_content; ?></td>
					</tr>
	 			<?php } ?>
	 		<?php } else { ?>
				<tr id='no-id'><td scope="row" colspan="4"><em><?php _e('A&uacute;n no hay ModBlocks'); ?></em></td></tr>
			<?php 
			}
		} else { ?>
			<tr id='no-id'><td scope="row" colspan="5"><span style="font-weight: bold; color: #f00;"><?php _e('Hubo un error ubicando la tabla del Modius en la base de datos, porfavor desactive y vuelva a activar el plugin.'); ?><br /><?php _e('Si eso no soluciona su problema, porfavor comunicarse al correo <a href="mailto:midesigner@hotmail.com">miodesigner@hotmail.com</a>'); ?></span></td></tr>

		<?php }	?>
			</tbody>
		</table>
		</form>
        <br class="clear" />
<table class="widefat" style="margin-top: .5em">
	<thead>
    	<tr valign="top">
        	<th>&iquest;Como usar los ModBlocks?</th>
		</tr>
	</thead>
	<tbody>
		<tr>
        	<td>Colocar en el template: <strong style="color:#36C">&lt;?php modius(<span style="color:#C00">ID</span>) ?&gt;</strong> <br />Reemplazar <span style="color:#C00; font-weight:bold;">ID</span> por el <strong>ID del ModBlock</strong></td>
        </tr>
	</tbody>
</table>
		<br class="clear" />
		<?php modius_credits(); ?>

	</div>
	<?php 
}

/*-------------------------------------------------------------
 Name:      modius_add

 Purpose:   Crear nuevo o editar ModBlock
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function modius_add() {
	global $wpdb, $userdata;

	$action = $_GET['action'];
	if($_GET['edit_modius']) {
		$modius_edit_id = $_GET['edit_modius'];
	}
	?>
	<div class="wrap">
		<?php if(!$modius_edit_id) { ?>
		<h2><?php _e('A&ntilde;adir nuevo'); ?></h2>
		<?php 
		} else { ?>
		<h2><?php _e('Editar Modblock'); ?></h2>
		<?php 
			$edit_modius = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."modius` WHERE `id` = $modius_edit_id");
		}

		if ($action == 'created') { ?>
			<div id="message" class="updated fade"><p><?php _e('ModBlock <strong>creado</strong>'); ?> | <a href="admin.php?page=modius"><?php _e('Ver todos los ModBlocks'); ?></a></p></div>
		<?php } else if ($action == 'no_access') { ?>
			<div id="message" class="updated fade"><p><?php _e('Accion Prohibida'); ?></p></div>
		<?php } else if ($action == 'field_error') { ?>
			<div id="message" class="updated fade"><p><?php _e('Por favor, no deje el campo <strong>T&iacute;tulo</strong> en blanco.'); ?></p></div>
		<?php } ?>
			  	<form method="post" action="admin.php?page=modius2">
			  	   	<input type="hidden" name="modius_submit" value="true" />
			    	<input type="hidden" name="modius_id" value="<?php echo $modius_edit_id;?>" />
					<?php if($modius_edit_id) { ?>
			    	<input type="hidden" name="modius_repeat_int" value="0" />
					<?php } ?>
			
			    	<table class="widefat" style="margin-top: .5em">
	
						<thead>
						<tr valign="top" id="quicktags">
							<td colspan="2"><?php _e('Ingresa los detalles del Modblock debajo'); ?></td>
						</tr>
				      	</thead>
	
				      	<tbody>
				      	<tr>
					        <th scope="row"><?php _e('T&iacute;tulo'); ?>:</th>
					        <td><input name="title" class="search-input" type="text" size="55" maxlength="255" value="<?php echo $edit_modius->modius_title;?>" tabindex="1" autocomplete="off" /><br /><em><?php echo _e('M&aacute;ximo 255 Caracteres'); ?></em></td>
						</tr>
						</tbody>
	
					</table>
	
					<br class="clear" />
					<div id="postdivrich" class="postarea">
						<?php modius_editor($edit_modius->modius_content, 'content', 'modius_title'); ?>
					</div>
	
					<br class="clear" />
					<?php modius_credits(); ?>
	
			    	<p class="submit">
						<?php if($modius_edit_id) { ?>
						<input type="submit" name="submit_save" class="button-primary" value="<?php _e('Actualizar Modblock'); ?>" tabindex="22" />
						<?php } else { ?>
						<input type="submit" name="submit_save" class="button-primary" value="<?php _e('Guardar ModBlock'); ?>" tabindex="22" />
						<?php } ?>
						<a href="admin.php?page=modius2" class="button"><?php _e('Limpiar'); ?></a>
			    	</p>
	
			  	</form>
	</div>
<?php } ?>
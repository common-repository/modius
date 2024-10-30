<?php 
/*-------------------------------------------------------------
 Name:      modius_editor

 Purpose:   Editor de contenido del Modblock con simple html
 Receive:   $content, $id, $prev_id
 Return:	-None-
-------------------------------------------------------------*/
function modius_editor($content, $id = 'content', $prev_id = 'title') {
	$media_buttons = false;
	$richedit = user_can_richedit();
	?>
	<div id="quicktags">
		<?php wp_print_scripts( 'quicktags' ); ?>
		<script type="text/javascript">edToolbar()</script>
	</div>

	<?php 
	$the_editor = apply_filters('the_editor', "<div id='editorcontainer'><textarea rows='6' cols='40' name='$id' tabindex='4' id='$id'>%s</textarea></div>\n");
	$the_editor_content = apply_filters('the_editor_content', $content);
	printf($the_editor, $content);
	?>
	<script type="text/javascript">
	// <![CDATA[
	edCanvas = document.getElementById('<?php echo $id; ?>');
	<?php if ( user_can_richedit() && $prev_id ) { ?>
	var dotabkey = true;
	// If tinyMCE is defined.
	if ( typeof tinyMCE != 'undefined' ) {
		// This code is meant to allow tabbing from Title to Post (TinyMCE).
		jQuery('#<?php echo $prev_id; ?>')[jQuery.browser.opera ? 'keypress' : 'keydown'](function (e) {
			if (e.which == 9 && !e.shiftKey && !e.controlKey && !e.altKey) {
				if ( (jQuery("#post_ID").val() < 1) && (jQuery("#title").val().length > 0) ) { autosave(); }
				if ( tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden() && dotabkey ) {
					e.preventDefault();
					dotabkey = false;
					tinyMCE.activeEditor.focus();
					return false;
				}
			}
		});
	}
	<?php } ?>
	// ]]>
	</script>
	<?php 
}

/*-------------------------------------------------------------
 Name:      modius_insert_input

 Purpose:   Agregar o Actualizar ModBlock
 Receive:   $_POST
 Return:	-None-
-------------------------------------------------------------*/
function modius_insert_input() {
	global $wpdb;

	if(current_user_can('manage_options')) {
		$modius_id 		= $_POST['modius_id'];
		$title	 			= htmlspecialchars(trim($_POST['title'], "\t\n "), ENT_QUOTES);
		$content 			= htmlspecialchars(trim($_POST['content'], "\t\n "), ENT_QUOTES);

		if (strlen($title) > 0 ) {

				if(strlen($modius_id) != 0 AND isset($_POST['submit_save'])) {
					/* Actualizar un Modblock */
					$postquery = "UPDATE `".$wpdb->prefix."modius` SET `modius_title` = '$title', `modius_content` = '$content' WHERE `id` = '$modius_id'";
					$action = "update";
				} else {
					/* Crear nuevo Modblock */
					$postquery = "INSERT INTO `".$wpdb->prefix."modius` (`modius_title`, `modius_content`) VALUES ('$title', '$content')";
					$action = "new";
				}
				if($wpdb->query($postquery) === FALSE) {
					die(mysql_error());
				}
				
			modius_return($action, array($modius_id));
			exit;
		} else {
			modius_return('field_error');
			exit;
		}
	} else {
		modius_return('no_access');
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      modius_request_delete

 Purpose:   Prepare removal of banner or category from database
 Receive:   $_POST
 Return:    -none-
-------------------------------------------------------------*/
function modius_request_delete() {
	global $wpdb, $userdata;

	if(current_user_can('manage_options')) {
		$modblocks_ids = $_POST['modiuscheck'];
		if($modblocks_ids != '') {
			foreach($modblocks_ids as $modblock_id) {
				modius_delete($modblock_id, 'modius');
			}
			modius_return('delete-modius');
			exit;
		}
	} else {
		modius_return('no_access');
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      modius_delete

 Purpose:   Elimina ModBlock de la base de datos
 Receive:   $id, $what
 Return:    -none-
-------------------------------------------------------------*/
function modius_delete($id, $what) {
	global $wpdb, $userdata;

	if($id > 0) {
		if($what == 'modius') {
				$SQL = "DELETE FROM `".$wpdb->prefix."modius` WHERE `id` = $id";
				if($wpdb->query($SQL) == FALSE) {
					die(mysql_error());
				}
		} else {
			modius_return('error');
			exit;
		}
	}
}

/*-------------------------------------------------------------
 Name:      modius_return

 Purpose:   Return para manejo de ModBlocks
 Receive:   $action, $arg
 Return:    -none-
-------------------------------------------------------------*/
function modius_return($action, $arg = null) {
	switch($action) {
		case "new" :
			wp_redirect('admin.php?page=modius2&action=created');
		break;

		case "update" :
			wp_redirect('admin.php?page=modius&action=updated');
		break;

		case "field_error" :
			wp_redirect('admin.php?page=modius2&action=field_error');
		break;

		case "error" :
			wp_redirect('admin.php?page=modius2&action=error');
		break;

		case "no_access" :
			wp_redirect('admin.php?page=modius&action=no_access');
		break;

		case "delete-modius" :
			wp_redirect('admin.php?page=modius&action=delete-modius');
		break;
	}
}
?>
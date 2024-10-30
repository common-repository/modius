<?php
/*-------------------------------------------------------------
 Name:      modius_activate

 Purpose:   Crea las tablas en la base de datos si no existe
 Receive:   -none-
 Return:	-none-
-------------------------------------------------------------*/
function modius_activate() {
	global $wpdb;

	$table_name1	= $wpdb->prefix . "modius";

	if ( $wpdb->has_cap( 'collation' ) ) {
		if ( ! empty($wpdb->charset) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty($wpdb->collate) )
			$charset_collate .= " COLLATE $wpdb->collate";
	}

	if(!modius_mysql_table_exists($table_name1)) { // Agrega la tabla si esta no existe
		$add1 = "CREATE TABLE `".$table_name1."` (
	  		`id` mediumint(8) unsigned NOT NULL auto_increment PRIMARY KEY,
			`modius_title` varchar(255) NOT NULL,
	  		`modius_content` longtext NOT NULL
			) ".$charset_collate;
			mysql_query($add1);
	} 
}

/*-------------------------------------------------------------
 Name:      modius_deactivate

 Purpose:   Desactiva script
 Receive:   -none-
 Return:	-none-
-------------------------------------------------------------*/
function modius_deactivate() {
}

/*-------------------------------------------------------------
 Name:      modius_mysql_table_exists

 Purpose:   Revisa si la tabla existe en la base de datos
 Receive:   -none-
 Return:	-none-
-------------------------------------------------------------*/
function modius_mysql_table_exists($table_name) {
	global $wpdb;

	foreach ($wpdb->get_col("SHOW TABLES",0) as $table ) {
		if ($table == $table_name) {
			return true;
		}
	}
	return false;
}
?>
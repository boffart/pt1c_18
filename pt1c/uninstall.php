<?php
/*------------------------------------------------------------------------------------------------------------------------------------------*/
global $db;
global $amp_conf;

require_once(dirname(__FILE__).'/bin/pt1c_global_var.php');

// Retrieve database and table name if defined, otherwise use FreePBX default
$db_name 		= !empty($amp_conf['CDRDBNAME'])?$amp_conf['CDRDBNAME']:"asteriskcdrdb";
$db_table_name 	= !empty($amp_conf['CDRDBTABLENAME'])?$amp_conf['CDRDBTABLENAME']:"cdr";

// if CDRDBHOST and CDRDBTYPE are not empty then we assume an external connection and don't use the default connection
//
if (!empty($amp_conf["CDRDBHOST"]) && !empty($amp_conf["CDRDBTYPE"])) {
	$db_hash = array('mysql' => 'mysql', 'postgres' => 'pgsql');
	$db_type = $db_hash[$amp_conf["CDRDBTYPE"]];
	$db_host = $amp_conf["CDRDBHOST"];
	$db_port = empty($amp_conf["CDRDBPORT"]) ? '' :  ':' . $amp_conf["CDRDBPORT"];
	$db_user = empty($amp_conf["CDRDBUSER"]) ? $amp_conf["AMPDBUSER"] : $amp_conf["CDRDBUSER"];
	$db_pass = empty($amp_conf["CDRDBPASS"]) ? $amp_conf["AMPDBPASS"] : $amp_conf["CDRDBPASS"];
	$datasource = $db_type . '://' . $db_user . ':' . $db_pass . '@' . $db_host . $db_port . '/' . $db_name;
	$dbcdr = DB::connect($datasource); // attempt connection
	
	if(DB::isError($dbcdr)) {
		die_freepbx($dbcdr->getDebugInfo()); 
	}
} else {
	$dbcdr = $db;
}

if (! function_exists("out")) {
	function out($text) {
    	echo $text."<br />";
	}
}
/*------------------------------------------------------------------------------------------------------------------------------------------*/
$sql[]='DROP TABLE IF EXISTS `PT1C_cdr`';

foreach ($sql as $statement){
	$check = $dbcdr->query($statement);
	if (DB::IsError($check)){
		die_freepbx( "Can not execute $statement : " . $check->getMessage() .  "\n");
	}
}

// удаленире agi файлов
$ASTAGIDIR  = $amp_conf['ASTAGIDIR'];
foreach ($pt1c_agi_files as $pt1c_file){
    if (unlink($ASTAGIDIR."/".$pt1c_file)) { 
    	out(_("Delete file ".$pt1c_file));
	}else{ 
		out(_("Error Delete ".$pt1c_file));
	}  
}
try {
	$AMPWEBROOT  = $amp_conf['AMPWEBROOT'];
	
	$linkfile=$AMPWEBROOT.'/admin/1c';
	out(_($linkfile));
	if(is_link($linkfile)) {
		unlink($linkfile);
	}	
} catch (Exception $e) {
}

$dst_pem_file  = $amp_conf['ASTETCDIR'].'/pt1c_ajam.pem';
if(is_file($dst_pem_file)){
    if (unlink($dst_pem_file)){ 
    	out(_("Delete file pt1c_ajam.pem"));
	}else{ 
		out(_("Error Delete pt1c_ajam.pem"));
	}  
}
?>

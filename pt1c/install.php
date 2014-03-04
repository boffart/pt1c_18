<?php
/*------------------------------------------------------------------------------------------------------------------------------------------*/
// if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }

require_once(dirname(__FILE__).'/bin/pt1c_global_var.php');
require_once(dirname(__FILE__).'/bin/pt1s_check_configs_file.php');
require_once(dirname(__FILE__).'/bin/pt1c_ini_parser.php');

if (!function_exists("out")) {
	function out($text) { echo $text."<br />";}
}
if (!function_exists("outn")) {
	function outn($text) { echo $text;}
}
/*------------------------------------------------------------------------------------------------------------------------------------------*/
global $db;
global $amp_conf;

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

$sql[]="CREATE TABLE IF NOT EXISTS `".$db_name."`.`PT1C_cdr` (
   `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
   `calldate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
   `clid` VARCHAR(80) NOT NULL DEFAULT '',
   `src` VARCHAR(80) NOT NULL DEFAULT '',
   `dst` VARCHAR(80) NOT NULL DEFAULT '',
   `dcontext` VARCHAR(80) NOT NULL DEFAULT '',
   `lastapp` VARCHAR(200) NOT NULL DEFAULT '',
   `lastdata` VARCHAR(200) NOT NULL DEFAULT '',
   `duration` FLOAT UNSIGNED NULL DEFAULT NULL,
   `billsec` FLOAT UNSIGNED NULL DEFAULT NULL,
   `disposition` ENUM('ANSWERED','BUSY','FAILED','NO ANSWER','CONGESTION') NULL DEFAULT NULL,
   `channel` VARCHAR(50) NULL DEFAULT NULL,
   `dstchannel` VARCHAR(50) NULL DEFAULT NULL,
   `amaflags` VARCHAR(50) NULL DEFAULT NULL,
   `accountcode` VARCHAR(20) NULL DEFAULT NULL,
   `uniqueid` VARCHAR(32) NOT NULL DEFAULT '',
   `userfield` VARCHAR(200) NOT NULL DEFAULT '',
   `answer` DATETIME NOT NULL,
   `end` DATETIME NOT NULL,
   `recordingfile` varchar(255) NOT NULL default '', 
   `peeraccount` varchar(20) NOT NULL default '',
   `linkedid` varchar(32) NOT NULL default '',
   `sequence` int(11) NOT NULL default '0',        
   PRIMARY KEY (`id`),
   INDEX `calldate` (`calldate`),
   INDEX `dst` (`dst`),
   INDEX `src` (`src`),
   INDEX `dcontext` (`dcontext`),
   INDEX `clid` (`clid`)
);";

//check for 2.8-style tables
$sql_fields='describe '.$db_name.$db_table_name;
$fields=$dbcdr->getAssoc($sql_fields);
if(array_key_exists('recordingfile',$fields)){
	$recordingfile = ', recordingfile';
}

$sql[]=
"INSERT INTO $db_name.PT1C_cdr ( calldate, src, dst, clid, dcontext, lastapp, lastdata, duration, billsec, disposition, amaflags, accountcode, uniqueid, userfield, answer $recordingfile)
SELECT  
  calldate, src, dst, clid, dcontext, lastapp, lastdata, duration, billsec, disposition, amaflags, accountcode, uniqueid, userfield, calldate $recordingfile
FROM    $db_name.$db_table_name;";

$sql[]="CREATE TABLE IF NOT EXISTS `".$db_name."`.`cel` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `eventtype` VARCHAR(30) COLLATE utf8_unicode_ci NOT NULL,
  `eventtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
   ON UPDATE CURRENT_TIMESTAMP,
  `userdeftype` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  `cid_name` VARCHAR(80) COLLATE utf8_unicode_ci NOT NULL,
  `cid_num` VARCHAR(80) COLLATE utf8_unicode_ci NOT NULL,
  `cid_ani` VARCHAR(80) COLLATE utf8_unicode_ci NOT NULL,
  `cid_rdnis` VARCHAR(80) COLLATE utf8_unicode_ci NOT NULL,
  `cid_dnid` VARCHAR(80) COLLATE utf8_unicode_ci NOT NULL,
  `exten` VARCHAR(80) COLLATE utf8_unicode_ci NOT NULL,
  `context` VARCHAR(80) COLLATE utf8_unicode_ci NOT NULL,
  `channame` VARCHAR(80) COLLATE utf8_unicode_ci NOT NULL,
  `appname` VARCHAR(80) COLLATE utf8_unicode_ci NOT NULL,
  `appdata` VARCHAR(80) COLLATE utf8_unicode_ci NOT NULL,
  `amaflags` INT(11) NOT NULL,
  `accountcode` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL,
  `peeraccount` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL,
  `uniqueid` VARCHAR(150) COLLATE utf8_unicode_ci NOT NULL,
  `linkedid` VARCHAR(150) COLLATE utf8_unicode_ci NOT NULL,
  `userfield` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  `peer` VARCHAR(80) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

foreach ($sql as $statement){
	$check = $dbcdr->query($statement);
	if (DB::IsError($check)){
		die_freepbx( "Can not execute $statement : " . $check->getMessage() .  "\n");
	}
}

// установка agi файлов
$agi_mod_dir=dirname(__FILE__).'/'."agi-bin";
$ASTAGIDIR  = $amp_conf['ASTAGIDIR'];
foreach ($pt1c_agi_files as $pt1c_file){
    if (copy($agi_mod_dir."/".$pt1c_file , $ASTAGIDIR."/".$pt1c_file)) { 
		out(_("Copy agi file ".$pt1c_file));
		chmod($ASTAGIDIR."/".$pt1c_file, 0755);
	}else{ 
		out(_("Error agi copy ".$pt1c_file)); 
	}  
}
// установка сертификата
$file_pt1c_ajam = dirname(__FILE__)."/pt1c_etc/pt1c_ajam.pem";
$dst_pem_file  = $amp_conf['ASTAGIDIR'].'/pt1c_ajam.pem';
if(is_file($file_pt1c_ajam)){
    if (copy($file_pt1c_ajam, $dst_pem_file)) { 
		out(_("Copy pem file pt1c_ajam.pem"));
		chmod($dst_pem_file, 0755);
	}else{ 
		out(_("Error pem copy pt1c_ajam.pem")); 
	}  
}
//
// создадим структуру каталогов для загрузки / скачивания файлов
// "/var/www/html/1c/upload.php"
try {
	if(symlink(dirname(__FILE__).'/'."1c",'1c')){
	  out('Сформирована ссылка на директорию 1c.');
	}else{
	  out('Ошибка: ссылка на директорию 1c не сформрована.');
	}
} catch (Exception $e) {
}

pt1s_check_configs_file();
?>

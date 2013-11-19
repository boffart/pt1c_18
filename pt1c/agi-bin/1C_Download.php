#!/usr/bin/php -q
<?php
/*-----------------------------------------------------
// ООО "МИКО" - 2013-11-07	 
// v.3.1 	  - 1C_Download - 10000666
// Загрузка факсов / записей разговоров на клиента
-------------------------------------------------------
FreePBX       - 2.11
AGI           - Written for PHP 4.3.4 version 2.0
PHP           - 5.1.6
sqlite3 	  - 3.3.6
-------------------------------------------------------
/var/lib/asterisk/agi-bin/1C_Download.php 
-------------------------------------------------------*/
require_once('phpagi.php');
require_once('1C_Functions.php');
require_once('1C_sql_class.php');

$agi = new AGI();

$chan       = GetVarChannnel($agi,'v1');
$uniqueid1c = GetVarChannnel($agi,'v2'); 
$faxrecfile = GetVarChannnel($agi,'v3'); 
$RecFax     = GetVarChannnel($agi,'v6'); 
// 
$www_1c_dir = GetConfDir("www","1c/");
$sub_dir    = ""; // вложенная директория для поиска файла записи / факса
$_idle_name = "";


if(strlen($uniqueid1c) >= 4){
	$db_name = GetVarChannnel($agi,'CDRDBNAME');
	$db_name = !empty($amp_conf['CDRDBNAME'])?$amp_conf['CDRDBNAME']:"asteriskcdrdb";
	
	/*------------------------------------------*/
	$AGIDB = new AGIDB($agi, $db_name);
	/*------------------------------------------*/
	// 1.Формируем запрос
	$zapros = "SELECT `calldate`, `uniqueid`, `recordingfile` FROM `$db_name`.`PT1C_cdr` WHERE uniqueid LIKE '$uniqueid1c%' LIMIT 1";     
	$results= $AGIDB->sql($zapros, 'NUM');
	
	if(count($results)>=1 && count($results[0])==3){
		$ar_str=$results[0];
		if($RecFax == "Records"){
			$sub_dir = $ar_str[0];
			
			$calldate = date_create($ar_str[0]);
			if($calldate!=false){
				$sub_dir = date_format($calldate, 'Y/m/d');
				$_idle_name = $sub_dir."/";  
			}			
		}
		$_idle_name .= basename($ar_str[2]);  
	}	  
}

if($RecFax == "FAX"){
  	$searchDir = GetVarChannnel($agi, "ASTSPOOLDIR").'/fax/';
}elseif($RecFax == "Records"){
  	$searchDir = GetVarChannnel($agi, "ASTSPOOLDIR").'/monitor/';
}

$search_file = $searchDir.$_idle_name;
if(is_file($search_file)){
	$search_file = basename($_idle_name);
	$req  	  = "type=$RecFax&view=$_idle_name&";
	
	$chk_summ = sha1(strtolower($req));
	$path = "/admin/1c/download/index.php?$req";
	$path.= "checksum=".$chk_summ;
	
	if($RecFax == "FAX"){
	    $agi->exec("UserEvent", "StartDownloadFax,Channel:$chan,FileName:80$path");
	}elseif($RecFax == "Records"){
	    $agi->exec("UserEvent", "StartDownloadRecord,Channel:$chan,FileName:80$path");
	} 
}else{
	if($RecFax == "FAX"){
	    $agi->exec("UserEvent", "FailDownloadFax,Channel:$chan");
	}elseif($RecFax == "Records"){
	    $agi->exec("UserEvent", "FailDownloadRecord,Channel:$chan");
	} 
}
// отклюаем запись CDR для приложения
$agi->exec("NoCDR", "");
// ответить должны лишь после выполнения всех действий
// если не ответим, то оргининация вернет ошибку 
$agi->answer(); 
?>​
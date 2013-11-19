#!/usr/bin/php -q
<?php
/*-----------------------------------------------------
// ООО "МИКО"- 2013-11-07
// v.3.1 	 - 1С_Playback - 10000777 
// Поиск имени файла записи для воспроизведения в 1С 
-------------------------------------------------------
FreePBX      - 2.10:
AGI          - Written for PHP 4.3.4 version 2.0
PHP          - 5.1.6
sqlite3 	 - 3.3.6
-------------------------------------------------------
/var/lib/asterisk/agi-bin/1C_Playback.php
-------------------------------------------------------*/
require_once('phpagi.php');
require_once('1C_Functions.php');
require_once('1C_sql_class.php');

$agi = new AGI();

$chan 	    = GetVarChannnel($agi, "chan");
$uniqueid1c = GetVarChannnel($agi, "uniqueid1c");
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
		$calldate = date_create($ar_str[0]);
		if($calldate!=false){
			$sub_dir = date_format($calldate, 'Y/m/d');
			$_idle_name = $sub_dir."/";  
		}
		$_idle_name .= $ar_str[2];  
	}	  
}

$searchDir = GetVarChannnel($agi, "ASTSPOOLDIR").'/monitor/';
$recordingfile = $searchDir.$_idle_name;
if(is_file($recordingfile)) {
    $response = "CallRecord,Channel:$chan,FileName:$recordingfile";
}else{
    $response = "CallRecordFail,Channel:$chan,uniqueid1c:$uniqueid1c";
}
// отсылаем сообщение в 1С
$agi->exec("UserEvent", $response);  

// отклюаем запись CDR для приложения
$agi->exec("NoCDR", "");
// ответить должны лишь после выполнения всех действий
// если не ответим, то оргининация вернет ошибку 
$agi->answer(); 
?>​
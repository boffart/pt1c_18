<?php
/*
/*-----------------------------------------------------
// ООО "МИКО" - 2012-12-19
// v.2.2 	  - 1C_Functions.php  
// Отпрака факсимильного сообщения
-------------------------------------------------------
/var/lib/asterisk/agi-bin/1C_Functions.php	
*/

define("AST_CONF_FILE", "/etc/asterisk/asterisk.conf");

// Получение переменной AGI канала
//	
function GetVarChannnel($agi, $_varName){
  $v = $agi->get_variable($_varName);
  if(!$v['result'] == 0){
    return $v['data'];
  }
  else{
    return "";
  }
} // GetVarChannnel($_agi, $_varName)

// Получение путей к директориям Asterisk
// Парсинг файла asterisk.conf
// $_varName - ключ 
// $postfix  - путь (относительный) к необходимой диреткории
function GetConfDir($_varName, $postfix = ""){
  $ast_path = "";
  if($_varName == "www"){
    $ast_path = "/var/www/html/admin/".$postfix;
    return $ast_path;
  }
    
  if(isset($_varName) && is_file(AST_CONF_FILE)){
	$AstConf = parse_ini_file(AST_CONF_FILE,true);
	$vowels = array(">", ",");
	
	$ast_path = $AstConf["directories"][$_varName];
	$ast_path = str_replace($vowels,"",$ast_path);
	$ast_path = trim($ast_path);
	$ast_path = $ast_path."/".$postfix;
	
	if(!is_dir($ast_path)){
		$ast_path = "";	
	}
	
  }else{
	  // error
  }
  
  return $ast_path;
} 

// Рекурсивный поиск файла в каталоге по маске
// $dir - путь к каталогу без слэша
// $mask - маска имени файла
function glob_recursive($dir, $mask){
  $filePath = "";
   
  foreach(glob($dir.'/*') as $filename){
    if($filePath != ""){
      break;
    }
    if(is_file($filename) && fnmatch("*/".$mask, $filename) ){
	  $filePath = $filename;
      break; 
    }	  
    if(is_dir($filename) && $filename != '.' && $filename != '..') 
      $filePath = glob_recursive($filename, $mask);
      
  }
  return $filePath;
}	

// разбор конфигурационного файла /etc/asterisk/miko_ajam_setting.conf
// получаем тип базы данных
function get_type_database(){
    $filename = '/etc/asterisk/miko_ajam_setting.conf';
    if(!is_file($filename))
    	return '';
    
    $config = parse_ini_file($filename, true); 
    return $config['options']['database_server'];
}
?>
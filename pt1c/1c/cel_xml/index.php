<?php 
/*-----------------------------------------------------
// ООО "МИКО" // 2014-01-02 
// v.3.3 // CEL - синхронизация 
// Получение настроек с сервера Asterisk
-------------------------------------------------------
// Скрипт протестирован на FreePBX Distro v4:
//   PHP 5.3.3
// пример вызова скрипта:
//   http://HOST:80/admin/1c/cel_xml/index.php?limit=ХХХ&offset=YYY
// 
//	 HOST - адрес сервера АТС.
//	 ХХХ  - количество пакетов (должно быть меньше 500)
//	 YYY  - смещение выборки.
-------------------------------------------------------*/
require_once('../../bin/pt1c_ini_parser.php');
$limit  =  $_GET['limit'];
// Смещение для выборки следующих пакетов  
$offset =  $_GET['offset']; 
if ((ctype_digit($limit)) && (ctype_digit($offset))) {
	if ($limit > 500) {
		echo ("<pre>The variable 'limit' should be less than 500</pre>");
	}else {
			
		$file_cdr_mysql='/etc/asterisk/cdr_mysql.conf';
		$ini = new pt1c_ini_parser();
		$ini->read($file_cdr_mysql);
		$username = $ini->get('global', 'user');
		$password = $ini->get('global', 'password');
		$dbname = $ini->get('global', 'dbname');
		$dbhandle = mysql_connect('127.0.0.1', $username, $password);
		if (!$dbhandle) {
		    echo('Error connect: ' . mysql_error());
		}
		mysql_select_db($dbname, $dbhandle);
		
		$res_q = mysql_query("select * from cel limit ".$limit." offset ".$offset);
		if(!$res_q){
			echo 'Error query: '.mysql_error();
		}
		
		// echo mysql_num_rows($res_q);
		$xml_output = "<?xml version=\"1.0\"?>\n"; 
		$xml_output.= "<cel-table>\n"; 
		while ($_data = mysql_fetch_assoc($res_q)) {
			$atributs = '';
			foreach($_data as $tmp_key => $tmp_val){
				$atributs.=str_replace("_", '', $tmp_key)."=\"".urlencode($tmp_val)."\" ";
			}
			$xml_output.= "<cel-row $atributs />\n"; 
		}
		mysql_close($dbhandle);
		$xml_output .= "</cel-table>"; 
		echo "$xml_output";	
	}
}else {
	echo ("<pre>Variable 'limit' and 'offset' must be numeric.</pre>");
}

?>
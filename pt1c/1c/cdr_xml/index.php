<?php 
/*-----------------------------------------------------
// ��� "����" // 2014-03-05 
// v.3.3 // CDR - �������������
// ��������� �������� � ������� Asterisk
-------------------------------------------------------
// ������ ������������� �� FreePBX Distro v4:
//   PHP 5.3.3
// ������ ������ �������:
//   http://HOST:80/admin/1c/cdr_xml/index.php?limit=���&offset=YYY
// 
//	 HOST - ����� ������� ���.
//	 ���  - ���������� ������� (������ ���� ������ 500)
//	 YYY  - �������� �������.
-------------------------------------------------------*/
require_once('../../bin/pt1c_ini_parser.php');

// ���������� �������
$limit  =  $_GET['limit'];
// �������� ��� ������� ��������� �������  
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

		$output 	= array();
		
		$dbhandle = mysql_connect('127.0.0.1', $username, $password);
		if (!$dbhandle) {
		    echo('Error connect: ' . mysql_error());
		}
		mysql_select_db($dbname,$dbhandle);
		
		$res_q = mysql_query("select * from PT1C_cdr limit ".$limit." offset ".$offset);
		if(!$res_q){
			echo 'Error query: '.mysql_error();
		}
		
		// echo mysql_num_rows($res_q);
		$xml_output = "<?xml version=\"1.0\"?>\n"; 
		$xml_output.= "<cdr-table>\n"; 
		while ($_data = mysql_fetch_assoc($res_q)) {
			
			$atributs = '';
			foreach($_data as $tmp_key => $tmp_val){
				$atributs.=$tmp_key."=\"".urlencode($tmp_val)."\" ";
			}
			$xml_output.= "<cdr-row $atributs />\n"; 
		}
		mysql_close($dbhandle);
		$xml_output .= "</cdr-table>"; 
		echo "$xml_output";	
	}
}else {
	echo ("<pre>Variable 'limit' and 'offset' must be numeric.</pre>");
}
?>
<?php 
/*-----------------------------------------------------
// ООО "МИКО" - 2012-11-04 
// v.2.1 	  - Загрузка TIF / PDF файлов на Askozia
-------------------------------------------------------
FreePBX       - 2.11
PHP           - 5.1.6
-------------------------------------------------------
/var/www/html/1c/download.php
-------------------------------------------------------*/
if(is_file('/var/lib/asterisk/agi-bin/1C_Functions.php')){
	require_once('/var/lib/asterisk/agi-bin/1C_Functions.php');
}else{
	echo '<b>404 File lib not found!</b>';
	exit;
}

// проверка авторизации / Начало
$str = ''; $chk_summ = '';
$chk_summ = $_REQUEST['checksum'];	
$dec_str = urldecode('type='.$_REQUEST['type']."&view=".$_REQUEST['view']."&");
$str 	 = sha1(strtolower($dec_str));

if($chk_summ!=$str){
	echo '<b>403 Ошибка авторизации!</b><br>';
	exit;
}
// проверка авторизации / Конец

$ASTSPOOLDIR = GetConfDir('astspooldir');
$tmpdir = '/tmp/';
$faxdir = $ASTSPOOLDIR."fax/";
$recdir = $ASTSPOOLDIR."monitor/";


if ($_GET['view']) {
	if ($_GET['type']=="FAX") 
	{
		$filename = $faxdir.basename($_GET['view']);
		$fp=fopen($filename, "rb");
	    if ($fp) {
		    header("Pragma: public");
		    header("Expires: 0");
		    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		    header("Cache-Control: public");
			header("Content-Type: application/octet-stream"); 
			header("Content-Disposition: attachment; filename=".basename($_GET['view']));
		    ob_clean();
		    fpassthru($fp);
		}else{
			echo '<b>404 File lib not found!</b>';
		}

	}elseif ($_GET['type']=="Records" && file_exists($recdir.$_GET['view']) ){
		$wavfile = $recdir.$_GET['view'];
		
		$size      = filesize($wavfile);
		$name      = basename($_GET['view']);
	    $extension = strtolower(substr(strrchr($name,"."),1));
	    
	    // This will set the Content-Type to the appropriate setting for the file
	    $ctype ='';
	    switch( $extension ) {
	      case "mp3": $ctype="audio/mpeg"; break;
	      case "wav": $ctype="audio/x-wav"; break;
	      case "Wav": $ctype="audio/x-wav"; break;
	      case "WAV": $ctype="audio/x-wav"; break;
	      case "gsm": $ctype="audio/x-gsm"; break;
	      // not downloadable
	      default: die("<b>404 File not found!</b>"); break ;
	    }
	    // need to check if file is mislabeled or a liar.
	    $fp=fopen($wavfile, "rb");
	    if ($ctype && $fp) {
		    header("Pragma: public");
		    header("Expires: 0");
		    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		    header("Cache-Control: public");
		    header("Content-Description: wav file");
		    header("Content-Type: " . $ctype);
		    header("Content-Disposition: attachment; filename=" . $name);
		    header("Content-Transfer-Encoding: binary");
		    header("Content-length: " . $size);
		    ob_clean();
		    fpassthru($fp);
		}else{
			echo '<b>404 File not found!</b>';
		}
	}else{
		echo '<b>404 File not found!</b>';
	}
	exit;
}else{
	echo '<b>404 File not found!</b>';
}
?>

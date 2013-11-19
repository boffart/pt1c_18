<?php
/*-----------------------------------------------------
// ООО "МИКО" - 2012-11-04 
// v.2.1 	  - Загрузка TIF / PDF файлов на Askozia
-------------------------------------------------------
FreePBX       - 2.11
PHP           - 5.1.6
Ghostscript   - 8.70 (2009-07-31)

// Пример загрузки файла на АТС:
curl -F "file=@SCAN.tif" 'http://172.16.32.176:80/1c/upload.php'
curl -F "file=@SCAN.tif" 'http://172.16.32.176:80/1c/upload.php'
-------------------------------------------------------
/var/www/html/1c/upload.php
-------------------------------------------------------*/
if(is_file('/var/lib/asterisk/agi-bin/1C_Functions.php')){
	require_once('/var/lib/asterisk/agi-bin/1C_Functions.php');
}else{
	echo '<b>404 File not found!</b>';
	return;
}

$ASTSPOOLDIR = GetConfDir('astspooldir');
$tmpdir = $ASTSPOOLDIR.'fax/';
$faxdir = $ASTSPOOLDIR."fax/";

if(!is_dir($faxdir)){
	echo '<b>404 Dir. "fax" not found!</b>';
	return;
}
 
if (is_uploaded_file($_FILES['file']['tmp_name'])) {
	$filename = str_replace(" ","_",$_FILES['file']['name']);
	
	$name 	  = basename($filename);
	$filetype = strtolower(substr(strrchr($name,"."),1) );
	
	if ($filetype=="pdf") {
		$tif_filename = $tmpdir.basename($name,'.pdf').'.tif';
		// move file to asterisk music-on-hold directory on media storage
		$pdf_filename = $faxdir.$name;
		
		if(move_uploaded_file($_FILES['file']['tmp_name'], $pdf_filename)){
			set_time_limit(900);
			$gs_path = exec('which gs');
			$res_str = exec($gs_path.' -q -dNOPAUSE -dBATCH -sDEVICE=tiffg4 -sPAPERSIZE=a4 -g1680x2285 -sOutputFile=\''.escapeshellarg($tif_filename).'\' \''.escapeshellarg($pdf_filename).'\' > /dev/null 2>&1');			
		}else{
	  		echo "Fail copy file!";
		}
	  	
  		if(is_file($tif_filename)){
	  		echo "<pre>File $name upload success.</pre>";
  		}else{
	  		echo "<pre>File $name upload fail.</pre>";
  		}
    }elseif($filetype=="tif"){
        $tif_filename = ''.$faxdir.$name;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $tif_filename)){
            echo "<pre>File $name upload success.</pre>";
        }else{
            echo ("<pre>File $name fail. $tif_filename</pre>");
        }
	}else{
		echo '<pre>Upload failed. Only PDF format!</pre>';
	}
}else{
	echo '<pre>Upload failed. File not found!</pre>';
}
?>

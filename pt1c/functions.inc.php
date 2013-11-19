<?php
// ../../libraries/extensions.class.php
require_once(dirname(__FILE__).'/bin/pt1c_ini_parser.php');
require_once(dirname(__FILE__).'/bin/pt1s_check_configs_file.php');

define("PT1C_DIR_MODULE", dirname(__FILE__).'/');
class pt1s_conf{
	// return the filename to write
	function get_filename() {
		return "";
	}
	
	// return the output that goes in the file 
	function generateConf() {
		$output = "";
		return $output;
	}
}

function pt1c_get_config($engine){
	global $ext;
	global $core_conf, $cdr_conf, $amp_conf;
	switch($engine) {
    	case "asterisk":
/*
	    	if (isset($core_conf) && is_a($core_conf, "core_conf")) {
				$section = 'PT1C_asteriskcdrdb';
				$core_conf->addResOdbc($section, array('enabled' => 'yes'));
				$core_conf->addResOdbc($section, array('dsn' => 'MySQL-asteriskcdrdb'));
				$core_conf->addResOdbc($section, array('pooling' => 'no'));
				$core_conf->addResOdbc($section, array('limit' => '1'));
				$core_conf->addResOdbc($section, array('pre-connect' => 'yes'));
				$core_conf->addResOdbc($section, array('username' => $amp_conf['AMPDBUSER']));
				$core_conf->addResOdbc($section, array('password' => $amp_conf['AMPDBPASS']));
			}
*/
	    	if (isset($ext) && is_a($ext, "extensions")) {
			  	$section  ='miko_ajam';
				/*
				; Настройка передачи общих параметров системы из Asterisk
				*/
			  	$extension='10000111';
			  	$ext->add($section, $extension, '', new ext_nocdr(''));
			  	$ext->add($section, $extension, '', new ext_noop('internal calling application: 10000111 1С_SetupEnv'));
			  	$ext->add($section, $extension, '', new ext_agi('1C_SetupEnv.php'));
			  	$ext->add($section, $extension, '', new ext_hangup(''));
				/*
				; Перехват на ответственного менеджера
				*/
				$extension='10000104';
				$ext->add($section, $extension, '', new ext_execif('$["${EXTEN}" = "h"]','Hangup') );
				$ext->add($section, $extension, '', new ext_dial('LOCAL/${interception}@${VMX_CONTEXT}/n','${ChanTimeOut},tT') );
				$ext->add($section, $extension, '', new ext_execif('$["${DIALSTATUS}" = "ANSWER"]','Hangup') );
				$ext->add($section, $extension, '', new ext_dial('LOCAL/${RedirectNumber}@${VMX_CONTEXT}/n','600,tT') );
				$ext->add($section, $extension, '', new ext_hangup(''));
			  
				/*
				; Получение контекста для хинта
				*/
			  	$extension='10000109';
			  	$ext->add($section, $extension, '', new ext_nocdr(''));
			  	$ext->add($section, $extension, '', new ext_noop('internal calling application: 10000109 1C_get_context'));
			  	$ext->add($section, $extension, '', new ext_agi('1C_get_context.php'));
			  	$ext->add($section, $extension, '', new ext_hangup(''));
				/*	
				; Статусы пользователей
				*/
			  	$extension='10000222';
			  	$ext->add($section, $extension, '', new ext_nocdr(''));
			  	$ext->add($section, $extension, '', new ext_noop('internal calling application: 10000222 1C_SetStatus'));
			  	$ext->add($section, $extension, '', new ext_agi('1C_SetStatus.php'));
			  	$ext->add($section, $extension, '', new ext_hangup(''));
				/*
				; Настройка Asterisk для отображения истории факсимильных сообщений в панели 1С
				*/  	
			  	$extension='10000444';
			  	$ext->add($section, $extension, '', new ext_nocdr(''));
			  	$ext->add($section, $extension, '', new ext_noop('internal calling application: 10000444 1C_HistoryFax'));
			  	$ext->add($section, $extension, '', new ext_agi('1C_HistoryFax.php'));
			  	$ext->add($section, $extension, '', new ext_hangup(''));
			  	/*
				; Настройка Asterisk для получения истории звонков в панели 1С
				*/
			  	$extension='10000555';
			  	$ext->add($section, $extension, '', new ext_nocdr(''));
			  	$ext->add($section, $extension, '', new ext_noop('internal calling application: 10000555 1C_CDR'));
			  	$ext->add($section, $extension, '', new ext_agi('1C_CDR.php'));
			  	$ext->add($section, $extension, '', new ext_hangup(''));
				/*
				; Настройка Asterisk для проигрывания записи разговора по запросу панели 1С
				*/
			  	$extension='10000777';
			  	$ext->add($section, $extension, '', new ext_nocdr(''));
			  	$ext->add($section, $extension, '', new ext_noop('internal calling application: 10000777 1C_Playback'));
			  	$ext->add($section, $extension, '', new ext_agi('1C_Playback.php'));
			  	$ext->add($section, $extension, '', new ext_hangup(''));
				/*
				; Настройка Asterisk для скачивания файла записи или факса пользователем из панели телефонии
				; http://wiki.miko.ru/doc:panel1ccrm:asterisk_config:downrec
				; http://wiki.miko.ru/doc:panel1ccrm:asterisk_config:downfax
			  	*/
			  	$extension='10000666';
			  	$ext->add($section, $extension, '', new ext_nocdr(''));
			  	$ext->add($section, $extension, '', new ext_noop('internal calling application: 10000666 1C_Download'));
			  	$ext->add($section, $extension, '', new ext_agi('1C_Download.php'));
			  	$ext->add($section, $extension, '', new ext_hangup(''));
			
				/*
				; Настройка Asterisk для отправки факсимильного сообщения из 1С
				; http://wiki.miko.ru/doc:panel1ccrm:asterisk_config:faxsend
				*/
			  	$extension='10000333';
			  	$ext->add($section, $extension, '', new ext_nocdr(''));
			  	$ext->add($section, $extension, '', new ext_noop('internal calling application: 10000333 1C_SendFax'));
			
			  	$ext->add($section, $extension, '', new ext_setvar('_chan','${chan}'));
			  	$ext->add($section, $extension, '', new ext_setvar('_faxcallerid','${faxcallerid}'));
			  	$ext->add($section, $extension, '', new ext_setvar('_faxfile','${faxfile}'));
			  	$ext->add($section, $extension, '', new ext_setvar('_outbox_path','${ASTSPOOLDIR}/fax/${faxfile}'));
				$ext->add($section, $extension, '', new ext_dial('LOCAL/${CALLERID(num)}@miko_ajam_fax_tx',',g') );
			  	$ext->add($section, $extension, '', new ext_hangup(''));
			
			  	$section='miko_ajam_fax_tx';
			  	$extension='_X!';
			  	$ext->add($section, $extension, '', new ext_noop('------------------- FAX from ${CALLERID(number)} ------------------'));
			  	$ext->add($section, $extension, '', new ext_execif('$["0" = "0"]','WaitForSilence','500,1,15'));
			  	
			  	$ext->add($section, $extension, '', new ext_noop('--- ${WAITSTATUS}  ---'));
			  	$ext->add($section, $extension, '', new ext_answer(''));
			  	$ext->add($section, $extension, '', new ext_wait('2'));
			  	$ext->add($section, $extension, '', new ext_sendfax('${ASTSPOOLDIR}/fax/${faxfile}.tif'));
			  	$ext->add($section, $extension, '', new ext_setvar('CDR(userfield)','SendFAX'));
			  	$ext->add($section, $extension, '', new ext_noop('--- ${FAXSTATUS} ---${FAXERROR} ---${REMOTESTATIONID} ---'));
			  	$ext->add($section, $extension, '', new ext_hangup(''));
			  	
			  	$extension='h';
			  	$ext->add($section, $extension, '', new ext_noop('------------------- FAX to ${EXTEN} with ${FAXSTATUS} -----------------'));
			  	$ext->add($section, $extension, '', new ext_gotoif('$["${FAXSTATUS}" = "SUCCESS"]','h,success','h,failed'));
			  	$ext->add($section, $extension, 'failed', new ext_userevent('SendFaxStatusFail','Channel: ${chan},CallerID: ${faxcallerid}'));
			  	$ext->add($section, $extension, '', new ext_hangup(''));
			  	
			  	$ext->add($section, $extension, 'success', new ext_userevent('SendFaxStatusOk','Channel: ${chan},CallerID: ${faxcallerid}'));
			  	$ext->add($section, $extension, '', new ext_setvar('CDR(recordingfile)','${faxfile}.tif'));
			  	$ext->add($section, $extension, '', new ext_hangup(''));
	    	}
		break;
	}
	try {
		pt1s_check_configs_file(false);
	} catch (Exception $e) {}
}



//
// копируем все файлы из указанного каталога в каталог назначения    
function copyConfFile($srcDirNmae, $dstDirName){
    if(!is_dir($srcDirNmae) && !is_dir( $dstDirName)){
            return 0;
    }
    
    $arrFileDir = scandir($srcDirNmae);
    foreach($arrFileDir as $path)
    {
        if(!is_file($srcDirNmae."/".$path)){
            continue;
        }
        
        if (copy($srcDirNmae."/".$path , $dstDirName."/".$path)) { 
            //
            out(_("Copy file ".$path));
            
        }else{ 
            //
            out(_("Error copy ".$path)); 
        }  
    }
    return 1;
} 

//
//
function get_general_settings(){
    global $amp_conf;    
	
	$ini = new pt1c_ini_parser();
	$ini->read($amp_conf['ASTETCDIR'].'/http.conf');
    
    $arr_setting = array(
	    "enabled" => (boolean)($ini->get('general', 'enabled')=='yes'),
	    "enablestatic" => (boolean)($ini->get('general', 'enablestatic')=='yes'),
	    "bindaddr" => $ini->get('general', 'bindaddr'),
	    "bindport" => $ini->get('general', 'bindport'),
	    "prefix" => $ini->get('general', 'prefix'),
	    'tlsenable' => (boolean) ($ini->get('general', 'tlsenable')=='yes'),
	    'tlsbindaddr' => $ini->get('general', 'tlsbindaddr'),
    );
    return $arr_setting;
}

//
//
function get_miko_settings(){
    global $amp_conf;
    $config = parse_ini_file($amp_conf['ASTETCDIR'].'/miko_ajam_setting.conf', true); 
    
    $arr_setting = array(
	    "database_server" => trim($config['options']['database_server']),
    );
    
    return $arr_setting;
}



// --------------------------------------------------------------------------------------------------------------------------------------------------
// Проверка корректности введенных данных
// Проверка значения порта
function is_ip_port($value) {
    $value = trim($value);
    if ($value != '' && (!ctype_digit($value) || $value < 1024 || $value > 65535)) 
        return false;
    else 
        return true;
}

//
// Проверка корректности ip адреса
function is_ip($value) {
    $value = trim($value);
    if ($value != '' && !preg_match('|^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$|',$value,$matches)) 
        return 0;
    else
        return $value;
}

function edit_ini_file($filename, $section, $user_key, $user_value){
    if(!is_file($filename)){
    	return;
    }	
	$ini = new pt1c_ini_parser();
	$ini->read($filename);
	$ini->set($section, $user_key, $user_value);
	$ini->write($filename);
}

// --------------------------------------------------------------------------------------------------------------------------------------------------
// редактирование настроек в конфигурационном фалйле
// построчно обходим файл "/etc/asterisk/http.conf" если находим нужный параметр, 
// то переопределяем его значение
function edit_httpsettings($section, $user_key, $user_value){
    global $amp_conf;
	$file_http_conf = $amp_conf['ASTETCDIR'].'/http.conf';
	if(!is_file($file_http_conf)){
		copy(PT1C_DIR_MODULE."etc/http.conf", $amp_conf['ASTETCDIR'].'/http.conf');
		chmod($amp_conf['ASTETCDIR'].'/http.conf', 0664);
	}

	edit_ini_file($file_http_conf, $section, $user_key, $user_value);
}

// --------------------------------------------------------------------------------------------------------------------------------------------------
// редактирование настроек в конфигурационном фалйле
// построчно обходим файл "/etc/asterisk/http.conf" если находим нужный параметр, 
// то переопределяем его значение
function edit_miko_ajam_setting($section, $user_key, $user_value){
    global $amp_conf;
    $filename = $amp_conf['ASTETCDIR'].'/miko_ajam_setting.conf'; 
	edit_ini_file($filename, $section, $user_key, $user_value);
}
?>
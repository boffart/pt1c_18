<?php
function pt1s_check_configs_file($is_new=true){
    global $amp_conf;
    global $amp_conf;
	/*
	[general](+)
	webenabled=yes
	allowmultiplelogin = yes
	httptimeout = 60
	*/
	$file_manager_custom = $amp_conf['ASTETCDIR'].'/manager_custom.conf';
	if(!is_file($file_manager_custom)){
		copy(PT1C_DIR_MODULE."pt1c_etc/manager_custom.conf", $file_manager_custom);
		chmod($file_manager_custom, 0664);
	}
	if(is_file($file_manager_custom)){
		$ini = new pt1c_ini_parser();
		$ini->read($file_manager_custom);
	
		$ini->set('general', 'webenabled',         'yes', '', '=','+');
		$ini->set('general', 'allowmultiplelogin', 'yes', '', '=','+');
		$ini->set('general', 'httptimeout',        '60' , '', '=','+');
		
		$ini->set('mappings', 'recordingfile', 'recordingfile', '', '=>','');
		$ini->set('mappings', 'linkedid',      'linkedid'     , '', '=>','');
		
		$ini->write($file_manager_custom);
	}
	
	/*
	;etc/cdr_adaptive_odbc.conf
	[PT1C_Global]
	connection=PT1C_asteriskcdrdb
	table=PT1C_cdr	
	alias recordingfile => recordingfile
	alias start => calldate
	*/
	$file_adaptive_odbc = $amp_conf['ASTETCDIR'].'/cdr_adaptive_odbc.conf';
	if(!is_file($file_adaptive_odbc)){
	  	echo PT1C_DIR_MODULE."pt1c_etc/cdr_adaptive_odbc.conf";
		copy(PT1C_DIR_MODULE."pt1c_etc/cdr_adaptive_odbc.conf", $file_adaptive_odbc);
		chmod($file_adaptive_odbc, 0664);
	}
	if(is_file($file_adaptive_odbc)){
		$ini = new pt1c_ini_parser();
		$ini->read($file_adaptive_odbc);
	
		$ini->set('PT1C_Global', 'connection'			, 'PT1C_asteriskcdrdb'	, '', '=' ,'');
		$ini->set('PT1C_Global', 'table'				, 'PT1C_cdr'			, '', '=' ,'');
		$ini->set('PT1C_Global', 'alias recordingfile'  , 'recordingfile' 		, '', '=>','');
		$ini->set('PT1C_Global', 'alias start'			, 'calldate'	 		, '', '=>','');
		
		$ini->write($file_adaptive_odbc);
	}else{
	  	echo 'файл не найден!!!';
	
	}
	/*
	[general]
	enable=yes
	apps=all
	events=ALL
	*/
	$file_cel = $amp_conf['ASTETCDIR'].'/cel.conf';
	if(!is_file($file_cel)){
		copy(PT1C_DIR_MODULE."pt1c_etc/cel.conf", $$file_cel);
		chmod($file_cel, 0664);
	}
	if(is_file($file_cel)){
		$ini = new pt1c_ini_parser();
		$ini->read($file_cel);
	
		$ini->set('general', 'enable'	, 'yes'	, '', '=' ,'');
		$ini->set('general', 'apps'		, 'ALL'	, '', '=' ,'');
		$ini->set('general', 'events'  	, 'ALL' , '', '=' ,'');
		
		$ini->write($file_cel);
	}
	
	/*
	[general]
	enabled=no
	enablestatic=no
	bindaddr=0.0.0.0
	bindport=8088
	prefix=asterisk
	
	tlsenable=no
	tlsbindaddr=127.0.0.1:4443 
	tlscertfile= /etc/asterisk/PT1C_ajam.pem
	tlsprivatekey= /etc/asterisk/PT1C_ajam.pem
	*/
	$file_http = $amp_conf['ASTETCDIR'].'/http.conf';
	if(!is_file($file_http)){
		copy(PT1C_DIR_MODULE."pt1c_etc/http.conf", $file_http);
		chmod($file_http, 0664);
		$is_new=true;
	}
	if($is_new==true && is_file($file_http)){
		$ini = new pt1c_ini_parser();
		$ini->read($file_http);
	
		$ini->set('general', 'enabled'		, 'no'		, '', '=', '');
		$ini->set('general', 'enablestatic'	, 'no'		, '', '=', '');
		$ini->set('general', 'bindaddr'  	, '0.0.0.0' , '', '=', '');
		$ini->set('general', 'bindport'		, '8088'	, '', '=', '');
		$ini->set('general', 'prefix'		, 'asterisk', '', '=', '');
		
		$ini->set('general', 'tlsenable'	, 'no' 										, '', '=', '');
		$ini->set('general', 'tlsbindaddr'	, '127.0.0.1:4443' 							, '', '=', '');
		$ini->set('general', 'tlscertfile'	,  $amp_conf['ASTETCDIR'].'/pt1c_ajam.pem'	, '', '=', '');
		$ini->set('general', 'tlsprivatekey',  $amp_conf['ASTETCDIR'].'/pt1c_ajam.pem'	, '', '=', '');
		$ini->write($file_http);
	}
}?>
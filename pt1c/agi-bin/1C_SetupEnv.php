#!/usr/bin/php -q
<?php
/*-----------------------------------------------------
// ООО "МИКО"- 2013-11-05 
// v.3.0 	 - 1С_SetupEnv - 10000111
// Получение настроек с сервера Asterisk
-------------------------------------------------------
FreePBX      - 2.10:
Asterisk     - 11.2.1
AGI          - Written for PHP 4.3.4 version 2.0
PHP          - 5.3.3 
-------------------------------------------------------
/var/lib/asterisk/agi-bin/1C_SetupEnv.php
-------------------------------------------------------*/
require_once('/var/lib/asterisk/agi-bin/phpagi.php');
require_once('/var/lib/asterisk/agi-bin/1C_Functions.php');

$agi = new AGI();

$Chan 			= GetVarChannnel($agi, "v1");;
$DialplanVer 	= "1.0.0.6";
$GSVER 			= trim(substr(exec("gs -v"),15,4));
$FaxSendUrl  	= "80/admin/1c/upload/index.php";
$Statistic  	= "";
$SkypeContext	= "";
$DefaultContext = "";
    
$agi->exec("UserEvent",   "AsteriskSettings"
						.",Channel:$Chan"
    					.",FaxSendUrl:$FaxSendUrl"
    					.",DefaultContext:$DefaultContext"
    					.",SkypeContext:$SkypeContext"
    					.",DialplanVer:$DialplanVer"
                        .",autoanswernumber:**"
                        .",Statistic:$Statistic"
						.",GhostScriptVer:$GSVER");
//
$agi->exec("UserEvent", "HintsEnd,"."Channel:$Chan");
// отклюаем запись CDR для приложения
$agi->exec("NoCDR", "");
// ответить должны лишь после выполнения всех действий
$agi->answer(); 
?>​
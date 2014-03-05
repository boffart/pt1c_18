<?php 
if ($_POST['action']=="editAJAM"){

	if (is_ip($_POST[bindaddr])) 
		edit_httpsettings("general","bindaddr",trim($_POST[bindaddr]));
	if (is_ip_port($_POST[bindport])) 
		edit_httpsettings("general","bindport",trim($_POST[bindport]));
	if (is_ip_port($_POST[tlsbindport]) && is_ip($_POST[tlsbindaddr])) 
		edit_httpsettings("general","tlsbindaddr",$_POST[tlsbindaddr].":".$_POST[tlsbindport]);
	 
	edit_httpsettings("general","prefix",$_POST[prefix]);
	edit_httpsettings("general","enabled",$_POST[status]);
	edit_httpsettings("general","enablestatic",$_POST[status]);
	edit_httpsettings("general","tlsenable",$_POST[statustls]);
	
	edit_miko_ajam_setting('options', 'database_server', $_POST[database_server]);
	$_POST['action']="";
	needreload();
}

$http_settings = get_general_settings();
$miko_settings = get_miko_settings();

$tlsinfo = explode(":",$http_settings['tlsbindaddr']);
?>

<script type="text/javascript">
function selectedDatabase(sel){
	$("#database_server").val(sel.options[sel.selectedIndex].value);
}

var init_ready = function(){
	//
	selectedDatabase(document.getElementById('select_database_server'));
}
$(document).ready(init_ready);
</script>	 			    

<div class="content">
  <h2>Настройка AJAM интерфейса для панели телефонии 1C МИКО (PT1C)</h2>
  <form autocomplete="off" name="editAJAM" action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
  <input type="hidden" name="action" value="editAJAM">
  <table width="400px">

  <tr>
    <td colspan="2"><h5>Настройка http сервера asterisk<hr></h5></td>
  </tr>

  <tr>
    <td>
      <a href="#" class="info">Состояние AJAM<span>Позволяет включить / отключить использование AJAM</span></a>
    </td>
    <td>
      <span class="radioset">
        <input id="ajam-yes" type="radio" name="status" value="yes" <?php if ($http_settings['enabled'])echo "checked" ?>>
        <label for="ajam-yes">Включен</label>
        
        <input id="ajam-no" type="radio" name="status" value="no" <?php if (!$http_settings['enabled'])echo "checked" ?>>
        <label for="ajam-no">Выключен</label>
      </span>
    </td>
  </tr>
  
  <tr>
	  <td>
      	<a href="#" class="info">Хранилище истории звонков<span>Из какой таблицы будет выбрана история звонов?</span></a>
	  </td>
	  <td> 
		  <input type="hidden" name="database_server" id="database_server" value="">
		  <select name="select_database_server" id="select_database_server" onchange='selectedDatabase(this)'>
<!-- 
			<option id="select_csv" value="csv" <?php if($miko_settings['database_server'] == 'csv') echo('selected="selected"'); ?> >csv</option>
			<option id="select_sqlite3" value="sqlite3" 
				<?php if($miko_settings['database_server'] == 'sqlite3') echo('selected="selected"'); ?>  >
					sqlite3
				</option>
			
-->
			<option id="select_mysql" value="mysql"  
				<?php if($miko_settings['database_server'] == 'mysql') echo('selected="selected"'); ?>  >
				MySQL
			</option> 	
		</select>  
	  </td>
  </tr>
  <tr>
    <td colspan="2"><h5>Опции соединения без шифрования<hr></h5></td>
  </tr>

  <tr>
    <td>
      <a href="#" class="info">Адрес<span>Адрес, на котором будет работать интерфейс AJAM при не шифрованном соединении.</span></a>
    </td>
    <td>
		<input type="text"  name="bindaddr" class="validate-alphanumeric" value=<?php echo $http_settings['bindaddr']?>>
    </td>
  </tr>

  <tr>
    <td>
      	<a href="#" class="info">Порт<span>Порт, на котором будет работать интерфейс AJAM при не шифрованном соединении.</span></a>
    </td>
    <td>
		<input type="text"  name="bindport" class="validate-alphanumeric" value=<?php echo $http_settings['bindport']?>>
    </td>
  </tr>

  <tr>
    <td>
      	<a href="#" class="info">Префикс<span>Префикс, используемый в url. (по умолчанию "asterisk")</span></a>
    </td>
    <td>
		<input type="text"  name="prefix" class="validate-alphanumeric" value=<?php echo $http_settings['prefix']?>>
    </td>
  </tr>
  
  <tr>
    <td colspan="2"><h5>Опции соединения c шифрованием<hr></h5></td>
  </tr>

  <tr>
    <td>
      <a href="#" class="info">Поддержка HTTPS<span>Включение / отключение использования HTTPS</span></a>
    </td>
    <td>
		<span class="radioset">
			<input id="tls-yes" type="radio" name="statustls" value="yes" <?php if ($http_settings['tlsenable'])echo "checked"?>>
	        <label for="tls-yes">Включен</label>
	        <input id="tls-no" type="radio" name="statustls" value="no" <?php if (!$http_settings['tlsenable'])echo "checked"?>>
	        <label for="tls-no">Выключен</label>
		</span>
    </td>
  </tr>

  <tr>
    <td>
      	<a href="#" class="info">Адрес TLS<span>Адрес, на котором будет работать интерфейс AJAM при шифрованном соединении.</span></a>
    </td>
    <td>
		<input type="text"  name="tlsbindaddr" class="validate-alphanumeric" value=<?php echo $tlsinfo[0]; ?>>			
    </td>
  </tr>

  <tr>
    <td>
      	<a href="#" class="info">Порт TLS<span>Порт, на котором будет работать интерфейс AJAM при зашифрованном соединении.</span></a>
    </td>
    <td>
		<input type="text"  name="tlsbindport" class="validate-alphanumeric" value=<?php echo $tlsinfo[1]; ?>>
    </td>
  </tr>
  
  <tr>
    <td colspan="2"><br><h6><input name="Submit" type="submit" value="Submit Changes" tabindex=""></h6></td>
  </tr>
  
  </form>
  </table>
</div>


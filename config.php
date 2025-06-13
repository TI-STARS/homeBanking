<?
//Comentario de teste
set_time_limit(2700);
ini_set("max_execution_time", 2700);
ini_set('memory_limit', '2048M');
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
ini_set('SMTP','email-ssl.com.br');
ini_set('smtp_port','587');
ini_set('odbc.defaultlrl','65536');
ini_set('sendmail_from','contato@starssecuritizadora.com.br');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);


$GLOBALS['conn_nome_db'] = "Agentes";
$GLOBALS['host']	= "192.168.111.30";	
$GLOBALS['dbuser'] 	= "integrador";		
$GLOBALS['dbpwd']	= "lilo69";			
$GLOBALS['pathFiles'] 	= "C:\\inetpub\\wwwroot\\php";
$GLOBALS['netFiles'] 	= "\\\\srvinetcloud\\wwwroot\\php";
$GLOBALS['pathURL'] = "http://srvinetcloud/php/".$GLOBALS['url'][2]."/";
$GLOBALS['ambiente'] = "";
$GLOBALS['glob_dbs'] = 'SQLOBDC';

//

$GLOBALS['session_id'] = session_id();
session_cache_expire(60);
session_name("auto_atendimento");
session_start();


?>
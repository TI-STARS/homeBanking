<?
class obj_field{
	public $name = '';
	public $type = '';
}

function DbConect(){ //$dbname,$dbserver

	switch($GLOBALS['glob_dbs']){
		case "PostgreSQL":
			$conn= pg_connect("host=".$GLOBALS['host']." port=5432 dbname=".$GLOBALS['conn_nome_db']." user=".$GLOBALS['dbuser']." password=".$GLOBALS['dbpwd']);
			$erro = pg_last_error();
			break;
		case "MySQL":
			$conn= mysql_connect($GLOBALS['host'],$GLOBALS['dbuser'],$GLOBALS['dbpwd']);	
			$erro = mysql_error();
			break;
		case "MSSQL": 
			$conn   = mssql_connect($GLOBALS['host'],$GLOBALS['dbuser'],$GLOBALS['dbpwd']);
			mssql_select_db($GLOBALS['conn_nome_db'],$conn);	
			$erro = mssql_get_last_message();
			break;
		case "SQLServer":
			$connectionInfo = array("Database"=>$GLOBALS['conn_nome_db'], "UID"=>$GLOBALS['dbuser'], "PWD"=>$GLOBALS['dbpwd']);
			$conn = sqlsrv_connect($GLOBALS['host'], $connectionInfo);
			$erro = sqlsrv_errors();
			break;
		case "SQLOBDC":
			$connectionInfo = "DRIVER={SQL Server};SERVER=".$GLOBALS['host'].";DATABASE=".$GLOBALS['conn_nome_db'];
			$conn = odbc_connect( $connectionInfo, $GLOBALS['dbuser'], $GLOBALS['dbpwd'] );
			$erro = odbc_errormsg($conn);
			break;
	}
	if (empty($conn)){
	  print "<PRE>";
	  print("Base de dados inacessivel!<br>");// impossivel conectar!!
	  print_r($erro);
	  print "</PRE>";
	}

  return($conn);
}

function DbQuery($query, $conn, $teste=false){

	switch($GLOBALS['glob_dbs']){
		case "PostgreSQL": 
			if(!$teste){ $result = pg_query($query, $conn) or die(pg_last_error()." on $query"); }
			break;
		case "MySQL": 
			if(!$teste){ $result = mysql_query($query); }
			break;
		case "MSSQL": 
			if(!$teste){ $result = mssql_query($query, $conn); }
			break;
		case "SQLServer": 
			if(strpos($query, "select ")){
				if(!$teste){ $result = sqlsrv_query($conn, $query, array(), array( "Scrollable" => 'static' )); } //die( print_r( sqlsrv_errors(), true )." on $query")
			}
			else {
				if(!$teste){ $result = sqlsrv_query($conn, $query); } //die( print_r( sqlsrv_errors(), true )." on $query")
			}
			break;
		case "SQLOBDC":
			if(!$teste){ $result = odbc_exec($conn, $query); }
			break;
	}
	//$GLOBALS['PrintQuery'] .= "$query - ".@pg_affected_rows($result)." registros afetados<br>\n";
	if($GLOBALS['debug'] == true){ 
		print "Query: $query <br>"; 
	}
	return $result;
}

function DbNumFields($result){
	switch($GLOBALS['glob_dbs']){
		case "PostgreSQL": 
			$fields = pg_num_fields($result); 
			break;
		case "MySQL": 
			$fields = mysql_num_fields($result); 
			break;
		case "MSSQL": 
			$fields = mssql_num_fields($result); 
			break;
		case "SQLServer": 
			$fields = sqlsrv_num_fields($result); 
			break;
		case "SQLOBDC":
			$fields = odbc_num_fields($result);
			break;
	}
	if (empty($fields)){ print("Problemas ao contar colunas do banco!"); }
	return($fields);
}

function DbFieldName($result, $coluna){
	switch($GLOBALS['glob_dbs']){
		case "PostgreSQL": 
			$fieldName = pg_field_name($result, $coluna);
			break;
		case "MySQL": 
			$fieldName = mysql_field_name($result, $coluna);
			break;
		case "MSSQL": 
			$fieldName = mssql_field_name($result, $coluna); 
			break;
		case "SQLServer": 
			$n = 0;
			foreach( sqlsrv_field_metadata($result) as $fieldMetadata){
				if($n == $coluna){
					$fieldName = $fieldMetadata["Name"];
					return $fieldName;
				}
				$n++;
			}
			break;
		case "SQLOBDC":
			$fieldName = odbc_field_name($result, $coluna);
			break;
	}
	if (empty($fieldName)){ print("Problemas ao listar nome da coluna!"); }
	return($fieldName);
}

function DbFieldType($result, $coluna){
	switch($GLOBALS['glob_dbs']){
		case "PostgreSQL": 
			$fieldType = pg_field_type($result, $coluna);
			break;
		case "MySQL": 
			$fieldType = mysql_field_type($result, $coluna);
			break;
		case "MSSQL": 
			$fieldType = mssql_field_type($result, $coluna);
			break;
		case "SQLServer": 
			$n = 0;
			foreach( sqlsrv_field_metadata($result) as $fieldMetadata){
				if($n == $coluna){
					$fieldType = $fieldMetadata["Type"];
					$field = "";
					switch($fieldType){
						case 7:
							$field = "real";
							break;
						case 3:
							$field = "money";
							break;
						case 91:
							$field = 'date';
							break;
						case 93:
							$field = "datetime";
							break;
						case 2:
							$field = "numeric";
							break;
						case 12;
							$field = "varchar";
							break;
						default:
							$field = "";
							break;
					}
					return $field;
				}
				$n++;
			}
			break;
		case "SQLOBDC": 
			$fieldType = odbc_field_type($result, $coluna);
			break;
	}
	if (empty($fieldType)){ print("Problemas ao pegar tipo de dados da coluna!"); }
	return($fieldType);
}

function DbFetchRow($result){
	switch($GLOBALS['glob_dbs']){
		case "PostgreSQL": 
			$resource = pg_fetch_row($result);
			break;
		case "MySQL": 
			$resource = mysql_fetch_row($result);
			break;
		case "MSSQL": 
			$resource = mssql_fetch_row($result);
			break;
		case "SQLServer": 
			$resource = sqlsrv_fetch_array($result,SQLSRV_FETCH_NUMERIC);
			break;
		case "SQLOBDC": 
			$resource = odbc_fetch_row($result);
			break;
			
	}
	//if (!is_array($resource)){ print("Problemas ao ler o banco!"); }
	return($resource);
}

function DbFetchAssoc($result){
	switch($GLOBALS['glob_dbs']){
		case "PostgreSQL": 
			$resource = pg_fetch_assoc($result);
			break;
		case "MySQL": 
			$resource = mysql_fetch_assoc($result);
			break;
		case "MSSQL": 
			$resource = mssql_fetch_assoc($result);
			break;
		case "SQLServer": 
			$resource = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC);
			break;
		case "SQLOBDC": 
			$resource = odbc_fetch_array($result);
			break;
	}
	//if (!is_array($resource)){ print("Problemas ao ler o banco!"); }
	return($resource);
}

function DbNumRows($result){
	switch($GLOBALS['glob_dbs']){
		case "PostgreSQL": 
			$num = pg_num_rows($result);
			break;
		case "MySQL": 
			$num = mysql_num_rows($result);
			break;
		case "MSSQL": 
			$num = mssql_num_rows($result);
			break;
		case "SQLServer": 
			$num = sqlsrv_num_rows($result);
			break;
		case "SQLOBDC": 
			$num = odbc_num_rows($result);
			break;
	}
	if (empty($result)){ print("Problemas ao contar linhas da consulta!"); }
	return($num);
}

function DbRowsAffected($result){
	switch($GLOBALS['glob_dbs']){
		case "PostgreSQL": 
			$num = pg_affected_rows($result);
			break;
		case "MySQL": 
			$num = mysql_affected_rows($result);
			break;
		case "MSSQL": 
			$num = mssql_rows_affected($result);
			break;
		case "SQLServer": 
			$num = sqlsrv_rows_affected($result);
			break;
		case "SQLOBDC": 
			$num = odbc_num_rows($result);
			break;
	}
	if (empty($result)){ print("Problemas ao contar linhas afetadas!"); }
	return($num);
}

function DbError($conn){
	switch($GLOBALS['glob_dbs']){
		case "PostgreSQL": 
			$erro = pg_last_error();
			break;
		case "MySQL": 
			$erro = mysql_error();
			break;
		case "MSSQL": 
			$erro = mssql_get_last_message();
			break;
		case "SQLServer": 
			$erro = sqlsrv_errors();
			break;
		case "SQLOBDC": 
			$erro = odbc_errormsg($conn);
			break;
	}
	//return(addslashes(print_r($erro)));
	return RetornaError(utf8_encode($erro),"");
}

?>
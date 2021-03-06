<?php
session_start();
include_once '../business/BusinessDB.php';
include_once '../core/Configuration.php';
include_once '../core/User.php';
include_once '../state/StateDB.php';

//Control de errores
$error=false;
//Toma los datos de usuario y rol de la aplicación
$user=new User(intval($_SESSION['html5sync_userId']),$_SESSION['html5sync_role']);
if($user->getId()<=0){
    $error="Cannot read the user id from session";
}else{
    //Carga la configuración del archivo server/config.php
    $config=new Configuration();
    //Establece el timezone definido en el archivo de configuración
    date_default_timezone_set($config->getParameter("main","timezone"));
    //Crea el objeto para el manejo de la base de datos del negocio
    $businessDB=new BusinessDB($user,$config);
    //Crea el objeto para manejo de la base de datos estática y crea el usuario si no existe
    $stateDB=new StateDB($user);
    //Carga los datos pasados por el cliente
    $transactions=$_POST["transactions"];
    //Variable para retornar los resultados del almacenamiento de cada transacción
    $txsResponse=array();
    //Revisa y almacena cada transacción
    foreach ($transactions as $transaction) {
        $txError=false;
        $txMessage="";
        //Filtra los datos de cada transacción
        $tableName=filter_var($transaction["table"],FILTER_SANITIZE_STRING);
        //Verifica si la tabla puede ser cargada por el usuario
        if(!$businessDB->isTableAllowed($transaction["table"])){
            $txError="Table ".$tableName." is not allowed for the user";
        }else{
            //Retorna la estructura de la tabla para filtrar los datos de la fila
            $table=$businessDB->getTableData($tableName);
            $operation=filter_var($transaction["transaction"],FILTER_SANITIZE_STRING);
            //Si la tabla está bloqueada, se retorna success, para borrar la transacción de BrowserDB
            if($table->getMode()==="lock"){
                $txError=false;
                $txMessage="Table ".$table->getName()." is locked for the user. Could not ".$operation." transactions";
            }else{
                $row=$transaction["row"];
                //Si la operación es DELETE y $row=false, solo se necesita la clave para eliminar el registro
                if($operation==="DELETE"&&(!$row||$row==="false")){
                    $row=array();
                    $key=filter_var($transaction["key"],FILTER_SANITIZE_NUMBER_INT);
                    $row[$table->getPk()->getName()]=$key;
                }
                //Si no hay error, se almacena en la BusinessDB
                $txError=$businessDB->processRegister($table,$row,$operation);
                //Actualiza la fecha de actualización para evitar que se vuelva a cargar
                $stateDB->setTableLastUpdate($table);
            }
            if(!$txError){
                $txResponse=array(
                    "id"=>$transaction["id"],
                    "success"=>"true",
                    "locked"=>"true",
                    "message"=>$txMessage
                );
            }else{
                $txResponse=array(
                    "id"=>$transaction["key"],
                    "success"=>"false",
                    "error"=>$txError
                );
            }
            array_push($txsResponse, $txResponse);
        }
    }
    $json='{"transactions":'.json_encode($txsResponse).'}';
}
//Si se encuentran errores, se retornan al cliente
if($error){
    $json='{"error":"'.$error.'"}';
}
echo $json;
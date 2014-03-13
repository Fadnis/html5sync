<?php
include_once '../core/Connection.php';
include_once '../core/Database.php';
include_once '../core/Field.php';
include_once '../core/Table.php';
include_once '../dao/DaoTable.php';
include_once '../dao/StateDB.php';

//Se establece timezone
date_default_timezone_set("America/Bogota");


//Detecta su hubo cambios en la estructura de alguna tabla

//Detecta si hubo cambios en los datos



//Se leen las variables de configuración
$config=require_once '../config.php';
$parameters=$config["parameters"];
$tablesData=$config["tables"];

//Se crea una instancia de la base de datos con la conexión (read+write)
$db=new Database(
        $config["database"]["name"],
        $config["database"]["driver"],
        $config["database"]["host"], 
        new Connection(
            "all",
            $config["database"]["login"],
            $config["database"]["password"]
        )
    );

//Se crea el objeto para manejar tablas con PDO
$dao=new DaoTable($db);

//Se lee cada tabla
$state="";
foreach ($tablesData as $tableData) {
    $table=$dao->loadTable($db->getDriver(),$tableData["name"],$tableData["mode"]);
    //Se usa el tipo de actualización seleccionada
    if($parameters["updateMode"]==="updatedColumn"){
        //Si la columna de actualización no existe, se crea
        $dao->setUpdatedColumnMode($db->getDriver(),$table);
    }
    //Se guarda la estructura de cada tabla serializada para comparar el estado con el anterior
    $state.=$table->jsonEncode();
}

//Se verifica si hubo cambios en alguna de las tablas para el usuario desde la 
//última conexión usando una función de Hash
$stateDB=new StateDB();
$userId=103;
$role="vendedor";
$version=$stateDB->version($state,$userId,$role);









////Se construye la respuesta en JSON
//$json='{"userId":'.$userId.',"version":'.$version.',"tables":['.$jsonTables.']}';
//echo $json;

echo '{"state":"true"}';


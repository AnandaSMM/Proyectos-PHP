<?php

include_once "dbConexConfig.php";
function conex(){
    
    try{
        $conexion=new PDO(ruta,user,psw);
        $conexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        return $conexion;

    }catch(PDOException $ex){
        echo "Error: ".$ex->getMessage();
    }
}
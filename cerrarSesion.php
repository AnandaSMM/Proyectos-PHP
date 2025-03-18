<?php
require_once './core/dbconex.php';
session_start();
try{
    $stm = conex()->prepare("UPDATE usuario SET token = NULL WHERE id = :id");
    $stm->bindValue(':id',$_SESSION['usuario']);
    $stm->execute();
}catch(PDOException $error){
    echo "error en borrar cookies". $error->getMessage();
}
session_unset();
session_destroy();
setcookie('recordarme','',time() -3000,'/');
header("Location: login.php");
exit;

?>
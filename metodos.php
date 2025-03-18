<?php
require_once './core/dbconex.php';

function crearTarea($titulo, $contenido, $fechaFin, $idUsuario, $errores, $mensajesBuenos){
    $errores = [];
    $mensajesBuenos = [];
    if (empty($titulo || empty($contenido))) { // que no haya ningun campo vacio
        $errores[] = "Asegurese de rellenar todos los campos.";
    } else {
        
        try {
            $titulo = htmlspecialchars(stripcslashes(trim($titulo)));
            $contenido = htmlspecialchars(stripcslashes(trim($contenido)));
            
            date_default_timezone_set('Europe/Madrid');
            $fechaIni = date('Y-m-d');
            if (empty($fechaFin)) {
                $query = "INSERT INTO tarea (titulo, contenido, fechaIni, idUsuario) VALUES (:titulo, :contenido , :fechaIni, :idUsuario)";
                $stm = conex()->prepare($query);
                $stm->bindValue(':titulo', $titulo);
                $stm->bindValue(':contenido', $contenido);
                $stm->bindValue(':fechaIni', $fechaIni); // buscar como se pone la fecha del momento
                $stm->bindValue(':idUsuario', $idUsuario); ///pasar el id por la sesion
                $stm->execute();
                $mensajesBuenos[] = "Tarea agregada correctamente";
            } else {
            $query = "INSERT INTO tarea (titulo, contenido, fechaFin, fechaIni, idUsuario) VALUES (:titulo, :contenido , :fechaFin, :fechaIni, :idUsuario)";
            $stm = conex()->prepare($query);
            $stm->bindValue(':titulo', $titulo);
            $stm->bindValue(':contenido', $contenido);
            $stm->bindValue(':fechaFin', $fechaFin);
            $stm->bindValue(':fechaIni', $fechaIni); // buscar como se pone la fecha del momento
            $stm->bindValue(':idUsuario', $idUsuario); ///pasar el id por la sesion
            $stm->execute();
            $mensajesBuenos[] = "Tarea agregada correctamente";
        }
    } catch (PDOException $error) {
        echo "error en agregar : " . $error->getMessage();
    }
    }
}

function eliminarTarea($idTarea){
    try {
        $stm = conex()->prepare("DELETE FROM tarea WHERE idTarea = :idtarea");
        $stm->bindParam(':idtarea', $idTarea, PDO::PARAM_INT);
        $stm->execute();
        $mensajesBuenos[] = "Tarea eliminada correctamente";
    } catch (PDOException $error) {
        echo "Error en eliminar tarea : " . $error->getMessage();
    }
}

//crear carpetas
function crearCarpetas($nombre)
{
    try {
        $query = "INSERT INTO carpeta (nombre, idUsuario) VALUES (:nombre, :idUsuario)";
        $stm = conex()->prepare($query);
        $stm->bindValue(':nombre', $nombre);
        $stm->bindValue(':idUsuario', $_SESSION['usuario']);
        $stm->execute();
    } catch (PDOException $error) {
        echo "error en crer carpetas: " . $error->getMessage();
    }
}
//eliminar carpeta 
function eliminarCarpeta($id)
{
    try {
        $stm = conex()->prepare("UPDATE tarea SET idCarpeta = null WHERE idCarpeta = :idcarpeta");
        $stm->bindValue(':idcarpeta', $id);
        $stm->execute();
        $stm = conex()->prepare("DELETE FROM carpeta WHERE idCarpeta = :id ");
        $stm->bindValue(':id', $id);
        $stm->execute();
    } catch (PDOException $error) {
        echo "error en eliminar carpetas: " . $error->getMessage();
    }
}
//agregar tareas a carpetas
function agregarTareaAcarpeta($idTarea, $idCarpeta){
    try {
        $stm = conex()->prepare("UPDATE tarea SET idCarpeta = :idCarpeta WHERE idTarea = :idTarea");
        $stm->bindValue(':idTarea', $idTarea);
        $stm->bindValue(':idCarpeta', $idCarpeta);
        $stm->execute();
    } catch (PDOException $error) {
        echo "error al agregar: " . $error->getMessage();
    }
}
//sacar tareas de carpetas
function eliminarTareaDeCarpeta($idTarea){
    try {
        $stm = conex()->prepare("UPDATE tarea SET idCarpeta = null WHERE idTarea = :idTarea");
        $stm->bindValue(':idTarea', $idTarea);
        $stm->execute();
    } catch (PDOException $error) {
        echo "error al eliminar de la carpeta: " . $error->getMessage();
    }
}

//buscar carpeta en tareas
function buscarCarpetaEnTareas($idCarpeta){
    try {
        $stm = conex()->prepare("SELECT idCarpeta FROM tarea WHERE idCarpeta = :idCarpeta");
        $stm->bindValue(':idCarpeta', $idCarpeta);
        $stm->execute();
        $esta = $stm->fetch();
        return $esta;
    } catch (PDOException $error) {
        echo "error al agregar: " . $error->getMessage();
    } 
}
//mostrar las tareas que tienen las carpetas
function mostrarTareasEnCarpeta($idCarpeta)
{
    try {
        $stm = conex()->prepare("SELECT 
                carpeta.nombre AS nombre_carpeta,
                tarea.titulo AS titulo_tarea,
                tarea.contenido AS contenido_tarea,
                tarea.fechaIni AS fecha_inicio,
                tarea.fechaFin AS fecha_fin,
                usuario.nombre AS nombre_usuario
            FROM 
                carpeta
            LEFT JOIN 
                tarea ON carpeta.idCarpeta = tarea.idCarpeta
            LEFT JOIN 
                usuario ON carpeta.idUsuario = usuario.id
            WHERE tarea.idCarpeta = :idCarpeta;
        ");
        $stm->bindValue(':idCarpeta',$idCarpeta);
        $stm->execute();
        $arrayCarpetas = $stm->fetchAll(PDO::FETCH_ASSOC);
        return $arrayCarpetas;
    } catch (PDOException $error) {
        echo "error al mostrar todas las tareas " . $error->getMessage();
    }
    return [];
}
//eliminar usuarios, solo admin
function eliminarUsuarios($id)
{
    try {
        $stm = conex()->prepare("DELETE FROM tarea WHERE idUsuario = :idUsuario");
        $stm->bindValue(':idUsuario', $id);
        $stm->execute();
        $stm = conex()->prepare("DELETE FROM carpeta WHERE idUsuario = :idUsuario");
        $stm->bindValue(':idUsuario', $id);
        $stm->execute();
        $stm = conex()->prepare("DELETE FROM usuario WHERE id = :idUsuario");
        $stm->bindValue(':idUsuario', $id);
        $stm->execute();
    } catch (PDOException $error) {
        echo "Error en eliminar usuario : " . $error->getMessage();
    }
}

//mostar tarea
function mostrarTareas()
{
    try {
        $stm = conex()->prepare("SELECT idTarea, titulo, contenido, fechaIni, fechaFin FROM tarea WHERE idUsuario = :id");
        $stm->bindValue(':id', $_SESSION['usuario']);
        $stm->execute();
        $tareas = $stm->fetchAll(PDO::FETCH_ASSOC);
        return $tareas;
    } catch (PDOException $error) {
        echo "Error en mostrar" . $error->getMessage();
    }
}
//mostrar contenido de las carpetas
function mostrarCarpetas()
{
    try {
        $stm = conex()->prepare("SELECT * FROM carpeta WHERE idUsuario = :id");
        $stm->bindValue(':id', $_SESSION['usuario']);
        $stm->execute();
        $carpetas = $stm->fetchAll(PDO::FETCH_ASSOC);
        return $carpetas;
    } catch (PDOException $error) {
        echo "Error en mostrar carpetas : " . $error->getMessage();
    }
}
//mostrar usuarios, solo para admin
function mostrarUsuarios()
{
    try {
        $stm = conex()->prepare("SELECT * FROM usuario");
        $stm->execute();
        $usuarios = $stm->fetchAll(PDO::FETCH_ASSOC);
        return $usuarios;
    } catch (PDOException $error) {
        echo "Error en mostrar usuario : " . $error->getMessage();
    }
}

function esAdmin($id){
    try {
        $stm = conex()->prepare("SELECT admin FROM usuario WHERE id = :id");
        $stm->bindValue(':id', $id);
        $stm->execute();
        $usuarios = $stm->fetch(PDO::FETCH_ASSOC);
        return $usuarios && $usuarios['admin']==1;
    } catch (PDOException $error) {
        echo "Error en es admin  : " . $error->getMessage();
        return false;
    }
}

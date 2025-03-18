<?php
require_once './core/dbconex.php';
//recogemos los datos comprobamos la contraseña, la hasheamos e insertamos al usuario en la base de datos
$errores=[];
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    session_start();
    //comprobamos que ningún campo este vacio.
 if(empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['contra'])){
    $errores[] = "Los campos no pueden estar vacios";
 }else{
    try {
        //comprobamos el correo, 1 el formato, 2 la base de datos.
        $nombre = htmlspecialchars(stripcslashes(trim($_POST['nombre'])));
        $contra = htmlspecialchars(stripcslashes(trim($_POST['contra'])));
        $correo = filter_var($_POST['correo'],FILTER_SANITIZE_EMAIL);
        if(!filter_var($correo, FILTER_VALIDATE_EMAIL)){
            $errores[] = "El correo electrónico no tiene el formato adecuado.";
        }else{
            //comprobamos que no este en la base de datos
            
            $query = "SELECT correo FROM usuario WHERE correo = :correo";
            $resultado = conex()->prepare($query);
            $resultado->bindValue(':correo',$correo);
            $resultado->execute();
            $usuarioEncontrado = $resultado->fetch(PDO::FETCH_ASSOC);
            //si hay datos ya hay una cuenta con ese correo 

            if($usuarioEncontrado){
                $errores[] = "El usuario ya existe.";
            }else{  //comprobamos la contraseña ya que el usuario no esta en la base de datos

                if (strlen($contra) < 8 ) {
                    $errores[] = "La contraseña no pueden tener menos de 8 caracteres.";
                }

                if(empty($errores)){
                  $contraHasheada = password_hash($contra,PASSWORD_DEFAULT);  
                  $query = "INSERT INTO usuario (correo, contra, nombre, admin) VALUES (:correo, :contra, :nombre, false)";
                  $insercion = conex()->prepare($query);
                  $insercion->bindValue(':correo',$correo);
                  $insercion->bindValue(':contra',$contraHasheada);
                  $insercion->bindValue(':nombre',$nombre);
                  $insercion->execute();

                  $_SESSION['correo'] = $correo;
                  $_SESSION['nombre'] = $nombre;
                  header("Location: index.php");
                  exit;
                }

            } 
        }
    } catch (PDOException $error) {
        echo "Error en la base de datos: ".$error->getMessage();
        exit;
    }  
 }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de registro</title>
    <link rel="stylesheet" href="./estilos/estiloRegistro.css">

</head>
<body>
    <div>
        <h3>Formulario de registro</h3>
        <form method="post" class="container">
            <p class="inputTexto">
            <input type="text" name="nombre" placeholder="Nombre">
            <input type="text" name="correo" placeholder="Correo electrónico">
            <input type="password" name="contra" placeholder="Contraseña">
            </p>
            <p class="botones">
            <input type="submit" name="registrar" value="Registrarse" class="registarse">
            <a href="login.php" class="btnSesion">Inicio de sesión</a>
            </p>
        </form>
        <?php
        if(!empty($errores)){
            foreach ($errores as $error) {
            ?><p class="error"><?php echo $error;?></p>
            <?php
            }
        }
        ?>
    </div>
</body>
</html>
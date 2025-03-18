<?php
require_once './core/dbconex.php';

$erorres = []; //Creamos el array donde estaran los errores que mostraremos al usuario
if(isset($_COOKIE['recordarme'])){ // comprobamos si hay una cookie con los datos de la sesión
    session_start(); //si es asi que empiece la sesión
    $token = $_COOKIE['recordarme']; //guaradamos la cookie en una variable token para comprobar si esta en la base de datos.
    try {
        //metemos un trycatch para que no falle
        $stm = conex()->prepare("SELECT * FROM usuario WHERE token = :token");
        $stm->bindValue(':token',$token);
        $stm->execute();
        // hacemos la query y un rowcount para ver si ha algun resultado, si es asi lo metemos en un array con fetch_assoc
        if($stm->rowCount() == 1){
            $datosUsuario = $stm->fetch(PDO::FETCH_ASSOC);
            $_SESSION['usuario'] = $datosUsuario['id'];
            $_SESSION['correo'] = $datosUsuario['correo'];
            $_SESSION['nombre'] = $datosUsuario['nombre'];
            header("Location: index.php");
            exit; // una vez tenemos todos los datos de la sesion redirigimos a la pagina principal.
        }
    } catch (PDOException $ex) {
        echo "Error en las cookies: " . $ex->getMessage();
        exit;
    }
}
//en caso de que no haya cookie recogemos los datos del login 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        //que los valores no esten vacios
        if (empty($_POST['usuario']) || empty($_POST['contra']) || !filter_var($_POST['usuario'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "Ingresa todos los datos correctamente";
        } else {
            $passw = htmlspecialchars(stripcslashes(trim($_POST['contra'])));
            $correo = filter_var($_POST['usuario'], FILTER_SANITIZE_EMAIL);
            
            //comprobamos en la base de datos si los datos estan 
            $query = "SELECT id, correo, contra, nombre FROM usuario WHERE correo = :correo";
            $result = conex()->prepare($query);
            $result->bindValue(':correo', $correo);
            $result->execute();
            $datosUsuario = $result->fetch(PDO::FETCH_ASSOC); // me devuelve un array asociativo 

            //comprobamos si datosUsuario tiene datos, si es asi 
            if ($datosUsuario) {
                
                if (password_verify($passw, $datosUsuario['contra'])) { //comprobamos si la contrasea deshasheada es la misma que la de la base de datos
                    
                    if(isset($_POST['recordarme'])){ //si quiere que se recuerde la sesion actualizamos la informacion y agregamos un token
                        $token = bin2hex(random_bytes(32));
                        $stm = conex()->prepare("UPDATE usuario SET token = :token WHERE id = :id");
                        $stm->bindValue(':token',$token);
                        $stm->bindValue(':id',$datosUsuario['id']);
                        $stm->execute();
                        setcookie('recordarme',$token,time() + (30 * 24 * 60 * 60), '/');
                    }

                    session_start(); //aqui comienza la sesion con los datos ingresados
                    $_SESSION['correo'] = $datosUsuario['correo'];
                    $_SESSION['nombre'] = $datosUsuario['nombre'];
                    $_SESSION['usuario'] = $datosUsuario['id'];
                    header('Location: index.php');
                    exit;
                } else {
                    $errores[] = "La contraseña es incorrecta";
                }
            } else {
                $errores[] = "El correo es incorrecto";
            }
        }
    } catch (PDOException $error) {
        echo $error->getMessage();
        exit;
    }
}   // si viene de registrar


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="./estilos/estiloLogin.css">
</head>

<body>
    <div class="principal">
    <h3>Inicio de sesión</h3>
        <form method="post">

            <p class="texto">
                <input type="text" id="usuario" name="usuario" placeholder="Correo">
                <input type="password" id="contra" name="contra" placeholder="Contraseña">
            </p>
            <div class="recordS">
                <label>Recordar sesión :</label>
                <input type="checkbox" name="recordarme" id="recordarme">
            </div>
            <p class="botones">
                <input type="submit" class="btnInicio" name="logear" value="Entrar">
                <a href="registrar.php" class="btnRegistro">Registrarse</a>
            </p>
            
        
        </form> 
        <?php
            if (!empty($errores)) {
                foreach ($errores as $error) {
            ?>
                <p class="error"><?php echo $error; ?></p>
            <?php
                }
            }
            ?>  
    </div>
</body>

</html>
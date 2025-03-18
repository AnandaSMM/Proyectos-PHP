<?php
require_once './core/dbconex.php';
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}
require_once 'metodos.php';
//crear tareas
$errores = [];
$mensajesBuenos = [];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="./estilos/estiloInicio.css">

</head>

<body>
    <h1> Bienvenida <?= $_SESSION['nombre'] ?> </h1>
    <div class="cuaderno">
        <!-- mostrar errores -->
        <?php if (!empty($errores)) {
            foreach ($errores as $error) { ?>
                <h3><?= $error ?></h3>
        <?php  }
        } ?>

        <!-- mostrar mensajes buenos -->
        <?php if (!empty($mensajesBuenos)) {
            foreach ($mensajesBuenos as $mensaje) { ?>
                <h3><?= $mensaje ?></h3>
        <?php  }
        }  ?>

        <form method="get" class="formGeneral">
            <!-- agregar tareas -->
            <input type="submit" name="crearTareas" value="Tareas">
            <!--Nueva carpeta, mostrar carpetas-->
            <input type="submit" name="carpeta" value="Carpetas">
            <!--admin elimina usuarios -->
            <?php
            if (esAdmin($_SESSION['usuario'])) {  ?>
                <input type="submit" name="mostrarUsuario" value="Mostrar usuarios">
            <?php  }  ?>
        </form>

        <!--crear carpeta-->
        <?php if (isset($_POST['nuevaCarpeta']) && !empty($_POST['nombreCarpeta'])) {
            crearCarpetas($_POST['nombreCarpeta']);
            $mensajesBuenos[] = "Crapeta creada correctamente";

        } ?>
        <!--eliminar carpeta-->
        <?php if (isset($_POST['eliminarcarpeta'])) {
            eliminarCarpeta($_POST['idCarpeta']);
            $mensajesBuenos[] = "carpeta eliminada correctamente.";
        } ?>

        <!--------------------------------------- Tareas ---------------------------------------------------->
        <?php if (isset($_POST['botonAgregarTarea']))
            crearTarea($_POST['titulotareas'], $_POST['contenido'], $_POST['fechaFin'], $_SESSION['usuario'], $errores, $mensajesBuenos) ?>
        <?php if (isset($_POST['eliminarTarea']))
            eliminarTarea($_POST['idTarea']) ?>
        <!--------------------------------------- Usuarios ---------------------------------------------------->

        <?php if (isset($_POST['eliminarUsuario'])) {
            eliminarUsuarios($_POST['idUsuario']);
            $mensajesBuenos[] = "usuario eliminado correctamente";
        }   ?>

        <?php if (isset($_GET['mostrarUsuario'])) { ?>
            <table class="tablaUsuarios">
                <tr>
                    <th>Nombre</th>
                    <th>Correo electrónico</th>
                    <th></th>
                </tr>
                <?php foreach (mostrarUsuarios() as $usuario) { ?>
                    <tr>
                        <td><?php echo $usuario['nombre']; ?></td>
                        <td><?php echo $usuario['correo']; ?></td>
                        <td>
                            <?php if ($usuario['id'] != 1) { ?>
                                <form method="post">
                                    <input type="hidden" name="idUsuario" value="<?= $usuario['id'] ?>">
                                    <input type="submit" name="eliminarUsuario" value="Eliminar" id="botones">
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php }?>
            </table>
            <?php }?>
    <!-- Mostrar tareas, crear nuevas, eliminar -->
    <?php if (!isset($_GET['carpeta']) && !isset($_GET['mostrarUsuario'])) { ?>
        <div class="mostrarTareas">
            <form method="post" id="formularioCrearTareas">
                <label class="agregarTareaFormulario">
                    <input type="text" name="titulotareas" id="titulo" placeholder="Titulo">
                    <textarea name="contenido" placeholder="Contenido" rows="5" cols="40"></textarea>
                    <input type="date" name="fechaFin" id="fechaFin" min="">
                    <input type="submit" name="botonAgregarTarea" value="Agregar" id="botones">
                </label>
            </form>
            <div class="scroll-table-tareas">
                <table class="tablaTareas">
                    <tr>
                        <th>Título</th>
                        <th>Contenido</th>
                        <th>Creación</th>
                        <th>Fin</th>
                    </tr>
                    <?php foreach (mostrarTareas() as $tarea) { ?>
                        <tr>
                            <td><?php echo $tarea['titulo']; ?></td>
                            <td><?php echo $tarea['contenido']; ?></td>
                            <td><?php echo $tarea['fechaIni']; ?></td>
                            <td><?php echo $tarea['fechaFin']; ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="idTarea" value="<?= $tarea['idTarea'] ?>">
                                    <input type="submit" name="eliminarTarea" value="Eliminar" id="botones">
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    <?php } ?>

    <!--------------------------------------- Carpetas ---------------------------------------------------->
    <?php if (isset($_POST['tareaCarpeta']) && isset($_POST['tareaSeleccionada'])) {
        agregarTareaAcarpeta($_POST['tareaSeleccionada'], $_POST['idCarpeta']); ?>
    <?php } ?>

    <?php if (isset($_GET['carpeta'])) { ?>
        <div class="carpetasDiv">
            <div class="mostrarCarpeta">
                <!-- crear carpetas -->
                <form method="post" class="creaCarpeta">
                    <input type="text" name="nombreCarpeta" placeholder="Nombre de la carpeta" id="nombreCarpeta">
                    <input type="submit" name="nuevaCarpeta" value="Crear carpeta" id="nuevaCarpeta">
                </form>
                <!-- mostrar carpetas -->
                <div class="scroll-table-carpeta">
                    <?php if (mostrarCarpetas()) { ?>
                        <table class="tablaCarpeta">
                            <?php foreach (mostrarCarpetas() as $carpeta) { ?>
                                <tr>
                                    <td><?= $carpeta['nombre'] ?></td>
                                    <td>
                                        <form method="post">
                                            <input type="hidden" name="idCarpeta" value="<?= $carpeta['idCarpeta'] ?>">
                                            <input type="submit" name="eliminarcarpeta" value="Eliminar" id="botones">
                                            <input type="submit" name="agregarTcarpeta" value="+ tareas" id="botones">
                                            <?php if (!empty(buscarCarpetaEnTareas($carpeta['idCarpeta']))) { ?>
                                                <input type="submit" name="verTareasEnCarpeta" value="Ver tareas" id="botones">
                                                <input type="hidden" name="idCarpeta" value="<?= $carpeta['idCarpeta'] ?>">
                                            <?php } ?>
                                            <input type="hidden" name="nombreCarpe" value="<?= $carpeta['nombre'] ?>">
                                        </form>
                                    </td>
                                </tr>
                        <?php }
                        } else  $errores[] = "Aún no hay carpetas creadas" ?>
                        </table>
                </div>
            </div>
        <?php } ?>

        <!-- agregar tareas a carpetas -->
        <?php if (isset($_POST['agregarTcarpeta'])) {
            $idCarpeta = $_POST['idCarpeta']; ?>
            <form method="post" name="selectTarea">
                <select name="tareaSeleccionada">
                    <option disabled selected>Tareas</option>
                    <?php foreach (mostrarTareas() as $tarea) { ?>
                        <option value="<?= $tarea['idTarea'] ?>"><?= $tarea['titulo'] ?></option>
                    <?php } ?>
                </select>
                <input type="hidden" name="carpeta">
                <input type="hidden" name="idCarpeta" value="<?= $_POST['idCarpeta'] ?>">
                <input type="submit" name="tareaCarpeta" value="Agregar" id="botones">
            </form>
        <?php } ?>

        <!--ver tareas en carpetas-->
        <?php if (isset($_POST['verTareasEnCarpeta'])) { ?>
            <table class="tareasEnCarpeta">
                <tr>
                    <th> - <?= $_POST['nombreCarpe'] ?></th>
                </tr>
                <?php foreach (mostrarTareasEnCarpeta($_POST['idCarpeta']) as $dato) { ?>
                    <tr>
                        <td><?= $dato['titulo_tarea'] ?></td>
                        <td><?= $dato['contenido_tarea'] ?></td>
                    </tr>
                    <tr>
                        <td><?= $dato['fecha_inicio'] ?></td>
                        <td><?= $dato['fecha_fin'] ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php   } ?>
        </div>

    </div>
    <a href="cerrarSesion.php">Cerrar sesión</a>
</body>

</html>
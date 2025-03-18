# TO do list

## Descripcíon
Este proyecto es una to do list, que sirve para asirgnar tareas y tener tus propias tareas.

## Funcionalidades
- Iniciar sesión [OK]
- Registrarse [OK]
- CRUD de tareas [OK]
- CRUD de carpetas [OK]
- Asignar tareas a personas []
- Asignar tareas personales a carpetas personales [OK]
- Ver tareas en carpetas personales [OK]
- Ver tareas en carpeta general []
- Uso de cookies con token [OK]
- Eliminar usuarios, desde el super usuario []
- Agregar administradores []


## Tecnologías 
- PHP
- HTML
- CSS
- Mysql

## Entidades 
- Usuario (id, correo, contra, nombre, token, admin)
- Carpeta (idCarpeta, nombre, idUsuario)
- Tarea (idTarea, titulo, contenido, fechaIni, fehcFin, idCarpeta, idUsuario)


## Roles 
- Super administrador : puede eliminar, crear mas administradores. Puede ver la lista de usuarios, resetear contraseñas.
- Administrador : puede asignar tareas a usuarios, desasignar.
- usuarios : pueden ver tareas asignadas, tareas personales o carpetas personales.

## Pantallas 
- Inicio ( Mostar tareas, carpetas personales, carpeta general)
- Login (Iniciar sesión)
- Registro (registrar nuevo usuario)
- Mostrar usuarios (solo para super admin)
- Asignar tareas (solo para administradores) 


## Base de datos
```sql

CREATE DATABASE IF NOT EXISTS todoList;
USE todoList;
DROP TABLE IF EXISTS carpeta;
DROP TABLE IF EXISTS tarea;
DROP TABLE IF EXISTS usuario;

CREATE TABLE usuario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    correo VARCHAR(255) not null UNIQUE,
    contra VARCHAR(255) not null,
    nombre VARCHAR(255) not null,
    token VARCHAR(255) null,
    admin BOOLEAN
);
CREATE TABLE carpeta (
    idCarpeta INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NULL,
    idUsuario INT not null,
    FOREIGN KEY (idUsuario) REFERENCES usuario(id)
        ON DELETE CASCADE
		ON UPDATE CASCADE

);

CREATE TABLE tarea (
    idTarea INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(255) null,
    contenido VARCHAR(511) null,
    fechaIni DATE,
    fechaFin DATE null,
    idCarpeta INT null,
	idUsuario INT not null,
    FOREIGN KEY (idCarpeta) REFERENCES carpeta(idCarpeta)
        ON DELETE CASCADE
		ON UPDATE CASCADE,
    FOREIGN KEY (idUsuario) REFERENCES usuario(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	
);


INSERT INTO usuario (correo, contra, nombre, admin) VALUES ('admin@admin.com','$2y$10$yY0MiVzAbdlwzsjiSSw1yul9/fd2fDA09FbnVwKPkaeGGIthCV8Ce','Ananda', TRUE);

```

super usuario: admin@admin.com
contrseña: admin
## Diagrama de clases
![Diagrama de clases]()


## Integrantes
- Savitara

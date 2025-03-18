CREATE DATABASE IF NOT EXISTS todoList;
USE todoList;
DROP TABLE IF EXISTS tarea;
DROP TABLE IF EXISTS carpeta;
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
		ON UPDATE CASCADE,
    FOREIGN KEY (idUsuario) REFERENCES usuario(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	
);


INSERT INTO usuario (correo, contra, nombre, admin) VALUES ('admin@admin.com','$2y$10$yY0MiVzAbdlwzsjiSSw1yul9/fd2fDA09FbnVwKPkaeGGIthCV8Ce','Ananda', TRUE);
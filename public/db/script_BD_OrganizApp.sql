
DROP DATABASE IF EXISTS organizapp;
CREATE DATABASE organizapp;
use organizapp;



CREATE TABLE usuario (
  id_usuario INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre_completo VARCHAR(255) NOT NULL,
  usuario VARCHAR(255) NOT NULL,
  pwd TEXT NOT NULL,
  tipo TINYINT(1) UNSIGNED NOT NULL,
  img VARCHAR(255) NULL DEFAULT NULL,
  fecha_registro DATETIME NOT NULL DEFAULT now(),
  fecha_ultima_vez DATETIME NOT NULL DEFAULT now() ON UPDATE now(),
  PRIMARY KEY(id_usuario)
)
AUTO_INCREMENT=1000,
ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

CREATE TABLE mensaje (
  id_mensaje INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  mensaje TEXT NOT NULL,
  PRIMARY KEY(id_mensaje)
)
AUTO_INCREMENT=1000,
ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;


CREATE TABLE carpeta (
  id_carpeta INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  path TEXT NOT NULL,
  path_name TEXT NOT NULL,
  nombre VARCHAR(255) NOT NULL,
  descripcion TEXT NULL,
  raiz BOOL NOT NULL DEFAULT 0,
  archivado BOOL NOT NULL DEFAULT 0,
  fecha_archivado DATETIME NULL,
  id_usuario INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(id_carpeta),
  FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
      ON DELETE CASCADE
      ON UPDATE CASCADE
)
AUTO_INCREMENT=1000,
ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

CREATE TABLE archivo (
  id_archivo INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  size INTEGER UNSIGNED NOT NULL,
  extension VARCHAR(8) NOT NULL,
  descripcion TEXT(400) NULL,
  archivado BOOL NOT NULL DEFAULT 0,
  fecha_archivado DATETIME NULL,
  id_carpeta INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(id_archivo),
  FOREIGN KEY(id_carpeta)
    REFERENCES carpeta(id_carpeta)
      ON DELETE CASCADE
      ON UPDATE CASCADE
)
AUTO_INCREMENT=1000,
ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

CREATE TABLE tarea (
  id_tarea INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  fecha_entrega DATETIME NOT NULL,
  estado TINYINT UNSIGNED NOT NULL,
  descripcion TEXT NULL,
  prioridad TINYINT UNSIGNED NULL,
  id_carpeta INTEGER UNSIGNED NOT NULL,
  id_archivo INTEGER UNSIGNED NULL,
  PRIMARY KEY(id_tarea),
  FOREIGN KEY(id_carpeta)
    REFERENCES carpeta(id_carpeta)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  FOREIGN KEY(id_archivo)
    REFERENCES archivo(id_archivo)
      ON DELETE SET NULL
      ON UPDATE CASCADE
)
AUTO_INCREMENT=1000,
ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

CREATE TABLE notificacion (
  id_notificacion INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  visto BOOL NOT NULL,
  eliminado BOOL NOT NULL,
  tipo TINYINT UNSIGNED NOT NULL,
  fecha_registro DATE NOT NULL,
  id_tarea INTEGER UNSIGNED NOT NULL,
  id_mensaje INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(id_notificacion),
  FOREIGN KEY(id_tarea)
    REFERENCES tarea(id_tarea)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  FOREIGN KEY(id_mensaje)
    REFERENCES mensaje(id_mensaje)
      ON DELETE CASCADE
      ON UPDATE CASCADE
)
AUTO_INCREMENT=1000,
ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;





-- -------------------------------------------------insert-----------------------------------------------

INSERT INTO `organizapp`.`usuario` (`nombre_completo`,`usuario`, `pwd`, `tipo`,`img`) VALUES
 ('admin', 'admin', '$2y$10$QSMbG1Z8Tm3HHZRoXKi07eOIfBiKoZ5C9LKCf0oabYQKKp1C.dC0W', 0, NULL );

INSERT INTO `organizapp`.`carpeta` (`path`, `path_name`, `nombre`, `descripcion`, `raiz`, `id_usuario`) VALUES 
 ('drive/', 'drive/1000', '1000', 'admin', '1', '1000');

INSERT INTO `organizapp`.`archivo` (`id_archivo`,`nombre`, `size`, `extension`,`descripcion`, `id_carpeta`) VALUES 
 (1000, 'null', '0', 'null', 'null', '1000');

INSERT INTO `organizapp`.`mensaje` (`mensaje`) VALUES ('Usted tiene una tarea que ya expiro');
INSERT INTO `organizapp`.`mensaje` (`mensaje`) VALUES ('Usted tiene una tarea para hoy');
INSERT INTO `organizapp`.`mensaje` (`mensaje`) VALUES ('Usted tiene una tarea para mañana');



-- ============================================view=================================================
DROP VIEW IF EXISTS view_get_task;
CREATE VIEW view_get_task AS
	SELECT  carpeta.path as carpeta_path, carpeta.path_name as carpeta_path_name, carpeta.nombre as carpeta_nombre, carpeta.archivado as carpeta_archivado,
			  archivo.nombre as archivo_nombre, archivo.extension as archivo_extension, archivo.archivado as archivo_archivado, archivo.size as archivo_size,
			  tarea.nombre as tarea_nombre, tarea.fecha_entrega as tarea_fecha_entrega, tarea.estado as tarea_estado,
			  tarea.descripcion as tarea_descripcion, tarea.prioridad as tarea_prioridad,
			  carpeta.id_carpeta, archivo.id_archivo, tarea.id_tarea 
		FROM carpeta
		INNER JOIN tarea
		INNER JOIN archivo
		ON 
		carpeta.id_carpeta = tarea.id_carpeta AND 
		tarea.id_archivo  = archivo.id_archivo ;
		
-- SELECT * FROM view_get_task where tarea_estado=1 AND carpeta_path = '';


-- ============================================view=================================================
DROP VIEW IF EXISTS view_get_notification;
CREATE VIEW view_get_notification AS
	SELECT  carpeta.path AS carpeta_path, carpeta.path_name AS carpeta_path_name, carpeta.nombre AS carpeta_nombre, carpeta.archivado AS carpeta_archivado,
			  tarea.nombre AS tarea_nombre, tarea.fecha_entrega AS tarea_fecha_entrega, tarea.estado AS tarea_estado,
			  tarea.descripcion AS tarea_descripcion, tarea.prioridad AS tarea_prioridad,
			  notificacion.visto AS notificacion_visto, notificacion.eliminado AS notificacion_eliminado, notificacion.tipo AS notificacion_tipo,
			  notificacion.fecha_registro AS notificacion_fecha_registro,
			  mensaje.mensaje AS mensaje_mensaje,
			  carpeta.id_carpeta,  tarea.id_tarea , notificacion.id_notificacion, mensaje.id_mensaje
		FROM carpeta
		INNER JOIN tarea
		INNER JOIN notificacion
		INNER JOIN mensaje
		ON 
		carpeta.id_carpeta = tarea.id_carpeta AND 
		tarea.id_tarea  = notificacion.id_tarea AND
		notificacion.id_mensaje = mensaje.id_mensaje;
		
-- SELECT * FROM view_get_notification where tarea_estado=1 AND carpeta_path = '';
-- ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;FUNCTION;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
DROP FUNCTION IF EXISTS getPath;
Delimiter |
	CREATE FUNCTION getPath( pathname TEXT )
		RETURNS TEXT
		BEGIN 
			DECLARE totalSlash INT;
			DECLARE path TEXT;
			SET totalSlash = ( CHAR_LENGTH(pathname) - CHAR_LENGTH(REPLACE(pathname, '/', '')));
			SET path = SUBSTRING_INDEX(Pathname, '/', totalSlash );
			
			RETURN CONCAT(path,'/');
		END |
Delimiter ;
       
-- SELECT getPath('1/2/3/4/5/6/7') as path;

-- ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;FUNCTION;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
DROP FUNCTION IF EXISTS getName;
Delimiter |
	CREATE FUNCTION getName( pathname text )
		RETURNS text
		BEGIN 
			RETURN SUBSTRING_INDEX(Pathname, '/', -1 );
		END |
Delimiter ;
       
-- SELECT getName('1/2/3/4/5/6/7') as name;


-- :::::::::::::::::::::::::::::::::::::::::::::::procedure::::::::::::::::::::::::::::::::::::::::::::::::::::
drop procedure if exists updateToTrash;
Delimiter |
create procedure updateToTrash(in pathname text, in newPathname text )
  BEGIN
          	UPDATE carpeta SET carpeta.path_name = REPLACE( carpeta.path_name, pathname , newPathname ),
                carpeta.path = REPLACE( carpeta.path, pathname , newPathname ),
 						    carpeta.archivado = '1' , carpeta.fecha_archivado = now()
				      WHERE carpeta.path_name like CONCAT( pathname,'%' );
  END |
Delimiter ;
-- call updateToTrash('drive/1001/1', 'drive/1001/1.trash');

-- :::::::::::::::::::::::::::::::::::::::::::::::procedure::::::::::::::::::::::::::::::::::::::::::::::::::::
drop procedure if exists restoreTrash;
Delimiter |
create procedure restoreTrash(in pathname text, in newPathname text )
  BEGIN
				UPDATE carpeta SET carpeta.path_name = REPLACE( carpeta.path_name, pathname , newPathname ),
            carpeta.path = REPLACE( carpeta.path, pathname , newPathname ),
				    carpeta.archivado = '0' , carpeta.fecha_archivado = null
				  WHERE carpeta.path_name like CONCAT( pathname,'%' );
  END |
Delimiter ;
-- call restoreTrash('drive/1001/1.trash', 'drive/1001/1');


-- :::::::::::::::::::::::::::::::::::::::::::::::procedure::::::::::::::::::::::::::::::::::::::::::::::::::::
drop procedure if exists updatePathname;
Delimiter |
create procedure updatePathname( in oldPathname text, in newPathname text )
  BEGIN
     
       SET @path = (SELECT getPath(newPathname) as path);
       SET @nombre = (SELECT getName(newPathname) as name);
       
	    UPDATE carpeta SET carpeta.path = @path, carpeta.nombre = @nombre
	      WHERE carpeta.path_name = oldPathname;
	      
	    UPDATE carpeta SET carpeta.path_name = REPLACE( carpeta.path_name, oldPathname , newPathname ),
         carpeta.path = REPLACE( carpeta.path, oldPathname , newPathname )
	      WHERE carpeta.path_name like CONCAT( oldPathname,'%' );
            
  END |
Delimiter ;

-- call updatePathname('drive/1001/1', 'drive/1001/renamed');
-- call updatePathname('drive/1001/renamed', 'drive/1001/1');



-- _____________________________trigger_______________________________________________
-- cuando se elimina en cascada, el trigger no se dispara por lo que se ralizo un segundo trigger.
DROP TRIGGER IF EXISTS setDefaultArchiveInTask;
DELIMITER |
CREATE TRIGGER setDefaultArchiveInTask BEFORE DELETE ON archivo 
FOR EACH ROW 
		BEGIN
				DECLARE id_task INT;
				-- obtener tarea.id_tarea
				set id_task=(SELECT id_tarea FROM tarea WHERE tarea.id_archivo = OLD.id_archivo);
				IF id_task = 1000 THEN 
				   set id_task=0; -- mo hacer nada
				else
				  	update tarea set id_archivo = 1000 where id_archivo = OLD.id_archivo;
				END IF;
		END |
DELIMITER ;

-- -- _____________________________trigger_______________________________________________
--  segundo trigger. establece a todos a archivo default en tarea cuando se borra una carpeta.
DROP TRIGGER IF EXISTS setDefaultArchiveInTaskParent;
DELIMITER |
CREATE TRIGGER setDefaultArchiveInTaskParent BEFORE DELETE ON carpeta
FOR EACH ROW 
		BEGIN
			
					DECLARE my_id_tarea INT;
    			DECLARE my_id_archivo INT;
					/*Declaro el cursor para la busqueda */
				    DECLARE fileInFolder CURSOR FOR 	-- obtener todos las tarea.id_tarea
				    				SELECT tarea.id_tarea, tarea.id_archivo FROM carpeta
										INNER JOIN archivo
										INNER JOIN tarea
										ON 
										carpeta.id_carpeta = archivo.id_carpeta AND
										archivo.id_archivo = tarea.id_archivo
										
										WHERE carpeta.id_carpeta = OLD.id_carpeta;
					/*Declaro un manejador de error tipo NOT FOUND*/
    				DECLARE CONTINUE HANDLER FOR NOT FOUND SET @hecho = true;
				    /*Abro el cursor*/
				    OPEN fileInFolder;
                /*Empiezo el bucle de lectura*/
                loop1: LOOP
                    /* Asigno la primera linea a las variables... */
                    FETCH fileInFolder INTO my_id_tarea, my_id_archivo;
                    IF my_id_tarea = 1000 THEN 
                      set my_id_tarea=0; -- mo hacer nada
                    else
                        update tarea set id_archivo = 1000 where id_tarea = my_id_tarea;
                  END IF;
                  
                  IF @hecho THEN
                      LEAVE loop1;
                  END IF;
                    
                END LOOP loop1;
				    CLOSE fileInFolder;
		END |
DELIMITER ;

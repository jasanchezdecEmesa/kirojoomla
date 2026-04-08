CREATE TABLE IF NOT EXISTS `#__ereservas_visita` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`nombre` VARCHAR(255)  NOT NULL ,
`id_sala` TEXT NOT NULL ,
`aforo_web` DOUBLE,
`aforo_privado` DOUBLE,
`comida` VARCHAR(255)  NOT NULL ,
`cata` VARCHAR(255)  NOT NULL ,
`observaciones` TEXT NOT NULL ,
`precio` DECIMAL NOT NULL ,
`fecha` DATETIME NOT NULL ,
`hora_inicio` TIME NOT NULL ,
`hora_fin` TIME NOT NULL ,
`idioma` INT NOT NULL ,
`id_tipo_visita` INT NOT NULL ,
`aforo_ocupado` DOUBLE,
`guia` INT NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__ereservas_tipo_visita` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`nombre` VARCHAR(255)  NOT NULL ,
`imagen` VARCHAR(255)  NOT NULL ,
`aforo_web` DOUBLE,
`aforo_privado` DOUBLE,
`comida` VARCHAR(255)  NOT NULL ,
`cata` VARCHAR(255)  NOT NULL ,
`id_sala` TEXT NOT NULL ,
`observaciones` TEXT NOT NULL ,
`precio` DECIMAL NOT NULL ,
`descripcion` TEXT NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__ereservas_sala` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`nombre` VARCHAR(255)  NOT NULL ,
`aforo_web` DOUBLE,
`aforo_privado` DOUBLE,
`observaciones` TEXT NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__ereservas_idioma` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`nombre` VARCHAR(255)  NOT NULL ,
`observaciones` TEXT NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__ereservas_pais` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`nombre` VARCHAR(255)  NOT NULL ,
`observaciones` TEXT NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__ereservas_tipo_cliente` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`nombre` VARCHAR(255)  NOT NULL ,
`observaciones` TEXT NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__ereservas_reserva` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`nombre` VARCHAR(255)  NOT NULL ,
`telefono` VARCHAR(255)  NOT NULL ,
`email` VARCHAR(255)  NOT NULL ,
`id_visita` INT NOT NULL ,
`id_idioma` INT NOT NULL ,
`personas` DOUBLE,
`tarjeta` BLOB NOT NULL ,
`pagado` VARCHAR(255)  NOT NULL ,
`estado` VARCHAR(255)  NOT NULL ,
`id_usuario` INT NOT NULL ,
`id_pais` INT NOT NULL ,
`cpostal` VARCHAR(255)  NOT NULL ,
`observaciones` TEXT NOT NULL ,
`id_tipo_cliente` INT NOT NULL ,
`referencia` VARCHAR(255)  NOT NULL ,
`vinos` VARCHAR(255)  NOT NULL ,
`tipo_cata` VARCHAR(255)  NOT NULL ,
`aperitivo` VARCHAR(255)  NOT NULL ,
`alergenos` VARCHAR(255)  NOT NULL ,
`menu_especial` VARCHAR(255)  NOT NULL ,
`token` VARCHAR(255)  NOT NULL ,
`fecha_creacion` DATETIME NOT NULL ,
`fecha_modificacion` DATETIME NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__ereservas_guias` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`nombre` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__ereservas_calendario` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`fecha_inicio` DATETIME NOT NULL ,
`fecha_fin` DATETIME NOT NULL ,
`id_tipo_visita` INT NOT NULL ,
`hora_inicio` TIME NOT NULL ,
`hora_final` TIME NOT NULL ,
`frecuencia` VARCHAR(255)  NOT NULL ,
`dias_semana` VARCHAR(255)  NOT NULL ,
`dias_mes` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__ereservas_factura` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`nombre` VARCHAR(255)  NOT NULL ,
`nif` VARCHAR(255)  NOT NULL ,
`cpostal` VARCHAR(255)  NOT NULL ,
`direccion` VARCHAR(255)  NOT NULL ,
`municipio` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Schema recuperalo
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema recuperalo
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `recuperalo` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
-- -----------------------------------------------------
-- Schema pegalinas_live
-- -----------------------------------------------------
USE `recuperalo` ;

-- -----------------------------------------------------
-- Table `recuperalo`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recuperalo`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `email` VARCHAR(200) NULL COMMENT '',
  `password` VARCHAR(300) NULL COMMENT '',
  `perfil` VARCHAR(45) NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '')
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `recuperalo`.`users_details`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recuperalo`.`users_details` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `id_user` INT NOT NULL COMMENT '',
  `name` VARCHAR(50) NULL COMMENT '',
  `surname` VARCHAR(200) NULL COMMENT '',
  `campus` VARCHAR(200) NULL COMMENT '',
  `phone` VARCHAR(45) NULL COMMENT '',
  `addres` VARCHAR(500) NULL COMMENT '',
  `image` LONGTEXT NULL COMMENT '',
  `pin` VARCHAR(300) NULL COMMENT '',
  `key_inventory` VARCHAR(300) NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `fk_users_details_users1_idx` (`id_user` ASC)  COMMENT '',
  CONSTRAINT `fk_users_details_users1`
    FOREIGN KEY (`id_user`)
    REFERENCES `recuperalo`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `recuperalo`.`status`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recuperalo`.`status` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(45) NULL COMMENT '',
  `description` VARCHAR(200) NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '')
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `recuperalo`.`orders`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recuperalo`.`orders` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `id_client` INT NULL COMMENT '',
  `folio_order` VARCHAR(45) NULL COMMENT '',
  `number_labels` VARCHAR(45) NULL COMMENT '',
  `date` DATETIME NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '')
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `recuperalo`.`register_qr`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recuperalo`.`register_qr` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `id_orders` INT NOT NULL COMMENT '',
  `id_status` INT NOT NULL COMMENT '',
  `foliocodeqr` VARCHAR(45) NULL COMMENT '',
  `info_qr` VARCHAR(500) NULL COMMENT '',
  `number_qr` VARCHAR(45) NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `fk_register_qr_status1_idx` (`id_status` ASC)  COMMENT '',
  INDEX `fk_register_qr_pedidos1_idx` (`id_orders` ASC)  COMMENT '',
  CONSTRAINT `fk_register_qr_status1`
    FOREIGN KEY (`id_status`)
    REFERENCES `recuperalo`.`status` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_register_qr_pedidos1`
    FOREIGN KEY (`id_orders`)
    REFERENCES `recuperalo`.`orders` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `recuperalo`.`color`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recuperalo`.`color` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(45) NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '')
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `recuperalo`.`category`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recuperalo`.`category` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `id_parent` INT NULL COMMENT '',
  `namecategory` VARCHAR(200) NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `fk_category_category1_idx` (`id_parent` ASC)  COMMENT '',
  CONSTRAINT `fk_category_category1`
    FOREIGN KEY (`id_parent`)
    REFERENCES `recuperalo`.`category` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `recuperalo`.`articles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recuperalo`.`articles` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `id_register_qr` INT NOT NULL COMMENT '',
  `id_user` INT NOT NULL COMMENT '',
  `name_article` VARCHAR(100) NULL COMMENT '',
  `id_category` INT NOT NULL COMMENT '',
  `id_color` INT NOT NULL COMMENT '',
  `brand` VARCHAR(100) NULL COMMENT '',
  `model_serie` VARCHAR(200) NULL COMMENT '',
  `clothes_size` VARCHAR(45) NULL COMMENT '',
  `description` VARCHAR(500) NULL COMMENT '',
  `reward` VARCHAR(45) NULL COMMENT '',
  `imageone` LONGTEXT NULL COMMENT '',
  `imagetwo` LONGTEXT NULL COMMENT '',
  `registration_date` DATETIME NULL COMMENT '',
  `own_alien` INT NULL COMMENT '',
  `id_userfound` INT NULL COMMENT '',
  `asignated_to` VARCHAR(200) NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `fk_articles_register_qr1_idx` (`id_register_qr` ASC)  COMMENT '',
  INDEX `fk_articles_user1_idx` (`id_user` ASC)  COMMENT '',
  INDEX `fk_articles_color1_idx` (`id_color` ASC)  COMMENT '',
  INDEX `fk_articles_category1_idx` (`id_category` ASC)  COMMENT '',
  CONSTRAINT `fk_articles_register_qr1`
    FOREIGN KEY (`id_register_qr`)
    REFERENCES `recuperalo`.`register_qr` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_articles_user1`
    FOREIGN KEY (`id_user`)
    REFERENCES `recuperalo`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_articles_color1`
    FOREIGN KEY (`id_color`)
    REFERENCES `recuperalo`.`color` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_articles_category1`
    FOREIGN KEY (`id_category`)
    REFERENCES `recuperalo`.`category` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `recuperalo`.`location`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recuperalo`.`location` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `id_articles` INT NOT NULL COMMENT '',
  `longitude` VARCHAR(45) NULL COMMENT '',
  `latitude` VARCHAR(45) NULL COMMENT '',
  `addres` VARCHAR(45) NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `fk_location_articles1_idx` (`id_articles` ASC)  COMMENT '',
  CONSTRAINT `fk_location_articles1`
    FOREIGN KEY (`id_articles`)
    REFERENCES `recuperalo`.`articles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `recuperalo`.`history_status`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recuperalo`.`history_status` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `id_status` INT NOT NULL COMMENT '',
  `id_articles` INT NOT NULL COMMENT '',
  `date_change` DATETIME NULL COMMENT '',
  `name_external` VARCHAR(45) NULL COMMENT '',
  `comment` VARCHAR(500) NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `fk_history_status_status1_idx` (`id_status` ASC)  COMMENT '',
  INDEX `fk_history_status_articles1_idx` (`id_articles` ASC)  COMMENT '',
  CONSTRAINT `fk_history_status_status1`
    FOREIGN KEY (`id_status`)
    REFERENCES `recuperalo`.`status` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_history_status_articles1`
    FOREIGN KEY (`id_articles`)
    REFERENCES `recuperalo`.`articles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `recuperalo`.`returns`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recuperalo`.`returns` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `id_articles` INT NOT NULL COMMENT '',
  `name` VARCHAR(50) NULL COMMENT '',
  `surname` VARCHAR(200) NULL COMMENT '',
  `phone` VARCHAR(45) NULL COMMENT '',
  `email` VARCHAR(200) NULL COMMENT '',
  `comment` VARCHAR(200) NULL COMMENT '',
  `warehouse` VARCHAR(200) NULL COMMENT '',
  `phone_warehouse` VARCHAR(45) NULL COMMENT '',
  `date_found` DATETIME NULL COMMENT '',
  `return_date` DATETIME NULL COMMENT '',
  `id_userfound` INT NULL COMMENT '',
  `id_status` INT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `fk_returns_articles1_idx` (`id_articles` ASC)  COMMENT '',
  CONSTRAINT `fk_returns_articles1`
    FOREIGN KEY (`id_articles`)
    REFERENCES `recuperalo`.`articles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `recuperalo`.`token`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recuperalo`.`token` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `id_users` INT NOT NULL COMMENT '',
  `token` VARCHAR(300) NULL COMMENT '',
  `token_created` DATETIME NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `fk_token_users1_idx` (`id_users` ASC)  COMMENT '',
  CONSTRAINT `fk_token_users1`
    FOREIGN KEY (`id_users`)
    REFERENCES `recuperalo`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `recuperalo`.`user_device`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recuperalo`.`user_device` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `id_users` INT NOT NULL COMMENT '',
  `Key_device` VARCHAR(255) NULL COMMENT '',
  `types` INT NULL COMMENT '',
  `date_registration` DATETIME NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `fk_user_device_users1_idx` (`id_users` ASC)  COMMENT '',
  CONSTRAINT `fk_user_device_users1`
    FOREIGN KEY (`id_users`)
    REFERENCES `recuperalo`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema todo_app
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `todo_app` ;

-- -----------------------------------------------------
-- Schema todo_app
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `todo_app` DEFAULT CHARACTER SET utf8 ;
USE `todo_app` ;

-- -----------------------------------------------------
-- Table `todo_app`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `todo_app`.`users` ;

CREATE TABLE IF NOT EXISTS `todo_app`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `register_date` DATETIME NOT NULL,
  `created_todos` INT NOT NULL DEFAULT 0,
  `avatar_url` VARCHAR(255) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `todo_app`.`todos`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `todo_app`.`todos` ;

CREATE TABLE IF NOT EXISTS `todo_app`.`todos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `status` ENUM('Pending', 'In Process', 'Completed') NOT NULL,
  `created_by` INT NULL,
  `created_at` DATETIME NOT NULL,
  `completed_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_todos_users_idx` (`created_by` ASC) VISIBLE,
  CONSTRAINT `fk_todos_users`
    FOREIGN KEY (`created_by`)
    REFERENCES `todo_app`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

SET GLOBAL event_scheduler = ON;

DELIMITER $$
CREATE EVENT daily_delete_temp_user_todos
ON SCHEDULE
    EVERY 1 DAY
DO BEGIN
	DELETE FROM todos
    WHERE created_at < NOW() - INTERVAL 1 DAY;
END $$
DELIMITER ;
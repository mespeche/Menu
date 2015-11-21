
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- menu
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `menu`;

CREATE TABLE `menu`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `visible` TINYINT,
    `position` INTEGER,
    `identifier` VARCHAR(255),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `idx_menu_id` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- menu_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `menu_i18n`;

CREATE TABLE `menu_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `name` VARCHAR(255),
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `menu_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `menu` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;

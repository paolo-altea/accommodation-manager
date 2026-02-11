CREATE TABLE IF NOT EXISTS `#__accommodation_manager_rooms` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',

`ordering` INT(11)  NULL  DEFAULT 0,
`state` TINYINT(1)  NULL  DEFAULT 1,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`created` DATETIME NULL DEFAULT NULL,
`modified_by` INT(11) NULL DEFAULT 0,
`modified` DATETIME NULL DEFAULT NULL,
`version_note` VARCHAR(255) NULL DEFAULT '',
`room_name` VARCHAR(255)  NOT NULL ,
`room_category` INT(10)  NOT NULL  DEFAULT 0,
`room_code` VARCHAR(255)  NOT NULL ,
`room_surface` VARCHAR(50)  NULL  DEFAULT "",
`room_people` VARCHAR(20)  NULL  DEFAULT "",
`room_price_from` VARCHAR(255)  NULL  DEFAULT "",
`room_class` VARCHAR(255)  NULL  DEFAULT "",
`room_title_de` VARCHAR(255)  NULL  DEFAULT "",
`room_title_it` VARCHAR(255)  NULL  DEFAULT "",
`room_title_en` VARCHAR(255)  NULL  DEFAULT "",
`room_title_fr` VARCHAR(255)  NULL  DEFAULT "",
`room_title_es` VARCHAR(255)  NULL  DEFAULT "",
`room_intro_de` TEXT NULL ,
`room_intro_it` TEXT NULL ,
`room_intro_en` TEXT NULL ,
`room_intro_fr` TEXT NULL ,
`room_intro_es` TEXT NULL ,
`room_description_de` TEXT NULL ,
`room_description_it` TEXT NULL ,
`room_description_en` TEXT NULL ,
`room_description_fr` TEXT NULL ,
`room_description_es` TEXT NULL ,
`room_floor_plan` VARCHAR(255)  NULL  DEFAULT "",
`room_floor_plan_alt_de` VARCHAR(255)  NULL  DEFAULT "",
`room_floor_plan_alt_it` VARCHAR(255)  NULL  DEFAULT "",
`room_floor_plan_alt_en` VARCHAR(255)  NULL  DEFAULT "",
`room_floor_plan_alt_fr` VARCHAR(255)  NULL  DEFAULT "",
`room_floor_plan_alt_es` VARCHAR(255)  NULL  DEFAULT "",
`room_thumbnail` VARCHAR(255)  NULL  DEFAULT "",
`room_thumbnail_alt_de` VARCHAR(255)  NULL  DEFAULT "",
`room_thumbnail_alt_it` VARCHAR(255)  NULL  DEFAULT "",
`room_thumbnail_alt_en` VARCHAR(255)  NULL  DEFAULT "",
`room_thumbnail_alt_fr` VARCHAR(255)  NULL  DEFAULT "",
`room_thumbnail_alt_es` VARCHAR(255)  NULL  DEFAULT "",
`room_gallery` TEXT  NULL,
`room_video` VARCHAR(255)  NULL  DEFAULT "",
`room_pano` VARCHAR(255)  NULL  DEFAULT "",
PRIMARY KEY (`id`),
UNIQUE INDEX `idx_room_name` (`room_name`),
INDEX `idx_room_category` (`room_category`),
INDEX `idx_state_ordering` (`state`, `ordering`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__accommodation_manager_room_categories` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NULL  DEFAULT 0,
`state` TINYINT(1)  NULL  DEFAULT 1,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`created` DATETIME NULL DEFAULT NULL,
`modified_by` INT(11) NULL DEFAULT 0,
`modified` DATETIME NULL DEFAULT NULL,
`version_note` VARCHAR(255) NULL DEFAULT '',
`room_category_title` VARCHAR(255)  NOT NULL ,
`room_category_parent` INT(10)  NULL  DEFAULT 0,
`room_category_image` VARCHAR(255) NULL DEFAULT '',
`room_category_image_alt_de` VARCHAR(255) NULL DEFAULT '',
`room_category_image_alt_it` VARCHAR(255) NULL DEFAULT '',
`room_category_image_alt_en` VARCHAR(255) NULL DEFAULT '',
`room_category_image_alt_fr` VARCHAR(255) NULL DEFAULT '',
`room_category_image_alt_es` VARCHAR(255) NULL DEFAULT '',
`room_category_name_de` VARCHAR(255)  NULL  DEFAULT "",
`room_category_name_it` VARCHAR(255)  NULL  DEFAULT "",
`room_category_name_en` VARCHAR(255)  NULL  DEFAULT "",
`room_category_name_fr` VARCHAR(255)  NULL  DEFAULT "",
`room_category_name_es` VARCHAR(255)  NULL  DEFAULT "",
`room_category_description_de` TEXT NULL ,
`room_category_description_it` TEXT NULL ,
`room_category_description_en` TEXT NULL ,
`room_category_description_fr` TEXT NULL ,
`room_category_description_es` TEXT NULL ,
PRIMARY KEY (`id`),
INDEX `idx_room_category_parent` (`room_category_parent`),
INDEX `idx_state_ordering` (`state`, `ordering`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__accommodation_manager_rate_periods` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NULL  DEFAULT 0,
`state` TINYINT(1)  NULL  DEFAULT 1,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`created` DATETIME NULL DEFAULT NULL,
`modified_by` INT(11) NULL DEFAULT 0,
`modified` DATETIME NULL DEFAULT NULL,
`version_note` VARCHAR(255) NULL DEFAULT '',
`period_start` DATE NOT NULL ,
`period_end` DATE NOT NULL ,
`period_title_de` VARCHAR(255)  NULL  DEFAULT "",
`period_title_it` VARCHAR(255)  NULL  DEFAULT "",
`period_title_en` VARCHAR(255)  NULL  DEFAULT "",
`period_title_fr` VARCHAR(255)  NULL  DEFAULT "",
`period_title_es` VARCHAR(255)  NULL  DEFAULT "",
PRIMARY KEY (`id`),
INDEX `idx_state_ordering` (`state`, `ordering`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__accommodation_manager_rates` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NULL  DEFAULT 0,
`state` TINYINT(1)  NULL  DEFAULT 1,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`created` DATETIME NULL DEFAULT NULL,
`modified_by` INT(11) NULL DEFAULT 0,
`modified` DATETIME NULL DEFAULT NULL,
`version_note` VARCHAR(255) NULL DEFAULT '',
`room_id` INT(10)  NOT NULL  DEFAULT 0,
`period_id` INT(10)  NOT NULL  DEFAULT 0,
`typology_id` INT(10)  NOT NULL  DEFAULT 0,
`rate` DECIMAL(10,2) NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
INDEX `idx_room_id` (`room_id`),
INDEX `idx_period_id` (`period_id`),
INDEX `idx_typology_id` (`typology_id`),
INDEX `idx_state` (`state`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__accommodation_manager_rate_typologies` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`rate_typology_title` VARCHAR(255) NOT NULL DEFAULT '',
`ordering` INT(11)  NULL  DEFAULT 0,
`state` TINYINT(1)  NULL  DEFAULT 1,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`created` DATETIME NULL DEFAULT NULL,
`modified_by` INT(11) NULL DEFAULT 0,
`modified` DATETIME NULL DEFAULT NULL,
`version_note` VARCHAR(255) NULL DEFAULT '',
`rate_typology_de` VARCHAR(255)  NULL  DEFAULT "",
`rate_typology_it` VARCHAR(255)  NULL  DEFAULT "",
`rate_typology_en` VARCHAR(255)  NULL  DEFAULT "",
`rate_typology_fr` VARCHAR(255)  NULL  DEFAULT "",
`rate_typology_es` VARCHAR(255)  NULL  DEFAULT "",
PRIMARY KEY (`id`),
INDEX `idx_state_ordering` (`state`, `ordering`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `content_history_options`)
SELECT * FROM ( SELECT 'Room Manager','com_accommodation_manager.roommanager','{"special":{"dbtable":"#__accommodation_manager_rooms","key":"id","type":"RoommanagerTable","prefix":"Joomla\\\\Component\\\\Accommodation_manager\\\\Administrator\\\\Table\\\\"}}', CASE 
                                    WHEN 'rules' is null THEN ''
                                    ELSE ''
                                    END as rules, CASE 
                                    WHEN 'field_mappings' is null THEN ''
                                    ELSE ''
                                    END as field_mappings, '{"formFile":"administrator\/components\/com_accommodation_manager\/forms\/roommanager.xml", "hideFields":["checked_out","checked_out_time","params","language" ,"room_description_es"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"room_category","targetTable":"#__accommodation_manager_room_categories","targetColumn":"id","displayColumn":"room_category_title"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_accommodation_manager.roommanager')
) LIMIT 1;

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `content_history_options`)
SELECT * FROM ( SELECT 'Manager Category','com_accommodation_manager.roommanagercategory','{"special":{"dbtable":"#__accommodation_manager_room_categories","key":"id","type":"RoommanagercategoryTable","prefix":"Joomla\\\\Component\\\\Accommodation_manager\\\\Administrator\\\\Table\\\\"}}', CASE 
                                    WHEN 'rules' is null THEN ''
                                    ELSE ''
                                    END as rules, CASE 
                                    WHEN 'field_mappings' is null THEN ''
                                    ELSE ''
                                    END as field_mappings, '{"formFile":"administrator\/components\/com_accommodation_manager\/forms\/roommanagercategory.xml", "hideFields":["checked_out","checked_out_time","params","language" ,"room_category_description_es"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"room_category_parent","targetTable":"#__accommodation_manager_room_categories","targetColumn":"id","displayColumn":"room_category_title"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_accommodation_manager.roommanagercategory')
) LIMIT 1;

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `content_history_options`)
SELECT * FROM ( SELECT 'Rate Period','com_accommodation_manager.managerrateperiod','{"special":{"dbtable":"#__accommodation_manager_rate_periods","key":"id","type":"ManagerrateperiodTable","prefix":"Joomla\\\\Component\\\\Accommodation_manager\\\\Administrator\\\\Table\\\\"}}', CASE 
                                    WHEN 'rules' is null THEN ''
                                    ELSE ''
                                    END as rules, CASE 
                                    WHEN 'field_mappings' is null THEN ''
                                    ELSE ''
                                    END as field_mappings, '{"formFile":"administrator\/components\/com_accommodation_manager\/forms\/managerrateperiod.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_accommodation_manager.managerrateperiod')
) LIMIT 1;

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `content_history_options`)
SELECT * FROM ( SELECT 'Rate','com_accommodation_manager.managerrate','{"special":{"dbtable":"#__accommodation_manager_rates","key":"id","type":"ManagerrateTable","prefix":"Joomla\\\\Component\\\\Accommodation_manager\\\\Administrator\\\\Table\\\\"}}', CASE 
                                    WHEN 'rules' is null THEN ''
                                    ELSE ''
                                    END as rules, CASE 
                                    WHEN 'field_mappings' is null THEN ''
                                    ELSE ''
                                    END as field_mappings, '{"formFile":"administrator\/components\/com_accommodation_manager\/forms\/managerrate.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"room_id","targetTable":"#__accommodation_manager_rooms","targetColumn":"id","displayColumn":"id"},{"sourceColumn":"period_id","targetTable":"#__accommodation_manager_rate_periods","targetColumn":"id","displayColumn":"id"},{"sourceColumn":"typology_id","targetTable":"#__accommodation_manager_rate_typologies","targetColumn":"id","displayColumn":"id"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_accommodation_manager.managerrate')
) LIMIT 1;

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `content_history_options`)
SELECT * FROM ( SELECT 'Rate Typology','com_accommodation_manager.managerratetypology','{"special":{"dbtable":"#__accommodation_manager_rate_typologies","key":"id","type":"ManagerratetypologyTable","prefix":"Joomla\\\\Component\\\\Accommodation_manager\\\\Administrator\\\\Table\\\\"}}', CASE 
                                    WHEN 'rules' is null THEN ''
                                    ELSE ''
                                    END as rules, CASE 
                                    WHEN 'field_mappings' is null THEN ''
                                    ELSE ''
                                    END as field_mappings, '{"formFile":"administrator\/components\/com_accommodation_manager\/forms\/managerratetypology.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_accommodation_manager.managerratetypology')
) LIMIT 1;

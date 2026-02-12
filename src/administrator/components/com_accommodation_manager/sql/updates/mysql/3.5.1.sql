-- Accommodation Manager 3.5.1
-- Migration from legacy 2.x schema to current version
-- All changes consolidated in a single update file

-- ── rooms: new columns ──

ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_price_from` DECIMAL(10,2) NULL DEFAULT NULL AFTER `room_people`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_class` VARCHAR(255) DEFAULT '' AFTER `room_price_from`;

ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_floor_plan_alt_de` VARCHAR(255) NULL DEFAULT '' AFTER `room_floor_plan`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_floor_plan_alt_it` VARCHAR(255) NULL DEFAULT '' AFTER `room_floor_plan_alt_de`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_floor_plan_alt_en` VARCHAR(255) NULL DEFAULT '' AFTER `room_floor_plan_alt_it`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_floor_plan_alt_fr` VARCHAR(255) NULL DEFAULT '' AFTER `room_floor_plan_alt_en`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_floor_plan_alt_es` VARCHAR(255) NULL DEFAULT '' AFTER `room_floor_plan_alt_fr`;

ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_thumbnail_alt_de` VARCHAR(255) NULL DEFAULT '' AFTER `room_thumbnail`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_thumbnail_alt_it` VARCHAR(255) NULL DEFAULT '' AFTER `room_thumbnail_alt_de`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_thumbnail_alt_en` VARCHAR(255) NULL DEFAULT '' AFTER `room_thumbnail_alt_it`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_thumbnail_alt_fr` VARCHAR(255) NULL DEFAULT '' AFTER `room_thumbnail_alt_en`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_thumbnail_alt_es` VARCHAR(255) NULL DEFAULT '' AFTER `room_thumbnail_alt_fr`;

-- ── rooms: type changes ──

ALTER TABLE `#__accommodation_manager_rooms` MODIFY `room_gallery` TEXT NULL;

UPDATE `#__accommodation_manager_rooms` SET `room_surface` = NULL WHERE `room_surface` = '' OR `room_surface` NOT REGEXP '^[0-9]+$';
ALTER TABLE `#__accommodation_manager_rooms` MODIFY `room_surface` INT NULL DEFAULT NULL;

ALTER TABLE `#__accommodation_manager_rooms` MODIFY `room_people` VARCHAR(20) NULL DEFAULT '';

-- ── rooms: indexes ──

ALTER TABLE `#__accommodation_manager_rooms` ADD UNIQUE INDEX `idx_room_name` (`room_name`);
ALTER TABLE `#__accommodation_manager_rooms` ADD UNIQUE INDEX `idx_room_code` (`room_code`);
ALTER TABLE `#__accommodation_manager_rooms` ADD INDEX `idx_room_category` (`room_category`);
ALTER TABLE `#__accommodation_manager_rooms` ADD INDEX `idx_state_ordering` (`state`, `ordering`);

-- ── rooms: drop obsolete columns ──

ALTER TABLE `#__accommodation_manager_rooms` DROP COLUMN `room_pano`;

-- ── rooms: tracking columns ──

ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `created` DATETIME NULL DEFAULT NULL AFTER `created_by`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `modified_by` INT NULL DEFAULT 0 AFTER `created`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `modified` DATETIME NULL DEFAULT NULL AFTER `modified_by`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `version_note` VARCHAR(255) NULL DEFAULT '' AFTER `modified`;

-- ── room_categories: new columns ──

ALTER TABLE `#__accommodation_manager_room_categories` ADD COLUMN `room_category_image` VARCHAR(255) NULL DEFAULT '' AFTER `room_category_parent`;
ALTER TABLE `#__accommodation_manager_room_categories` ADD COLUMN `room_category_image_alt_de` VARCHAR(255) NULL DEFAULT '' AFTER `room_category_image`;
ALTER TABLE `#__accommodation_manager_room_categories` ADD COLUMN `room_category_image_alt_it` VARCHAR(255) NULL DEFAULT '' AFTER `room_category_image_alt_de`;
ALTER TABLE `#__accommodation_manager_room_categories` ADD COLUMN `room_category_image_alt_en` VARCHAR(255) NULL DEFAULT '' AFTER `room_category_image_alt_it`;
ALTER TABLE `#__accommodation_manager_room_categories` ADD COLUMN `room_category_image_alt_fr` VARCHAR(255) NULL DEFAULT '' AFTER `room_category_image_alt_en`;
ALTER TABLE `#__accommodation_manager_room_categories` ADD COLUMN `room_category_image_alt_es` VARCHAR(255) NULL DEFAULT '' AFTER `room_category_image_alt_fr`;

-- ── room_categories: indexes + tracking ──

ALTER TABLE `#__accommodation_manager_room_categories` ADD INDEX `idx_room_category_parent` (`room_category_parent`);
ALTER TABLE `#__accommodation_manager_room_categories` ADD INDEX `idx_state_ordering` (`state`, `ordering`);

ALTER TABLE `#__accommodation_manager_room_categories` ADD COLUMN `created` DATETIME NULL DEFAULT NULL AFTER `created_by`;
ALTER TABLE `#__accommodation_manager_room_categories` ADD COLUMN `modified_by` INT NULL DEFAULT 0 AFTER `created`;
ALTER TABLE `#__accommodation_manager_room_categories` ADD COLUMN `modified` DATETIME NULL DEFAULT NULL AFTER `modified_by`;
ALTER TABLE `#__accommodation_manager_room_categories` ADD COLUMN `version_note` VARCHAR(255) NULL DEFAULT '' AFTER `modified`;

-- ── rate_typologies: new columns + indexes + tracking ──

ALTER TABLE `#__accommodation_manager_rate_typologies` ADD COLUMN `rate_typology_title` VARCHAR(255) NOT NULL DEFAULT '' AFTER `id`;

ALTER TABLE `#__accommodation_manager_rate_typologies` ADD INDEX `idx_state_ordering` (`state`, `ordering`);

ALTER TABLE `#__accommodation_manager_rate_typologies` ADD COLUMN `created` DATETIME NULL DEFAULT NULL AFTER `created_by`;
ALTER TABLE `#__accommodation_manager_rate_typologies` ADD COLUMN `modified_by` INT NULL DEFAULT 0 AFTER `created`;
ALTER TABLE `#__accommodation_manager_rate_typologies` ADD COLUMN `modified` DATETIME NULL DEFAULT NULL AFTER `modified_by`;
ALTER TABLE `#__accommodation_manager_rate_typologies` ADD COLUMN `version_note` VARCHAR(255) NULL DEFAULT '' AFTER `modified`;

-- ── rates: type changes + indexes + tracking ──

ALTER TABLE `#__accommodation_manager_rates` MODIFY `rate` DECIMAL(10,2) NULL DEFAULT NULL;

ALTER TABLE `#__accommodation_manager_rates` ADD INDEX `idx_room_id` (`room_id`);
ALTER TABLE `#__accommodation_manager_rates` ADD INDEX `idx_period_id` (`period_id`);
ALTER TABLE `#__accommodation_manager_rates` ADD INDEX `idx_typology_id` (`typology_id`);
ALTER TABLE `#__accommodation_manager_rates` ADD INDEX `idx_state` (`state`);

ALTER TABLE `#__accommodation_manager_rates` ADD COLUMN `created` DATETIME NULL DEFAULT NULL AFTER `created_by`;
ALTER TABLE `#__accommodation_manager_rates` ADD COLUMN `modified_by` INT NULL DEFAULT 0 AFTER `created`;
ALTER TABLE `#__accommodation_manager_rates` ADD COLUMN `modified` DATETIME NULL DEFAULT NULL AFTER `modified_by`;
ALTER TABLE `#__accommodation_manager_rates` ADD COLUMN `version_note` VARCHAR(255) NULL DEFAULT '' AFTER `modified`;

-- ── rate_periods: type changes + indexes + tracking ──

ALTER TABLE `#__accommodation_manager_rate_periods` MODIFY `period_start` DATE NOT NULL;
ALTER TABLE `#__accommodation_manager_rate_periods` MODIFY `period_end` DATE NOT NULL;

ALTER TABLE `#__accommodation_manager_rate_periods` ADD INDEX `idx_state_ordering` (`state`, `ordering`);

ALTER TABLE `#__accommodation_manager_rate_periods` ADD COLUMN `created` DATETIME NULL DEFAULT NULL AFTER `created_by`;
ALTER TABLE `#__accommodation_manager_rate_periods` ADD COLUMN `modified_by` INT NULL DEFAULT 0 AFTER `created`;
ALTER TABLE `#__accommodation_manager_rate_periods` ADD COLUMN `modified` DATETIME NULL DEFAULT NULL AFTER `modified_by`;
ALTER TABLE `#__accommodation_manager_rate_periods` ADD COLUMN `version_note` VARCHAR(255) NULL DEFAULT '' AFTER `modified`;

-- ── Collation: update all tables to utf8mb4 ──

ALTER TABLE `#__accommodation_manager_rooms` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__accommodation_manager_room_categories` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__accommodation_manager_rate_periods` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__accommodation_manager_rates` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__accommodation_manager_rate_typologies` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

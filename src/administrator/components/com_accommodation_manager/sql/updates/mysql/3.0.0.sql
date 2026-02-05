-- Accommodation Manager 3.0.0 Update
-- Add new fields for Room edit form

-- Price from field
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_price_from` VARCHAR(255) NULL DEFAULT '' AFTER `room_people`;

-- Floor plan alt text fields
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_floor_plan_alt_de` VARCHAR(255) NULL DEFAULT '' AFTER `room_floor_plan`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_floor_plan_alt_it` VARCHAR(255) NULL DEFAULT '' AFTER `room_floor_plan_alt_de`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_floor_plan_alt_en` VARCHAR(255) NULL DEFAULT '' AFTER `room_floor_plan_alt_it`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_floor_plan_alt_fr` VARCHAR(255) NULL DEFAULT '' AFTER `room_floor_plan_alt_en`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_floor_plan_alt_es` VARCHAR(255) NULL DEFAULT '' AFTER `room_floor_plan_alt_fr`;

-- Thumbnail alt text fields
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_thumbnail_alt_de` VARCHAR(255) NULL DEFAULT '' AFTER `room_thumbnail`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_thumbnail_alt_it` VARCHAR(255) NULL DEFAULT '' AFTER `room_thumbnail_alt_de`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_thumbnail_alt_en` VARCHAR(255) NULL DEFAULT '' AFTER `room_thumbnail_alt_it`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_thumbnail_alt_fr` VARCHAR(255) NULL DEFAULT '' AFTER `room_thumbnail_alt_en`;
ALTER TABLE `#__accommodation_manager_rooms` ADD COLUMN `room_thumbnail_alt_es` VARCHAR(255) NULL DEFAULT '' AFTER `room_thumbnail_alt_fr`;

-- Change room_gallery from VARCHAR to TEXT for JSON subform data
ALTER TABLE `#__accommodation_manager_rooms` MODIFY COLUMN `room_gallery` TEXT NULL;

-- Add rate_typology_title field for backend display
ALTER TABLE `#__accommodation_manager_rate_typologies` ADD COLUMN `rate_typology_title` VARCHAR(255) NOT NULL DEFAULT '' AFTER `id`;

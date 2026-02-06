-- 3.2.0 - Add image field to room categories

ALTER TABLE `#__accommodation_manager_room_categories` ADD COLUMN `room_category_image` VARCHAR(255) NULL DEFAULT '' AFTER `room_category_parent`;
ALTER TABLE `#__accommodation_manager_room_categories` ADD COLUMN `room_category_image_alt_de` VARCHAR(255) NULL DEFAULT '' AFTER `room_category_image`;
ALTER TABLE `#__accommodation_manager_room_categories` ADD COLUMN `room_category_image_alt_it` VARCHAR(255) NULL DEFAULT '' AFTER `room_category_image_alt_de`;
ALTER TABLE `#__accommodation_manager_room_categories` ADD COLUMN `room_category_image_alt_en` VARCHAR(255) NULL DEFAULT '' AFTER `room_category_image_alt_it`;
ALTER TABLE `#__accommodation_manager_room_categories` ADD COLUMN `room_category_image_alt_fr` VARCHAR(255) NULL DEFAULT '' AFTER `room_category_image_alt_en`;
ALTER TABLE `#__accommodation_manager_room_categories` ADD COLUMN `room_category_image_alt_es` VARCHAR(255) NULL DEFAULT '' AFTER `room_category_image_alt_fr`;

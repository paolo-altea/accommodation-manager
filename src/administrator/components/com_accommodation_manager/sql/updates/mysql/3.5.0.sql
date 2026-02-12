-- 3.5.0 - Add UNIQUE constraint on room_code, change surface/price_from types

ALTER TABLE `#__accommodation_manager_rooms` ADD UNIQUE INDEX `idx_room_code` (`room_code`);

-- Convert room_surface from VARCHAR to INT (strip non-numeric data first)
UPDATE `#__accommodation_manager_rooms` SET `room_surface` = NULL WHERE `room_surface` = '' OR `room_surface` NOT REGEXP '^[0-9]+$';
UPDATE `#__accommodation_manager_rooms` SET `room_surface` = CAST(TRIM(`room_surface`) AS UNSIGNED) WHERE `room_surface` IS NOT NULL AND TRIM(`room_surface`) REGEXP '^[0-9]+$';
ALTER TABLE `#__accommodation_manager_rooms` MODIFY `room_surface` INT(11) NULL DEFAULT NULL;

-- Convert room_price_from from VARCHAR to DECIMAL (strip non-numeric data first)
UPDATE `#__accommodation_manager_rooms` SET `room_price_from` = NULL WHERE `room_price_from` = '' OR `room_price_from` NOT REGEXP '^[0-9]+([.,][0-9]+)?$';
UPDATE `#__accommodation_manager_rooms` SET `room_price_from` = REPLACE(`room_price_from`, ',', '.') WHERE `room_price_from` IS NOT NULL;
ALTER TABLE `#__accommodation_manager_rooms` MODIFY `room_price_from` DECIMAL(10,2) NULL DEFAULT NULL;

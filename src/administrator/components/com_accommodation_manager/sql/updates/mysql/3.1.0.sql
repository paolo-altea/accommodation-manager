-- Accommodation Manager 3.1.0 Update
-- Fix column types for better data integrity

-- Change rate from VARCHAR to DECIMAL (NULL = not available)
-- Note: Existing "--" values will become NULL, numeric strings will be converted
ALTER TABLE `#__accommodation_manager_rates` MODIFY COLUMN `rate` DECIMAL(10,2) NULL DEFAULT NULL;

-- Reduce room_surface and room_people field sizes (still VARCHAR for range values like "20-24")
ALTER TABLE `#__accommodation_manager_rooms` MODIFY COLUMN `room_surface` VARCHAR(50) NULL DEFAULT '';
ALTER TABLE `#__accommodation_manager_rooms` MODIFY COLUMN `room_people` VARCHAR(20) NULL DEFAULT '';

-- Change period dates from DATETIME to DATE (time component not needed)
ALTER TABLE `#__accommodation_manager_rate_periods` MODIFY COLUMN `period_start` DATE NOT NULL;
ALTER TABLE `#__accommodation_manager_rate_periods` MODIFY COLUMN `period_end` DATE NOT NULL;

-- Add indexes for foreign keys (improve JOIN performance)
ALTER TABLE `#__accommodation_manager_rooms` ADD INDEX `idx_room_category` (`room_category`);
ALTER TABLE `#__accommodation_manager_room_categories` ADD INDEX `idx_room_category_parent` (`room_category_parent`);
ALTER TABLE `#__accommodation_manager_rates` ADD INDEX `idx_room_id` (`room_id`);
ALTER TABLE `#__accommodation_manager_rates` ADD INDEX `idx_period_id` (`period_id`);
ALTER TABLE `#__accommodation_manager_rates` ADD INDEX `idx_typology_id` (`typology_id`);

-- Add indexes for state and ordering (frequently filtered/sorted)
ALTER TABLE `#__accommodation_manager_rooms` ADD INDEX `idx_state_ordering` (`state`, `ordering`);
ALTER TABLE `#__accommodation_manager_room_categories` ADD INDEX `idx_state_ordering` (`state`, `ordering`);
ALTER TABLE `#__accommodation_manager_rate_periods` ADD INDEX `idx_state_ordering` (`state`, `ordering`);
ALTER TABLE `#__accommodation_manager_rates` ADD INDEX `idx_state` (`state`);
ALTER TABLE `#__accommodation_manager_rate_typologies` ADD INDEX `idx_state_ordering` (`state`, `ordering`);

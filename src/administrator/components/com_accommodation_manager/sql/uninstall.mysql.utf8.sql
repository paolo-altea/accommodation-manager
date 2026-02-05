-- Drop component tables
DROP TABLE IF EXISTS `#__accommodation_manager_rooms`;
DROP TABLE IF EXISTS `#__accommodation_manager_room_categories`;
DROP TABLE IF EXISTS `#__accommodation_manager_rate_periods`;
DROP TABLE IF EXISTS `#__accommodation_manager_rates`;
DROP TABLE IF EXISTS `#__accommodation_manager_rate_typologies`;

-- Remove content type entries
DELETE FROM `#__content_types` WHERE `type_alias` LIKE 'com_accommodation_manager.%';

-- Remove asset entries
DELETE FROM `#__assets` WHERE `name` LIKE 'com_accommodation_manager%';

-- Remove UCM content entries
DELETE FROM `#__ucm_content` WHERE `core_type_alias` LIKE 'com_accommodation_manager.%';

-- Remove action logs config (if exists)
DELETE FROM `#__action_logs_extensions` WHERE `extension` = 'com_accommodation_manager';
DELETE FROM `#__action_log_config` WHERE `type_alias` LIKE 'com_accommodation_manager.%';

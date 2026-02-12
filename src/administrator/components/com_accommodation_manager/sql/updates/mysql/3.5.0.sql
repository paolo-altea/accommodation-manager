-- 3.5.0 - Add UNIQUE constraint on room_code

ALTER TABLE `#__accommodation_manager_rooms` ADD UNIQUE INDEX `idx_room_code` (`room_code`);

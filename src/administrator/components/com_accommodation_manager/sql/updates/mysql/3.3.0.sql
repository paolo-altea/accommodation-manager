-- 3.3.0 - Add room_class column for custom CSS classes

ALTER TABLE `#__accommodation_manager_rooms`
    ADD COLUMN `room_class` VARCHAR(255) DEFAULT '' AFTER `room_price_from`;

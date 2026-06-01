-- Add video columns to temples table
-- Run this migration before using the Video section in add-temple.php

-- For MySQL 8.0.12+ (supports IF NOT EXISTS):
-- ALTER TABLE `temples` ADD COLUMN IF NOT EXISTS `video_url` VARCHAR(500) DEFAULT NULL;
-- ALTER TABLE `temples` ADD COLUMN IF NOT EXISTS `video_thumbnail` VARCHAR(255) DEFAULT NULL;

-- For older MySQL (run these; ignore error if column already exists):
ALTER TABLE `temples` ADD COLUMN `video_url` VARCHAR(500) DEFAULT NULL;
ALTER TABLE `temples` ADD COLUMN `video_thumbnail` VARCHAR(255) DEFAULT NULL;

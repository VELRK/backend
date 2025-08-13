-- Add status field to projects table
ALTER TABLE `projects` ADD COLUMN `status` ENUM('active', 'inactive', 'completed') DEFAULT 'active' AFTER `image`;

-- Update existing projects to have 'active' status
UPDATE `projects` SET `status` = 'active' WHERE `status` IS NULL;

-- Add index on status field for better performance
ALTER TABLE `projects` ADD INDEX `idx_status` (`status`); 
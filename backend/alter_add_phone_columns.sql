-- ALTER TABLE script to add separate phone columns
-- This adds columns for up to 3 phone numbers with labels

ALTER TABLE `osa_support_details`
ADD COLUMN `phone_label_1` VARCHAR(50) DEFAULT NULL AFTER `address`,
ADD COLUMN `phone_number_1` VARCHAR(50) DEFAULT NULL AFTER `phone_label_1`,
ADD COLUMN `phone_label_2` VARCHAR(50) DEFAULT NULL AFTER `phone_number_1`,
ADD COLUMN `phone_number_2` VARCHAR(50) DEFAULT NULL AFTER `phone_label_2`,
ADD COLUMN `phone_label_3` VARCHAR(50) DEFAULT NULL AFTER `phone_number_2`,
ADD COLUMN `phone_number_3` VARCHAR(50) DEFAULT NULL AFTER `phone_label_3`;

-- Optional: Drop the old phone column if you want to remove it completely
-- ALTER TABLE `osa_support_details` DROP COLUMN `phone`;

-- Update existing data (if you have data in the old phone column)
-- This example sets the first phone number
UPDATE `osa_support_details`
SET 
    `phone_label_1` = 'Office',
    `phone_number_1` = '+255 (26) 22610700'
WHERE `id` = 1;

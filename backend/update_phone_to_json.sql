-- Update script for existing osa_support_details table
-- This updates the phone column data to use the new JSON format with labels

-- Update existing phone data to JSON format with labels
-- This assumes the current data is in simple text format
UPDATE `osa_support_details`
SET `phone` = JSON_ARRAY(
    JSON_OBJECT('label', 'Office', 'number', `phone`)
)
WHERE `phone` IS NOT NULL 
  AND `phone` != '' 
  AND `phone` NOT LIKE '[%'  -- Skip if already JSON
  AND `id` = 1;

-- Example: If you want to add multiple phone numbers with labels manually:
-- UPDATE `osa_support_details`
-- SET `phone` = '[{"label":"Office","number":"+255 (26) 22610700"},{"label":"Mobile","number":"+255 123 456 789"}]'
-- WHERE `id` = 1;

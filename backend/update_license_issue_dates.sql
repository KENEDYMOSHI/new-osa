-- Update existing licenses to set issue_date based on payment_date
-- This script populates the newly added issue_date column for existing licenses

UPDATE licenses 
SET issue_date = payment_date 
WHERE issue_date IS NULL 
  AND payment_date IS NOT NULL;

-- For licenses without payment_date, use created_at date
UPDATE licenses 
SET issue_date = DATE(created_at)
WHERE issue_date IS NULL 
  AND created_at IS NOT NULL;

-- Verify the update
SELECT 
    license_number,
    issue_date,
    expiry_date,
    payment_date,
    created_at
FROM licenses
ORDER BY created_at DESC
LIMIT 10;

-- Fix Document Categories in license_application_attachments
-- This script updates documents that should be 'qualification' but are marked as 'attachment'

USE osa_app;

-- Update qualification documents based on document_type
UPDATE license_application_attachments 
SET category = 'qualification'
WHERE document_type IN (
    'psle',           -- Primary School Leaving Certificate
    'csee',           -- Certificate of Secondary Education Examination
    'acsee',          -- Advanced Certificate of Secondary Education Examination
    'veta',           -- VETA Certificate
    'nta4',           -- NTA Level 4
    'nta5',           -- NTA Level 5 (Technician)
    'nta6',           -- NTA Level 6 (Ordinary Diploma)
    'specialized',    -- Other Specialized Certificates
    'bachelor'        -- Bachelor's Degree
)
AND category != 'qualification';

-- Verify the changes
SELECT 
    document_type,
    category,
    COUNT(*) as count
FROM license_application_attachments
GROUP BY document_type, category
ORDER BY document_type, category;

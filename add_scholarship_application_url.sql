-- Youth Education Support: add official scholarship application links
-- Run once in phpMyAdmin while the youth_education_support database is selected.
ALTER TABLE scholarships
ADD COLUMN application_url VARCHAR(500) NULL AFTER description;

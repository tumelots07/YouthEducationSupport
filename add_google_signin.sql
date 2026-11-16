-- Run this once in phpMyAdmin against youth_education_support.
-- Adds Google's stable account identifier without changing existing user accounts.
ALTER TABLE users
    ADD COLUMN google_sub VARCHAR(255) NULL DEFAULT NULL,
    ADD UNIQUE KEY uq_users_google_sub (google_sub);

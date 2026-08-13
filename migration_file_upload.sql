-- Migration: Add file attachment columns to consultations table
-- Run this on existing installations to enable file upload feature

ALTER TABLE consultations ADD COLUMN attachment_file VARCHAR(255) DEFAULT NULL AFTER message;
ALTER TABLE consultations ADD COLUMN attachment_name VARCHAR(255) DEFAULT NULL AFTER attachment_file;

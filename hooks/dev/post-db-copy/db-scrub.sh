#!/bin/sh
#
# db-copy Cloud hook: db-scrub
#
# Scrub important information from a Drupal database.
#
# Usage: db-scrub.sh site target-env db-name source-env

site="$1"
target_env="$2"
db_name="$3"
source_env="$4"

echo "$site.$target_env: Scrubbing database $db_name"

(cat <<EOF
-- 
-- Scrub important information from a Drupal database.
-- 
-- Remove all email addresses.
UPDATE users SET mail=CONCAT('user', uid, '@nagyontitkos.hu'), init=CONCAT('user', uid, '@nagyontitkos.hu') WHERE uid != 0;
-- Remove email addresses of OpenID users.
UPDATE authmap SET mail=CONCAT('user', uid, '@nagyontitkos.hu'), init=CONCAT('user', uid, '@nagyontitkos.hu') WHERE uid != 0;

-- Scrub url aliases for non-admins since these also reveal names
-- Add the IGNORE keyword, since a user may have multiple aliases, and without
-- this keyword the attempt to store duplicate dst values causes the query to fail.
UPDATE IGNORE url_alias SET dst = CONCAT('users/', REPLACE(src,'/', '')) WHERE src IN (SELECT CONCAT('user/', u.uid) FROM users u WHERE u.uid NOT IN (SELECT uid FROM users_roles WHERE rid=3) AND u.uid > 0);
-- don't leave e-mail addresses, etc in comments table.
UPDATE comments SET name='Anonymous', mail='', homepage='http://nagyontitkos.hu' WHERE uid=0;

-- These statements assume you want to preserve real passwords for developers. Change 'rid=3' to the 
-- developer or test role you want to preserve.
-- DRUPAL 7
-- Drupal 7 requires sites to generate a hashed password specific to their site. A script in the 
-- docroot/scripts directory is provided for doing this. 
-- Remove passwords unless users have 'developer role'
UPDATE users SET pass='$S$DMXNF9w9lpy9xux.OJ5kyNgzh5hEerBjdHhF5v.BH7Ekp7PMJsc7' WHERE uid IN (SELECT uid FROM users_roles WHERE rid=3) AND uid > 0;
-- Admin user should not be same but not really well known
UPDATE users SET pass='$S$DFQ1z0sJTAiqizkM4Z0PColNbNy1f.EbIPobs0fZBBmADPRUtjZu' WHERE uid = 1;

-- Remove webform related data.
UPDATE webform_emails SET email='webform-email@nagyontitkos.hu';
TRUNCATE webform_submissions;
TRUNCATE webform_submitted_data;

-- Remove contact email addresses from job post nodes.
UPDATE field_data_field_job_contact_email SET field_job_contact_email_email='job-contact@nagyontitkos.hu';
UPDATE field_revision_field_job_contact_email SET field_job_contact_email_email='job-contact@nagyontitkos.hu';

-- Empty some tables which might contain sensitive data.
TRUNCATE accesslog;
TRUNCATE access;
TRUNCATE blocked_ips;
TRUNCATE flood;
TRUNCATE history;
-- TRUNCATE search_dataset;
-- TRUNCATE search_index;
-- TRUNCATE search_total;
TRUNCATE sessions;
TRUNCATE watchdog;
EOF
) | drush @$site.$target_env ah-sql-cli --db=$db_name
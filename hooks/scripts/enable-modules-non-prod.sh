#!/bin/sh
#
# Hook helper script. Enable necessary modules.
#
# Usage: enable-modules-non-prod.sh drush_alias

drush_alias=$1

# Enable shield and its configuration to protect non production environments
# from search engines.
drush "@${drush_alias}" pm-enable --yes drupalhu_staging

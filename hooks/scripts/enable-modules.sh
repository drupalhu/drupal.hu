#!/bin/sh
#
# Hook helper script. Enable necessary modules.
#
# Usage: enable-modules.sh site target-env db-name source-env

drush_alias=$1

# Enable the acquia specific modules

# Enable site profile information collector for the insight score
drush @$drush_alias pm-enable --yes acquia_spi
drush @$drush_alias pm-enable --yes search_api_acquia

# Enable syslog and disable dblog

drush @$drush_alias pm-disable --yes dblog
drush @$drush_alias pm-enable --yes syslog

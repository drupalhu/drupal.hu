#!/bin/sh
#
# Cloud Hook: post-db-copy
#
# The post-db-copy hook is run whenever you use the Workflow page to copy a
# database from one environment to another. See ../README.md for
# details.
#
# Usage: post-db-copy site target-env db-name source-env

site="$1"
target_env="$2"
db_name="$3"
source_env="$4"

# Enable the acquia specific modules

# Enable site profile information collector for the insight score
drush @$site.$target_env pm-enable --yes acquia_spi

# Enable syslog and disable dblog

drush @$site.$target_env pm-disable --yes dblog
drush @$site.$target_env pm-enable --yes syslog

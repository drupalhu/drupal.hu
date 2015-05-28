#!/bin/sh
#
# Cloud Hook: drush-registry-rebuild
#
# Download registry_rebuild project and run drush registry-rebuild in the target environment. This script works as
# any Cloud hook.

drush_alias=$1

# Execute standard drush commands.
drush @$drush_alias dl registry_rebuild --yes
drush @$drush_alias rr

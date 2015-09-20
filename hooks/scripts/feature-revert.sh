#!/bin/sh
#
# Hook helper script. Enable necessary modules.
#
# Usage: enable-modules.sh site target-env db-name source-env

drush_alias=$1

drush @$drush_alias features-revert-all --yes

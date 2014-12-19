#!/bin/sh
#
# Hook helper script. Enable necessary modules.
#
# Usage: enable-modules.sh site target-env db-name source-env

drush_alias=$1

# Revert specific modules

drush @$drush_alias drush fr --yes drupalhu_multi_index_search

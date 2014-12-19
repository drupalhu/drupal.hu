#!/bin/sh

site="$1"
target_env="$2"
db_name="$3"
source_env="$4"

drush_alias=$site'.'$target_env

# Get the current directory
DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
sh ${DIR}/../../scripts/drush-cache-clear.sh $drush_alias

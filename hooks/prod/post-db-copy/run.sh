#!/bin/bash

site="$1"
target_env="$2"
db_name="$3"
source_env="$4"

drush_alias=$site'.'$target_env

. `dirname $0`/../../scripts/update-db.sh $drush_alias
. `dirname $0`/../../scripts/enable-modules.sh $drush_alias

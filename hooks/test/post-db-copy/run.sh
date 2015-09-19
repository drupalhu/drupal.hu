#!/bin/bash

site="$1"
target_env="$2"
db_name="$3"
source_env="$4"

drush_alias=$site'.'$target_env

. `dirname $0`/../../scripts/drush-registry-rebuild.sh $drush_alias
. `dirname $0`/../../scripts/update-db.sh $drush_alias
. `dirname $0`/../../scripts/enable-modules.sh $drush_alias
. `dirname $0`/../../scripts/enable-modules-non-prod.sh $drush_alias
. `dirname $0`/../../scripts/drush-cache-clear.sh $drush_alias
. `dirname $0`/../../scripts/feature-revert.sh $drush_alias

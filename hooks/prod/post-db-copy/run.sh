#!/bin/sh

site="$1"
target_env="$2"
db_name="$3"
source_env="$4"

../../scripts/update-db.sh
../../scripts/enable-modules.sh
